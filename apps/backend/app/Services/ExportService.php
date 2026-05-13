<?php

namespace App\Services;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;

/**
 * ExportService
 * 
 * Mengelola export data transaksi pengguna ke berbagai format
 * (PDF, Excel, CSV). Service ini mengambil data dari database,
 * memformatnya, dan menghasilkan file yang siap diunduh.
 */
class ExportService
{
    /**
     * exportTransactions
     * 
     * Mengexport transaksi pengguna dalam rentang tanggal tertentu ke format yang dipilih.
     * Format yang didukung: pdf, excel, csv.
     * 
     * @param User $user Pengguna yang transaksinya akan diexport
     * @param string $startDate Tanggal mulai (format: Y-m-d)
     * @param string $endDate Tanggal akhir (format: Y-m-d)
     * @param string $format Format export (pdf, excel, csv)
     * @return string Output file dalam format yang diminta
     */
    public function exportTransactions(User $user, string $startDate, string $endDate, string $format): string
    {
        // Ambil data transaksi dengan format yang sudah dirapikan
        $transactions = $this->getExportData($user, $startDate, $endDate);
        
        // Export ke format yang diminta
        if ($format === 'pdf') {
            return $this->exportToPDF($user, $transactions, $startDate, $endDate);
        }
        
        if ($format === 'excel') {
            return $this->exportToExcel($transactions);
        }
        
        // Default ke CSV
        return $this->exportToCSV($transactions);
    }

    /**
     * getExportData
     * 
     * Mengambil data transaksi dari database dan memformatnya untuk export.
     * Data diurutkan berdasarkan tanggal terbaru terlebih dahulu.
     * 
     * @param User $user Pengguna yang transaksinya diambil
     * @param string $startDate Tanggal mulai
     * @param string $endDate Tanggal akhir
     * @return Collection Collection transaksi yang sudah diformat
     */
    private function getExportData(User $user, string $startDate, string $endDate)
    {
        return $user->transactions()
            ->with('category')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($transaction) {
                return [
                    'Tanggal' => $transaction->date->format('d/m/Y'),
                    'Keterangan' => $transaction->note ?? '-',
                    'Kategori' => $transaction->category->name,
                    'Jenis' => $transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran',
                    'Jumlah' => $transaction->amount,
                ];
            });
    }

    /**
     * exportToPDF
     * 
     * Mengexport transaksi ke format PDF dengan layout yang rapi.
     * Menggunakan library DomPDF untuk rendering.
     * 
     * @param User $user Pengguna pemilik transaksi
     * @param Collection $transactions Transaksi yang akan diexport
     * @param string $startDate Tanggal mulai periode
     * @param string $endDate Tanggal akhir periode
     * @return string Output PDF
     */
    private function exportToPDF(User $user, $transactions, string $startDate, string $endDate): string
    {
        $pdf = Pdf::loadView('exports.transactions', [
            'user' => $user,
            'transactions' => $transactions,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'generated_at' => now()->format('d/m/Y H:i:s'),
        ]);
        
        return $pdf->output();
    }

    /**
     * exportToExcel
     * 
     * Mengexport transaksi ke format Excel (.xlsx).
     * Menggunakan library Maatwebsite Excel.
     * 
     * @param Collection $transactions Transaksi yang akan diexport
     * @return string Output Excel file
     */
    private function exportToExcel($transactions): string
    {
        return Excel::raw(new TransactionsExport($transactions), \Maatwebsite\Excel\Excel::XLSX);
    }

    /**
     * exportToCSV
     * 
     * Mengexport transaksi ke format CSV (Comma Separated Values).
     * File CSV dapat dibuka dengan berbagai aplikasi spreadsheet.
     * 
     * @param Collection $transactions Transaksi yang akan diexport
     * @return string Output CSV
     */
    private function exportToCSV($transactions): string
    {
        // Buat header CSV
        $csv = "Tanggal,Keterangan,Kategori,Jenis,Jumlah\n";
        
        // Iterasi setiap transaksi dan tambahkan ke CSV
        foreach ($transactions as $transaction) {
            // Escape keterangan dengan double quotes jika mengandung comma
            $csv .= implode(',', [
                $transaction['Tanggal'],
                '"' . $transaction['Keterangan'] . '"',
                $transaction['Kategori'],
                $transaction['Jenis'],
                $transaction['Jumlah'],
            ]) . "\n";
        }
        
        return $csv;
    }
}