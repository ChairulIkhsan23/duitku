<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed seluruh data aplikasi.
     */
    public function run(): void
    {
        $this->call([
            // Master data
            CategorySeeder::class,
            BadgeSeeder::class,
            KeywordMappingSeeder::class,

            // User data
            UserSeeder::class,

            // Data yang bergantung pada user & category
            BudgetSeeder::class,
            TransactionSeeder::class,
        ]);
    }
}