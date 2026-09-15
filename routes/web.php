<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LookupController;
use App\Http\Controllers\SiteController;
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
    Route::get('/visitors/export', [VisitorLogController::class, 'export'])->name('visitors.export');
    Route::get('/visitors/check-in', [CheckInController::class, 'create'])->name('visitors.checkin');
    Route::post('/visitors/check-in', [CheckInController::class, 'store'])->name('visitors.checkin.store');
    Route::post('/visits/{visit}/checkout', [CheckInController::class, 'checkout'])->name('visits.checkout');

    // Sites & Locations Management
    Route::get('/sites', [SiteController::class, 'index'])->name('sites.index');
    Route::post('/sites', [SiteController::class, 'store'])->name('sites.store');
    Route::put('/sites/{site}', [SiteController::class, 'update'])->name('sites.update');
    Route::delete('/sites/{site}', [SiteController::class, 'destroy'])->name('sites.destroy');

    // Department Management
    Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
    Route::post('/departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::put('/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

    // Employee Directory Management
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::post('/employees/{employee}/leave', [EmployeeController::class, 'toggleLeave'])->name('employees.leave');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    // Lookup Settings Management
    Route::get('/lookups', [LookupController::class, 'index'])->name('lookups.index');

    Route::post('/lookups/purposes', [LookupController::class, 'storePurpose'])->name('lookups.purposes.store');
    Route::put('/lookups/purposes/{purpose}', [LookupController::class, 'updatePurpose'])->name('lookups.purposes.update');

    Route::post('/lookups/id-types', [LookupController::class, 'storeIdType'])->name('lookups.id_types.store');
    Route::put('/lookups/id-types/{idType}', [LookupController::class, 'updateIdType'])->name('lookups.id_types.update');

    Route::post('/lookups/visitor-types', [LookupController::class, 'storeVisitorType'])->name('lookups.visitor_types.store');
    Route::put('/lookups/visitor-types/{visitorType}', [LookupController::class, 'updateVisitorType'])->name('lookups.visitor_types.update');

    Route::post('/lookups/shifts', [LookupController::class, 'storeShift'])->name('lookups.shifts.store');
    Route::put('/lookups/shifts/{shift}', [LookupController::class, 'updateShift'])->name('lookups.shifts.update');

    Route::post('/lookups/floors', [LookupController::class, 'storeFloor'])->name('lookups.floors.store');
    Route::put('/lookups/floors/{floor}', [LookupController::class, 'updateFloor'])->name('lookups.floors.update');
});
