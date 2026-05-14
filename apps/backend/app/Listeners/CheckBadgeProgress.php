<?php

namespace App\Listeners;

use App\Events\TransactionCreated;
use App\Services\BadgeService;

class CheckBadgeProgress
{
    /**
     * Service untuk mengecek dan memberikan badge
     */
    protected BadgeService $badgeService;

    /**
     * Inject BadgeService ke listener
     */
    public function __construct(BadgeService $badgeService)
    {
        $this->badgeService = $badgeService;
    }

    /**
     * Handle event TransactionCreated
     * Mengecek progres badge berdasarkan aktivitas transaksi user
     */
    public function handle(TransactionCreated $event): void
    {
        // Cek dan update badge berdasarkan aktivitas transaksi
        $this->badgeService->checkAndAwardBadge(
            $event->user,
            'transaction'
        );
    }
}