<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\KategoriController;
use App\Http\Controllers\API\AlatController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\PeminjamanController;
use App\Http\Controllers\API\PengembalianController;
use App\Http\Controllers\API\LogAktivitasController;
use App\Http\Controllers\API\LaporanController;
use App\Http\Controllers\API\ProfileController;

// ─── Public Routes (Tidak perlu token) ───────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// ─── Protected Routes (Wajib membawa Bearer Token dari Sanctum) ──────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::get('/me',       [AuthController::class, 'me']);
    Route::post('/logout',  [AuthController::class, 'logout']);

    // Profile — bisa diakses semua role yang sudah login
    Route::get('/profile',              [ProfileController::class, 'show']);
    Route::post('/profile/foto',        [ProfileController::class, 'uploadFoto']);
    Route::delete('/profile/foto',      [ProfileController::class, 'destroyFoto']);

    // Alat — bisa diakses semua role yang sudah login (peminjam butuh lihat katalog)
    Route::get('/alat',       [AlatController::class, 'index']);
    Route::get('/alat/{alat}', [AlatController::class, 'show']);

    // ─── Admin only ───────────────────────────────────────────────────────────
    Route::middleware('role.admin')->group(function () {

        // Manajemen User
        Route::apiResource('users', UserController::class);

        // Manajemen Kategori
        Route::apiResource('kategori', KategoriController::class);

        // Manajemen Alat (write operations)
        Route::post('/alat',          [AlatController::class, 'store']);
        Route::put('/alat/{alat}',    [AlatController::class, 'update']);
        Route::patch('/alat/{alat}',  [AlatController::class, 'update']);
        Route::delete('/alat/{alat}', [AlatController::class, 'destroy']);

        // Log Aktivitas
        Route::get('/log-aktivitas', [LogAktivitasController::class, 'index']);

        // Laporan
        Route::get('/laporan-peminjaman', [LaporanController::class, 'index']);

        // Semua peminjaman (admin bisa lihat semua)
        Route::get('/peminjaman',              [PeminjamanController::class, 'index']);
        Route::get('/peminjaman/{peminjaman}', [PeminjamanController::class, 'show']);
        Route::delete('/peminjaman/{peminjaman}', [PeminjamanController::class, 'destroy']);

        // Semua pengembalian
        Route::apiResource('pengembalian', PengembalianController::class);
    });

    // ─── Petugas only ─────────────────────────────────────────────────────────
    Route::middleware('role.petugas')->group(function () {

        // Lihat daftar pengajuan peminjaman
        Route::get('/peminjaman',              [PeminjamanController::class, 'index']);
        Route::get('/peminjaman/{peminjaman}', [PeminjamanController::class, 'show']);

        // Setujui / Tolak peminjaman
        Route::post('/peminjaman/{peminjaman}/setujui', [PeminjamanController::class, 'setujui']);
        Route::delete('/peminjaman/{peminjaman}/tolak', [PeminjamanController::class, 'tolak']);

        // Proses pengembalian
        Route::post('/pengembalian',                    [PengembalianController::class, 'store']);
        Route::get('/pengembalian',                     [PengembalianController::class, 'index']);
        Route::get('/pengembalian/{pengembalian}',      [PengembalianController::class, 'show']);
        Route::put('/pengembalian/{pengembalian}',      [PengembalianController::class, 'update']);
        Route::patch('/pengembalian/{pengembalian}',    [PengembalianController::class, 'update']);

        // Laporan (petugas juga bisa akses)
        Route::get('/laporan-peminjaman', [LaporanController::class, 'index']);
    });

    // ─── Peminjam only ────────────────────────────────────────────────────────
    Route::middleware('role.peminjam')->group(function () {

        // Ajukan peminjaman baru
        Route::post('/peminjaman', [PeminjamanController::class, 'store']);

        // Lihat riwayat peminjaman sendiri
        Route::get('/peminjaman',              [PeminjamanController::class, 'index']);
        Route::get('/peminjaman/{peminjaman}', [PeminjamanController::class, 'show']);

        // Batalkan peminjaman yang masih "diajukan"
        Route::delete('/peminjaman/{peminjaman}', [PeminjamanController::class, 'destroy']);
    });
});
