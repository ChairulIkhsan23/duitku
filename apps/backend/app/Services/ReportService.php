<?php

namespace App\Services;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;
use Illuminate\Support\Facades\Storage;

/**
 * ReportService
 * 
 * Mengelola pembuatan laporan keuangan untuk pengguna dalam berbagai format.
 * Service ini mengumpulkan data transaksi, menghitung summary, dan compile menjadi
 * laporan yang dapat di-export ke JSON, PDF, atau Excel.
 */
class ReportService
{
    /**
     * generateReport
     * 
     * Generate laporan keuangan untuk periode tertentu dalam format yang diminta.
     * Format yang didukung: json (default), pdf, excel.
     * Laporan mencakup ringkasan (summary) dan detail transaksi.
     * 
     * @param User $user Pengguna yang laporan-nya akan di-generate
     * @param string $startDate Tanggal mulai periode (format: Y-m-d)
     * @param string $endDate Tanggal akhir periode (format: Y-m-d)
     * @param string $format Format laporan (json, pdf, excel) - default: json
     * @return mixed Array (JSON), string (PDF), atau string (Excel) sesuai format
     */
    public function generateReport(User $user, string $startDate, string $endDate, string $format = 'json')
    {
        // Ambil transaksi dalam rentang tanggal
        $transactions = $this->getTransactionsInRange($user, $startDate, $endDate);
        // Hitung summary laporan
        $summary = $this->calculateSummary($transactions);
        
        // Compile data laporan
        $data = [
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'currency' => $user->currency_code,
            ],
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
            'summary' => $summary,
            'transactions' => $transactions,
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];
        
        // Export ke format yang diminta
        if ($format === 'pdf') {
            return $this->generatePDF($data);
        }
        
        if ($format === 'excel') {
            return $this->generateExcel($transactions, $summary);
        }
        
        // Default return JSON
        return $data;
    }

    /**
     * getTransactionsInRange
     * 
     * Mengambil transaksi pengguna dalam rentang tanggal tertentu.
     * Transaksi diformat dan diurutkan berdasarkan tanggal (terbaru terlebih dahulu).
     * 
     * @param User $user Pengguna yang transaksinya diambil
     * @param string $startDate Tanggal mulai
     * @param string $endDate Tanggal akhir
     * @return Collection Collection transaksi yang sudah diformat
     */
    private function getTransactionsInRange(User $user, string $startDate, string $endDate)
    {
        return $user->transactions()
            ->with('category')
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'desc')
            ->get()
            ->map(function ($transaction) {
                return [
                    'date' => \Carbon\Carbon::parse($transaction->date)->format('Y-m-d'),
                    'description' => $transaction->note,
                    'category' => $transaction->category?->name ?? 'Uncategorized',
                    'amount' => $transaction->amount,
                    'type' => $transaction->type,
                ];
            });
    }

    /**
     * calculateSummary
     * 
     * Menghitung ringkasan laporan dari data transaksi.
     * Termasuk total income/expense, balance, savings rate, dan top categories.
     * 
     * @param Collection $transactions Collection transaksi untuk dihitung
     * @return array Array berisi summary laporan
     */
    private function calculateSummary($transactions): array
    {
        // Hitung total pemasukan dan pengeluaran
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');

        // Kelompokkan pengeluaran berdasarkan kategori dan hitung persentasenya
        $byCategory = $transactions
            ->where('type', 'expense')
            ->groupBy('category')
            ->map(function ($group) use ($totalExpense) {
                // Nama kategori dari item pertama di group
                $categoryName = $group->first()['category'] ?? 'Uncategorized';

                // Total untuk kategori ini
                $amount = $group->sum('amount');

                // Persentase terhadap total expense
                return [
                    'category' => $categoryName,
                    'amount' => $amount,
                    'percentage' => $totalExpense > 0
                        ? round(($amount / $totalExpense) * 100, 2)
                        : 0,
                ];
            })
            // Sort berdasarkan amount (tertinggi dulu)
            ->sortByDesc('amount')
            ->values();

        return [
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'balance' => $totalIncome - $totalExpense,
            'savings_rate' => $totalIncome > 0
                ? round((($totalIncome - $totalExpense) / $totalIncome) * 100, 2)
                : 0,
            'total_transactions' => $transactions->count(),
            'top_categories' => $byCategory->take(5),
        ];
    }

    /**
     * generatePDF
     * 
     * Generate laporan dalam format PDF menggunakan DomPDF.
     * 
     * @param array $data Data laporan yang akan di-render
     * @return string Output PDF
     */
    private function generatePDF(array $data)
    {
        $pdf = Pdf::loadView('reports.transactions', $data);
        return $pdf->output();
    }

    /**
     * generateExcel
     * 
     * Generate laporan dalam format Excel (.xlsx).
     * 
     * @param Collection $transactions Transaksi untuk di-export
     * @param array $summary Summary laporan
     * @return string Output Excel file
     */
    private function generateExcel($transactions, $summary)
    {
        return Excel::raw(new TransactionsExport($transactions, $summary), \Maatwebsite\Excel\Excel::XLSX);
    }
}