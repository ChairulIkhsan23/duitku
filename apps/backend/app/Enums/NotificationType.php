<?php

namespace App\Enums;

enum NotificationType: string
{
    case BUDGET_ALERT = 'budget_alert'; // Notifikasi alert budget
    case BUDGET_WARNING = 'budget_warning'; // Notifikasi peringatan budget
    case BUDGET_OVERSPENT = 'budget_overspent'; // Notifikasi budget sudah melebihi limit
    case STREAK_MILESTONE = 'streak_milestone'; // Notifikasi pencapaian streak
    case BADGE_EARNED = 'badge_earned'; // Notifikasi badge baru didapatkan
    case REMINDER = 'reminder'; // Notifikasi pengingat harian
    case INSIGHT_READY = 'insight_ready'; // Notifikasi insight sudah tersedia
    
    public function label(): string
    {
        return match($this) {
            self::BUDGET_ALERT => 'Peringatan Budget', // Label alert budget
            self::BUDGET_WARNING => 'Peringatan Budget', // Label warning budget
            self::BUDGET_OVERSPENT => 'Budget Overspent', // Label overspent
            self::STREAK_MILESTONE => 'Milestone Streak', // Label streak milestone
            self::BADGE_EARNED => 'Badge Baru', // Label badge earned
            self::REMINDER => 'Pengingat', // Label reminder
            self::INSIGHT_READY => 'Insight Siap', // Label insight ready
        };
    }
}