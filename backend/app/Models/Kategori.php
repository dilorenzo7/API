<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $table = 'kategori';
    protected $fillable = ['nama_kategori'];

    public function alat(): HasMany {
        return $this->hasMany(Alat::class);
    }

    // [BARU] True jika ada alat dalam kategori ini yang sedang dipinjam
    public function punyaAlatDipinjam(): bool
    {
        return $this->alat()
            ->whereHas('detailPinjam.peminjaman', fn ($q) => $q->whereIn('status', Peminjaman::STATUS_AKTIF))
            ->exists();
    }
}