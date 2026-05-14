<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TransactionsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * Data transaksi yang akan diexport ke Excel
     */
    protected $transactions;

    /**
     * Set data transaksi saat class di-instantiate
     *
     * @param mixed $transactions
     */
    public function __construct($transactions)
    {
        $this->transactions = $transactions;
    }

    /**
     * Mengembalikan data collection untuk export
     */
    public function collection()
    {
        return $this->transactions;
    }

    /**
     * Header kolom pada file Excel
     */
    public function headings(): array
    {
        return [
            'Tanggal',
            'Keterangan',
            'Kategori',
            'Jenis',
            'Jumlah (Rp)'
        ];
    }

    /**
     * Mapping data transaksi ke format export Excel
     *
     * @param mixed $transaction
     * @return array
     */
    public function map($transaction): array
    {
        return [
            $transaction['Tanggal'], // Tanggal transaksi
            $transaction['Keterangan'], // Deskripsi transaksi
            $transaction['Kategori'], // Nama kategori
            $transaction['Jenis'], // Income / Expense
            number_format($transaction['Jumlah'], 0, ',', '.'), // Format rupiah
        ];
    }
}