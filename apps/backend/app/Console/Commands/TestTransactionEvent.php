<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Category;
use App\Models\Budget;
use App\Models\Transaction;
use App\Events\TransactionCreated;
use Illuminate\Support\Str;
use Illuminate\Console\Command;

class TestTransactionEvent extends Command
{
    /**
     * Nama command yang dapat dijalankan melalui artisan
     */
    protected $signature = 'test:transaction';

    /**
     * Deskripsi dari command ini
     */
    protected $description = 'Test TransactionCreated event';

    /**
     * Menjalankan proses testing event TransactionCreated dengan membuat transaksi dummy
     * dan mengecek apakah budget serta data user ter-update dengan benar.
     *
     * @return void
     */
    public function handle()
    {
        /**
         * Ambil user pertama sebagai sample test
         */
        $user = User::first();

        /**
         * Ambil kategori "Makan & Minum"
         */
        $category = Category::where('name', 'Makan & Minum')->first();

        /**
         * Ambil budget berdasarkan user dan kategori
         */
        $budget = Budget::where('user_id', $user->id)
            ->where('category_id', $category->id)
            ->first();

        /**
         * Tampilkan data awal budget
         */
        $this->line("Category: {$budget->category->name}");
        $this->line("Limit: " . number_format($budget->limit_amount, 0, ',', '.'));
        $this->line("Current Spent: " . number_format($budget->spent_amount, 0, ',', '.'));
        $this->line("Month: {$budget->month_year}");

        /**
         * Simpan nilai awal untuk perbandingan
         */
        $initialSpent = $budget->spent_amount;
        $this->line("Initial spent: " . number_format($initialSpent, 0, ',', '.'));

        /**
         * Buat transaksi dummy untuk testing event
         */
        $transaction = $user->transactions()->create([
            'id' => (string) Str::uuid(),
            'category_id' => $category->id,
            'amount' => 50000,
            'type' => 'expense',
            'date' => now(),
            'note' => 'Test TransactionCreated event'
        ]);

        $this->info("Transaction created with amount: 50,000");

        /**
         * Trigger event TransactionCreated
         */
        event(new TransactionCreated($transaction, $user));
        $this->info("Event TransactionCreated fired");

        /**
         * Refresh data setelah event berjalan
         */
        $budget->refresh();
        $user->refresh();

        /**
         * Tampilkan hasil setelah update
         */
        $this->line("New Spent: " . number_format($budget->spent_amount, 0, ',', '.'));
        $this->line("Increase: " . number_format($budget->spent_amount - $initialSpent, 0, ',', '.'));
        $this->line("Streak days: {$user->streak_days}");

        /**
         * Validasi hasil test
         */
        if ($budget->spent_amount - $initialSpent == 50000) {
            $this->info("Success! Budget updated correctly!");
        } else {
            $this->error("Failed! Budget not updated correctly!");
        }

        $this->newLine();
        $this->info('Test completed!');
    }
}