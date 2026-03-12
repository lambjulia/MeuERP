<?php

use App\Http\Controllers\AccountPayableController;
use App\Http\Controllers\AccountReceivableController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\SupplierController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Cadastros
    Route::resource('companies', CompanyController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('customers', CustomerController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('suppliers', SupplierController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('products', ProductController::class)->only(['index', 'store', 'update', 'destroy']);

    // Estoque
    Route::get('stock', [StockMovementController::class, 'index'])->name('stock.index');
    Route::post('stock', [StockMovementController::class, 'store'])->name('stock.store');

    // Compras
    Route::resource('purchases', PurchaseController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('purchases/{purchase}/confirm', [PurchaseController::class, 'confirm'])->name('purchases.confirm');
    Route::post('purchases/{purchase}/cancel', [PurchaseController::class, 'cancel'])->name('purchases.cancel');

    // Vendas
    Route::resource('sales', SaleController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('sales/{sale}/confirm', [SaleController::class, 'confirm'])->name('sales.confirm');
    Route::post('sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');

    // Financeiro
    Route::resource('accounts-payable', AccountPayableController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('accounts-payable/{accounts_payable}/pay', [AccountPayableController::class, 'markAsPaid'])->name('accounts-payable.pay');

    Route::resource('accounts-receivable', AccountReceivableController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::post('accounts-receivable/{accounts_receivable}/receive', [AccountReceivableController::class, 'markAsReceived'])->name('accounts-receivable.receive');
});
