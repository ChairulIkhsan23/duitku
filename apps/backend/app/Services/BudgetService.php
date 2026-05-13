<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * BudgetService
 * 
 * Mengelola budget pengguna termasuk pembuatan, pembaruan, dan monitoring.
 * Service ini bertanggung jawab untuk menghitung pengeluaran vs budget limit,
 * mengirim notifikasi ketika budget terlampaui, dan inisialisasi budget bulanan.
 */
class BudgetService
{
    protected NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * createBudget
     * 
     * Membuat budget baru untuk kategori tertentu dalam bulan tertentu.
     * Input bulan dalam format Y-m akan dikonversi ke Y-m-01 (awal bulan).
     * Mencegah duplikasi dengan mengecek budget yang sudah ada.
     * 
     * @param User $user Pengguna yang akan membuat budget
     * @param array $data Array berisi 'month_year' (Y-m), 'category_id', 'limit_amount'
     * @return Budget Budget yang baru dibuat
     * @throws Exception Jika budget sudah ada untuk kategori dan bulan yang sama
     */
    public function createBudget($user, array $data): Budget
    {
        // Parse tanggal input dan konversi ke awal bulan
        $month = Carbon::createFromFormat('Y-m', $data['month_year'])->startOfMonth();

        // Cek apakah budget sudah ada untuk kategori dan bulan ini
        $existing = Budget::where('user_id', $user->id)
            ->where('category_id', $data['category_id'])
            ->whereYear('month_year', $month->year)
            ->whereMonth('month_year', $month->month)
            ->first();

        if ($existing) {
            throw new \Exception('Budget already exists for this category and month');
        }

        // Buat budget baru
        $budget = Budget::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'category_id' => $data['category_id'],
            'limit_amount' => $data['limit_amount'],
            'spent_amount' => 0,
            'month_year' => $month,
            'notification_sent' => [],
        ]);

        return $budget->load('category');
    }

    /**
     * updateSpentAmount
     * 
     * Memperbarui jumlah pengeluaran budget ketika ada transaksi pengeluaran baru.
     * Hanya memprosses transaksi dengan tipe 'expense'.
     * Secara otomatis mengirim notifikasi jika budget mencapai threshold tertentu.
     * 
     * @param Transaction $transaction Transaksi yang baru dibuat
     * @return Budget|null Budget yang diupdate atau null jika tidak ada budget
     */
    public function updateSpentAmount(Transaction $transaction): ?Budget
    {
        // Hanya proses transaksi pengeluaran (bukan pemasukan)
        if ($transaction->type->value !== 'expense') {
            return null;
        }

        // Cari budget berdasarkan bulan transaksi dan kategori
        $monthPattern = $transaction->date->format('Y-m') . '%';

        $budget = Budget::where('user_id', $transaction->user_id)
            ->where('category_id', $transaction->category_id)
            ->where('month_year', 'LIKE', $monthPattern)
            ->first();

        // Jika budget ditemukan, update jumlah spent dan cek notifikasi
        if ($budget) {
            $budget->increment('spent_amount', $transaction->amount);
            $this->checkBudgetNotification($budget);
            return $budget;
        }
        
        return null;
    }

    /**
     * revertSpentAmount
     * 
     * Mengurangi jumlah pengeluaran budget ketika transaksi dihapus atau diubah.
     * Hanya memprosses transaksi dengan tipe 'expense'.
     * 
     * @param Transaction $transaction Transaksi yang akan di-revert
     * @return void
     */
    public function revertSpentAmount(Transaction $transaction): void
    {
        // Hanya proses transaksi pengeluaran
        if ($transaction->type->value !== 'expense') {
            return;
        }

        // Cari budget berdasarkan bulan transaksi dan kategori
        $monthPattern = $transaction->date->format('Y-m') . '%';

        $budget = Budget::where('user_id', $transaction->user_id)
            ->where('category_id', $transaction->category_id)
            ->where('month_year', 'LIKE', $monthPattern)
            ->first();

        // Jika budget ditemukan, kurangi jumlah spent
        if ($budget) {
            $budget->decrement('spent_amount', $transaction->amount);
        }
    }

    /**
     * checkBudgetNotification
     * 
     * Memeriksa dan mengirim notifikasi ketika budget mencapai threshold tertentu.
     * Threshold: 80% (warning), 100% (overspent), 110% (critical overspend).
     * Mencegah pengiriman notifikasi duplikat dengan tracking notification_sent.
     * 
     * @param Budget $budget Budget yang akan diperiksa
     * @return void
     */
    public function checkBudgetNotification(Budget $budget): void
    {
        // Hitung persentase pengeluaran vs budget
        $percentage = $budget->percentage;
        $sent = $budget->notification_sent ?? [];

        // Notifikasi jika pengeluaran melebihi 110% dari budget
        if ($percentage >= 110 && !in_array('110', $sent)) {
            $this->notificationService->sendBudgetAlert($budget->user, $budget, 'overspend_critical');
            $sent[] = '110';
        }

        // Notifikasi jika pengeluaran mencapai 100% dari budget
        if ($percentage >= 100 && !in_array('100', $sent)) {
            $this->notificationService->sendBudgetAlert($budget->user, $budget, 'overspent');
            $sent[] = '100';
        }

        // Notifikasi jika pengeluaran mencapai 80% dari budget
        if ($percentage >= 80 && !in_array('80', $sent)) {
            $this->notificationService->sendBudgetAlert($budget->user, $budget, 'warning');
            $sent[] = '80';
        }

        // Update record notification_sent di database
        $budget->update(['notification_sent' => $sent]);
    }

    /**
     * initializeMonthlyBudgets
     * 
     * Membuat budget otomatis untuk bulan berjalan berdasarkan budget default dari kategori.
     * Hanya membuat budget untuk kategori dengan tipe 'expense' yang memiliki budget_default.
     * Menggunakan firstOrCreate untuk menghindari duplikasi.
     * 
     * @param User $user Pengguna yang akan mendapat budget otomatis
     * @return void
     */
    public function initializeMonthlyBudgets(User $user): void
    {
        // Ambil awal bulan berjalan
        $currentMonth = now()->startOfMonth();

        // Cari semua kategori expense yang memiliki budget default
        $categories = Category::where('type', 'expense')
            ->whereNotNull('budget_default')
            ->where(function ($query) use ($user) {
                // Kategori milik pengguna atau kategori default system
                $query->where('user_id', $user->id)
                    ->orWhere('is_default', true);
            })
            ->get();

        // Buat budget untuk setiap kategori
        foreach ($categories as $category) {
            Budget::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'month_year' => $currentMonth,
                ],
                [
                    'id' => Str::uuid(),
                    'limit_amount' => $category->budget_default,
                    'spent_amount' => 0,
                    'notification_sent' => [],
                ]
            );
        }
    }

    /**
     * getUserBudgetStatus
     * 
     * Mengambil status budget pengguna untuk bulan tertentu (default: bulan berjalan).
     * Mengembalikan overall summary dan detail setiap budget category.
     * 
     * @param User $user Pengguna yang statusnya diambil
     * @param string|null $monthYear Format Y-m untuk bulan tertentu, default bulan berjalan
     * @return array Array berisi 'overall' (summary) dan 'budgets' (detail per kategori)
     */
    public function getUserBudgetStatus($user, ?string $monthYear = null): array
    {
        // Gunakan format Y-m% untuk LIKE query
        $monthPattern = ($monthYear ?? now()->format('Y-m')) . '%';

        // Ambil semua budget pengguna untuk bulan tertentu
        $budgets = Budget::where('user_id', $user->id)
            ->where('month_year', 'LIKE', $monthPattern)
            ->with('category')
            ->get();

        // Hitung summary overall
        $overall = [
            'total_budget' => $budgets->sum('limit_amount'),
            'total_spent' => $budgets->sum('spent_amount'),
            'remaining' => $budgets->sum('limit_amount') - $budgets->sum('spent_amount'),
            'categories_at_risk' => $budgets->filter(fn($b) => $b->percentage >= 80 && $b->percentage < 100)->count(),
            'categories_overspent' => $budgets->filter(fn($b) => $b->percentage >= 100)->count(),
        ];

        return [
            'overall' => $overall,
            'budgets' => $budgets
        ];
    }
}