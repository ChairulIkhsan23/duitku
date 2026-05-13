<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;

class BudgetSeeder extends Seeder
{
    /**
     * Seed data budget untuk semua user.
     */
    public function run(): void
    {
        /**
         * Ambil semua user
         */
        $users = User::all();

        /**
         * Periode budget: bulan berjalan
         */
        $currentMonth = now()->startOfMonth()->format('Y-m-d');

        foreach ($users as $user) {

            /**
             * Ambil semua kategori expense default
             */
            $expenseCategories = Category::where('type', 'expense')
                ->where('is_default', true)
                ->get();

            foreach ($expenseCategories as $category) {

                /**
                 * Hanya buat budget jika kategori memiliki default budget
                 */
                if ($category->budget_default) {
                    Budget::create([
                        'id' => Str::uuid(),
                        'user_id' => $user->id,
                        'category_id' => $category->id,
                        'month_year' => $currentMonth,
                        'limit_amount' => $category->budget_default,
                        'spent_amount' => 0,
                        'notification_sent' => json_encode([]),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        /**
         * Info jumlah budget yang berhasil dibuat
         */
        $this->command->info('Budget berhasil dibuat: ' . Budget::count());
    }
}