<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,        // 1. Kategori dulu
            BadgeSeeder::class,           // 2. Badges
            KeywordMappingSeeder::class,  // 3. Keyword mapping
            UserSeeder::class,            // 4. Users
            BudgetSeeder::class,          // 5. Budget (butuh user & category)
            TransactionSeeder::class,     // 6. Transaction (butuh user & category)
        ]);
    }
}
