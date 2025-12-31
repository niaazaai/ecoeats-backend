<?php

use App\Http\Controllers\Api\V1\ProjectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::apiResource('projects', ProjectController::class);
});

