<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        $users = User::all();

        return response()->json([
            'message'    => 'Data user berhasil diambil.',
            'total_data' => $users->count(),
            'data'       => UserResource::collection($users),
        ]);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        if ($request->hasFile('foto_profile')) {
            $data['foto_profile'] = $request->file('foto_profile')->store('foto_profile', 'public');
        }

        $user = User::create($data);

        return response()->json([
            'message' => 'User berhasil ditambahkan.',
            'data'    => new UserResource($user),
        ], 201);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json([
            'message' => 'Detail user berhasil diambil.',
            'data'    => new UserResource($user),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();

        // Admin tidak boleh mengubah role akunnya sendiri
        if ($user->id === auth()->id() && isset($data['role']) && $data['role'] !== $user->role) {
            return response()->json([
                'message' => 'Anda tidak dapat mengubah role akun Anda sendiri.',
            ], 422);
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        if ($request->hasFile('foto_profile')) {
            if ($user->foto_profile) {
                Storage::disk('public')->delete($user->foto_profile);
            }
            $data['foto_profile'] = $request->file('foto_profile')->store('foto_profile', 'public');
        }

        $user->update($data);

        return response()->json([
            'message' => 'User berhasil diperbarui.',
            'data'    => new UserResource($user),
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        if ($user->id === auth()->id()) {
            return response()->json([
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri.',
            ], 422);
        }

        // [BARU] User yang masih punya peminjaman berjalan tidak boleh dihapus
        if ($user->punyaPeminjamanAktif()) {
            return response()->json([
                'message' => 'User tidak dapat dihapus karena masih memiliki peminjaman yang belum selesai.',
            ], 422);
        }

        if ($user->foto_profile) {
            Storage::disk('public')->delete($user->foto_profile);
        }

        $user->delete();

        return response()->json([
            'message' => 'User berhasil dihapus.',
        ]);
    }
}