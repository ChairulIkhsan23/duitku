<?php

namespace App\Providers;

use App\Events\TransactionCreated;
use App\Events\BudgetThresholdReached;
use App\Events\InsightGenerated;
use App\Listeners\UpdateBudgetSpent;
use App\Listeners\UpdateUserStreak;
use App\Listeners\CheckBadgeProgress;
use App\Listeners\SendBudgetNotification;
use App\Listeners\SendInsightNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Service Provider untuk registrasi Event & Listener
 * Mengatur alur otomatis ketika event tertentu terjadi
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * Mapping event ke listener yang akan dijalankan
     */
    protected $listen = [
        TransactionCreated::class => [
            UpdateBudgetSpent::class,   // update budget saat transaksi dibuat
            UpdateUserStreak::class,    // update streak user
            CheckBadgeProgress::class,  // cek progres badge
        ],

        BudgetThresholdReached::class => [
            SendBudgetNotification::class, // kirim notifikasi budget alert
        ],

        InsightGenerated::class => [
            SendInsightNotification::class, // kirim notifikasi insight
        ],
    ];

    /**
     * Boot event service provider
     */
    public function boot(): void
    {
        parent::boot();
    }
}