<?php

namespace App\Providers;

use App\Models\Budget;
use App\Models\Transaction;
use App\Models\Category;
use App\Policies\BudgetPolicy;
use App\Policies\TransactionPolicy;
use App\Policies\CategoryPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

/**
 * Service Provider untuk registrasi semua Policy (Authorization)
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mapping model ke policy masing-masing
     */
    protected $policies = [
        Budget::class => BudgetPolicy::class, // Policy untuk Budget
        Transaction::class => TransactionPolicy::class, // Policy untuk Transaction
        Category::class => CategoryPolicy::class, // Policy untuk Category
    ];

    /**
     * Register services (tidak digunakan di sini)
     */
    public function register(): void
    {
        //
    }

    /**
     * Boot semua policy agar Laravel bisa menggunakan authorize()
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}