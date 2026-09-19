<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\UpdateFotoProfileRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // GET /api/profile — lihat profil sendiri
    public function show(): JsonResponse
    {
        return response()->json([
            'message' => 'Data profil berhasil diambil.',
            'data'    => new UserResource(auth()->user()),
        ]);
    }

    // POST /api/profile/foto — upload atau ganti foto profil
    public function uploadFoto(UpdateFotoProfileRequest $request): JsonResponse
    {
        $user = auth()->user();

        // Hapus foto lama jika ada
        if ($user->foto_profile) {
            Storage::disk('public')->delete($user->foto_profile);
        }

        // Simpan foto baru
        $path = $request->file('foto_profile')->store('foto_profile', 'public');
        $user->update(['foto_profile' => $path]);

        return response()->json([
            'message'      => 'Foto profil berhasil diperbarui.',
            'foto_profile' => url('storage/' . $path),
        ]);
    }

    // DELETE /api/profile/foto — hapus foto profil
    public function destroyFoto(): JsonResponse
    {
        $user = auth()->user();

        if (!$user->foto_profile) {
            return response()->json([
                'message' => 'Anda belum memiliki foto profil.',
            ], 422);
        }

        Storage::disk('public')->delete($user->foto_profile);
        $user->update(['foto_profile' => null]);

        return response()->json([
            'message' => 'Foto profil berhasil dihapus.',
        ]);
    }
}
