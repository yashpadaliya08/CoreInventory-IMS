<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\TransferController;
use App\Http\Controllers\AdjustmentController;
use App\Http\Controllers\LedgerController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserManagementController;

/*
|--------------------------------------------------------------------------
| Authentication Routes (Guest only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login'])->name('login.post');
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::get('/otp/request',  [AuthController::class, 'showOtpRequestForm'])->name('otp.request.form');
    Route::post('/otp/request', [AuthController::class, 'requestOtp'])->name('otp.request');
    Route::get('/otp/verify',   [AuthController::class, 'showOtpVerifyForm'])->name('otp.verify.form');
    Route::post('/otp/verify',  [AuthController::class, 'verifyOtp'])->name('otp.verify');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|
| Role Hierarchy:
|   admin   → Full access (CRUD + Settings + User Management)
|   manager → Create, edit, validate documents — no delete, no settings
|   staff   → View/read only + draft creation
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', fn () => view('profile.index'))->name('profile.index');

    /*
    |----------------------------------------------------------------------
    | Universal (All Roles) — Dashboard + Ledger (read-only)
    |----------------------------------------------------------------------
    */
    Route::get('/',          [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/ledger',    [LedgerController::class, 'index'])->name('ledger.index');

    /*
    |----------------------------------------------------------------------
    | Read-Only Routes (staff | manager | admin)
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin,manager,staff')->group(function () {
        Route::get('products',              [ProductController::class, 'index'])->name('products.index');
        Route::get('products/{product}',    [ProductController::class, 'show'])->name('products.show')->whereNumber('product');

        Route::get('receipts',              [ReceiptController::class, 'index'])->name('receipts.index');
        Route::get('receipts/{receipt}',    [ReceiptController::class, 'show'])->name('receipts.show')->whereNumber('receipt');
        Route::get('receipts/{receipt}/pdf', [ReceiptController::class, 'downloadPdf'])->name('receipts.pdf')->whereNumber('receipt');

        Route::get('deliveries',              [DeliveryController::class, 'index'])->name('deliveries.index');
        Route::get('deliveries/{delivery}',   [DeliveryController::class, 'show'])->name('deliveries.show')->whereNumber('delivery');
        Route::get('deliveries/{delivery}/pdf', [DeliveryController::class, 'downloadPdf'])->name('deliveries.pdf')->whereNumber('delivery');

        Route::get('transfers',             [TransferController::class, 'index'])->name('transfers.index');
        Route::get('transfers/{transfer}',  [TransferController::class, 'show'])->name('transfers.show')->whereNumber('transfer');

        Route::get('adjustments',               [AdjustmentController::class, 'index'])->name('adjustments.index');
        Route::get('adjustments/{adjustment}',  [AdjustmentController::class, 'show'])->name('adjustments.show')->whereNumber('adjustment');
    });

    /*
    |----------------------------------------------------------------------
    | Manager Routes (manager | admin) — Create, Edit, Validate
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin,manager')->group(function () {
        // Products
        Route::get('products/create',            [ProductController::class, 'create'])->name('products.create');
        Route::post('products',                   [ProductController::class, 'store'])->name('products.store');
        Route::get('products/{product}/edit',    [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}',          [ProductController::class, 'update'])->name('products.update');
        Route::patch('products/{product}',        [ProductController::class, 'update']);

        // Receipts
        Route::get('receipts/create',            [ReceiptController::class, 'create'])->name('receipts.create');
        Route::post('receipts',                   [ReceiptController::class, 'store'])->name('receipts.store');
        Route::get('receipts/{receipt}/edit',    [ReceiptController::class, 'edit'])->name('receipts.edit');
        Route::put('receipts/{receipt}',          [ReceiptController::class, 'update'])->name('receipts.update');
        Route::patch('receipts/{receipt}',        [ReceiptController::class, 'update']);
        Route::post('receipts/{receipt}/validate', [ReceiptController::class, 'validateReceipt'])->name('receipts.validate');

        // Deliveries
        Route::get('deliveries/create',             [DeliveryController::class, 'create'])->name('deliveries.create');
        Route::post('deliveries',                    [DeliveryController::class, 'store'])->name('deliveries.store');
        Route::get('deliveries/{delivery}/edit',    [DeliveryController::class, 'edit'])->name('deliveries.edit');
        Route::put('deliveries/{delivery}',          [DeliveryController::class, 'update'])->name('deliveries.update');
        Route::patch('deliveries/{delivery}',        [DeliveryController::class, 'update']);
        Route::post('deliveries/{delivery}/validate', [DeliveryController::class, 'validateDelivery'])->name('deliveries.validate');

        // Transfers
        Route::get('transfers/create',           [TransferController::class, 'create'])->name('transfers.create');
        Route::post('transfers',                  [TransferController::class, 'store'])->name('transfers.store');
        Route::post('transfers/{transfer}/validate', [TransferController::class, 'validateTransfer'])->name('transfers.validate');

        // Adjustments
        Route::get('adjustments/create',             [AdjustmentController::class, 'create'])->name('adjustments.create');
        Route::post('adjustments',                    [AdjustmentController::class, 'store'])->name('adjustments.store');
        Route::post('adjustments/{adjustment}/validate', [AdjustmentController::class, 'validateAdjustment'])->name('adjustments.validate');
    });

    /*
    |----------------------------------------------------------------------
    | Admin-Only Routes — Delete, Settings, User Management
    |----------------------------------------------------------------------
    */
    Route::middleware('role:admin')->group(function () {
        // Destructive operations
        Route::delete('products/{product}',     [ProductController::class, 'destroy'])->name('products.destroy');
        Route::delete('receipts/{receipt}',     [ReceiptController::class, 'destroy'])->name('receipts.destroy');
        Route::delete('deliveries/{delivery}',  [DeliveryController::class, 'destroy'])->name('deliveries.destroy');
        Route::delete('transfers/{transfer}',   [TransferController::class, 'destroy'])->name('transfers.destroy');
        Route::delete('adjustments/{adjustment}', [AdjustmentController::class, 'destroy'])->name('adjustments.destroy');

        // Infrastructure settings
        Route::get('/settings',                          [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings/warehouse',               [SettingController::class, 'storeWarehouse'])->name('settings.warehouse.store');
        Route::get('/settings/warehouse/{warehouse}/edit', [SettingController::class, 'editWarehouse'])->name('settings.warehouse.edit');
        Route::put('/settings/warehouse/{warehouse}',    [SettingController::class, 'updateWarehouse'])->name('settings.warehouse.update');
        Route::delete('/settings/warehouse/{warehouse}', [SettingController::class, 'destroyWarehouse'])->name('settings.warehouse.destroy');
        Route::post('/settings/location',                [SettingController::class, 'storeLocation'])->name('settings.location.store');
        Route::delete('/settings/location/{location}',  [SettingController::class, 'destroyLocation'])->name('settings.location.destroy');

        // Company profile
        Route::post('/settings/company',                 [SettingController::class, 'updateCompany'])->name('settings.company.update');

        // Product categories
        Route::post('/settings/category',                [SettingController::class, 'storeCategory'])->name('settings.category.store');
        Route::delete('/settings/category/{category}',   [SettingController::class, 'destroyCategory'])->name('settings.category.destroy');

        // User management panel
        Route::resource('users', UserManagementController::class)->only(['index', 'edit', 'update', 'destroy']);
    });
});
