<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KeywordMapping;
use Illuminate\Support\Str;

/**
 * KeywordMappingSeeder
 *
 * Seeder ini bertugas untuk menginisialisasi data keyword mapping
 * dari file external agar lebih clean, scalable, dan mudah dikelola.
 */
class KeywordMappingSeeder extends Seeder
{
    /**
     * Jalankan seeding keyword mapping
     */
    public function run(): void
    {
        /**
         * Load data dari file external
         * supaya tidak hardcode di dalam seeder
         */
        $mappings = require database_path('data/keyword_mappings.php');

        /**
         * Loop setiap mapping dan simpan ke database
         */
        foreach ($mappings as $mapping) {

            KeywordMapping::updateOrCreate(
                [
                    // key unik berdasarkan kategori
                    'category_name' => $mapping['category_name'],
                ],
                [
                    'id' => (string) Str::uuid(),

                    // data utama mapping
                    'keywords' => $mapping['keywords'],
                    'confidence' => $mapping['confidence'],
                    'is_active' => $mapping['is_active'],

                    // system field
                    'created_by' => null,
                ]
            );
        }

        /**
         * Log hasil seeding
         */
        $this->command->info(
            'Keyword mapping berhasil disync: ' . count($mappings)
        );
    }
}