<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Admin\XmlImportController;
use App\Http\Controllers\Admin\XMLImportController as NewXMLImportController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\MarketplaceController;
use App\Http\Controllers\Admin\XmlHistoryController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\PaymentProviderController;
use App\Http\Controllers\Admin\PaymentTransactionController;
use App\Http\Controllers\Admin\CargoCompanyController;
use App\Http\Controllers\Admin\CargoTrackingController;
use App\Http\Controllers\Customer\CargoTrackingController as CustomerCargoTrackingController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\CartCouponController;

// Ana sayfa
Route::get('/', [HomeController::class, 'index'])->name('home');

// Ürün sayfaları
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/product/{kod}', [ProductController::class, 'show'])->name('products.show');
Route::get('/api/products', [ProductController::class, 'api'])->name('products.api');

// Sepet işlemleri
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/count', [CartController::class, 'getCount'])->name('cart.count');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Kupon işlemleri
Route::prefix('cart/coupon')->name('cart.coupon.')->group(function () {
    Route::post('/apply', [CartCouponController::class, 'apply'])->name('apply');
    Route::post('/remove', [CartCouponController::class, 'remove'])->name('remove');
    Route::get('/applied', [CartCouponController::class, 'getAppliedCoupon'])->name('applied');
    Route::post('/validate', [CartCouponController::class, 'validateCoupon'])->name('validate');
});

// Checkout işlemleri
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    
    // Sipariş işlemleri
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::post('/orders/{order}/delivered', [OrderController::class, 'markAsDelivered'])->name('orders.delivered');
});

// Payment işlemleri
Route::prefix('payment')->name('payment.')->group(function () {
    // Public payment routes
    Route::post('/initiate', [PaymentController::class, 'initiate'])->name('initiate');
    Route::get('/providers', [PaymentController::class, 'getProviders'])->name('providers');
    Route::get('/success', [PaymentController::class, 'success'])->name('success');
    Route::get('/failure', [PaymentController::class, 'failure'])->name('failure');
    Route::get('/cancel', [PaymentController::class, 'cancel'])->name('cancel');
    
    // Callback ve webhook routes
    Route::post('/callback/{provider}', [PaymentController::class, 'callback'])->name('callback');
    Route::post('/webhook/{provider}', [PaymentController::class, 'webhook'])->name('webhook');
    
    // Status check (authenticated)
    Route::middleware('auth')->group(function () {
        Route::post('/check-status', [PaymentController::class, 'checkStatus'])->name('check-status');
    });
});

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
    
    // Kargo takip
    Route::get('/cargo-tracking', [CustomerCargoTrackingController::class, 'myOrders'])->name('cargo-tracking.orders');
    Route::get('/cargo-tracking/order/{orderId}', [CustomerCargoTrackingController::class, 'orderTracking'])->name('cargo-tracking.order');
});

// Favoriler (sadece giriş yapmış kullanıcılar)
Route::prefix('favorites')->name('favorites.')->middleware('auth')->group(function () {
    Route::get('/', [FavoriteController::class, 'index'])->name('index');
    Route::post('/add', [FavoriteController::class, 'add'])->name('add');
    Route::post('/remove', [FavoriteController::class, 'remove'])->name('remove');
    Route::post('/toggle', [FavoriteController::class, 'toggle'])->name('toggle');
    Route::post('/check', [FavoriteController::class, 'check'])->name('check');
    Route::get('/count', [FavoriteController::class, 'count'])->name('count');
});

