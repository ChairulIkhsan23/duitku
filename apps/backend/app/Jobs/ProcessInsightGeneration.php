<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Insight;
use App\Services\InsightService;
use App\Events\InsightGenerated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessInsightGeneration implements ShouldQueue
{
    use Queueable;

    /**
     * User yang akan diproses insight-nya
     */
    protected User $user;

    /**
     * Tipe periode insight (weekly / monthly)
     */
    protected string $periodType;

    /**
     * Create job instance untuk generate insight
     */
    public function __construct(User $user, string $periodType = 'weekly')
    {
        $this->user = $user;
        $this->periodType = $periodType;
    }

    /**
     * Handle proses generate insight di queue worker
     */
    public function handle(InsightService $insightService): void
    {
        // Generate insight berdasarkan user & periode
        $insightService->generate($this->user, $this->periodType);
        
        // Ambil insight terbaru yang baru dibuat
        $insight = Insight::where('user_id', $this->user->id)
            ->where('period_type', $this->periodType)
            ->latest()
            ->first();
            
        // Trigger event jika insight berhasil dibuat
        if ($insight) {
            event(new InsightGenerated($insight, $this->user));
        }
    }
}