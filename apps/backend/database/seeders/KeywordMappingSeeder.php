<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KeywordMapping;
use Illuminate\Support\Str;

class KeywordMappingSeeder extends Seeder
{
    /**
     * Seed data keyword mapping kategori.
     */
    public function run(): void
    {
        /**
         * Daftar mapping keyword ke kategori
         */
        $mappings = [

            [
                'id' => Str::uuid(),
                'keywords' => json_encode([
                    'starbucks', 'kopi kenangan', 'fore', 'jco', 'chatime', 'mcd',
                    'kfc', 'burger king', 'pizza hut', 'domino', 'restoran',
                    'warung', 'cafe', 'coffee shop', 'makan siang', 'makan malam',
                    'grab food', 'go food'
                ]),
                'category_name' => 'Makan & Minum',
                'confidence' => 0.90,
                'is_active' => true,
                'created_by' => null,
            ],

            [
                'id' => Str::uuid(),
                'keywords' => json_encode([
                    'grab', 'gojek', 'go-car', 'go-ride', 'taxi', 'blue bird',
                    'bensin', 'pertalite', 'pertamax', 'tol', 'parkir', 'e-toll',
                    'motor', 'mobil', 'bengkel', 'service', 'oli'
                ]),
                'category_name' => 'Transportasi',
                'confidence' => 0.90,
                'is_active' => true,
                'created_by' => null,
            ],

            [
                'id' => Str::uuid(),
                'keywords' => json_encode([
                    'listrik', 'pln', 'pdam', 'air', 'internet', 'indihome',
                    'first media', 'kontrakan', 'sewa rumah', 'kpr', 'bpjs',
                    'iuran rumah'
                ]),
                'category_name' => 'Hunian',
                'confidence' => 0.90,
                'is_active' => true,
                'created_by' => null,
            ],

            [
                'id' => Str::uuid(),
                'keywords' => json_encode([
                    'netflix', 'spotify', 'youtube premium', 'game', 'steam',
                    'playstation', 'bioskop', 'nonton', 'konser', 'disney+',
                    'hbo go', 'vidio'
                ]),
                'category_name' => 'Hiburan',
                'confidence' => 0.90,
                'is_active' => true,
                'created_by' => null,
            ],

            [
                'id' => Str::uuid(),
                'keywords' => json_encode([
                    'apotik', 'obat', 'dokter', 'rumah sakit', 'puskesmas',
                    'klinik', 'gym', 'fitness', 'vitamin', 'suplemen',
                    'cek kesehatan', 'lab'
                ]),
                'category_name' => 'Kesehatan',
                'confidence' => 0.90,
                'is_active' => true,
                'created_by' => null,
            ],

            [
                'id' => Str::uuid(),
                'keywords' => json_encode([
                    'gaji', 'salary', 'honor', 'thr', 'bonus', 'upah', 'pendapatan'
                ]),
                'category_name' => 'Gaji Pokok',
                'confidence' => 0.95,
                'is_active' => true,
                'created_by' => null,
            ],

            [
                'id' => Str::uuid(),
                'keywords' => json_encode([
                    'freelance', 'proyek', 'project', 'design', 'coding',
                    'writing', 'konten', 'content creator', 'youtube', 'tiktok'
                ]),
                'category_name' => 'Pendapatan Freelance',
                'confidence' => 0.90,
                'is_active' => true,
                'created_by' => null,
            ],

            [
                'id' => Str::uuid(),
                'keywords' => json_encode([
                    'dividen', 'saham', 'reksadana', 'crypto', 'bitcoin',
                    'deposito', 'bunga bank', 'capital gain', 'investasi'
                ]),
                'category_name' => 'Investasi',
                'confidence' => 0.90,
                'is_active' => true,
                'created_by' => null,
            ],
        ];

        /**
         * Insert semua mapping ke database
         */
        foreach ($mappings as $mapping) {
            KeywordMapping::create($mapping);
        }

        /**
         * Info jumlah data yang berhasil dibuat
         */
        $this->command->info('Keyword mapping berhasil dibuat: ' . KeywordMapping::count());
    }
}