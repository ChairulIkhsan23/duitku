<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Budget;
use Illuminate\Support\Str;

/**
 * NotificationService
 * 
 * Mengelola pengiriman dan retrieval notifikasi untuk pengguna.
 * Service ini bertanggung jawab untuk membuat dan mengirim berbagai jenis notifikasi
 * seperti budget alert, badge earned, streak milestone, dan reminder.
 */
class NotificationService
{
    /**
     * getUserNotifications
     * 
     * Mengambil semua notifikasi pengguna dengan pagination.
     * Diurutkan berdasarkan waktu pembuatan (terbaru terlebih dahulu).
     * 
     * @param User $user Pengguna yang notifikasinya diambil
     * @param int $perPage Jumlah notifikasi per halaman (default: 20)
     * @return Paginator Paginated hasil notifikasi
     */
    public function getUserNotifications(User $user, int $perPage = 20)
    {
        return $user->notifications()
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    /**
     * getUserUnreadNotifications
     * 
     * Mengambil semua notifikasi yang belum dibaca oleh pengguna.
     * Diurutkan berdasarkan waktu pembuatan (terbaru terlebih dahulu).
     * 
     * @param User $user Pengguna yang notifikasi belum dibacanya diambil
     * @return Collection Collection notifikasi yang belum dibaca
     */
    public function getUserUnreadNotifications(User $user)
    {
        return $user->notifications()
            ->where('is_read', false)
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * getUnreadCount
     * 
     * Menghitung jumlah notifikasi yang belum dibaca oleh pengguna.
     * 
     * @param User $user Pengguna yang jumlah notifikasi belum dibacanya dihitung
     * @return int Jumlah notifikasi yang belum dibaca
     */
    public function getUnreadCount(User $user): int
    {
        return $user->notifications()
            ->where('is_read', false)
            ->count();
    }

    /**
     * markAsRead
     * 
     * Menandai satu notifikasi sebagai sudah dibaca.
     * 
     * @param User $user Pengguna pemilik notifikasi
     * @param string $notificationId ID notifikasi yang akan ditandai
     * @return void
     * @throws ModelNotFoundException Jika notifikasi tidak ditemukan
     */
    public function markAsRead(User $user, string $notificationId): void
    {
        $notification = $user->notifications()
            ->where('id', $notificationId)
            ->firstOrFail();

        $notification->markAsRead();
    }

    /**
     * markAllAsRead
     * 
     * Menandai semua notifikasi pengguna sebagai sudah dibaca.
     * 
     * @param User $user Pengguna yang semua notifikasinya akan ditandai
     * @return void
     */
    public function markAllAsRead(User $user): void
    {
        $user->notifications()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }

    /**
     * sendBudgetAlert
     * 
     * Mengirim notifikasi peringatan budget kepada pengguna.
     * Terdapat 3 level peringatan: warning (80%), overspent (100%), critical (110%+).
     * 
     * @param User $user Pengguna penerima notifikasi
     * @param Budget $budget Budget yang alert-nya dikirim
     * @param string $level Level alert (warning, overspent, overspend_critical)
     * @return void
     */
    public function sendBudgetAlert(User $user, Budget $budget, string $level): void
    {
        // Tentukan pesan berdasarkan level alert
        $messages = [
            'warning' => [
                'title' => 'Budget Menipis',
                'body' => "Pengeluaran {$budget->category->name} sudah mencapai {$budget->percentage}%. Sisa budget tinggal Rp " . number_format($budget->remaining, 0, ',', '.')
            ],
            'overspent' => [
                'title' => 'Budget Terlampaui',
                'body' => "Budget {$budget->category->name} sudah habis. Pengeluaran kamu sudah melewati batas yang ditentukan."
            ],
            'overspend_critical' => [
                'title' => 'Over Budget',
                'body' => "Pengeluaran {$budget->category->name} melebihi batas sebesar Rp " . number_format($budget->spent_amount - $budget->limit_amount, 0, ',', '.') . ". Segera lakukan penyesuaian keuangan."
            ],
        ];

        // Ambil pesan yang sesuai dengan level
        $message = $messages[$level] ?? $messages['warning'];

        // Buat notifikasi
        $this->createNotification(
            $user,
            $message['title'],
            $message['body'],
            'budget_alert',
            [
                'budget_id' => $budget->id,
                'category_name' => $budget->category->name,
                'percentage' => $budget->percentage,
                'level' => $level
            ]
        );
    }

    /**
     * sendReminder
     * 
     * Mengirim notifikasi reminder untuk pengguna yang belum mencatat transaksi.
     * Biasanya digunakan untuk reminder harian.
     * 
     * @param User $user Pengguna penerima reminder
     * @return void
     */
    public function sendReminder(User $user): void
    {
        $this->createNotification(
            $user,
            'Jangan Lupa Catat Transaksi',
            'Kamu belum mencatat transaksi hari ini. Konsistensi kecil hari ini akan membantu keuangan kamu lebih sehat ke depannya.',
            'reminder',
            ['type' => 'daily_reminder']
        );
    }

    /**
     * sendBadgeEarned
     * 
     * Mengirim notifikasi ketika pengguna mendapatkan badge baru.
     * 
     * @param User $user Pengguna penerima badge
     * @param Badge $badge Badge yang berhasil didapat
     * @return void
     */
    public function sendBadgeEarned(User $user, $badge): void
    {
        $this->createNotification(
            $user,
            'Badge Tercapai',
            "Selamat! Kamu mendapatkan badge {$badge->name}: {$badge->description}. Terus jaga progres finansial kamu!",
            'badge_earned',
            [
                'badge_id' => $badge->id,
                'badge_name' => $badge->name
            ]
        );
    }

    /**
     * sendStreakMilestone
     * 
     * Mengirim notifikasi ketika pengguna mencapai milestone streak tertentu.
     * Milestone yang dirayakan: 3, 7, 14, 30, 60, 100 hari.
     * 
     * @param User $user Pengguna penerima notifikasi
     * @param int $days Jumlah hari streak yang dicapai
     * @return void
     */
    public function sendStreakMilestone(User $user, int $days): void
    {
        $this->createNotification(
            $user,
            'Streak Milestone',
            "Kamu sudah mencatat transaksi selama {$days} hari berturut-turut. Pertahankan streak ini untuk membangun kebiasaan finansial yang kuat.",
            'streak_milestone',
            [
                'streak_days' => $days
            ]
        );
    }

    /**
     * sendInsightNotification
     * 
     * Mengirim notifikasi ketika insight baru sudah siap dibaca oleh pengguna.
     * 
     * @param User $user Pengguna penerima notifikasi
     * @param Insight $insight Insight yang baru di-generate
     * @return void
     */
    public function sendInsightNotification(User $user, $insight): void
    {
        // Format label periode untuk pesan
        $periodLabel = $insight->period_type?->label() ?? ucfirst($insight->period_type ?? 'weekly');
        
        $this->createNotification(
            $user,
            'Insight Baru Tersedia',
            "Insight {$periodLabel} kamu sudah siap dibaca. Klik untuk lihat analisis keuanganmu!",
            'insight_ready',
            ['insight_id' => $insight->id]
        );
    }

    /**
     * createNotification
     * 
     * Method private untuk membuat dan menyimpan notifikasi baru.
     * Digunakan oleh semua method public untuk standardisasi pembuatan notifikasi.
     * 
     * @param User $user Pengguna penerima notifikasi
     * @param string $title Judul notifikasi
     * @param string $body Isi/body notifikasi
     * @param string $type Tipe notifikasi (budget_alert, badge_earned, dll)
     * @param array $data Data tambahan untuk notifikasi (optional)
     * @return void
     */
    private function createNotification(
        User $user,
        string $title,
        string $body,
        string $type,
        array $data = []
    ): void {
        Notification::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data,
            'is_read' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}