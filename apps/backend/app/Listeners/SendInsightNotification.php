<?php

namespace App\Listeners;

use App\Events\InsightGenerated;
use App\Services\NotificationService;

class SendInsightNotification
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
     * Handle event InsightGenerated
     * Mengirim notifikasi ketika insight baru berhasil dibuat
     */
    public function handle(InsightGenerated $event): void
    {
        // Kirim notifikasi insight ke user
        $this->notificationService->sendInsightNotification(
            $event->user,
            $event->insight
        );
    }
}