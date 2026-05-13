<?php

namespace App\Services;

use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;

/**
 * StreakService
 * 
 * Mengelola konsistensi streak (hari berturut-turut) pengguna dalam mencatat transaksi.
 * Service ini menghitung dan update streak, serta mengirim notifikasi ketika mencapai milestone.
 */
class StreakService
{
    protected $notificationService;
    protected $badgeService;

    public function __construct(
        NotificationService $notificationService,
        BadgeService $badgeService
    ) {
        $this->notificationService = $notificationService;
        $this->badgeService = $badgeService;
    }

    /**
     * updateStreak
     * 
     * Memperbarui streak pengguna berdasarkan aktivitas mencatat transaksi hari ini.
     * Logic:
     * - Jika hari ini belum ada transaksi: mulai streak baru (1 hari)
     * - Jika hari kemarin ada transaksi: tambah streak
     * - Jika lebih dari sehari yang lalu: reset streak ke 1
     * - Jika sudah update hari ini: skip update
     * 
     * @param User $user Pengguna yang streaknya akan diupdate
     * @return void
     */
    public function updateStreak(User $user): void
    {
        // Ambil hari ini
        $today = Carbon::today();
        // Ambil tanggal transaksi terakhir pengguna
        $lastTransaction = $user->last_transaction_date;
        
        // Jika belum pernah ada transaksi, mulai dari hari pertama
        if (!$lastTransaction) {
            $user->streak_days = 1;
        } else {
            // Parse tanggal terakhir transaksi
            $lastDate = Carbon::parse($lastTransaction);
            $yesterday = Carbon::yesterday();
            
            // Jika sudah mencatat hari ini, skip
            if ($lastDate->isToday()) {
                return;
            }
            // Jika hari kemarin ada transaksi, tambah streak
            elseif ($lastDate->isYesterday()) {
                $user->streak_days++;
            }
            // Jika gap lebih dari 1 hari, reset streak
            else {
                $user->streak_days = 1;
            }
        }
        
        // Update tanggal transaksi terakhir dan tanggal streak terakhir
        $user->last_transaction_date = $today;
        $user->last_streak_date = $today;
        $user->save();
        
        // Cek apakah streak mencapai milestone
        $this->checkStreakMilestone($user);
    }

    /**
     * checkStreakMilestone
     * 
     * Memeriksa apakah pengguna telah mencapai milestone streak (3, 7, 14, 30, 60, 100 hari).
     * Jika mencapai milestone, kirim notifikasi dan cek badge.
     * 
     * @param User $user Pengguna yang streak-nya akan diperiksa
     * @return void
     */
    public function checkStreakMilestone(User $user): void
    {
        // Milestone yang dirayakan
        $milestones = [3, 7, 14, 30, 60, 100];
        
        // Jika streak pengguna adalah salah satu milestone
        if (in_array($user->streak_days, $milestones)) {
            // Kirim notifikasi milestone
            $this->notificationService->sendStreakMilestone($user, $user->streak_days);
            
            // Cek dan award badge jika memenuhi syarat
            $this->badgeService->checkAndAwardBadge($user, 'streak');
        }
    }

    /**
     * getStreakStatus
     * 
     * Mengambil informasi lengkap tentang status streak pengguna.
     * Termasuk streak saat ini, milestone berikutnya, dan apakah active hari ini.
     * 
     * @param User $user Pengguna yang streak statusnya diambil
     * @return array Array berisi informasi streak
     */
    public function getStreakStatus(User $user): array
    {
        // Daftar milestone streak
        $milestones = [3, 7, 14, 30, 60, 100];
        // Cari milestone berikutnya
        $nextMilestone = null;
        
        foreach ($milestones as $milestone) {
            if ($milestone > $user->streak_days) {
                $nextMilestone = $milestone;
                break;
            }
        }
        
        return [
            'current_streak' => $user->streak_days,
            'next_milestone' => $nextMilestone,
            'days_to_next_milestone' => $nextMilestone ? $nextMilestone - $user->streak_days : null,
            'last_transaction_date' => $user->last_transaction_date,
            'is_active_today' => $user->last_transaction_date && 
                Carbon::parse($user->last_transaction_date)->isToday()
        ];
    }
}