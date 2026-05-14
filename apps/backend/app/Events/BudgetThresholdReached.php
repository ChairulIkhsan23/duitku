<?php

namespace App\Events;

use App\Models\Budget;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BudgetThresholdReached
{
    use Dispatchable, SerializesModels;

    /**
     * Data budget yang mencapai threshold tertentu
     */
    public Budget $budget;

    /**
     * Level threshold yang dicapai (misalnya: warning, overspent)
     */
    public string $level; 

    /**
     * User pemilik budget
     */
    public User $user;

    /**
     * Create a new event instance
     *
     * @param Budget $budget
     * @param string $level
     * @param User $user
     */
    public function __construct(Budget $budget, string $level, User $user)
    {
        $this->budget = $budget;
        $this->level = $level;
        $this->user = $user;
    }
}