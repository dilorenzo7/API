<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pengembalian\StorePengembalianRequest;
use App\Http\Requests\Pengembalian\UpdatePengembalianRequest;
use App\Http\Resources\PengembalianResource;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PengembalianController extends Controller
{
    public function index(): JsonResponse
    {
        $pengembalians = Pengembalian::with(['peminjaman.user', 'petugas'])
            ->latest()
            ->get();

        return response()->json([
            'message'    => 'Data pengembalian berhasil diambil.',
            'total_data' => $pengembalians->count(),
            'data'       => PengembalianResource::collection($pengembalians),
        ]);
    }

    public function store(StorePengembalianRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $peminjaman = Peminjaman::with('detailPinjam')->findOrFail($request->peminjaman_id);

            if ($peminjaman->status !== 'dipinjam') {
                return response()->json([
                    'message' => 'Hanya peminjaman dengan status "dipinjam" yang dapat dikembalikan.',
                ], 422);
            }

            // Cek apakah sudah ada pengembalian untuk peminjaman ini
            if ($peminjaman->pengembalian()->exists()) {
                return response()->json([
                    'message' => 'Pengembalian untuk peminjaman ini sudah tercatat.',
                ], 422);
            }

            // Hitung denda otomatis jika terlambat (Rp 1.000/hari)
            $tglKembali     = \Carbon\Carbon::parse($request->tgl_kembali);
            $tglKembaliPlan = \Carbon\Carbon::parse($peminjaman->tgl_kembali_plan);
            $denda          = 0;

            if ($tglKembali->gt($tglKembaliPlan)) {
                $selisihHari = $tglKembaliPlan->diffInDays($tglKembali);
                $denda       = $selisihHari * 1000;
            }

            // Simpan pengembalian
            $pengembalian = Pengembalian::create([
                'peminjaman_id'   => $peminjaman->id,
                'tgl_kembali'     => $request->tgl_kembali,
                'kondisi_kembali' => $request->kondisi_kembali,
                'denda'           => $request->denda ?? $denda,
                'petugas_id'      => auth()->id(),
            ]);

            // Kembalikan stok alat
            foreach ($peminjaman->detailPinjam as $detail) {
                Alat::where('id', $detail->alat_id)->increment('stok', $detail->jumlah);
            }

            // Update status peminjaman
            $statusBaru = $tglKembali->gt($tglKembaliPlan) ? 'telat' : 'dikembalikan';
            $peminjaman->update(['status' => $statusBaru]);

            DB::commit();
            $pengembalian->load(['peminjaman.user', 'petugas']);

            return response()->json([
                'message' => 'Pengembalian berhasil dicatat.',
                'data'    => new PengembalianResource($pengembalian),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat mencatat pengembalian.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function show(Pengembalian $pengembalian): JsonResponse
    {
        $pengembalian->load(['peminjaman.user', 'peminjaman.detailPinjam.alat', 'petugas']);

        return response()->json([
            'message' => 'Detail pengembalian berhasil diambil.',
            'data'    => new PengembalianResource($pengembalian),
        ]);
    }

    public function update(UpdatePengembalianRequest $request, Pengembalian $pengembalian): JsonResponse
    {
        $pengembalian->update($request->validated());

        return response()->json([
            'message' => 'Data pengembalian berhasil diperbarui.',
            'data'    => new PengembalianResource($pengembalian),
        ]);
    }

    public function destroy(Pengembalian $pengembalian): JsonResponse
    {
        DB::beginTransaction();
        try {
            $peminjaman = $pengembalian->peminjaman()->with('detailPinjam')->first();

            // Kurangi kembali stok (batalkan pengembalian)
            foreach ($peminjaman->detailPinjam as $detail) {
                Alat::where('id', $detail->alat_id)->decrement('stok', $detail->jumlah);
            }

            // Kembalikan status peminjaman ke dipinjam
            $peminjaman->update(['status' => 'dipinjam']);

            $pengembalian->delete();
            DB::commit();

            return response()->json([
                'message' => 'Data pengembalian berhasil dihapus dan status peminjaman dikembalikan.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Terjadi kesalahan saat menghapus pengembalian.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
