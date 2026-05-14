<?php

use Illuminate\Support\Facades\Route;

/**
 * =========================
 * PUBLIC ROUTES (tanpa auth)
 * =========================
 */

// Register user baru
Route::post('/register', [App\Http\Controllers\API\AuthController::class, 'register']);

// Login user
Route::post('/login', [App\Http\Controllers\API\AuthController::class, 'login']);

/**
 * =========================
 * PROTECTED ROUTES (Sanctum)
 * =========================
 */
Route::middleware('auth:sanctum')->group(function () {

    // Logout & user profile dasar
    Route::post('/logout', [App\Http\Controllers\API\AuthController::class, 'logout']);
    Route::get('/user', [App\Http\Controllers\API\AuthController::class, 'user']);

    /**
     * Dashboard
     */
    Route::get('/dashboard', [App\Http\Controllers\API\DashboardController::class, 'index']);

    /**
     * Transactions
     */
    Route::apiResource('transactions', App\Http\Controllers\API\TransactionController::class);

    // Summary transaksi per kategori
    Route::get('transactions/summary/by-category', [
        App\Http\Controllers\API\TransactionController::class,
        'summaryByCategory'
    ]);

    /**
     * Budgets
     */
    Route::get('budgets/current', [App\Http\Controllers\API\BudgetController::class, 'currentMonth']);
    Route::apiResource('budgets', App\Http\Controllers\API\BudgetController::class);

    /**
     * Categories
     */
    Route::apiResource('categories', App\Http\Controllers\API\CategoryController::class);

    /**
     * Profile
     */
    Route::get('/profile', [App\Http\Controllers\API\ProfileController::class, 'show']);
    Route::put('/profile', [App\Http\Controllers\API\ProfileController::class, 'update']);
    Route::put('/profile/settings', [App\Http\Controllers\API\ProfileController::class, 'updateSettings']);
    Route::post('/profile/notification-token', [App\Http\Controllers\API\ProfileController::class, 'updateNotificationToken']);

    /**
     * Reports
     */
    Route::get('/reports/weekly', [App\Http\Controllers\API\ReportController::class, 'weekly']);
    Route::get('/reports/monthly', [App\Http\Controllers\API\ReportController::class, 'monthly']);
    Route::get('/reports/custom', [App\Http\Controllers\API\ReportController::class, 'custom']);
    Route::post('/reports/export', [App\Http\Controllers\API\ReportController::class, 'export']);

    /**
     * Badges
     */
    Route::get('/badges', [App\Http\Controllers\API\BadgeController::class, 'index']);
    Route::get('/badges/all', [App\Http\Controllers\API\BadgeController::class, 'allBadges']);

    /**
     * Insights
     */
    Route::get('/insights', [App\Http\Controllers\API\InsightController::class, 'index']);
    Route::get('/insights/latest', [App\Http\Controllers\API\InsightController::class, 'latest']);
    Route::post('/insights/{id}/read', [App\Http\Controllers\API\InsightController::class, 'markAsRead']);
    Route::post('/insights/generate', [App\Http\Controllers\API\InsightController::class, 'generate']);

    /**
     * Notifications
     */
    Route::get('/notifications', [App\Http\Controllers\API\NotificationController::class, 'index']);
    Route::get('/notifications/unread', [App\Http\Controllers\API\NotificationController::class, 'unread']);
    Route::get('/notifications/unread-count', [App\Http\Controllers\API\NotificationController::class, 'unreadCount']);
    Route::post('/notifications/{id}/read', [App\Http\Controllers\API\NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [App\Http\Controllers\API\NotificationController::class, 'markAllAsRead']);
});