<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

/**
 * Command bawaan Laravel untuk menampilkan quote inspiratif
 */
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Scheduler untuk mengirim reminder harian
 * dijalankan setiap hari jam 20:00 WIB
 */
Schedule::command('app:send-daily-reminder')
    ->dailyAt('20:00') // waktu eksekusi setiap hari
    ->timezone('Asia/Jakarta') // timezone Indonesia
    ->description('Send daily reminder to inactive users'); // deskripsi scheduler