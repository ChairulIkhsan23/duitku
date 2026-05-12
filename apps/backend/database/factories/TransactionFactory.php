<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Transaction::class;
    
    public function definition(): array
    {
        $type = $this->faker->randomElement(['income', 'expense']);
        
        // Ambil category sesuai type
        $category = Category::where('type', $type)
            ->where('is_default', true)
            ->inRandomOrder()
            ->first();
        
        if (!$category) {
            $category = Category::factory()->create(['type' => $type]);
        }
        
        $amount = $type === 'income' 
            ? $this->faker->numberBetween(50000, 15000000)
            : $this->faker->numberBetween(5000, 3000000);
        
        $notes = [
            'Starbucks', 'Makan siang', 'Gojek', 'Bensin', 'Listrik', 
            'Netflix', 'Gaji bulanan', 'Freelance project', 'Belanja bulanan',
            'Kopi Kenangan', 'MCD', 'KFC', 'Pulsa', 'Parkir', 'Tol'
        ];
        
        return [
            'id' => Str::uuid(),
            'user_id' => User::factory(),
            'category_id' => $category->id,
            'amount' => $amount,
            'type' => $type,
            'date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'note' => $this->faker->randomElement($notes),
            'photo_url' => null,
            'is_duplicate' => false,
            'location_name' => $this->faker->city(),
            'metadata' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    // State untuk income
    public function income(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'income',
            'amount' => $this->faker->numberBetween(50000, 15000000),
        ]);
    }
    
    // State untuk expense
    public function expense(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'expense',
            'amount' => $this->faker->numberBetween(5000, 3000000),
        ]);
    }
    
    // State untuk hari ini
    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'date' => now(),
        ]);
    }
}
