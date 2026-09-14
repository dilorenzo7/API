<?php

namespace App\Observers;

use App\Models\Peminjaman;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class PeminjamanObserver
{
    public function created(Peminjaman $peminjaman): void
    {
        LogAktivitas::create([
            'user_id'   => Auth::id() ?? $peminjaman->user_id,
            'aktivitas' => "Mengajukan peminjaman baru (ID: {$peminjaman->id}) untuk tanggal {$peminjaman->tgl_pinjam}.",
        ]);
    }

    public function updated(Peminjaman $peminjaman): void
    {
        if ($peminjaman->wasChanged('status')) {
            $statusLama = $peminjaman->getOriginal('status');
            $statusBaru = $peminjaman->status;

            $pesan = match ($statusBaru) {
                'dipinjam'      => "Menyetujui peminjaman ID: {$peminjaman->id} (status: {$statusLama} → {$statusBaru}).",
                'dikembalikan'  => "Mencatat pengembalian peminjaman ID: {$peminjaman->id} (status: {$statusLama} → {$statusBaru}).",
                'telat'         => "Mencatat pengembalian TERLAMBAT peminjaman ID: {$peminjaman->id} (status: {$statusLama} → {$statusBaru}).",
                default         => "Memperbarui status peminjaman ID: {$peminjaman->id} dari '{$statusLama}' menjadi '{$statusBaru}'.",
            };

            LogAktivitas::create([
                'user_id'   => Auth::id() ?? $peminjaman->user_id,
                'aktivitas' => $pesan,
            ]);
        }
    }

    public function deleted(Peminjaman $peminjaman): void
    {
        LogAktivitas::create([
            'user_id'   => Auth::id() ?? $peminjaman->user_id,
            'aktivitas' => "Membatalkan/menolak peminjaman ID: {$peminjaman->id}.",
        ]);
    }
}
