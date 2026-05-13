<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Factory untuk model Notification
 *
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    /**
     * Default state untuk data Notification.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),

            'user_id' => User::factory(),

            /**
             * Tipe notifikasi:
             * - budget_alert : peringatan budget
             * - reminder     : pengingat
             * - badge_earned : pencapaian/badge
             */
            'type' => $this->faker->randomElement([
                'budget_alert',
                'reminder',
                'badge_earned'
            ]),

            /**
             * Judul notifikasi
             */
            'title' => $this->faker->sentence(),

            /**
             * Isi pesan notifikasi
             */
            'body' => $this->faker->paragraph(),

            /**
             * Data tambahan dalam format JSON
             */
            'data' => json_encode([]),

            /**
             * Status apakah notifikasi sudah dibaca
             */
            'is_read' => false,

            /**
             * Waktu notifikasi dibaca (null jika belum dibaca)
             */
            'read_at' => null,

            'created_at' => now(),

            'updated_at' => now(),
        ];
    }
}