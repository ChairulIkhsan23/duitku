<?php

namespace App\Enums;

enum TransactionType: string
{
    case INCOME = 'income'; // Tipe transaksi pemasukan
    case EXPENSE = 'expense'; // Tipe transaksi pengeluaran
    
    public function label(): string
    {
        return match($this) {
            self::INCOME => 'Pemasukan', // Label untuk income
            self::EXPENSE => 'Pengeluaran', // Label untuk expense
        };
    }
    
    public function color(): string
    {
        return match($this) {
            self::INCOME => '#4CAF50', // Warna hijau untuk pemasukan
            self::EXPENSE => '#F44336', // Warna merah untuk pengeluaran
        };
    }
}