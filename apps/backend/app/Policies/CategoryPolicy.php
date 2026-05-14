<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;

/**
 * Policy untuk mengatur akses user terhadap resource Category
 */
class CategoryPolicy
{
    /**
     * User boleh melihat semua kategori (akan difilter di level query)
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * User boleh melihat kategori jika:
     * - kategori default (global), atau
     * - kategori milik user sendiri
     */
    public function view(User $user, Category $category): bool
    {
        return $category->is_default || $category->user_id === $user->id;
    }

    /**
     * User tidak boleh membuat kategori melalui policy ini
     * (kemungkinan hanya sistem / seeding yang membuat)
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * User boleh update jika:
     * - bukan kategori default, dan
     * - milik user sendiri
     */
    public function update(User $user, Category $category): bool
    {
        return !$category->is_default && $user->id === $category->user_id;
    }

    /**
     * User boleh delete jika:
     * - bukan kategori default, dan
     * - milik user sendiri
     */
    public function delete(User $user, Category $category): bool
    {
        return !$category->is_default && $user->id === $category->user_id;
    }

    /**
     * Tidak diizinkan restore kategori
     */
    public function restore(User $user, Category $category): bool
    {
        return false;
    }

    /**
     * Tidak diizinkan force delete kategori permanen
     */
    public function forceDelete(User $user, Category $category): bool
    {
        return false;
    }
}