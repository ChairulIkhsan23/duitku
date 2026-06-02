<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Badge;

class BadgeSeeder extends Seeder
{
    /**
     * Seed data badge ke database.
     */
    public function run(): void
    {
        /**
         * Load data dari database/data/badges.php
         */
        $badges = require database_path('data/badges.php');

        foreach ($badges as $badge) {

            Badge::updateOrCreate(
                [
                    'slug' => $badge['slug'], 
                ],
                [
                    'name' => $badge['name'],
                    'description' => $badge['description'],
                    'requirement' => $badge['requirement'], 
                    'icon' => $badge['icon'],
                    'color' => $badge['color'],
                    'points' => $badge['points'],
                    'trigger' => $badge['trigger'],
                ]
            );
        }

        /**
         * Info jumlah badge yang berhasil disync
         */
        $this->command->info('Badge berhasil disync: ' . count($badges));
    }
}