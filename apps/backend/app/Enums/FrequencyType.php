<?php

namespace App\Enums;

enum FrequencyType: string
{
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    
    public function label(): string
    {
        return match($this) {
            self::WEEKLY => 'Mingguan',
            self::MONTHLY => 'Bulanan',
        };
    }
}