<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Izinkan semua user untuk melakukan registrasi
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi untuk registrasi user baru
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255', // Nama user
            'email' => 'required|email|unique:users,email', // Email harus unik
            'password' => 'required|string|min:6|confirmed', // Password + konfirmasi
            'currency_code' => 'nullable|string|size:3', // Kode mata uang (IDR, USD, dll)
            'initial_balance' => 'nullable|numeric|min:0', // Saldo awal
            'onboarding_template' => 'nullable|string|in:standard,freelancer,mahasiswa', // Template onboarding
        ];
    }
}