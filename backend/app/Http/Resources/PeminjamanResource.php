<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PeminjamanResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'peminjam'         => [
                'id'   => $this->user->id ?? null,
                'name' => $this->user->name ?? null,
                'role' => $this->user->role ?? null,
            ],
            'tgl_pinjam'       => $this->tgl_pinjam?->format('Y-m-d'),
            'tgl_kembali_plan' => $this->tgl_kembali_plan?->format('Y-m-d'),
            'status'           => $this->status,
            'detail_pinjam'    => DetailPinjamResource::collection($this->whenLoaded('detailPinjam')),
            'pengembalian'     => new PengembalianResource($this->whenLoaded('pengembalian')),
            'created_at'       => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
