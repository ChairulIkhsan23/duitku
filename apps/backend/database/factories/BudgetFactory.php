<?php

namespace Database\Factories;

use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Budget>
 */
class BudgetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Budget::class;

    public function definition(): array
    {
        // Ambil category expense
        $category = Category::where('type', 'expense')
            ->where('is_default', true)
            ->inRandomOrder()
            ->first();
        
        if (!$category) {
            $category = Category::factory()->create(['type' => 'expense']);
        }
        
        $limitAmount = $this->faker->numberBetween(500000, 5000000);
        $spentAmount = $this->faker->numberBetween(0, $limitAmount * 1.2);
        
        return [
            'id' => Str::uuid(),
            'user_id' => User::factory(),
            'category_id' => $category->id,
            'month_year' => $this->faker->dateTimeBetween('-2 months', 'now')->format('Y-m-01'),
            'limit_amount' => $limitAmount,
            'spent_amount' => $spentAmount,
            'notification_sent' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // Budget yang sudah overspent
    public function overspent(): static
    {
        return $this->state(fn (array $attributes) => [
            'spent_amount' => $this->faker->numberBetween(
                $attributes['limit_amount'] + 1, 
                $attributes['limit_amount'] * 1.5
            ),
        ]);
    }
    
    // Budget yang aman (belum 80%)
    public function safe(): static
    {
        return $this->state(fn (array $attributes) => [
            'spent_amount' => $this->faker->numberBetween(0, $attributes['limit_amount'] * 0.7),
        ]);
    }
    
    // Budget yang warning (80-99%)
    public function warning(): static
    {
        return $this->state(fn (array $attributes) => [
            'spent_amount' => $this->faker->numberBetween(
                $attributes['limit_amount'] * 0.8, 
                $attributes['limit_amount'] * 0.99
            ),
        ]);
    }
}
