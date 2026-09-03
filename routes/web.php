<?php

use App\Http\Controllers\GoalController;
use App\Http\Controllers\WantController;
use App\Http\Controllers\NeedController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\WithdrawalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [GoalController::class, 'dashboard'])->name('dashboard');
    Route::resource('goals', GoalController::class);
    Route::resource('wants', WantController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('needs', NeedController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('deposits', DepositController::class)->only(['index', 'create', 'store']);
    Route::resource('withdrawals', WithdrawalController::class)->only(['index', 'create', 'store', 'show']);

    Route::post('withdrawals/{withdrawal}/process', [WithdrawalController::class, 'processDecision'])
        ->name('withdrawals.process');
});

require __DIR__.'/auth.php';
