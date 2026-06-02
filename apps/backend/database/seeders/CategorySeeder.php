<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

/**
 * CategorySeeder
 *
 * Bertugas menginisialisasi kategori default sistem
 * berdasarkan data dari file external (database/data/categories.php)
 */
class CategorySeeder extends Seeder
{
    /**
     * Jalankan seeding kategori default
     */
    public function run(): void
    {
        /**
         * Load data kategori dari file terpisah
         * agar lebih maintainable dan tidak hardcode di seeder
         */
        $categories = require database_path('data/categories.php');

        /**
         * Proses setiap kategori:
         * - insert jika belum ada
         * - update jika sudah ada (sync data)
         */
        foreach ($categories as $category) {

            Category::updateOrCreate(
                [
                    // Unique key untuk menghindari duplikasi
                    'name' => $category['name'],
                    'type' => $category['type'],
                ],
                [
                    // Data yang akan disimpan / diupdate
                    'icon' => $category['icon'],
                    'color' => $category['color'],
                    'budget_default' => $category['budget_default'],
                    'is_default' => $category['is_default'],

                    'user_id' => null, // Kategori default tidak punya 
                ]
            );
        }

        /**
         * Info jumlah kategori yang berhasil disync
         */
        $this->command->info('Kategori berhasil disync: ' . count($categories));
    }
}