<?php

namespace App\Services;

use App\Events\TransactionCreated;
use App\Models\Transaction;
use App\Services\AI\CategorizationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User; 

/**
 * TransactionService
 * 
 * Mengelola CRUD operasi transaksi dengan business logic terkait.
 * Service ini menangani auto-categorization menggunakan AI, update budget,
 * update streak, dan pendeteksian transaksi duplikat.
 */
class TransactionService
{
    protected $categorizationService;
    protected $budgetService;
    protected $streakService;

    public function __construct(
        CategorizationService $categorizationService,
        BudgetService $budgetService,
        StreakService $streakService
    ) {
        $this->categorizationService = $categorizationService;
        $this->budgetService = $budgetService;
        $this->streakService = $streakService;
    }

    /**
     * createTransaction
     * 
     * Membuat transaksi baru dengan handling auto-categorization dan update state.
     * Jika category_id tidak diberikan, akan menggunakan AI untuk auto-categorize.
     * Mendeteksi duplikasi transaksi secara otomatis.
     * Dijalankan dalam database transaction untuk consistency.
     * 
     * @param User $user Pengguna pemilik transaksi
     * @param array $data Data transaksi (amount, type, note, category_id, date, dll)
     * @return Transaction Transaksi yang baru dibuat
     */
    public function createTransaction($user, array $data): Transaction
    {
        return DB::transaction(function () use ($user, $data) {

            // Auto-categorize jika category_id tidak ada tapi ada note
            if (empty($data['category_id']) && !empty($data['note'])) {
                $category = $this->categorizationService
                    ->categorize($data['note'], $data['type']);

                $data['category_id'] = $category->id;
            }

            // Deteksi apakah transaksi ini adalah duplikat
            $isDuplicate = $this->isDuplicate($user->id, $data);

            // Buat transaksi baru
            $transaction = Transaction::create(array_merge(
                $data,
                [
                    'id' => Str::uuid(),
                    'user_id' => $user->id,
                    'is_duplicate' => $isDuplicate,
                ]
            ));

            // Trigger event TransactionCreated untuk side effects (budget, streak, dst)
            event(new TransactionCreated($transaction, $user));

            return $transaction->load('category');
        });
    }

    /**
     * updateTransaction
     * 
     * Memperbarui transaksi yang sudah ada.
     * Melakukan revert pengeluaran budget sebelum update, kemudian update lagi.
     * Dijalankan dalam database transaction untuk consistency.
     * 
     * @param Transaction $transaction Transaksi yang akan diupdate
     * @param array $data Data baru transaksi
     * @return Transaction Transaksi yang sudah diupdate
     */
    public function updateTransaction(Transaction $transaction, array $data): Transaction
    {
        return DB::transaction(function () use ($transaction, $data) {

            // Revert spent amount dari budget (untuk transaction lama)
            $this->budgetService->revertSpentAmount($transaction);

            // Update transaksi dengan data baru
            $transaction->update($data);

            // Refresh transaction dari database
            $transaction->refresh();

            // Update spent amount ke budget (untuk transaction baru)
            $this->budgetService->updateSpentAmount($transaction);

            return $transaction->load('category');
        });
    }

    /**
     * deleteTransaction
     * 
     * Menghapus transaksi.
     * Melakukan revert pengeluaran budget sebelum deletion.
     * Dijalankan dalam database transaction untuk consistency.
     * 
     * @param Transaction $transaction Transaksi yang akan dihapus
     * @return bool True jika berhasil dihapus
     */
    public function deleteTransaction(Transaction $transaction): bool
    {
        return DB::transaction(function () use ($transaction) {

            // Revert spent amount dari budget sebelum delete
            $this->budgetService->revertSpentAmount($transaction);

            // Delete transaksi
            return $transaction->delete();
        });
    }

