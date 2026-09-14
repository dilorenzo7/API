<?php

namespace App\Observers;

use App\Models\Alat;
use App\Models\LogAktivitas;
use Illuminate\Support\Facades\Auth;

class AlatObserver
{
    public function created(Alat $alat): void
    {
        LogAktivitas::create([
            'user_id'    => Auth::id(),
            'aktivitas'  => "Menambahkan alat baru: '{$alat->nama_alat}' (stok: {$alat->stok}).",
        ]);
    }

    public function updated(Alat $alat): void
    {
        $perubahan = [];

        if ($alat->wasChanged('nama_alat')) {
            $perubahan[] = "nama dari '{$alat->getOriginal('nama_alat')}' menjadi '{$alat->nama_alat}'";
        }
        if ($alat->wasChanged('stok')) {
            $perubahan[] = "stok dari {$alat->getOriginal('stok')} menjadi {$alat->stok}";
        }
        if ($alat->wasChanged('status_kondisi')) {
            $perubahan[] = "kondisi dari '{$alat->getOriginal('status_kondisi')}' menjadi '{$alat->status_kondisi}'";
        }

        if (!empty($perubahan)) {
            LogAktivitas::create([
                'user_id'   => Auth::id(),
                'aktivitas' => "Memperbarui alat '{$alat->nama_alat}': " . implode(', ', $perubahan) . '.',
            ]);
        }
    }

    public function deleted(Alat $alat): void
    {
        LogAktivitas::create([
            'user_id'   => Auth::id(),
            'aktivitas' => "Menghapus alat: '{$alat->nama_alat}'.",
        ]);
    }
}
