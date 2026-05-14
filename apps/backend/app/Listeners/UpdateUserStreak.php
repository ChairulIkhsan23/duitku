<?php

namespace App\Listeners;

use App\Events\TransactionCreated;
use App\Services\StreakService;

class UpdateUserStreak
{
    /**
     * Service untuk mengelola streak user
     */
    protected StreakService $streakService;

    /**
     * Inject StreakService ke listener
     */
    public function __construct(StreakService $streakService)
    {
        $this->streakService = $streakService;
    }

    /**
     * Handle event TransactionCreated
     * Update streak user setiap kali ada transaksi baru
     */
    public function handle(TransactionCreated $event): void
    {
        // Update streak berdasarkan aktivitas user
        $this->streakService->updateStreak($event->user);
    }
}