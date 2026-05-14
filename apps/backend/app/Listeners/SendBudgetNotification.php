<?php

namespace App\Listeners;

use App\Events\BudgetThresholdReached;
use App\Services\NotificationService;

class SendBudgetNotification
{
    /**
     * Service untuk mengirim notifikasi ke user
     */
    protected NotificationService $notificationService;

    /**
     * Inject NotificationService ke listener
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle event BudgetThresholdReached
     * Mengirim notifikasi ketika budget mencapai batas tertentu
     */
    public function handle(BudgetThresholdReached $event): void
    {
        // Kirim notifikasi budget alert ke user
        $this->notificationService->sendBudgetAlert(
            $event->user,
            $event->budget,
            $event->level
        );
    }
}