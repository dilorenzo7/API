<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailPinjamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'     => $this->id,
            'alat'   => [
                'id'        => $this->alat->id ?? null,
                'nama_alat' => $this->alat->nama_alat ?? null,
                'gambar'    => $this->alat->gambar ? url('storage/' . $this->alat->gambar) : null,
            ],
            'jumlah' => $this->jumlah,
        ];
    }
}
