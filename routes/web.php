<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\XmlImportController;
use App\Http\Controllers\Admin\XMLImportController as NewXMLImportController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\MarketplaceController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Customer\CustomerController;

// Ana sayfa
Route::get('/', [HomeController::class, 'index'])->name('home');

// Ürün sayfaları
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{kod}', [ProductController::class, 'show'])->name('products.show');
Route::get('/api/products', [ProductController::class, 'api'])->name('products.api');

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Müşteri paneli
Route::prefix('customer')->name('customer.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [CustomerController::class, 'profile'])->name('profile');
    Route::put('/profile', [CustomerController::class, 'updateProfile'])->name('profile.update');
    Route::get('/orders', [CustomerController::class, 'orders'])->name('orders');
});

// Admin paneli - sadece admin kullanıcılar
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    
    // Ürün yönetimi
    Route::post('/products', [AdminProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
    
    // XML İçe Aktarma
    Route::get('/xml-import', [XmlImportController::class, 'index'])->name('xml-import');
    Route::post('/xml-import', [XmlImportController::class, 'import'])->name('xml-import.upload');
    Route::post('/xml-import/liste', [XmlImportController::class, 'importListeXml'])->name('xml-import.liste');
    Route::post('/stock-control', [XmlImportController::class, 'stockControl'])->name('stock-control');
    
    // Yeni XML İçe Aktarma
    Route::post('/xml/import', [NewXMLImportController::class, 'import'])->name('xml.import');
    Route::get('/xml/export', [NewXMLImportController::class, 'export'])->name('xml.export');
    
    // Pazaryeri Entegrasyonu
    Route::post('/marketplace/hepsiburada/test', [MarketplaceController::class, 'testHepsiburadaConnection'])->name('marketplace.hepsiburada.test');
    Route::post('/marketplace/trendyol/test', [MarketplaceController::class, 'testTrendyolConnection'])->name('marketplace.trendyol.test');
    Route::post('/marketplace/hepsiburada/sync', [MarketplaceController::class, 'syncToHepsiburada'])->name('marketplace.hepsiburada.sync');
    Route::post('/marketplace/trendyol/sync', [MarketplaceController::class, 'syncToTrendyol'])->name('marketplace.trendyol.sync');
    Route::post('/marketplace/sync-settings', [MarketplaceController::class, 'saveSyncSettings'])->name('marketplace.sync-settings');
});
