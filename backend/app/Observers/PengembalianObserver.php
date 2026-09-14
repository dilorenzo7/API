<?php

namespace App\Observers;

use App\Models\Pengembalian;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class PengembalianObserver
{
    public function created(Pengembalian $pengembalian): void
    {
        $dendaInfo = $pengembalian->denda > 0
            ? " dengan denda Rp " . number_format($pengembalian->denda, 0, ',', '.')
            : ' tanpa denda';

        LogAktivitas::create([
            'user_id'   => Auth::id(),
            'aktivitas' => "Mencatat pengembalian untuk peminjaman ID: {$pengembalian->peminjaman_id}{$dendaInfo}.",
        ]);
    }

    public function updated(Pengembalian $pengembalian): void
    {
        LogAktivitas::create([
            'user_id'   => Auth::id(),
            'aktivitas' => "Memperbarui data pengembalian ID: {$pengembalian->id} (peminjaman ID: {$pengembalian->peminjaman_id}).",
        ]);
    }

    public function deleted(Pengembalian $pengembalian): void
    {
        LogAktivitas::create([
            'user_id'   => Auth::id(),
            'aktivitas' => "Menghapus data pengembalian ID: {$pengembalian->id} (peminjaman ID: {$pengembalian->peminjaman_id}).",
        ]);
    }
}
