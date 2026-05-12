<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'user_id' => User::factory(),
            'type' => $this->faker->randomElement(['budget_alert', 'reminder', 'badge_earned']),
            'title' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(),
            'data' => json_encode([]),
            'is_read' => false,
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}