<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    /**
     * Izinkan semua user yang sudah login
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk update profile user
     */
    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255', // Nama user
            'email' => [
                'sometimes',
                'email', // Format email valid
                Rule::unique('users', 'email')->ignore($this->user()->id) // Email unik kecuali milik sendiri
            ],
            'password' => 'nullable|string|min:6|confirmed', // Password opsional + konfirmasi
            'currency_code' => 'nullable|string|size:3|in:IDR,USD,SGD,MYR', // Mata uang
            'avatar' => 'nullable|string|max:255', // URL/avatar path
            'settings' => 'nullable|array', // Setting user
            'notification_token' => 'nullable|string', // Token push notification
        ];
    }

    /**
     * Custom message validasi profile
     */
    public function messages(): array
    {
        return [
            'name.max' => 'Nama maksimal 255 karakter',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan user lain',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'currency_code.in' => 'Mata uang tidak valid',
        ];
    }
}