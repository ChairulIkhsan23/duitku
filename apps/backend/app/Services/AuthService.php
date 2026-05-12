<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Register a new user
     *
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        // Create new user
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'currency' => $data['currency'] ?? 'IDR',
            'initial_balance' => $data['initial_balance'] ?? 0,
        ]);

        return $user;
    }

    /**
     * Authenticate user and generate token
     *
     * @param string $email
     * @param string $password
     * @return array ['user' => User, 'token' => string]
     * @throws ValidationException
     */
    public function login(string $email, string $password): array
    {
        // Find user by email
        $user = User::where('email', $email)->first();

        // Validate credentials
        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Kredensial tidak sesuai'],
            ]);
        }

        // Generate API token menggunakan Sanctum
        $token = $user->createToken('api-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Logout user (revoke all tokens)
     *
     * @param User $user
     * @return bool
     */
    public function logout(User $user): bool
    {
        // Revoke all tokens
        return $user->tokens()->delete() > 0;
    }

    /**
     * Get current authenticated user
     *
     * @param User $user
     * @return User
     */
    public function getCurrentUser(User $user): User
    {
        return $user;
    }
}
