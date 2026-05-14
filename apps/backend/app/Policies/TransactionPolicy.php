<?php

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;

/**
 * Policy untuk mengatur akses user terhadap resource Transaction
 */
class TransactionPolicy
{
    /**
     * User boleh melihat daftar transaksi (akan difilter di query)
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * User hanya boleh melihat transaksi miliknya sendiri
     */
    public function view(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id;
    }

    /**
     * Semua user yang login boleh membuat transaksi
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * User hanya boleh update transaksi miliknya sendiri
     */
    public function update(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id;
    }

    /**
     * User hanya boleh menghapus transaksi miliknya sendiri
     */
    public function delete(User $user, Transaction $transaction): bool
    {
        return $user->id === $transaction->user_id;
    }

    /**
     * Tidak diizinkan restore transaksi
     */
    public function restore(User $user, Transaction $transaction): bool
    {
        return false;
    }

    /**
     * Tidak diizinkan hapus permanen transaksi
     */
    public function forceDelete(User $user, Transaction $transaction): bool
    {
        return false;
    }
}