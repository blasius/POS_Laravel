<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Public Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/portal/login', [AuthController::class, 'portalLogin']);

// Protected Routes
Route::middleware('auth')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    // --- POS SaaS Portal Routes ---
    Route::prefix('portal')->group(function () {
        Route::get('categories', [CategoryController::class, 'index']);
        Route::post('categories', [CategoryController::class, 'store']);
        Route::get('categories/{category}', [CategoryController::class, 'show']);
        Route::put('categories/{category}', [CategoryController::class, 'update']);
        Route::delete('categories/{category}', [CategoryController::class, 'destroy']);
    });
});
