<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DonationController;
use App\Http\Controllers\DashboardController;

// Page Routes (Open to public, Javascript guards handle role checks where applicable)
Route::get('/', [DashboardController::class, 'index'])->name('index');
Route::get('/kampanye', [DashboardController::class, 'kampanye'])->name('kampanye');
Route::get('/dampak', [DashboardController::class, 'dampak'])->name('dampak');
Route::get('/donasi', [DashboardController::class, 'donasi'])->name('donasi');

Route::get('/buat-kampanye', function () {
    return view('buat-kampanye');
});

// Authentication routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Dashboards
    Route::get('/donatur', [DashboardController::class, 'donaturDashboard'])->name('donatur');
    Route::get('/fundraiser', [DashboardController::class, 'fundraiserDashboard'])->name('fundraiser');
    Route::get('/admin', [DashboardController::class, 'adminDashboard'])->name('admin');
    
    // Actions
    Route::post('/buat-kampanye', [CampaignController::class, 'store']);
    Route::post('/donasi', [DonationController::class, 'store']);
    Route::post('/switch-role', [DashboardController::class, 'switchRole']);
    
    // Admin Actions
    Route::post('/admin/campaigns/{id}/approve', [CampaignController::class, 'approve']);
    Route::post('/admin/campaigns/{id}/reject', [CampaignController::class, 'reject']);
    Route::post('/admin/campaigns/{id}/delete', [CampaignController::class, 'destroy']);
});
