<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Public routes (no authentication required)
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Protected routes (authentication required via Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me'])->name('profile');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
