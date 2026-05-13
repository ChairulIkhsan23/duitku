<?php

namespace App\Services;

use App\Events\InsightGenerated;
use App\Models\Insight;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * InsightService
 * 
 * Mengelola generation dan retrieval insight keuangan untuk pengguna.
 * Service ini menggunakan AI untuk generate insight berdasarkan data transaksi pengguna
 * dalam periode yang ditentukan (daily, weekly, monthly).
 */
class InsightService
{
    public function __construct(
        protected AI\InsightGeneratorService $insightGenerator
    ) {}

    /**
     * generate
     * 
     * Generate insight baru untuk pengguna berdasarkan periode tertentu.
     * Mencegah duplikasi dengan mengecek apakah insight sudah ada untuk periode tersebut.
     * Mengirim event ketika insight berhasil di-generate.
     * 
     * @param User $user Pengguna yang insightnya akan di-generate
     * @param string $periodType Tipe periode (daily, weekly, monthly) - default: weekly
     * @return void
     */
    public function generate(User $user, string $periodType = 'weekly'): void
    {
        // Resolve periode berdasarkan tipe periode
        [$start, $end] = $this->resolvePeriod($periodType);

        // Cek apakah insight sudah ada untuk periode ini
        $exists = Insight::where('user_id', $user->id)
            ->where('period_type', $periodType)
            ->whereDate('period_start', $start)
            ->exists();

        // Jika sudah ada, skip generation
        if ($exists) return;

        // Generate insight menggunakan AI
        $data = $this->insightGenerator->generate($user, $periodType);

        // Simpan insight ke database
        $insight = Insight::create([
            'id' => Str::uuid(),
            'user_id' => $user->id,
            'period_type' => $periodType,
            'period_start' => $start,
            'period_end' => $end,
            'data' => $data,
            'is_read' => false,
            'generated_at' => now(),
        ]);

        // Trigger event InsightGenerated untuk notifikasi
        event(new InsightGenerated($insight, $user));
    }

    /**
     * getUserInsights
     * 
     * Mengambil semua insight yang telah di-generate untuk pengguna.
     * Bisa di-filter berdasarkan tipe periode.
     * Diurutkan berdasarkan waktu generation (terbaru terlebih dahulu).
     * 
     * @param User $user Pengguna yang insightnya diambil
     * @param string|null $type Tipe periode untuk di-filter (optional)
     * @return Collection Collection insight pengguna
     */
    public function getUserInsights(User $user, ?string $type = null)
    {
        return Insight::query()
            ->where('user_id', $user->id)
            ->when($type, fn ($q) => $q->where('period_type', $type))
            ->orderByDesc('generated_at')
            ->get();
    }

    /**
     * getLatestUnread
     * 
     * Mengambil insight terbaru yang belum dibaca oleh pengguna.
     * 
     * @param User $user Pengguna yang insight-nya diambil
     * @return Insight|null Insight terbaru atau null jika semua sudah dibaca
     */
    public function getLatestUnread(User $user): ?Insight
    {
        return Insight::query()
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->latest('generated_at')
            ->first();
    }

    /**
     * markAsRead
     * 
     * Menandai insight sebagai sudah dibaca oleh pengguna.
     * 
     * @param User $user Pengguna pemilik insight
     * @param string $id ID insight yang akan ditandai dibaca
     * @return void
     */
    public function markAsRead(User $user, string $id): void
    {
        Insight::where('id', $id)
            ->where('user_id', $user->id)
            ->update(['is_read' => true]);
    }

    /**
     * resolvePeriod
     * 
     * Mengkonversi tipe periode (daily, weekly, monthly) menjadi pasangan
     * tanggal start dan end.
     * 
     * @param string $type Tipe periode
     * @return array Array berisi [$startDate, $endDate]
     */
    private function resolvePeriod(string $type): array
    {
        return match ($type) {
            'daily' => [now()->startOfDay(), now()->endOfDay()],
            'monthly' => [now()->startOfMonth(), now()->endOfMonth()],
            default => [now()->startOfWeek(), now()->endOfWeek()],
        };
    }
}