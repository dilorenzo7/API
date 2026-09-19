<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PetugasController;
use App\Http\Controllers\PeminjamController;
use App\Http\Controllers\AuthController;

// Landing Page
Route::get('/', function () {
    return view('welcome');
});

// Group Route: Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // CRUD User
    Route::get('/users', [AdminController::class, 'indexUser'])->name('user.index');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('user.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('user.store');
    Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('user.edit');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('user.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('user.destroy');

    // CRUD Kategori
    Route::get('/kategori', [AdminController::class, 'indexKategori'])->name('kategori.index');
    Route::get('/kategori/create', [AdminController::class, 'createKategori'])->name('kategori.create');
    Route::post('/kategori', [AdminController::class, 'storeKategori'])->name('kategori.store');
    Route::get('/kategori/{id}/edit', [AdminController::class, 'editKategori'])->name('kategori.edit');
    Route::put('/kategori/{id}', [AdminController::class, 'updateKategori'])->name('kategori.update');
    Route::delete('/kategori/{id}', [AdminController::class, 'destroyKategori'])->name('kategori.destroy');

    // CRUD Alat
    Route::get('/alat', [AdminController::class, 'indexAlat'])->name('alat.index');
    Route::get('/alat/create', [AdminController::class, 'createAlat'])->name('alat.create');
    Route::post('/alat', [AdminController::class, 'storeAlat'])->name('alat.store');
    Route::get('/alat/{id}/edit', [AdminController::class, 'editAlat'])->name('alat.edit');
    Route::put('/alat/{id}', [AdminController::class, 'updateAlat'])->name('alat.update');
    Route::delete('/alat/{id}', [AdminController::class, 'destroyAlat'])->name('alat.destroy');

    // CRUD Peminjaman
    Route::get('/peminjaman', [AdminController::class, 'indexPeminjaman'])->name('peminjaman.index');
    Route::get('/peminjaman/create', [AdminController::class, 'createPeminjaman'])->name('peminjaman.create');
    Route::post('/peminjaman', [AdminController::class, 'storePeminjaman'])->name('peminjaman.store');
    Route::put('/peminjaman/{id}/status', [AdminController::class, 'updateStatusPeminjaman'])->name('peminjaman.updateStatus');
    Route::delete('/peminjaman/{id}', [AdminController::class, 'destroyPeminjaman'])->name('peminjaman.destroy');

    // CRUD Pengembalian
    Route::get('/pengembalian', [AdminController::class, 'indexPengembalian'])->name('pengembalian.index');
    Route::get('/pengembalian/{id}/create', [AdminController::class, 'createPengembalian'])->name('pengembalian.create');
    Route::post('/pengembalian/{id}', [AdminController::class, 'storePengembalian'])->name('pengembalian.store');
    Route::delete('/pengembalian/{id}', [AdminController::class, 'destroyPengembalian'])->name('pengembalian.destroy');
});

// Group Route: Petugas (Bisa diakses oleh Petugas & Admin)
Route::middleware(['auth', 'role:petugas,admin'])->prefix('petugas')->name('petugas.')->group(function () {
    // Persetujuan Peminjaman
    Route::get('/peminjaman', [PetugasController::class, 'indexPeminjaman'])->name('peminjaman.index');
    Route::post('/peminjaman/{id}/setujui', [PetugasController::class, 'setujuiPeminjaman'])->name('peminjaman.setujui');
    Route::delete('/peminjaman/{id}/tolak', [PetugasController::class, 'tolakPeminjaman'])->name('peminjaman.tolak');

    // Persetujuan Pengembalian
    Route::get('/pengembalian', [PetugasController::class, 'indexPengembalian'])->name('pengembalian.index');
    Route::post('/pengembalian/{id}', [PetugasController::class, 'prosesPengembalian'])->name('pengembalian.proses');

    // Cetak Laporan
    Route::get('/laporan', [PetugasController::class, 'laporanIndex'])->name('laporan.index');
    Route::get('/laporan/cetak-pdf', [PetugasController::class, 'cetakPdf'])->name('laporan.cetakPdf');
});

// Group Route: Peminjam
Route::middleware(['auth', 'role:peminjam'])->prefix('peminjam')->name('peminjam.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [PeminjamController::class, 'dashboard'])->name('dashboard');

    // Katalog & Pengajuan
    Route::get('/katalog', [PeminjamController::class, 'katalogAlat'])->name('katalog');
    Route::post('/peminjaman/ajukan', [PeminjamController::class, 'ajukanPeminjaman'])->name('peminjaman.ajukan');
    Route::get('/riwayat', [PeminjamController::class, 'riwayatPeminjaman'])->name('riwayat');
});

// Route Tamu / Guest (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Route Profil (Semua role yang sudah login)
Route::middleware('auth')->group(function () {
    Route::get('/profile',             [AuthController::class, 'showProfile'])->name('profile.show');
    Route::put('/profile/foto',        [AuthController::class, 'updateFoto'])->name('profile.updateFoto');
    Route::delete('/profile/foto',     [AuthController::class, 'destroyFoto'])->name('profile.destroyFoto');
});

// Route Logout (Harus sudah login)
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');