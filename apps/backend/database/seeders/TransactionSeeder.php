<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Enums\TransactionType;
use Illuminate\Support\Str;

class TransactionSeeder extends Seeder
{
    /**
     * Seed data transaksi untuk semua user.
     */
    public function run(): void
    {
        /**
         * Ambil semua user
         */
        $users = User::all();

        if ($users->isEmpty()) {
            $this->command->error('Tidak ada user! Jalankan UserSeeder dulu!');
            return;
        }

        /**
         * Daftar catatan transaksi income
         */
        $incomeNotes = [
            'Gaji bulanan', 'Bonus tahunan', 'THR', 'Hasil freelance',
            'Investasi cair', 'Cashback', 'Hadiah ulang tahun', 'Jual barang bekas'
        ];

        /**
         * Daftar catatan transaksi expense
         */
        $expenseNotes = [
            'Starbucks', 'Makan siang', 'Gojek', 'Bensin', 'Listrik',
            'Netflix', 'Belanja bulanan', 'Parkir', 'Pulsa', 'Makan malam',
            'Kopi', 'Nonton bioskop', 'Beli baju', 'Olahraga', 'Obat', 'Donasi'
        ];

        $totalIncome = 0;
        $totalExpense = 0;

        foreach ($users as $user) {

            $this->command->info('Processing user: ' . $user->name);

            /**
             * =========================
             * Income Transactions
             * =========================
             */

            $incomeCount = rand(5, 10);

            $incomeCategories = Category::where('type', 'income')
                ->where('is_default', true)
                ->get();

            for ($i = 0; $i < $incomeCount; $i++) {

                $date = now()->subDays(rand(0, 30));
                $amount = rand(100000, 15000000);

                Transaction::create([
                    'id' => Str::uuid(),
                    'user_id' => $user->id,
                    'category_id' => $incomeCategories->random()->id,
                    'amount' => $amount,
                    'type' => TransactionType::INCOME->value,
                    'date' => $date,
                    'note' => $incomeNotes[array_rand($incomeNotes)],
                    'photo_url' => null,
                    'is_duplicate' => false,
                    'location_name' => 'Jakarta',
                    'metadata' => null,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                $totalIncome++;
            }

            /**
             * =========================
             * Expense Transactions
             * =========================
             */

            $expenseCount = rand(20, 40);

            $expenseCategories = Category::where('type', 'expense')
                ->where('is_default', true)
                ->get();

            for ($i = 0; $i < $expenseCount; $i++) {

                $date = now()->subDays(rand(0, 30));
                $amount = rand(5000, 2000000);

                Transaction::create([
                    'id' => Str::uuid(),
                    'user_id' => $user->id,
                    'category_id' => $expenseCategories->random()->id,
                    'amount' => $amount,
                    'type' => TransactionType::EXPENSE->value,
                    'date' => $date,
                    'note' => $expenseNotes[array_rand($expenseNotes)],
                    'photo_url' => null,
                    'is_duplicate' => false,
                    'location_name' => 'Jakarta',
                    'metadata' => null,
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                $totalExpense++;
            }
        }

        /**
         * Summary hasil seeding
         */
        $this->command->newLine();
        $this->command->info('Transaksi berhasil dibuat: ' . Transaction::count());
        $this->command->info('Income: ' . $totalIncome);
        $this->command->info('Expense: ' . $totalExpense);
        $this->command->newLine();
    }
}