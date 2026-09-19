<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFotoProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'foto_profile' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'foto_profile.required' => 'File foto profil wajib diisi.',
            'foto_profile.image'    => 'File harus berupa gambar.',
            'foto_profile.mimes'    => 'Format foto harus jpg, jpeg, png, atau webp.',
            'foto_profile.max'      => 'Ukuran foto maksimal 2MB.',
        ];
    }
}
