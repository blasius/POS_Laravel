<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

// Public Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/portal/login', [AuthController::class, 'portalLogin']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    // --- POS SaaS Portal Routes ---
    Route::prefix('portal')->group(function () {

        // Products & Inventory
        Route::apiResource('products', 'ProductController');
        Route::apiResource('categories', 'CategoryController');
        Route::post('inventory/adjust', 'InventoryController@adjust');

        // POS Operations
        Route::post('pos/checkout', 'POSController@checkout');
        Route::get('pos/products', 'POSController@searchProducts');

        // Sales
        Route::get('sales', 'SaleController@index');
        Route::get('sales/{sale}', 'SaleController@show');

        // Customers
        Route::apiResource('customers', 'CustomerController');

        // Expenses
        Route::apiResource('expenses', 'ExpenseController');

        // Reports
        Route::prefix('reports')->group(function () {
            Route::get('summary', 'ReportController@summary');
            Route::get('revenue', 'ReportController@revenue');
            Route::get('inventory', 'ReportController@inventory');
        });

        // Settings
        Route::get('settings', 'SettingController@index');
        Route::post('settings', 'SettingController@update');

        // Support
        Route::prefix('support')->group(function () {
            Route::get('tickets', 'SupportTicketController@index');
            Route::post('tickets', 'SupportTicketController@store');
        });
    });
});
