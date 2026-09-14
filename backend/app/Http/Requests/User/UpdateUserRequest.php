<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name'         => ['sometimes', 'string', 'max:255'],
            'email'        => ['sometimes', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password'     => ['sometimes', 'string', 'min:8', 'confirmed'],
            'role'         => ['sometimes', 'string', 'in:admin,petugas,peminjam'],
            'no_hp'        => ['nullable', 'string', 'max:15'],
            'alamat'       => ['nullable', 'string'],
            'foto_profile' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}
