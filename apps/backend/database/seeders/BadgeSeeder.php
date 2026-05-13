<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Badge;
use Illuminate\Support\Str;

class BadgeSeeder extends Seeder
{
    /**
     * Seed data badge ke database.
     */
    public function run(): void
    {
        /**
         * Daftar badge default sistem
         */
        $badges = [
            [
                'id' => Str::uuid(),
                'name' => 'First Step',
                'slug' => 'first-step',
                'description' => 'Mencatat transaksi pertama Anda',
                'requirement' => json_encode([
                    'type' => 'transaction_count',
                    'min' => 1
                ]),
                'icon' => 'FaWalking',
                'color' => '#4CAF50',
                'points' => 10,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Budget Ninja',
                'slug' => 'budget-ninja',
                'description' => 'Tidak overspend selama 30 hari berturut-turut',
                'requirement' => json_encode([
                    'type' => 'no_overspend_days',
                    'days' => 30
                ]),
                'icon' => 'FaShieldAlt',
                'color' => '#2196F3',
                'points' => 50,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Early Bird',
                'slug' => 'early-bird',
                'description' => 'Mencatat transaksi 7x sebelum jam 9 pagi',
                'requirement' => json_encode([
                    'type' => 'morning_transaction',
                    'count' => 7
                ]),
                'icon' => 'FaSun',
                'color' => '#FFC107',
                'points' => 25,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Saver King',
                'slug' => 'saver-king',
                'description' => 'Menabung >30% dari income selama 3 bulan',
                'requirement' => json_encode([
                    'type' => 'savings_rate',
                    'percentage' => 30,
                    'months' => 3
                ]),
                'icon' => 'FaPiggyBank',
                'color' => '#FF9800',
                'points' => 100,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Category Master',
                'slug' => 'category-master',
                'description' => 'Gunakan semua kategori minimal 1x',
                'requirement' => json_encode([
                    'type' => 'all_categories_used'
                ]),
                'icon' => 'FaThLarge',
                'color' => '#9C27B0',
                'points' => 75,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Night Owl',
                'slug' => 'night-owl',
                'description' => '5x transaksi malam >Rp500k',
                'requirement' => json_encode([
                    'type' => 'night_transaction',
                    'amount' => 500000,
                    'count' => 5
                ]),
                'icon' => 'FaMoon',
                'color' => '#3F51B5',
                'points' => 30,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Streak Legend',
                'slug' => 'streak-legend',
                'description' => 'Mencapai streak 60 hari',
                'requirement' => json_encode([
                    'type' => 'streak',
                    'days' => 60
                ]),
                'icon' => 'FaCrown',
                'color' => '#FFD700',
                'points' => 200,
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Data Analyst',
                'slug' => 'data-analyst',
                'description' => 'Generate laporan 10x',
                'requirement' => json_encode([
                    'type' => 'report_count',
                    'count' => 10
                ]),
                'icon' => 'FaChartLine',
                'color' => '#00BCD4',
                'points' => 40,
            ],
        ];

        /**
         * Insert semua badge ke database
         */
        foreach ($badges as $badge) {
            Badge::create($badge);
        }

        $this->command->info('Badge berhasil dibuat: ' . Badge::count());
    }
}