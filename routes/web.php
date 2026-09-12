<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\VisitorLogController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard or login
Route::get('/', function () {
    return Auth::check() ? redirect()->route('dashboard') : redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Admin & Operations Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Visitor Log & Check-In Routes
    Route::get('/visitors', [VisitorLogController::class, 'index'])->name('visitors.index');
    Route::get('/visitors/check-in', [CheckInController::class, 'create'])->name('visitors.checkin');
    Route::post('/visitors/check-in', [CheckInController::class, 'store'])->name('visitors.checkin.store');
    Route::post('/visits/{visit}/checkout', [CheckInController::class, 'checkout'])->name('visits.checkout');
});
