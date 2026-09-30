<?php

use App\Http\Controllers\ExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Export routes
Route::middleware(['auth'])->prefix('exports')->group(function () {
    Route::get('/deposits', [ExportController::class, 'exportDeposits'])->name('exports.deposits');
    Route::get('/accounts', [ExportController::class, 'exportAccounts'])->name('exports.accounts');
    Route::get('/targets', [ExportController::class, 'exportTargets'])->name('exports.targets');
    Route::get('/full-report', [ExportController::class, 'exportFullReport'])->name('exports.full-report');
    Route::get('/performance-pdf', [ExportController::class, 'exportPerformancePdf'])->name('exports.performance-pdf');
});
