<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\WelcomeController;
use App\Http\Middleware\CheckSetupIsDone;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    CheckSetupIsDone::class
])->group(function () {
    Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('setup')->group(function () {
        Route::get('/', [SetupController::class, 'index'])->name('setup.index');

        Route::post('/save-credentials', [SetupController::class, 'saveCredentials'])->name('setup.save-credentials');
    });

    Route::prefix('orders')->group(function () {
        Route::get('/import', [OrderController::class, 'importOrders'])->name('orders.import');
        Route::get('/', [OrderController::class, 'index'])->name('orders.index');
    });
});
