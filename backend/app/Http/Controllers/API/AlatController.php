<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Alat\StoreAlatRequest;
use App\Http\Requests\Alat\UpdateAlatRequest;
use App\Http\Resources\AlatResource;
use App\Models\Alat;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class AlatController extends Controller
{
    public function index(): JsonResponse
    {
        $alats = Alat::with('kategori')->get();

        return response()->json([
            'message'    => 'Data alat berhasil diambil.',
            'total_data' => $alats->count(),
            'data'       => AlatResource::collection($alats),
        ]);
    }

    public function store(StoreAlatRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('alat', 'public');
        }

        $alat = Alat::create($data);
        $alat->load('kategori');

        return response()->json([
            'message' => 'Alat berhasil ditambahkan.',
            'data'    => new AlatResource($alat),
        ], 201);
    }

    public function show(Alat $alat): JsonResponse
    {
        $alat->load('kategori');

        return response()->json([
            'message' => 'Detail alat berhasil diambil.',
            'data'    => new AlatResource($alat),
        ]);
    }

    public function update(UpdateAlatRequest $request, Alat $alat): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama jika ada
            if ($alat->gambar) {
                Storage::disk('public')->delete($alat->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('alat', 'public');
        }

        $alat->update($data);
        $alat->load('kategori');

        return response()->json([
            'message' => 'Alat berhasil diperbarui.',
            'data'    => new AlatResource($alat),
        ]);
    }

    public function destroy(Alat $alat): JsonResponse
    {
        if ($alat->detailPinjam()->count() > 0) {
            return response()->json([
                'message' => 'Alat tidak dapat dihapus karena masih memiliki riwayat peminjaman.',
            ], 422);
        }

        if ($alat->gambar) {
            Storage::disk('public')->delete($alat->gambar);
        }

        $alat->delete();

        return response()->json([
            'message' => 'Alat berhasil dihapus.',
        ]);
    }
}
