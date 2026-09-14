<?php

namespace App\Http\Requests\Pengembalian;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePengembalianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tgl_kembali'     => ['sometimes', 'date', 'date_format:Y-m-d'],
            'kondisi_kembali' => ['sometimes', 'string', 'max:255'],
            'denda'           => ['nullable', 'integer', 'min:0'],
        ];
    }
}
