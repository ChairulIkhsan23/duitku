<?php

namespace Database\Factories;

use App\Models\Insight;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Factory untuk model Insight
 *
 * @extends Factory<Insight>
 */
class InsightFactory extends Factory
{
    protected $model = Insight::class;

    /**
     * Default state untuk data Insight.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),

            'user_id' => User::factory(),

            /**
             * Tipe periode insight:
             * - daily   : harian
             * - weekly  : mingguan
             * - monthly : bulanan
             */
            'period_type' => $this->faker->randomElement([
                'daily',
                'weekly',
                'monthly'
            ]),

            /**
             * Rentang awal periode insight
             */
            'period_start' => $this->faker->date(),

            /**
             * Rentang akhir periode insight
             */
            'period_end' => $this->faker->date(),

            /**
             * Data insight dalam format JSON
             */
            'data' => json_encode([
                'insights' => []
            ]),

            /**
             * Status apakah insight sudah dibaca
             */
            'is_read' => false,

            /**
             * Waktu insight di-generate
             */
            'generated_at' => now(),

            'created_at' => now(),

            'updated_at' => now(),
        ];
    }
}