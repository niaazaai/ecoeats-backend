<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    // Public routes
    Route::get('/csrf-cookie', [AuthController::class, 'csrfCookie'])
        ->name('auth.csrf-cookie');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('auth.login');

    // Optional registration (guarded by config)
    if (config('app.feature_registration_enabled', false)) {
        Route::post('/register', [AuthController::class, 'register'])
            ->name('auth.register');
    }

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me'])
            ->name('auth.me');

        Route::post('/logout', [AuthController::class, 'logout'])
            ->name('auth.logout');
    });
});

