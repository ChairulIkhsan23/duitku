<?php

namespace App\Enums;

enum NotificationType: string
{
    case BUDGET_ALERT = 'budget_alert';
    case BUDGET_WARNING = 'budget_warning';
    case BUDGET_OVERSPENT = 'budget_overspent';
    case STREAK_MILESTONE = 'streak_milestone';
    case BADGE_EARNED = 'badge_earned';
    case REMINDER = 'reminder';
    
    public function label(): string
    {
        return match($this) {
            self::BUDGET_ALERT => 'Peringatan Budget',
            self::BUDGET_WARNING => 'Peringatan Budget',
            self::BUDGET_OVERSPENT => 'Budget Overspent',
            self::STREAK_MILESTONE => 'Milestone Streak',
            self::BADGE_EARNED => 'Badge Baru',
            self::REMINDER => 'Pengingat',
        };
    }
}