<?php

namespace Database\Factories;

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Factory untuk model Budget
 *
 * @extends Factory<Budget>
 */
class BudgetFactory extends Factory
{
    protected $model = Budget::class;

    /**
     * Default state untuk data Budget.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /**
         * Membuat kategori expense untuk setiap budget.
         * Digunakan untuk menghindari konflik unique constraint
         * pada kombinasi data budget per bulan.
         */
        $category = Category::factory()->create([
            'type' => 'expense',
        ]);

        $limitAmount = $this->faker->numberBetween(500000, 5000000);

        return [
            'id' => (string) Str::uuid(),

            'user_id' => User::factory(),

            'category_id' => $category->id,

            /**
             * Periode bulan untuk budget.
             * Selalu dinormalisasi ke tanggal 1 setiap bulan (Y-m-01)
             * agar konsisten saat digunakan di query dan service.
             */
            'month_year' => $this->faker
                ->dateTimeBetween('-2 months', 'now')
                ->format('Y-m-01'),

            'limit_amount' => $limitAmount,

            /**
             * Total pengeluaran pada budget.
             * Bisa melebihi limit untuk simulasi kondisi realistik.
             */
            'spent_amount' => $this->faker->numberBetween(
                0,
                (int) ($limitAmount * 1.2)
            ),

            'notification_sent' => [],

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * State: budget overspent (melewati batas)
     */
    public function overspent(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'spent_amount' => $this->faker->numberBetween(
                    $attributes['limit_amount'] + 1,
                    (int) ($attributes['limit_amount'] * 1.5)
                ),
            ];
        });
    }

    /**
     * State: budget aman (safe)
     */
    public function safe(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'spent_amount' => $this->faker->numberBetween(
                    0,
                    (int) ($attributes['limit_amount'] * 0.7)
                ),
            ];
        });
    }

    /**
     * State: budget warning
     */
    public function warning(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'spent_amount' => $this->faker->numberBetween(
                    (int) ($attributes['limit_amount'] * 0.8),
                    (int) ($attributes['limit_amount'] * 0.99)
                ),
            ];
        });
    }
}