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
    // ─── DASHBOARD ────────────────────────────────────────────────────────────

    public function dashboard()
    {
        $today = Carbon::today();

        $menungguPersetujuan = Peminjaman::where('status', 'diajukan')->count();

        // Belum dikembalikan = status dipinjam/telat dan belum punya record pengembalian
        $belumKembali = Peminjaman::whereIn('status', ['dipinjam', 'telat'])
            ->whereDoesntHave('pengembalian');

        $sedangDipinjam = (clone $belumKembali)->count();
        $terlambat      = (clone $belumKembali)->whereDate('tgl_kembali_plan', '<', $today)->count();

        $dikembalikanHariIni = Pengembalian::whereDate('tgl_kembali', $today)->count();

        // 5 pengajuan terbaru yang menunggu persetujuan
        $pengajuanTerbaru = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->where('status', 'diajukan')
            ->latest()
            ->take(5)
            ->get();

        // 5 peminjaman yang paling dekat / lewat jatuh tempo
        $jatuhTempo = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->whereIn('status', ['dipinjam', 'telat'])
            ->whereDoesntHave('pengembalian')
            ->orderBy('tgl_kembali_plan')
            ->take(5)
            ->get();

        return view('petugas.dashboard', compact(
            'menungguPersetujuan',
            'sedangDipinjam',
            'terlambat',
            'dikembalikanHariIni',
            'pengajuanTerbaru',
            'jatuhTempo'
        ));
    }

    // ─── PERSETUJUAN PEMINJAMAN ───────────────────────────────────────────────

    public function indexPeminjaman()
    {
        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->latest()
            ->get()
            ->map(function ($p) {
                $stokKurang = [];

                // Cek stok hanya untuk peminjaman yang masih menunggu persetujuan
                if ($p->status === 'diajukan') {
                    foreach ($p->detailPinjam->groupBy('alat_id') as $alatId => $rows) {
                        $alat  = $rows->first()->alat;
                        $butuh = $rows->sum('jumlah');

                        if (!$alat) {
                            $stokKurang[$alatId] = [
                                'nama'  => 'Alat Dihapus',
                                'stok'  => 0,
                                'butuh' => $butuh,
                            ];
                        } elseif ($alat->stok < $butuh) {
                            $stokKurang[$alatId] = [
                                'nama'  => $alat->nama_alat,
                                'stok'  => max(0, $alat->stok),
                                'butuh' => $butuh,
                            ];
                        }
                    }
                }

                $p->stok_kurang = $stokKurang;
                return $p;
            });

        return view('petugas.peminjaman.index', compact('peminjamans'));
    }

    public function setujuiPeminjaman($id)
    {
        DB::beginTransaction();
        try {
            // Kunci baris peminjaman supaya tidak diproses dua kali bersamaan
            $peminjaman = Peminjaman::with('detailPinjam')->lockForUpdate()->findOrFail($id);

            // Hanya peminjaman berstatus "diajukan" yang boleh disetujui
            if ($peminjaman->status !== 'diajukan') {
                DB::rollBack();
                return redirect()->back()->with(
                    'error',
                    'Peminjaman ini sudah diproses sebelumnya (status: ' . $peminjaman->status . ').'
                );
            }

            // Total kebutuhan per alat (jaga-jaga kalau alat yang sama muncul di lebih dari satu baris)
            $kebutuhan = $peminjaman->detailPinjam
                ->groupBy('alat_id')
                ->map(fn ($rows) => $rows->sum('jumlah'));

            // Kunci baris alat supaya stok tidak berubah di tengah proses
            $alatList = Alat::whereIn('id', $kebutuhan->keys())
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            // Validasi stok SEBELUM ada yang dikurangi
            foreach ($kebutuhan as $alatId => $jumlah) {
                $alat = $alatList->get($alatId);

                if (!$alat) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Alat dengan ID ' . $alatId . ' tidak ditemukan.');
                }

                if ($alat->stok < $jumlah) {
                    DB::rollBack();
                    $keterangan = $alat->stok <= 0 ? 'habis' : 'tidak mencukupi';
                    return redirect()->back()->with(
                        'error',
                        'Stok ' . $alat->nama_alat . ' ' . $keterangan . ' (tersisa ' . max(0, $alat->stok) . ', diminta ' . $jumlah . '). Peminjaman tidak bisa disetujui.'
                    );
                }
            }

            // Semua stok cukup, baru kurangi
            foreach ($kebutuhan as $alatId => $jumlah) {
                $alatList->get($alatId)->decrement('stok', $jumlah);
            }

            $peminjaman->update(['status' => 'dipinjam']);

            DB::commit();
            return redirect()->back()->with('success', 'Peminjaman disetujui dan stok alat dikurangi.');
        } catch (\Exception $e) {
            DB::rollBack();
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
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ─── PERSETUJUAN PENGEMBALIAN ─────────────────────────────────────────────

    public function indexPengembalian()
    {
        // Tampilkan peminjaman yang sedang dipinjam / telat dan BELUM punya record pengembalian
        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat'])
            ->whereIn('status', ['dipinjam', 'telat'])
            ->whereDoesntHave('pengembalian')
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
            $peminjaman = Peminjaman::with(['detailPinjam', 'pengembalian'])
                ->lockForUpdate()
                ->findOrFail($peminjamanId);

            // Tolak jika sudah pernah dikembalikan
            if ($peminjaman->pengembalian) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Peminjaman ini sudah dikembalikan sebelumnya.');
            }

            // Hanya peminjaman yang sedang dipinjam / telat yang boleh dikembalikan
            if (!in_array($peminjaman->status, ['dipinjam', 'telat'])) {
                DB::rollBack();
                return redirect()->back()->with(
                    'error',
                    'Peminjaman berstatus "' . $peminjaman->status . '" tidak bisa dikembalikan.'
                );
            }

            // Hitung denda otomatis jika tidak diisi manual (Rp 1.000/hari)
            $tglKembali    = now()->toDateString();
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
                Alat::findOrFail($detail->alat_id)->increment('stok', $detail->jumlah);
            }

            $statusBaru = Carbon::today()->gt(Carbon::parse($peminjaman->tgl_kembali_plan))
                ? 'telat' : 'dikembalikan';
            $peminjaman->update(['status' => $statusBaru]);

            DB::commit();
            return redirect()->back()->with('success', 'Pengembalian berhasil dicatat dan stok dipulihkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ─── CETAK LAPORAN ────────────────────────────────────────────────────────

    public function laporanIndex(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'status'     => 'nullable|in:diajukan,dipinjam,telat,dikembalikan',
        ]);

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
        $totalSemua        = $peminjamans->count();
        $totalDikembalikan = $peminjamans->whereIn('status', ['dikembalikan', 'telat'])->count();
        $totalDipinjam     = $peminjamans->where('status', 'dipinjam')->count();
        $totalDenda        = $peminjamans->sum(fn($p) => $p->pengembalian->denda ?? 0);

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