<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\Alat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetugasController extends Controller
{
    // ─── PERSETUJUAN PEMINJAMAN ───────────────────────────────────────────────

    public function indexPeminjaman()
    {
        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->latest()
            ->get();
        return view('petugas.peminjaman.index', compact('peminjamans'));
    }

    public function setujuiPeminjaman($id)
    {
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($id);
            $peminjaman->update(['status' => 'dipinjam']);

            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok -= $detail->jumlah;
                $alat->save();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function tolakPeminjaman($id)
    {
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::where('status', 'diajukan')->findOrFail($id);
            $peminjaman->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman berhasil ditolak. Peminjam dapat mengajukan ulang.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ─── PERSETUJUAN PENGEMBALIAN ─────────────────────────────────────────────

    public function indexPengembalian()
    {
        // Tampilkan peminjaman yang sedang dipinjam / telat (belum dikembalikan)
        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->whereIn('status', ['dipinjam', 'telat'])
            ->latest()
            ->get()
            ->map(function ($p) {
                // Hitung keterlambatan untuk tiap baris
                $p->hari_terlambat = Carbon::today()->gt(Carbon::parse($p->tgl_kembali_plan))
                    ? Carbon::parse($p->tgl_kembali_plan)->diffInDays(Carbon::today())
                    : 0;
                $p->estimasi_denda = $p->hari_terlambat * 1000;
                return $p;
            });

        return view('petugas.pengembalian.index', compact('peminjamans'));
    }

    public function prosesPengembalian(Request $request, $peminjamanId)
    {
        $request->validate([
            'kondisi_kembali' => 'required|string',
            'denda'           => 'nullable|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($peminjamanId);

            // Hitung denda otomatis jika tidak diisi manual (Rp 1.000/hari)
            $tglKembali = now()->toDateString();
            $dendaOtomatis = 0;
            if (Carbon::today()->gt(Carbon::parse($peminjaman->tgl_kembali_plan))) {
                $telatHari     = Carbon::parse($peminjaman->tgl_kembali_plan)->diffInDays(Carbon::today());
                $dendaOtomatis = $telatHari * 1000;
            }

            Pengembalian::create([
                'peminjaman_id'   => $peminjaman->id,
                'tgl_kembali'     => $tglKembali,
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda'           => $request->filled('denda') ? $request->denda : $dendaOtomatis,
                'petugas_id'      => auth()->id(),
            ]);

            // Kembalikan stok alat ke inventaris
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                $alat->stok += $detail->jumlah;
                $alat->save();
            }

            $statusBaru = Carbon::today()->gt(Carbon::parse($peminjaman->tgl_kembali_plan))
                ? 'telat' : 'dikembalikan';
            $peminjaman->update(['status' => $statusBaru]);

            DB::commit();
            return redirect()->back()->with('success', 'Pengembalian berhasil dicatat dan stok dipulihkan.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ─── CETAK LAPORAN ────────────────────────────────────────────────────────

    public function laporanIndex(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $status    = $request->input('status');

        $query = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian']);

        if ($startDate && $endDate) {
            $query->whereBetween('tgl_pinjam', [$startDate, $endDate]);
        }
        if ($status) {
            $query->where('status', $status);
        }

        $peminjamans = $query->latest()->get();

        // Ringkasan statistik
        $totalSemua       = $peminjamans->count();
        $totalDikembalikan = $peminjamans->whereIn('status', ['dikembalikan', 'telat'])->count();
        $totalDipinjam    = $peminjamans->where('status', 'dipinjam')->count();
        $totalDenda       = $peminjamans->sum(fn($p) => $p->pengembalian->denda ?? 0);

        return view('petugas.laporan.index', compact(
            'peminjamans',
            'startDate',
            'endDate',
            'status',
            'totalSemua',
            'totalDikembalikan',
            'totalDipinjam',
            'totalDenda'
        ));
    }
}
