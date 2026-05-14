<?php

namespace App\Listeners;

use App\Events\TransactionCreated;
use App\Services\BudgetService;
use App\Enums\TransactionType;
use App\Events\BudgetThresholdReached;

class UpdateBudgetSpent
{
    /**
     * Service untuk update data budget user
     */
    protected BudgetService $budgetService;

    /**
     * Inject BudgetService ke listener
     */
    public function __construct(BudgetService $budgetService)
    {
        $this->budgetService = $budgetService;
    }

    /**
     * Handle event TransactionCreated
     * Update total pengeluaran budget jika transaksi adalah expense
     */
    public function handle(TransactionCreated $event): void
    {
        // Hanya proses jika transaksi adalah pengeluaran
        if ($event->transaction->type !== TransactionType::EXPENSE) {
            return;
        }

        // Update spent amount pada budget terkait
        $budget = $this->budgetService->updateSpentAmount($event->transaction);
        
        // Jika budget ada dan sudah mencapai threshold
        if ($budget && $budget->percentage >= 80) {

            // Tentukan level alert budget
            $level = $budget->percentage >= 100 ? 'overspent' : 'warning';

            // Trigger event untuk notifikasi budget
            event(new BudgetThresholdReached(
                $budget,
                $level,
                $event->user
            ));
        }
    }
}