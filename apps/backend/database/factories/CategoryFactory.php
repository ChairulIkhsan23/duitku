<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['income', 'expense'];
        $type = $this->faker->randomElement($types);

        return [
            'id' => Str::uuid(),
            'name' => $this->faker->word(),
            'type' => $type,
            'icon' => $this->faker->randomElement(['FaMoneyBill', 'FaUtensils', 'FaCar', 'FaHome', 'FaShoppingCart']),
            'color' => $this->faker->hexColor(),
            'budget_default' => $type === 'expense' ? $this->faker->numberBetween(500000, 5000000) : null,
            'is_default' => false,
            'user_id' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
