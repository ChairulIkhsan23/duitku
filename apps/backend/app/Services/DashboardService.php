<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

/**
 * DashboardService
 * 
 * Mengumpulkan dan menyiapkan semua data yang diperlukan untuk dashboard dashboard pengguna.
 * Service ini mengintegrasikan data dari berbagai service lain seperti transaksi, budget, dan streak.
 */
class DashboardService
{
    public function __construct(
        protected TransactionService $transactionService,
        protected BudgetService $budgetService,
        protected StreakService $streakService
    ) {}

    /**
     * getDashboardData
     * 
     * Mengumpulkan semua data dashboard untuk ditampilkan kepada pengguna.
     * Menggabungkan ringkasan transaksi bulan ini, perbandingan dengan bulan lalu,
     * status budget, streak, dan transaksi terbaru.
     * 
     * @param User $user Pengguna yang data dashboardnya diambil
     * @return array Array berisi semua data dashboard
     */
    public function getDashboardData(User $user): array
    {
        // Ambil ringkasan transaksi bulan berjalan
        $current = $this->transactionService->getSummary($user, 'monthly');
        // Ambil ringkasan transaksi bulan lalu untuk perbandingan
        $last = $this->getLastMonthSummary($user);

        // Ambil status budget pengguna
        $budget = $this->budgetService->getUserBudgetStatus($user);
        // Ambil status streak pengguna
        $streak = $this->streakService->getStreakStatus($user);

        return [
            // Ringkasan transaksi bulan ini
            'summary' => $current,

            // Perbandingan dengan bulan lalu (perubahan dalam persen)
            'comparison' => [
                'income_change' => $this->calculateChange(
                    $last['total_income'],
                    $current['total_income']
                ),
                'expense_change' => $this->calculateChange(
                    $last['total_expense'],
                    $current['total_expense']
                ),
            ],

            // Status budget dan kategori yang berisiko
            'budget' => [
                'overall' => $budget['overall'],
                'at_risk' => $this->getBudgetAtRisk($budget['budgets']),
            ],

            // Status streak mencatat transaksi
            'streak' => $streak,

            // 10 transaksi terbaru
            'recent_transactions' => $this->getRecentTransactions($user),

            // Kode mata uang pengguna
            'currency' => $user->currency_code,
        ];
    }

    /**
     * getLastMonthSummary
     * 
     * Mengambil ringkasan transaksi bulan lalu untuk keperluan perbandingan.
     * 
     * @param User $user Pengguna yang data bulan lalunya diambil
     * @return array Array berisi total_income dan total_expense bulan lalu
     */
    private function getLastMonthSummary(User $user): array
    {
        // Ambil semua transaksi bulan lalu
        $transactions = $user->transactions()
            ->whereBetween('date', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth(),
            ])
            ->get();

        // Hitung total income dan expense bulan lalu
        return [
            'total_income' => $transactions->where('type', 'income')->sum('amount'),
            'total_expense' => $transactions->where('type', 'expense')->sum('amount'),
        ];
    }

    /**
     * calculateChange
     * 
     * Menghitung perubahan persentase antara nilai lama dan nilai baru.
     * Berguna untuk membandingkan data bulan ini dengan bulan lalu.
     * 
     * @param float $old Nilai lama (periode sebelumnya)
     * @param float $new Nilai baru (periode sekarang)
     * @return float Perubahan dalam persen (2 decimal places)
     */
    private function calculateChange(float $old, float $new): float
    {
        // Jika nilai lama adalah 0, return 0 untuk menghindari division by zero
        if ($old == 0) return 0;

        // Formula: ((nilai_baru - nilai_lama) / nilai_lama) * 100
        return round((($new - $old) / $old) * 100, 2);
    }

    /**
     * getRecentTransactions
     * 
     * Mengambil transaksi terbaru pengguna dengan informasi kategori.
     * 
     * @param User $user Pengguna yang transaksinya diambil
     * @param int $limit Jumlah transaksi terbaru yang diambil (default: 10)
     * @return Collection Collection berisi transaksi terbaru
     */
    private function getRecentTransactions(User $user, int $limit = 10)
    {
        return $user->transactions()
            ->with('category')
            ->latest('date')
            ->limit($limit)
            ->get();
    }

    /**
     * getBudgetAtRisk
     * 
     * Mengambil daftar budget yang berisiko (pengeluaran >= 80%) dan sortir berdasarkan
     * persentase pengeluaran (tertinggi terlebih dahulu). Maksimal 5 kategori ditampilkan.
     * 
     * @param Collection $budgets Collection budget yang akan difilter
     * @return Collection Collection budget yang berisiko (max 5)
     */
    private function getBudgetAtRisk(Collection $budgets): Collection
    {
        return $budgets
            // Filter budget dengan pengeluaran >= 80%
            ->filter(fn ($b) => $b->percentage >= 80)
            // Sort berdasarkan percentage (tertinggi dulu)
            ->sortByDesc('percentage')
            // Ambil maksimal 5
            ->take(5)
            // Reindex collection
            ->values();
    }
}