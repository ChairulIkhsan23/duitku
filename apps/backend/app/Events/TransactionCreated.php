<?php

namespace App\Events;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransactionCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Data transaksi yang baru dibuat
     */
    public Transaction $transaction;

    /**
     * User yang membuat transaksi
     */
    public User $user;

    /**
     * Create a new event instance
     *
     * @param Transaction $transaction
     * @param User $user
     */
    public function __construct(Transaction $transaction, User $user)
    {
        $this->transaction = $transaction;
        $this->user = $user;
    }
}