<?php

namespace App\Enums;

enum CategoryType: string
{
    case INCOME = 'income';
    case EXPENSE = 'expense';
    
    public function label(): string
    {
        return match($this) {
            self::INCOME => 'Pemasukan',
            self::EXPENSE => 'Pengeluaran',
        };
    }
    
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}