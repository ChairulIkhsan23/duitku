<?php

namespace App\Enums;

enum CategoryType: string
{
    case INCOME = 'income'; // Tipe kategori pemasukan
    case EXPENSE = 'expense'; // Tipe kategori pengeluaran
    
    public function label(): string
    {
        return match($this) {
            self::INCOME => 'Pemasukan', // Label untuk income
            self::EXPENSE => 'Pengeluaran', // Label untuk expense
        };
    }
    
    public static function values(): array
    {
        /**
         *  Mengambil semua value dari enum cases
         * 
         * @return array<string> Array berisi semua value dari enum cases
         */
        return array_column(self::cases(), 'value');
    }
}