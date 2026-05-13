<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\UserBadge;
use App\Models\User;
use App\Models\Category;  
use App\Services\NotificationService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

/**
 * BadgeService
 * 
 * Mengelola logic penghargaan badge kepada pengguna berdasarkan berbagai kriteria
 * seperti streak, jumlah transaksi, tingkat tabungan, dan pencapaian lainnya.
 * Service ini juga menangani pengiriman notifikasi ketika pengguna mendapatkan badge baru.
 */
class BadgeService
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * checkAndAwardBadge
     * 
     * Memeriksa dan memberikan badge kepada pengguna berdasarkan trigger tertentu.
     * Mencegah duplikasi dengan mengecek badge yang sudah dimiliki pengguna.
     * 
     * @param User $user Pengguna yang akan diperiksa untuk badge
     * @param string $trigger Pemicu badge (misalnya: 'streak', 'transaction_count', dll)
     * @return void
     */
    public function checkAndAwardBadge(User $user, string $trigger): void
    {
        // Cari semua badge yang cocok dengan trigger
        $badges = Badge::where('trigger', $trigger)->get();
        
        // Ambil ID badge yang sudah dimiliki pengguna
        $ownedBadgeIds = $user->badges()->pluck('badges.id')->toArray();
        
        // Iterasi setiap badge dan cek requirement
        foreach ($badges as $badge) {
            // Skip jika badge sudah dimiliki
            if (in_array($badge->id, $ownedBadgeIds)) {
                continue;
            }
            
            // Cek apakah badge requirement terpenuhi
            if ($this->checkBadgeRequirement($user, $badge)) {
                $this->awardBadge($user, $badge);
            }
        }
    }

    /**
     * checkBadgeRequirement
     * 
     * Memeriksa apakah pengguna telah memenuhi semua requirement untuk mendapatkan badge.
     * Menggunakan switch case untuk berbagai tipe requirement yang berbeda.
     * 
     * @param User $user Pengguna yang akan diperiksa
     * @param Badge $badge Badge yang requirement-nya akan diperiksa
     * @return bool True jika requirement terpenuhi, false jika tidak
     */
    private function checkBadgeRequirement(User $user, Badge $badge): bool
    {
        // Ambil requirement dari badge
        $requirement = $badge->requirement ?? [];
        
        // Decode jika masih string JSON
        if (is_string($requirement)) {
            $requirement = json_decode($requirement, true);
        }

        // Ambil tipe requirement
        $type = $requirement['type'] ?? null;

        if (!$type) {
            return false;
        }

        // Periksa requirement berdasarkan tipe
        switch ($type) {
            case 'streak':
                // Requirement: jumlah hari streak minimum
                $days = $requirement['days'] ?? 0;
                $userStreak = $user->streak_days ?? 0;
                return $userStreak >= $days;

            case 'transaction_count':
                // Requirement: jumlah transaksi minimum
                $count = $user->transactions()->count();
                $required = $requirement['min'] ?? 1;
                return $count >= $required;

            case 'savings_rate':
                // Requirement: tingkat tabungan minimum dalam periode
                return $this->checkSavingsRate($user, $requirement);

            case 'morning_transaction':
                // Requirement: jumlah minimum transaksi yang dibuat di pagi hari
                return $this->checkMorningTransactions($user, $requirement);

            case 'night_transaction':
                // Requirement: jumlah minimum transaksi malam dengan jumlah minimum
                return $this->checkNightTransactions($user, $requirement);

            case 'all_categories_used':
                // Requirement: pengguna sudah menggunakan semua kategori
                return $this->checkAllCategoriesUsed($user);

            case 'report_count':
                // Requirement: jumlah laporan terjadwal minimum
                $count = $user->scheduledReports()->count();
                $required = $requirement['count'] ?? 1;
                return $count >= $required;

            case 'no_overspend_days':
                // Requirement: tidak ada budget yang terlampaui dalam periode
                return $this->checkNoOverspend($user, $requirement);

            default:
                return false;
        }
    }

    /**
     * checkSavingsRate
     * 
     * Memeriksa apakah pengguna telah mencapai tingkat tabungan (savings rate) minimum
     * dalam periode yang ditentukan.
     * 
     * @param User $user Pengguna yang akan diperiksa
     * @param array $requirement Array berisi 'months' (jumlah bulan) dan 'percentage' (persen minimum)
     * @return bool True jika savings rate >= percentage requirement
     */
    private function checkSavingsRate(User $user, array $requirement): bool
    {
        // Ambil jumlah bulan dan persentase target dari requirement
        $months = $requirement['months'] ?? 3;
        $requiredPercentage = $requirement['percentage'] ?? 30;
        
        // Hitung tanggal awal dan akhir periode
        $endDate = now();
        $startDate = now()->subMonths($months);
        
        // Hitung total pemasukan dalam periode
        $income = $user->transactions()
            ->where('type', 'income')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');
            
        // Hitung total pengeluaran dalam periode
        $expense = $user->transactions()
            ->where('type', 'expense')
            ->whereBetween('date', [$startDate, $endDate])
            ->sum('amount');
            
        // Jika tidak ada pemasukan, badge tidak bisa didapat
        if ($income == 0) return false;
        
        // Hitung savings rate: (Pemasukan - Pengeluaran) / Pemasukan * 100
        $savingsRate = (($income - $expense) / $income) * 100;
        return $savingsRate >= $requiredPercentage;
    }

    /**
     * checkMorningTransactions
     * 
     * Memeriksa apakah pengguna telah membuat transaksi pagi (sebelum jam 9)
     * dengan jumlah minimum yang ditentukan.
     * 
     * @param User $user Pengguna yang akan diperiksa
     * @param array $requirement Array berisi 'count' (jumlah transaksi minimum)
     * @return bool True jika jumlah transaksi pagi >= count requirement
     */
    private function checkMorningTransactions(User $user, array $requirement): bool
    {
        // Ambil jumlah transaksi pagi yang dibutuhkan
        $requiredCount = $requirement['count'] ?? 7;
        
        // Hitung transaksi yang dibuat sebelum jam 9 pagi
        $count = $user->transactions()
            ->whereRaw("EXTRACT(HOUR FROM created_at) < 9")
            ->count();
            
        return $count >= $requiredCount;
    }

    /**
     * checkNightTransactions
     * 
     * Memeriksa apakah pengguna telah membuat transaksi malam (20:00-23:00)
     * dengan jumlah dan nilai minimum yang ditentukan.
     * 
     * @param User $user Pengguna yang akan diperiksa
     * @param array $requirement Array berisi 'count' (jumlah minimum) dan 'amount' (jumlah nominal)
     * @return bool True jika transaksi malam >= requirements
     */
    private function checkNightTransactions(User $user, array $requirement): bool
    {
        // Ambil jumlah transaksi dan nominal minimum yang dibutuhkan
        $requiredCount = $requirement['count'] ?? 5;
        $minAmount = $requirement['amount'] ?? 500000;
        
        // Hitung transaksi malam (20:00-23:00) dengan nominal >= minAmount
        $count = $user->transactions()
            ->where('amount', '>=', $minAmount)
            ->whereRaw("EXTRACT(HOUR FROM created_at) BETWEEN 20 AND 23")
            ->count();
            
        return $count >= $requiredCount;
    }

    /**
     * checkAllCategoriesUsed
     * 
     * Memeriksa apakah pengguna sudah menggunakan semua kategori yang tersedia.
     * 
     * @param User $user Pengguna yang akan diperiksa
     * @return bool True jika pengguna sudah menggunakan semua kategori
     */
    private function checkAllCategoriesUsed(User $user): bool
    {
        // Hitung kategori yang sudah digunakan oleh pengguna
        $usedCategories = $user->transactions()
            ->distinct()
            ->pluck('category_id')
            ->count();
            
        // Hitung total kategori yang tersedia untuk pengguna
        $totalCategories = Category::where(function ($q) use ($user) {
            // Kategori default atau kategori milik pengguna
            $q->where('user_id', $user->id)
            ->orWhere('is_default', true);
        })->count();
            
        // Badge dapat jika sudah menggunakan semua kategori
        return $usedCategories >= $totalCategories;
    }

    /**
     * checkNoOverspend
     * 
     * Memeriksa apakah pengguna tidak pernah melampaui budget dalam periode tertentu.
     * 
     * @param User $user Pengguna yang akan diperiksa
     * @param array $requirement Array berisi 'days' (jumlah hari yang diperiksa)
     * @return bool True jika tidak ada budget yang terlampaui
     */
    private function checkNoOverspend(User $user, array $requirement): bool
    {
        // Ambil jumlah hari yang ingin diperiksa
        $days = $requirement['days'] ?? 30;
        
        // Hitung berapa bulan perlu diperiksa
        $startMonth = now()->subMonths(ceil($days / 30))->format('Y-m');

        // Ambil semua budget dari periode yang diinginkan
        $budgets = $user->budgets()
            ->where('month_year', '>=', $startMonth)
            ->get();
            
        // Periksa setiap budget, jika ada yang terlampaui return false
        foreach ($budgets as $budget) {
            if ($budget->spent_amount > $budget->limit_amount) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * awardBadge
     * 
     * Memberikan badge kepada pengguna dan mengirim notifikasi.
     * Menangani edge case jika badge sudah diberikan sebelumnya (duplicate).
     * 
     * @param User $user Pengguna yang akan mendapat badge
     * @param Badge $badge Badge yang akan diberikan
     * @return void
     */
    private function awardBadge(User $user, Badge $badge): void
    {
        try {
            // Buat record UserBadge di database
            UserBadge::create([
                'id' => Str::uuid(),
                'user_id' => $user->id,
                'badge_id' => $badge->id,
                'awarded_at' => now(),
                'progress_data' => null,
            ]);

            // Kirim notifikasi ke pengguna tentang badge yang didapat
            $this->notificationService->sendBadgeEarned($user, $badge);

        } catch (QueryException $e) {
            // Abaikan error jika badge sudah ada (duplicate entry error - 23000)
            if ($e->getCode() === '23000') {
                return;
            }

            throw $e;
        }
    }

    /**
     * getUserBadges
     * 
     * Mengambil semua badge yang telah didapat oleh pengguna,
     * diurutkan berdasarkan waktu perolehan (terbaru terlebih dahulu).
     * 
     * @param User $user Pengguna yang badge-nya akan diambil
     * @return Collection Collection berisi badge-badge pengguna
     */
    public function getUserBadges(User $user)
    {
        return $user->badges()
            ->withPivot('awarded_at', 'progress_data')
            ->orderBy('user_badges.awarded_at', 'desc')
            ->get();
    }
}