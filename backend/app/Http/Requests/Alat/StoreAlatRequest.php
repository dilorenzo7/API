<?php

namespace App\Http\Requests\Alat;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kategori_id'    => ['required', 'integer', 'exists:kategori,id'],
            'nama_alat'      => ['required', 'string', 'max:255'],
            'stok'           => ['required', 'integer', 'min:0'],
            'status_kondisi' => ['required', 'string', 'in:baik,rusak ringan,rusak berat'],
            'deskripsi'      => ['nullable', 'string'],
            'gambar'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
