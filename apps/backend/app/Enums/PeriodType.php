<?php

namespace App\Enums;

enum PeriodType: string
{
    case DAILY = 'daily'; // Periode harian
    case WEEKLY = 'weekly'; // Periode mingguan
    case MONTHLY = 'monthly'; // Periode bulanan
    
    public function label(): string
    {
        return match($this) {
            self::DAILY => 'Harian', // Label untuk daily
            self::WEEKLY => 'Mingguan', // Label untuk weekly
            self::MONTHLY => 'Bulanan', // Label untuk monthly
        };
    }
}