<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BranchController;
use App\Http\Controllers\Api\FinancialController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PerformanceController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login'])->middleware('login.rate_limit');
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Branches
    Route::get('/branches', [BranchController::class, 'index']);
    Route::get('/branches/{id}', [BranchController::class, 'show']);

    // Districts
    Route::get('/districts', [FinancialController::class, 'districts']);
    Route::get('/districts/{id}', [FinancialController::class, 'districtDetail']);

    // Financial Years
    Route::get('/financial-years', [FinancialController::class, 'financialYears']);
    Route::get('/financial-years/current', [FinancialController::class, 'currentFinancialYear']);

    // Financial Periods
    Route::get('/financial-periods', [FinancialController::class, 'financialPeriods']);

    // Annual Plans
    Route::get('/annual-plans', [FinancialController::class, 'annualPlans']);
    Route::get('/annual-plans/{id}', [FinancialController::class, 'annualPlanDetail']);

    // Branch Plans
    Route::get('/branch-deposit-plans', [FinancialController::class, 'branchDepositPlans']);
    Route::get('/branch-account-plans', [FinancialController::class, 'branchAccountPlans']);

    // Performances
    Route::get('/daily-deposit-performances', [FinancialController::class, 'dailyDepositPerformances']);
    Route::get('/daily-account-performances', [FinancialController::class, 'dailyAccountPerformances']);

    // Performance Summary
    Route::get('/performance/summary', [PerformanceController::class, 'summary']);
    Route::get('/performance/branch/{id}', [PerformanceController::class, 'branchPerformance']);
    Route::get('/performance/district/{id}', [PerformanceController::class, 'districtPerformance']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/mark-read', [NotificationController::class, 'markRead']);
});
