<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\DetailPinjam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamController extends Controller
{
    // ─── DASHBOARD ────────────────────────────────────────────────────────────

    public function dashboard()
    {
        $user = auth()->user();

        $totalPeminjaman     = Peminjaman::where('user_id', $user->id)->count();
        $sedangDipinjam      = Peminjaman::where('user_id', $user->id)->where('status', 'dipinjam')->count();
        $menungguPersetujuan = Peminjaman::where('user_id', $user->id)->where('status', 'diajukan')->count();
        $totalSelesai        = Peminjaman::where('user_id', $user->id)
                                ->whereIn('status', ['dikembalikan', 'telat'])->count();

        // 5 peminjaman terbaru
        $peminjamanTerbaru = Peminjaman::with(['detailPinjam.alat', 'pengembalian'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        // Preview 6 alat tersedia
        $alatTersedia = Alat::with('kategori')
            ->where('stok', '>', 0)
            ->take(6)
            ->get();

        return view('peminjam.dashboard', compact(
            'user',
            'totalPeminjaman',
            'sedangDipinjam',
            'menungguPersetujuan',
            'totalSelesai',
            'peminjamanTerbaru',
            'alatTersedia'
        ));
    }

    // ─── KATALOG ALAT ─────────────────────────────────────────────────────────

    public function katalogAlat(Request $request)
    {
        $search     = $request->input('search');
        $kategoriId = $request->input('kategori_id');

        $kategoris = Kategori::orderBy('nama_kategori')->get();

        $alats = Alat::with('kategori')
            ->where('stok', '>', 0)
            ->when($search, function ($q) use ($search) {
                $q->where('nama_alat', 'like', "%{$search}%")
                  ->orWhereHas('kategori', function ($q2) use ($search) {
                      $q2->where('nama_kategori', 'like', "%{$search}%");
                  });
            })
            ->when($kategoriId, function ($q) use ($kategoriId) {
                $q->where('kategori_id', $kategoriId);
            })
            ->get();

        return view('peminjam.katalog', compact('alats', 'kategoris', 'search', 'kategoriId'));
    }

    // ─── AJUKAN PEMINJAMAN ────────────────────────────────────────────────────

    public function ajukanPeminjaman(Request $request)
    {
        $request->validate([
            'tgl_pinjam'       => 'required|date',
            'tgl_kembali_plan' => 'required|date|after_or_equal:tgl_pinjam',
            'alat_id'          => 'required|array|min:1',
            'alat_id.*'        => 'exists:alat,id',
            'jumlah'           => 'required|array',
            'jumlah.*'         => 'integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::create([
                'user_id'          => auth()->id(),
                'tgl_pinjam'       => $request->tgl_pinjam,
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status'           => 'diajukan',
            ]);

            foreach ($request->alat_id as $index => $alatId) {
                $jumlah = $request->jumlah[$index] ?? null;
                if (!$jumlah) continue;

                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id'       => $alatId,
                    'jumlah'        => $jumlah,
                ]);
            }

            DB::commit();
            return redirect()->route('peminjam.riwayat')
                ->with('success', 'Pengajuan peminjaman berhasil dikirim. Tunggu persetujuan petugas.');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Gagal mengajukan peminjaman: ' . $e->getMessage());
        }
    }

    // ─── RIWAYAT PEMINJAMAN ───────────────────────────────────────────────────

    public function riwayatPeminjaman(Request $request)
    {
        $status = $request->input('status');

        $riwayat = Peminjaman::with(['detailPinjam.alat', 'pengembalian'])
            ->where('user_id', auth()->id())
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->latest()
            ->paginate(10);

        return view('peminjam.riwayat', compact('riwayat', 'status'));
    }
}