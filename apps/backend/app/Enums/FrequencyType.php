<?php

namespace App\Enums;

enum FrequencyType: string
{
    case WEEKLY = 'weekly'; // Frekuensi mingguan
    case MONTHLY = 'monthly'; // Frekuensi bulanan
    
    public function label(): string
    {
        return match($this) {
            self::WEEKLY => 'Mingguan', // Label untuk weekly
            self::MONTHLY => 'Bulanan', // Label untuk monthly
        };
    }
}