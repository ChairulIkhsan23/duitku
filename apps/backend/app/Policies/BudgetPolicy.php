<?php

namespace App\Policies;

use App\Models\Budget;
use App\Models\User;

/**
 * Policy untuk mengatur akses user terhadap resource Budget
 */
class BudgetPolicy
{
    /**
     * User boleh melihat semua budget (akan difilter di query)
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Hanya pemilik budget yang boleh melihat detail budget
     */
    public function view(User $user, Budget $budget): bool
    {
        return $user->id === $budget->user_id;
    }

    /**
     * Semua user boleh membuat budget
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Hanya pemilik budget yang boleh mengubah data budget
     */
    public function update(User $user, Budget $budget): bool
    {
        return $user->id === $budget->user_id;
    }

    /**
     * Hanya pemilik budget yang boleh menghapus budget
     */
    public function delete(User $user, Budget $budget): bool
    {
        return $user->id === $budget->user_id;
    }

    /**
     * Tidak diizinkan restore budget
     */
    public function restore(User $user, Budget $budget): bool
    {
        return false;
    }

    /**
     * Tidak diizinkan force delete budget permanen
     */
    public function forceDelete(User $user, Budget $budget): bool
    {
        return false;
    }
}