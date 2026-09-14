<?php

namespace App\Http\Requests\Peminjaman;

use Illuminate\Foundation\Http\FormRequest;

class StorePeminjamanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tgl_pinjam'        => ['required', 'date', 'date_format:Y-m-d'],
            'tgl_kembali_plan'  => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:tgl_pinjam'],
            'detail'            => ['required', 'array', 'min:1'],
            'detail.*.alat_id'  => ['required', 'integer', 'exists:alat,id'],
            'detail.*.jumlah'   => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'detail.required'            => 'Minimal satu alat harus dipilih.',
            'detail.*.alat_id.exists'    => 'Salah satu alat yang dipilih tidak ditemukan.',
            'detail.*.jumlah.min'        => 'Jumlah alat minimal 1.',
            'tgl_kembali_plan.after_or_equal' => 'Tanggal rencana kembali tidak boleh sebelum tanggal pinjam.',
        ];
    }
}
