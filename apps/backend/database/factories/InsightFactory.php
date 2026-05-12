<?php

namespace Database\Factories;

use App\Models\Insight;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class InsightFactory extends Factory
{
    protected $model = Insight::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'user_id' => User::factory(),
            'period_type' => $this->faker->randomElement(['daily', 'weekly', 'monthly']),
            'period_start' => $this->faker->date(),
            'period_end' => $this->faker->date(),
            'data' => json_encode(['insights' => []]),
            'is_read' => false,
            'generated_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}