<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class SendDailyReminder extends Command
{
    /**
     * Nama command yang dapat dijalankan melalui artisan
     */
    protected $signature = 'app:send-daily-reminder';

    /**
     * Deskripsi dari command ini
     */
    protected $description = 'Send daily reminder to users who have not recorded transactions today';

    /**
     * Menjalankan proses pengecekan user yang belum melakukan transaksi hari ini,
     * lalu mengirimkan notifikasi reminder ke mereka.
     *
     * @param NotificationService $notificationService
     * @return int
     */
    public function handle(NotificationService $notificationService)
    {
        /**
         * Info awal saat proses dimulai
         */
        $this->info('Checking for users who haven\'t recorded transactions today...');

        /**
         * Ambil user yang tidak memiliki transaksi pada hari ini
         */
        $users = User::whereDoesntHave('transactions', function ($query) {
            $query->whereDate('date', today());
        })->get();

        $count = 0;

        /**
         * Loop semua user dan kirim reminder
         */
        foreach ($users as $user) {
            // Kirim notifikasi reminder ke user
            $notificationService->sendReminder($user);

            $count++;

            // Log user yang sudah dikirim reminder
            $this->line("Reminder sent to: {$user->email}");
        }

        /**
         * Output total user yang menerima reminder
         */
        $this->info("Daily reminder sent to {$count} users");

        return Command::SUCCESS;
    }
}