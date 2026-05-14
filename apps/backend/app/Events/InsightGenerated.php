<?php

namespace App\Events;

use App\Models\Insight;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InsightGenerated
{
    use Dispatchable, SerializesModels;

    /**
     * Data insight yang telah di-generate
     */
    public Insight $insight;

    /**
     * User yang menerima insight
     */
    public User $user;

    /**
     * Create a new event instance
     *
     * @param Insight $insight
     * @param User $user
     */
    public function __construct(Insight $insight, User $user)
    {
        $this->insight = $insight;
        $this->user = $user;
    }
}