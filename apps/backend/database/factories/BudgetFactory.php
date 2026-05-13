<?php

namespace Database\Factories;

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BudgetFactory extends Factory
{
    protected $model = Budget::class;

    public function definition(): array
    {
        // Always create a unique category for each budget to avoid unique constraint violations
        // when multiple budgets are created with the same month_year
        $category = Category::factory()->create([
            'type' => 'expense',
        ]);

        $limitAmount = $this->faker->numberBetween(500000, 5000000);

        return [
            'id' => (string) Str::uuid(),

            'user_id' => User::factory(),

            'category_id' => $category->id,

            // ✅ FIX: format harus Y-m-d (start of month, untuk konsistensi dengan service)
            'month_year' => $this->faker
                ->dateTimeBetween('-2 months', 'now')
                ->format('Y-m-01'),  // Always first day of month

            'limit_amount' => $limitAmount,

            'spent_amount' => $this->faker->numberBetween(
                0,
                (int) ($limitAmount * 1.2)
            ),

            'notification_sent' => [],

            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // -------------------------
    // STATE: overspent
    // -------------------------
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

    // -------------------------
    // STATE: safe (<70%)
    // -------------------------
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

    // -------------------------
    // STATE: warning (80–99%)
    // -------------------------
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