<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Seed data user demo aplikasi.
     */
    public function run(): void
    {
        /**
         * User demo: Budget Santai
         */
        User::create([
            'id' => Str::uuid(),
            'name' => 'Budi Santoso',
            'email' => 'budi@duitku.com',
            'password' => bcrypt('password123'),
            'currency_code' => 'IDR',
            'initial_balance' => 5000000,
            'streak_days' => 7,
            'last_transaction_date' => now(),
            'last_streak_date' => now(),
            'onboarding_template' => 'standard',
            'is_premium' => true,
            'settings' => json_encode([
                'notifications' => true,
                'theme' => 'dark'
            ]),
        ]);

        /**
         * User demo: Rajin Nabung
         */
        User::create([
            'id' => Str::uuid(),
            'name' => 'Siti Aminah',
            'email' => 'siti@duitku.com',
            'password' => bcrypt('password123'),
            'currency_code' => 'IDR',
            'initial_balance' => 10000000,
            'streak_days' => 30,
            'last_transaction_date' => now(),
            'last_streak_date' => now(),
            'onboarding_template' => 'freelancer',
            'is_premium' => true,
            'settings' => json_encode([
                'notifications' => true,
                'theme' => 'light'
            ]),
        ]);

        /**
         * User demo: Pemula
         */
        User::create([
            'id' => Str::uuid(),
            'name' => 'Charlie Brown',
            'email' => 'charlie@duitku.com',
            'password' => bcrypt('password123'),
            'currency_code' => 'IDR',
            'initial_balance' => 1000000,
            'streak_days' => 0,
            'last_transaction_date' => null,
            'last_streak_date' => null,
            'onboarding_template' => 'mahasiswa',
            'is_premium' => false,
            'settings' => json_encode([
                'notifications' => true,
                'theme' => 'light'
            ]),
        ]);

        /**
         * User demo: Freelancer aktif
         */
        User::create([
            'id' => Str::uuid(),
            'name' => 'Denny Wirawan',
            'email' => 'denny@duitku.com',
            'password' => bcrypt('password123'),
            'currency_code' => 'IDR',
            'initial_balance' => 15000000,
            'streak_days' => 60,
            'last_transaction_date' => now(),
            'last_streak_date' => now(),
            'onboarding_template' => 'freelancer',
            'is_premium' => true,
            'settings' => json_encode([
                'notifications' => true,
                'theme' => 'dark'
            ]),
        ]);

        /**
         * User demo: Pengeluaran besar
         */
        User::create([
            'id' => Str::uuid(),
            'name' => 'Eka Putri',
            'email' => 'eka@duitku.com',
            'password' => bcrypt('password123'),
            'currency_code' => 'IDR',
            'initial_balance' => 2000000,
            'streak_days' => 3,
            'last_transaction_date' => now(),
            'last_streak_date' => now(),
            'onboarding_template' => 'standard',
            'is_premium' => false,
            'settings' => json_encode([
                'notifications' => false,
                'theme' => 'light'
            ]),
        ]);

        /**
         * Summary hasil seeding
         */
        $this->command->info('User berhasil dibuat: ' . User::count());
    }
}