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
use App\Http\Controllers\VendorController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AlertController;

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
    Route::get('/ledger',       [LedgerController::class, 'index'])->name('ledger.index');
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
    
    // Alerts (Available to all authenticated users)
    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::post('/alerts/quick-reorder', [AlertController::class, 'quickReorder'])->name('alerts.quickReorder');

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

        // Vendors (read)
        Route::get('vendors',            [VendorController::class, 'index'])->name('vendors.index');
        Route::get('vendors/{vendor}',   [VendorController::class, 'show'])->name('vendors.show')->whereNumber('vendor');

        // Purchase Orders (read)
        Route::get('purchase-orders',                  [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
        Route::get('purchase-orders/{purchaseOrder}',  [PurchaseOrderController::class, 'show'])->name('purchase-orders.show')->whereNumber('purchaseOrder');

        // Barcodes & QR Codes (Phase 7)
        Route::get('products/{product}/labels',   [BarcodeController::class, 'labels'])->name('products.labels')->whereNumber('product');
        Route::get('products/{product}/barcode',  [BarcodeController::class, 'barcodePng'])->name('products.barcode')->whereNumber('product');
        Route::get('products/{product}/qrcode',   [BarcodeController::class, 'qrcodeSvg'])->name('products.qrcode')->whereNumber('product');

        // Excel / CSV Exports (Phase 8)
        Route::get('export/products',       [ExportController::class, 'products'])->name('export.products');
        Route::get('export/products/csv',   [ExportController::class, 'productsCsv'])->name('export.products.csv');
        Route::get('export/ledger',         [ExportController::class, 'ledger'])->name('export.ledger');
        Route::get('export/ledger/csv',     [ExportController::class, 'ledgerCsv'])->name('export.ledger.csv');
        Route::get('export/vendors',        [ExportController::class, 'vendors'])->name('export.vendors');
        Route::get('export/vendors/csv',    [ExportController::class, 'vendorsCsv'])->name('export.vendors.csv');
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

        // Vendors (create, edit)
        Route::get('vendors/create',           [VendorController::class, 'create'])->name('vendors.create');
        Route::post('vendors',                  [VendorController::class, 'store'])->name('vendors.store');
        Route::get('vendors/{vendor}/edit',    [VendorController::class, 'edit'])->name('vendors.edit');
        Route::put('vendors/{vendor}',          [VendorController::class, 'update'])->name('vendors.update');
        Route::patch('vendors/{vendor}',        [VendorController::class, 'update']);

        // Purchase Orders (create, edit, approve, cancel)
        Route::get('purchase-orders/create',                       [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
        Route::post('purchase-orders',                              [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
        Route::get('purchase-orders/{purchaseOrder}/edit',         [PurchaseOrderController::class, 'edit'])->name('purchase-orders.edit');
        Route::put('purchase-orders/{purchaseOrder}',              [PurchaseOrderController::class, 'update'])->name('purchase-orders.update');
        Route::patch('purchase-orders/{purchaseOrder}',            [PurchaseOrderController::class, 'update']);
        Route::post('purchase-orders/{purchaseOrder}/approve',     [PurchaseOrderController::class, 'approve'])->name('purchase-orders.approve');
        Route::post('purchase-orders/{purchaseOrder}/cancel',      [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');
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

        // Procurement destructive
        Route::delete('vendors/{vendor}',                      [VendorController::class, 'destroy'])->name('vendors.destroy');
        Route::delete('purchase-orders/{purchaseOrder}',       [PurchaseOrderController::class, 'destroy'])->name('purchase-orders.destroy');

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
