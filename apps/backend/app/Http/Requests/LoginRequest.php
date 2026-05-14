<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Izinkan semua request login (handled oleh auth logic)
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk login user
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email', // Email wajib valid
            'password' => 'required|string|min:6', // Password minimal 6 karakter
        ];
    }
}