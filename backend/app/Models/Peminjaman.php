<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    // [BARU] Status yang dianggap "masih berjalan" (memblokir penghapusan)
    public const STATUS_AKTIF = ['diajukan', 'dipinjam', 'telat'];

    protected $fillable = [
        'user_id', 'tgl_pinjam', 'tgl_kembali_plan', 'status', 'tgl_ajukan_kembali'
    ];

    protected function casts(): array {
        return [
            'tgl_pinjam' => 'date:Y-m-d',
            'tgl_kembali_plan' => 'date:Y-m-d',
            'tgl_ajukan_kembali' => 'datetime',
        ];
    }

    public function user (): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function detailPinjam(): HasMany {
        return $this->hasMany(DetailPinjam::class);
    }

    public function pengembalian(): HasOne {
        return $this->hasOne(Pengembalian::class);
    }
}