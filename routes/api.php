<?php

use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ]);
})->name('health');

Route::prefix('v1')->group(function () {
    require __DIR__.'/api/v1/auth.php';
    require __DIR__.'/api/v1/projects.php';
});

