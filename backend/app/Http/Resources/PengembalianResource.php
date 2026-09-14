<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PengembalianResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'peminjaman_id'   => $this->peminjaman_id,
            'tgl_kembali'     => $this->tgl_kembali?->format('Y-m-d'),
            'kondisi_kembali' => $this->kondisi_kembali,
            'denda'           => $this->denda,
            'petugas'         => [
                'id'   => $this->petugas->id ?? null,
                'name' => $this->petugas->name ?? null,
            ],
            'created_at'      => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