    /**
     * isDuplicate
     * 
     * Mendeteksi apakah transaksi ini adalah duplikat berdasarkan beberapa kriteria:
     * - Tanggal: dalam range 5 menit sebelum dan sesudah
     * - Jumlah: 95-105% dari nominal yang diberikan (tolerance untuk round-up)
     * - Tipe: sama (income/expense)
     * - Kategori: sama
     * - Note: similar (LIKE search)
     * 
     * @param string $userId ID pengguna
     * @param array $data Data transaksi yang akan dicek
     * @return bool True jika duplikat ditemukan
     */
    public function isDuplicate(string $userId, array $data): bool
    {
        // Tentukan tanggal transaksi (default hari ini)
        $date = isset($data['date'])
            ? \Carbon\Carbon::parse($data['date'])
            : now();

        // Tentukan range waktu (5 menit sebelum dan sesudah)
        $start = $date->copy()->subMinutes(5);
        $end = $date->copy()->addMinutes(5);

        // Query transaksi yang mirip
        $query = Transaction::where('user_id', $userId)
            ->whereBetween('date', [$start, $end])
            // Cek amount dengan tolerance 5%
            ->whereBetween('amount', [
                $data['amount'] * 0.95,
                $data['amount'] * 1.05
            ]);

        // Filter berdasarkan tipe jika ada
        if (!empty($data['type'])) {
            $query->where('type', $data['type']);
        }

        // Filter berdasarkan kategori jika ada
        if (!empty($data['category_id'])) {
            $query->where('category_id', $data['category_id']);
        }

        // Filter berdasarkan note jika ada (LIKE search)
        if (!empty($data['note'])) {
            $note = trim($data['note']);
            // Use only LIKE for SQLite compatibility (SOUNDEX not supported)
            $query->where('note', 'LIKE', "%{$note}%");
        }

        // Return true jika ada duplikat
        return $query->exists();
    }

    /**
     * getSummary
     * 
     * Mengambil ringkasan transaksi untuk periode tertentu (daily, weekly, monthly, last_month).
     * Mengembalikan total income/expense, balance, savings rate, top categories, dan transaction count.
     * 
     * @param User $user Pengguna yang ringkasan transaksinya diambil
     * @param string $period Periode (weekly, last_month, atau default monthly)
     * @return array Array berisi summary transaksi
     */
    public function getSummary($user, string $period = 'monthly'): array
    {
        // Resolve periode menjadi start dan end date
        [$startDate, $endDate] = $this->resolvePeriod($period);

        // Query dasar untuk transaksi dalam periode
        $baseQuery = $user->transactions()
            ->whereBetween('date', [$startDate, $endDate]);

        // Hitung total pemasukan
        $totalIncome = (clone $baseQuery)
            ->where('type', 'income')
            ->sum('amount');

        // Hitung total pengeluaran
        $totalExpense = (clone $baseQuery)
            ->where('type', 'expense')
            ->sum('amount');

        // Ambil top 5 kategori pengeluaran
        $topCategories = (clone $baseQuery)
            ->selectRaw('category_id, SUM(amount) as total')
            ->with('category:id,name,icon,color')
            ->where('type', 'expense')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'category_id' => $item->category->id,
                    'category_name' => $item->category->name,
                    'category_icon' => $item->category->icon,
                    'category_color' => $item->category->color,
                    'amount' => (float) $item->total,
                ];
            })
            ->values();

        return [
            'total_income' => (float) $totalIncome,
            'total_expense' => (float) $totalExpense,
            'balance' => (float) ($totalIncome - $totalExpense),
            'savings_rate' => $this->calculateSavingsRate($totalIncome, $totalExpense),
            'top_categories' => $topCategories,
            'transaction_count' => (clone $baseQuery)->count(),
        ];
    }

    /**
     * resolvePeriod
     * 
     * Mengkonversi string periode menjadi start dan end date.
     * 
     * @param string $period Periode (weekly, last_month, or default monthly)
     * @return array Array berisi [$startDate, $endDate]
     */
    private function resolvePeriod(string $period): array
    {
        return match ($period) {
            'weekly' => [now()->startOfWeek(), now()->endOfWeek()],
            'last_month' => [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            ],
            default => [now()->startOfMonth(), now()->endOfMonth()],
        };
    }
    
    /**
     * calculateSavingsRate
     * 
     * Menghitung persentase tabungan dari pemasukan.
     * Formula: ((pemasukan - pengeluaran) / pemasukan) * 100
     * 
     * @param float $income Total pemasukan
     * @param float $expense Total pengeluaran
     * @return float Persentase tabungan (2 decimal places)
     */
    private function calculateSavingsRate(float $income, float $expense): float
    {
        // Jika tidak ada pemasukan, return 0
        if ($income <= 0) {
            return 0;
        }

        // Hitung percentage dan round ke 2 desimal
        return round((($income - $expense) / $income) * 100, 2);
    }

    /**
     * handlePostCreate
     * 
     * Private method untuk handle side effects setelah transaksi dibuat.
     * Berisi logic update budget dan streak (dipindahkan ke event handler).
     * 
     * @param Transaction $transaction Transaksi yang baru dibuat
     * @param User $user Pengguna pemilik transaksi
     * @return void
     */
    private function handlePostCreate(Transaction $transaction, $user): void
    {
        try {
            // Update budget berdasarkan transaksi baru
            $this->budgetService->updateSpentAmount($transaction);
            // Update streak mencatat
            $this->streakService->updateStreak($user);
        } catch (\Throwable $e) {
            // Log error tapi jangan stop proses utama
            report($e);
        }
    }
}