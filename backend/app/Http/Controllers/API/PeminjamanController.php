<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Peminjaman\StorePeminjamanRequest;
use App\Http\Resources\PeminjamanResource;
use App\Models\Alat;
use App\Models\DetailPinjam;
use App\Models\Peminjaman;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    public function index(): JsonResponse
    {
        $peminjamans = Peminjaman::with(['user', 'detailPinjam.alat', 'pengembalian'])
            ->latest()
            ->get();

        return response()->json([
            'message'    => 'Data peminjaman berhasil diambil.',
            'total_data' => $peminjamans->count(),
            'data'       => PeminjamanResource::collection($peminjamans),
        ]);
    }

    public function store(StorePeminjamanRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            // Validasi stok setiap alat
            foreach ($request->detail as $item) {
                $alat = Alat::findOrFail($item['alat_id']);
                if ($alat->stok < $item['jumlah']) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "Stok alat '{$alat->nama_alat}' tidak mencukupi. Stok tersedia: {$alat->stok}.",
                    ], 422);
                }
            }

            // Buat header peminjaman
            $peminjaman = Peminjaman::create([
                'user_id'          => auth()->id(),
                'tgl_pinjam'       => $request->tgl_pinjam,
                'tgl_kembali_plan' => $request->tgl_kembali_plan,
                'status'           => 'diajukan',
            ]);

            // Buat detail peminjaman
            foreach ($request->detail as $item) {
                DetailPinjam::create([
                    'peminjaman_id' => $peminjaman->id,
                    'alat_id'       => $item['alat_id'],
                    'jumlah'        => $item['jumlah'],
                ]);
            }

            DB::commit();
            $peminjaman->load(['user', 'detailPinjam.alat']);

            return response()->json([
                'message' => 'Pengajuan peminjaman berhasil dibuat.',
                'data'    => new PeminjamanResource($peminjaman),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat membuat peminjaman.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Peminjaman $peminjaman): JsonResponse
    {
        $peminjaman->load(['user', 'detailPinjam.alat', 'pengembalian.petugas']);

        return response()->json([
            'message' => 'Detail peminjaman berhasil diambil.',
            'data'    => new PeminjamanResource($peminjaman),
        ]);
    }

    public function destroy(Peminjaman $peminjaman): JsonResponse
    {
        if ($peminjaman->status !== 'diajukan') {
            return response()->json([
                'message' => 'Hanya peminjaman dengan status "diajukan" yang dapat dibatalkan.',
            ], 422);
        }

        $peminjaman->delete();

        return response()->json([
            'message' => 'Peminjaman berhasil dibatalkan.',
        ]);
    }

    // Petugas/Admin: setujui peminjaman (ubah status → dipinjam & kurangi stok)
    public function setujui(Peminjaman $peminjaman): JsonResponse
    {
        if ($peminjaman->status !== 'diajukan') {
            return response()->json([
                'message' => 'Hanya peminjaman dengan status "diajukan" yang dapat disetujui.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Cek & kurangi stok
            foreach ($peminjaman->detailPinjam as $detail) {
                $alat = Alat::findOrFail($detail->alat_id);
                if ($alat->stok < $detail->jumlah) {
                    DB::rollBack();
                    return response()->json([
                        'message' => "Stok alat '{$alat->nama_alat}' tidak mencukupi.",
                    ], 422);
                }
                $alat->decrement('stok', $detail->jumlah);
            }

            $peminjaman->update(['status' => 'dipinjam']);
            DB::commit();

            $peminjaman->load(['user', 'detailPinjam.alat']);
            return response()->json([
                'message' => 'Peminjaman berhasil disetujui.',
                'data'    => new PeminjamanResource($peminjaman),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyetujui peminjaman.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // Petugas/Admin: tolak peminjaman (hapus data)
    public function tolak(Peminjaman $peminjaman): JsonResponse
    {
        if ($peminjaman->status !== 'diajukan') {
            return response()->json([
                'message' => 'Hanya peminjaman dengan status "diajukan" yang dapat ditolak.',
            ], 422);
        }

        $peminjaman->delete();

        return response()->json([
            'message' => 'Peminjaman berhasil ditolak dan dihapus.',
        ]);
    }
}