// Kargo takip (public)
Route::get('/cargo-tracking', [CustomerCargoTrackingController::class, 'track'])->name('cargo-tracking.track');

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
    Route::post('/xml/import', [XmlImportController::class, 'import'])->name('xml.import');
    Route::get('/xml/export', [XmlImportController::class, 'export'])->name('xml.export');
    
    // XML Import Geçmişi
    Route::get('/xml-history', [XmlHistoryController::class, 'index'])->name('xml-history');
    Route::get('/xml-history/{xmlHistory}', [XmlHistoryController::class, 'show'])->name('xml-history.show');
    Route::post('/xml-history/manual-import', [XmlHistoryController::class, 'runManualImport'])->name('xml-history.manual-import');
    Route::delete('/xml-history/{xmlHistory}', [XmlHistoryController::class, 'destroy'])->name('xml-history.destroy');
    Route::get('/xml-history/{xmlHistory}/download', [XmlHistoryController::class, 'download'])->name('xml-history.download');
    Route::get('/xml-history-stats', [XmlHistoryController::class, 'stats'])->name('xml-history.stats');
    Route::get('/xml-history-error-analysis', [XmlHistoryController::class, 'errorAnalysis'])->name('xml-history.error-analysis');
    
    // Pazaryeri Entegrasyonu
    Route::post('/marketplace/hepsiburada/test', [MarketplaceController::class, 'testHepsiburadaConnection'])->name('marketplace.hepsiburada.test');
    Route::post('/marketplace/trendyol/test', [MarketplaceController::class, 'testTrendyolConnection'])->name('marketplace.trendyol.test');
    Route::post('/marketplace/hepsiburada/sync', [MarketplaceController::class, 'syncToHepsiburada'])->name('marketplace.hepsiburada.sync');
    Route::post('/marketplace/trendyol/sync', [MarketplaceController::class, 'syncToTrendyol'])->name('marketplace.trendyol.sync');
    Route::post('/marketplace/sync-settings', [MarketplaceController::class, 'saveSyncSettings'])->name('marketplace.sync-settings');
    
    // Payment Provider Yönetimi
    Route::resource('payment-providers', PaymentProviderController::class);
    Route::post('/payment-providers/{paymentProvider}/toggle-status', [PaymentProviderController::class, 'toggleStatus'])->name('payment-providers.toggle-status');
    Route::get('/payment-providers/{paymentProvider}/config', [PaymentProviderController::class, 'config'])->name('payment-providers.config');
    Route::put('/payment-providers/{paymentProvider}/config', [PaymentProviderController::class, 'updateConfig'])->name('payment-providers.update-config');
    Route::post('/payment-providers/{paymentProvider}/test-connection', [PaymentProviderController::class, 'testConnection'])->name('payment-providers.test-connection');
    
    // Payment Transaction Yönetimi
    Route::get('/payment-transactions', [PaymentTransactionController::class, 'index'])->name('payment-transactions.index');
    Route::get('/payment-transactions/{paymentTransaction}', [PaymentTransactionController::class, 'show'])->name('payment-transactions.show');
    Route::post('/payment-transactions/{paymentTransaction}/check-status', [PaymentTransactionController::class, 'checkStatus'])->name('payment-transactions.check-status');
    Route::post('/payment-transactions/{paymentTransaction}/refund', [PaymentTransactionController::class, 'refund'])->name('payment-transactions.refund');
    Route::post('/payment-transactions/{paymentTransaction}/cancel', [PaymentTransactionController::class, 'cancel'])->name('payment-transactions.cancel');
    Route::get('/payment-transactions/statistics', [PaymentTransactionController::class, 'statistics'])->name('payment-transactions.statistics');
    Route::get('/payment-transactions/export', [PaymentTransactionController::class, 'export'])->name('payment-transactions.export');
    
    // Kargo Şirketi Yönetimi
    Route::resource('cargo-companies', CargoCompanyController::class);
    Route::post('/cargo-companies/{cargoCompany}/toggle-status', [CargoCompanyController::class, 'toggleStatus'])->name('cargo-companies.toggle-status');
    
    // Kargo Takip Yönetimi
    Route::resource('cargo-trackings', CargoTrackingController::class);
    Route::get('/cargo-trackings/order/{orderId}', [CargoTrackingController::class, 'orderTracking'])->name('cargo-trackings.order');
    Route::get('/cargo-trackings/search', [CargoTrackingController::class, 'search'])->name('cargo-trackings.search');
    
    // Kupon Yönetimi
    Route::resource('coupons', CouponController::class);
    Route::get('/coupons/{coupon}/usage-history', [CouponController::class, 'usageHistory'])->name('coupons.usage-history');
    Route::post('/coupons/{coupon}/toggle-status', [CouponController::class, 'toggleStatus'])->name('coupons.toggle-status');
    Route::post('/coupons/generate-code', [CouponController::class, 'generateCode'])->name('coupons.generate-code');
});
