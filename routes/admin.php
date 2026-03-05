<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\MachineController;
use App\Http\Controllers\Admin\ProductionReportController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\SF001Controller;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "admin" middleware group.
|
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'check.active.user'])->group(function () {
    
    // Admin Only Routes
    Route::middleware(['check.admin.role'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Items Routes
        Route::get('/items', [ItemController::class, 'index'])->name('items.index');
        Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
        Route::post('/items', [ItemController::class, 'store'])->name('items.store');
        Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');
        Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
        Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');

        // Machines Routes
        Route::get('/machines', [MachineController::class, 'index'])->name('machines.index');
        Route::get('/machines/create', [MachineController::class, 'create'])->name('machines.create');
        Route::post('/machines', [MachineController::class, 'store'])->name('machines.store');
        Route::get('/machines/{machine}', [MachineController::class, 'show'])->name('machines.show');
        Route::get('/machines/{machine}/edit', [MachineController::class, 'edit'])->name('machines.edit');
        Route::put('/machines/{machine}', [MachineController::class, 'update'])->name('machines.update');
        Route::delete('/machines/{machine}', [MachineController::class, 'destroy'])->name('machines.destroy');

        // Users Routes
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    });

    // Routes accessible to all authenticated users (Admin & User)
    // Production Reports Routes
    Route::get('/production-reports', [ProductionReportController::class, 'index'])->name('production-reports.index');
    Route::get('/production-reports/sf001', [ProductionReportController::class, 'sf001'])->name('production-reports.sf001');
    Route::get('/production-reports/sf001/coil-stock', [SF001Controller::class, 'coilStock'])->name('production-reports.sf001.coil-stock');
    Route::get('/production-reports/sf002', [ProductionReportController::class, 'sf002'])->name('production-reports.sf002');
    Route::get('/production-reports/sf003', [ProductionReportController::class, 'sf003'])->name('production-reports.sf003');
    Route::get('/production-reports/create', [ProductionReportController::class, 'create'])->name('production-reports.create');
    Route::post('/production-reports', [ProductionReportController::class, 'store'])->name('production-reports.store');
    Route::get('/production-reports/{productionReport}', [ProductionReportController::class, 'show'])->name('production-reports.show');
    Route::get('/production-reports/{productionReport}/edit', [ProductionReportController::class, 'edit'])->name('production-reports.edit');
    Route::put('/production-reports/{productionReport}', [ProductionReportController::class, 'update'])->name('production-reports.update');
    Route::delete('/production-reports/{productionReport}', [ProductionReportController::class, 'destroy'])->name('production-reports.destroy');

    // Profile Management Routes
    Route::get('/profile/manage-password', [ProfileController::class, 'managePassword'])->name('profile.manage-password');
    Route::post('/profile/update-password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::get('/profile/manage-profile', [ProfileController::class, 'manageProfile'])->name('profile.manage-profile');
    Route::post('/profile/update-profile', [ProfileController::class, 'updateProfile'])->name('profile.update-profile');
});

