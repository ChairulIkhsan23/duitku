<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken; 

/**
 * AuthService
 * 
 * Mengelola proses autentikasi pengguna termasuk registrasi, login, logout,
 * dan pembaruan profil. Service ini menangani validasi email, enkripsi password,
 * dan pembuatan token autentikasi.
 */
class AuthService
{
    /**
     * register
     * 
     * Mendaftarkan pengguna baru dengan data yang diberikan.
     * Melakukan validasi email (unik dan format valid), enkripsi password,
     * dan pembuatan token autentikasi untuk sesi pertama.
     * 
     * @param array $data Data registrasi (name, email, password, currency_code, dll)
     * @return array Array berisi user dan token autentikasi
     */
    public function register(array $data): array
    {
        // Normalize email ke lowercase dan hapus whitespace
        $email = strtolower(trim($data['email'] ?? ''));

        // Validasi email: cek jika kosong atau sudah terdaftar
        if ($email === '' || User::where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => ['Email already in use or invalid'],
            ]);
        }

        // Buat pengguna baru dengan data pemberian nilai default jika tidak ada
        $user = User::create([
            'id' => Str::uuid(),
            'name' => $data['name'],
            'email' => $email,
            'password' => Hash::make($data['password']),
            'currency_code' => $data['currency_code'] ?? 'IDR',
            'onboarding_template' => $data['onboarding_template'] ?? 'standard',
            'initial_balance' => $data['initial_balance'] ?? 0,
        ]);

        // Buat token autentikasi untuk sesi pertama
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * login
     * 
     * Melakukan login pengguna dengan email dan password.
     * Memverifikasi kredensial pengguna dan membuat token autentikasi jika valid.
     * 
     * @param string $email Email pengguna (akan diubah menjadi lowercase)
     * @param string $password Password pengguna yang belum di-hash
     * @return array Array berisi user dan token autentikasi
     * @throws ValidationException Jika email atau password tidak sesuai
     */
    public function login(string $email, string $password): array
    {
        // Normalize email ke lowercase dan hapus whitespace
        $email = strtolower(trim($email));

        // Cari pengguna berdasarkan email
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * logout
     * 
     * Menghapus token autentikasi pengguna yang sedang aktif.
     * Ini akan menghentikan sesi pengguna dan memaksa login kembali di kali berikutnya.
     * 
     * @param User $user Pengguna yang akan di-logout
     * @return void
     */
    public function logout($user): void
    {
        // Hapus token akses yang sedang digunakan
        $token = $user->currentAccessToken();
        
        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }
    }

    /**
     * updateProfile
     * 
     * Memperbarui profil pengguna dengan data yang diizinkan.
     * Hanya field tertentu yang dapat diupdate (name, email, currency_code, dll).
     * Melakukan validasi untuk email yang unique dan password akan di-hash jika diubah.
     * 
     * @param User $user Pengguna yang akan diupdate
     * @param array $data Data profil yang ingin diupdate
     * @return User Pengguna yang sudah diupdate
     * @throws ValidationException Jika email sudah digunakan pengguna lain
     */
    public function updateProfile($user, array $data): User
    {
        // Hanya izinkan field tertentu untuk diupdate (whitelist)
        $allowed = collect($data)->only([
            'name',
            'email',
            'currency_code',
            'avatar',
            'settings',
            'password'
        ])->toArray();

        // Validasi dan normalize email jika ada
        if (isset($allowed['email'])) {
            $allowed['email'] = strtolower(trim($allowed['email']));

            // Cek apakah email sudah digunakan pengguna lain
            if (
                User::where('email', $allowed['email'])
                    ->where('id', '!=', $user->id)
                    ->exists()
            ) {
                throw ValidationException::withMessages([
                    'email' => ['Email already in use'],
                ]);
            }
        }

        // Hash password jika ada dan tidak kosong
        if (isset($allowed['password']) && trim($allowed['password']) !== '') {
            $allowed['password'] = Hash::make($allowed['password']);
        } else {
            // Hapus password dari array jika kosong
            unset($allowed['password']);
        }

        // Merge settings dengan existing settings jika ada
        if (isset($allowed['settings'])) {
            $allowed['settings'] = array_merge(
                is_array($user->settings) ? $user->settings : [],
                is_array($allowed['settings']) ? $allowed['settings'] : []
            );
        }

        // Simpan perubahan dan return user terbaru
        $user->update($allowed);

        return $user->fresh();
    }
}