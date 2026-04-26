<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\MachineController;
use App\Http\Controllers\Admin\ProductionReportController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RejectReasonController;
use App\Http\Controllers\Admin\SF001Controller;
use App\Http\Controllers\Admin\SF002Controller;
use App\Http\Controllers\Admin\SF003Controller;
use App\Http\Controllers\Admin\PPCController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WeightCapacityController;
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
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Item master routes for Admin and Stock roles
    Route::middleware(['check.admin.or.stock.role'])->group(function () {
        Route::get('/items', [ItemController::class, 'index'])->name('items.index');
        Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
        Route::post('/items', [ItemController::class, 'store'])->name('items.store');
        Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');
        Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
        Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
    });
    
    // Admin Only Routes
    Route::middleware(['check.admin.role'])->group(function () {
        // Machines Routes
        Route::get('/machines', [MachineController::class, 'index'])->name('machines.index');
        Route::get('/machines/create', [MachineController::class, 'create'])->name('machines.create');
        Route::post('/machines', [MachineController::class, 'store'])->name('machines.store');
        Route::get('/machines/{machine}', [MachineController::class, 'show'])->name('machines.show');
        Route::get('/machines/{machine}/edit', [MachineController::class, 'edit'])->name('machines.edit');
        Route::put('/machines/{machine}', [MachineController::class, 'update'])->name('machines.update');
        Route::delete('/machines/{machine}', [MachineController::class, 'destroy'])->name('machines.destroy');

        // Reject Reasons Routes
        Route::get('/reject-reasons', [RejectReasonController::class, 'index'])->name('reject-reasons.index');
        Route::get('/reject-reasons/create', [RejectReasonController::class, 'create'])->name('reject-reasons.create');
        Route::post('/reject-reasons', [RejectReasonController::class, 'store'])->name('reject-reasons.store');
        Route::get('/reject-reasons/{encryptedId}', [RejectReasonController::class, 'show'])->name('reject-reasons.show');
        Route::get('/reject-reasons/{encryptedId}/edit', [RejectReasonController::class, 'edit'])->name('reject-reasons.edit');
        Route::put('/reject-reasons/{encryptedId}', [RejectReasonController::class, 'update'])->name('reject-reasons.update');
        Route::delete('/reject-reasons/{encryptedId}', [RejectReasonController::class, 'destroy'])->name('reject-reasons.destroy');

        // Weight Capacities Routes
        Route::get('/weight-capacities', [WeightCapacityController::class, 'index'])->name('weight-capacities.index');
        Route::get('/weight-capacities/create', [WeightCapacityController::class, 'create'])->name('weight-capacities.create');
        Route::post('/weight-capacities', [WeightCapacityController::class, 'store'])->name('weight-capacities.store');
        Route::get('/weight-capacities/{encryptedId}/edit', [WeightCapacityController::class, 'edit'])->name('weight-capacities.edit');
        Route::put('/weight-capacities/{encryptedId}', [WeightCapacityController::class, 'update'])->name('weight-capacities.update');
        Route::delete('/weight-capacities/{encryptedId}', [WeightCapacityController::class, 'destroy'])->name('weight-capacities.destroy');

        // Users Routes
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/login-activity', [UserController::class, 'loginActivity'])->name('users.login-activity');
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
    Route::redirect('/production-reports', '/admin/production-reports/sf001')->name('production-reports.index');
    Route::get('/production-reports/sf001', [ProductionReportController::class, 'sf001'])->name('production-reports.sf001');
    Route::get('/production-reports/sf001/coil-stock', [SF001Controller::class, 'coilStock'])->name('production-reports.sf001.coil-stock');
    Route::get('/production-reports/sf001/coil-stock/{coilId}/view', [SF001Controller::class, 'viewCoilStock'])->name('production-reports.sf001.coil-stock.view');
    Route::get('/production-reports/sf001/coil-stock/{coilId}/multi-load', [SF001Controller::class, 'multiLoadCoil'])->name('production-reports.sf001.coil-stock.multi-load');
    Route::post('/production-reports/sf001/coil-stock/{coilId}/multi-load', [SF001Controller::class, 'storeMultiLoadAllocation'])->name('production-reports.sf001.coil-stock.multi-load.store');
    Route::post('/production-reports/sf001/coil-stock/{coilId}/multi-load/{allocationId}/unload', [SF001Controller::class, 'unloadMultiLoadAllocation'])->name('production-reports.sf001.coil-stock.multi-load.unload');
    Route::post('/production-reports/sf001/coil-stock/{coilId}/multi-load/{allocationId}/update', [SF001Controller::class, 'updateMultiLoadAllocation'])->name('production-reports.sf001.coil-stock.multi-load.update');
    Route::post('/production-reports/sf001/coil-stock', [SF001Controller::class, 'storeCoilStock'])->name('production-reports.sf001.coil-stock.store');
    Route::post('/production-reports/sf001/coil-stock/load-machine', [SF001Controller::class, 'loadCoilToMachine'])->name('production-reports.sf001.coil-stock.load-machine');
    Route::put('/production-reports/sf001/coil-stock/{coilId}', [SF001Controller::class, 'updateCoilStock'])->name('production-reports.sf001.coil-stock.update');
    Route::delete('/production-reports/sf001/coil-stock/{coilId}', [SF001Controller::class, 'destroyCoilStock'])->name('production-reports.sf001.coil-stock.destroy');
    Route::post('/production-reports/sf001/coil-manufacturers', [SF001Controller::class, 'storeManufacturer'])->name('production-reports.sf001.coil-manufacturers.store');
    Route::put('/production-reports/sf001/coil-manufacturers/{id}', [SF001Controller::class, 'updateManufacturer'])->name('production-reports.sf001.coil-manufacturers.update');
    Route::delete('/production-reports/sf001/coil-manufacturers/{id}', [SF001Controller::class, 'destroyManufacturer'])->name('production-reports.sf001.coil-manufacturers.destroy');
    Route::get('/production-reports/sf001/stock', [SF001Controller::class, 'stock'])->name('production-reports.sf001.stock');
    Route::post('/production-reports/sf001/stock/transfer', [SF001Controller::class, 'storeTransfer'])->name('production-reports.sf001.stock.transfer');
    Route::get('/production-reports/sf001/stock/{itemId}/history', [SF001Controller::class, 'stockHistory'])->name('production-reports.sf001.stock.history');
    Route::get('/production-reports/sf001/stock/export', [SF001Controller::class, 'exportStock'])->name('production-reports.sf001.stock.export');
    Route::prefix('production-reports/sf002')->name('production-reports.sf002.')->group(function () {
        Route::get('/stock', [SF002Controller::class, 'index'])->name('stock');
        Route::get('/process', [SF002Controller::class, 'process'])->name('process');
        Route::get('/process/export', [SF002Controller::class, 'exportProcess'])->name('process.export');
        Route::post('/stock/{transferId}/status', [SF002Controller::class, 'updateStatus'])->name('stock.status');
        Route::get('/production-report/{transferId}', [SF002Controller::class, 'productionReport'])->name('production-report');
        Route::post('/production-report/{transferId}', [SF002Controller::class, 'storeProductionReport'])->name('production-report.store');
        Route::get('/production/show/{encryptedId}', [SF002Controller::class, 'showProductionReport'])->name('production.show');
        Route::delete('/production/{id}', [SF002Controller::class, 'destroyProductionReport'])->name('production.destroy');
        Route::get('/sf2-stock', [SF002Controller::class, 'sf2Stock'])->name('sf2-stock');
        Route::post('/sf2-stock/transfer', [SF002Controller::class, 'storeSf2Transfer'])->name('sf2-stock.transfer');
        Route::post('/sf2-stock/self-transfer', [SF002Controller::class, 'storeSelfTransfer'])->name('sf2-stock.self-transfer');
        Route::get('/sf2-stock/{itemId}/history', [SF002Controller::class, 'sf2StockHistory'])->name('sf2-stock.history');
    });
    Route::prefix('production-reports/ppc')->name('production-reports.ppc.')->group(function () {
        Route::get('/process', [PPCController::class, 'process'])->name('process');
        Route::post('/process/{transferId}/status', [PPCController::class, 'updateStatus'])->name('process.status');
        Route::get('/stock', [PPCController::class, 'stock'])->name('stock');
        Route::post('/stock/transfer', [PPCController::class, 'storePpcTransfer'])->name('stock.transfer');
    });
    Route::prefix('production-reports/sf003')->name('production-reports.sf003.')->group(function () {
        Route::get('/stock', [SF003Controller::class, 'index'])->name('stock');
        Route::get('/process', [SF003Controller::class, 'process'])->name('process');
        Route::get('/final-stock', [SF003Controller::class, 'finalStock'])->name('final-stock');
        Route::get('/final-stock/{encryptedId}', [SF003Controller::class, 'finalStockShow'])->name('final-stock.show');
        Route::get('/production-report/{transferId?}', [SF003Controller::class, 'productionReport'])->name('production-report');
        Route::post('/production-report/{transferId?}', [SF003Controller::class, 'storeProductionReport'])->name('production-report.store');
        Route::post('/stock/{transferId}/status', [SF003Controller::class, 'updateStatus'])->name('stock.status');
        Route::get('/item-products', [SF003Controller::class, 'getItemProducts'])->name('item-products');
        Route::get('/item-products-stock', [SF003Controller::class, 'getItemProductsStock'])->name('item-products-stock');
    });
    Route::get('/production-reports/sf002', [ProductionReportController::class, 'sf002'])->name('production-reports.sf002');
    Route::get('/production-reports/sf003', [ProductionReportController::class, 'sf003'])->name('production-reports.sf003');
    Route::get('/production-reports/export', [ProductionReportController::class, 'export'])->name('production-reports.export');
    Route::get('/production-reports/create', [ProductionReportController::class, 'create'])->name('production-reports.create');
    Route::post('/production-reports/check-duplicate', [ProductionReportController::class, 'checkDuplicate'])->name('production-reports.check-duplicate');
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

