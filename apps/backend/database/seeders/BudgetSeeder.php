<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Budget;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;

class BudgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $currentMonth = now()->startOfMonth()->format('Y-m-d');
        
        foreach ($users as $user) {
            // Ambil kategori expense default
            $expenseCategories = Category::where('type', 'expense')
                ->where('is_default', true)
                ->get();
            
            foreach ($expenseCategories as $category) {
                // Hanya buat budget untuk kategori yang punya budget_default
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

        $this->command->info('✅ ' . Budget::count() . ' budget berhasil dibuat.');
    }
}
