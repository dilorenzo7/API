<?php

namespace App\Http\Requests\Alat;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAlatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kategori_id'    => ['sometimes', 'integer', 'exists:kategori,id'],
            'nama_alat'      => ['sometimes', 'string', 'max:255'],
            'stok'           => ['sometimes', 'integer', 'min:0'],
            'status_kondisi' => ['sometimes', 'string', 'in:baik,rusak ringan,rusak berat'],
            'deskripsi'      => ['nullable', 'string'],
            'gambar'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
