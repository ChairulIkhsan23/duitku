<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Factory untuk model User
 *
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * Password default yang digunakan oleh factory
     */
    protected static ?string $password;

    /**
     * Default state untuk data User.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),

            'name' => fake()->name(),

            'email' => fake()->unique()->safeEmail(),

            /**
             * Status verifikasi email
             */
            'email_verified_at' => now(),

            /**
             * Password default untuk testing
             */
            'password' => static::$password ??= Hash::make('password'),

            'remember_token' => Str::random(10),

            /**
             * Mata uang default user
             */
            'currency_code' => 'IDR',

            /**
             * Saldo awal user
             */
            'initial_balance' => $this->faker->numberBetween(1000000, 10000000),

            /**
             * Jumlah streak hari aktif user
             */
            'streak_days' => $this->faker->numberBetween(0, 60),

            /**
             * Tanggal transaksi terakhir user
             */
            'last_transaction_date' => $this->faker->dateTimeBetween('-30 days', 'now'),

            /**
             * Template onboarding user
             */
            'onboarding_template' => 'standard',

            /**
             * Status premium user
             */
            'is_premium' => $this->faker->boolean(20),

            /**
             * Pengaturan user dalam format JSON
             */
            'settings' => json_encode([
                'notifications' => true,
                'theme' => 'light'
            ]),

            'created_at' => now(),

            'updated_at' => now(),
        ];
    }

    /**
     * State: user dengan email belum diverifikasi
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}