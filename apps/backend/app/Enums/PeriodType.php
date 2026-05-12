<?php

namespace App\Enums;

enum PeriodType: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    
    public function label(): string
    {
        return match($this) {
            self::DAILY => 'Harian',
            self::WEEKLY => 'Mingguan',
            self::MONTHLY => 'Bulanan',
        };
    }
}