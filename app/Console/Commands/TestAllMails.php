<?php

namespace App\Console\Commands;

use App\Mail\CargoNotificationMail;
use App\Mail\CargoStatusUpdateMail;
use App\Mail\OrderConfirmationMail;
use App\Mail\OrderStatusUpdateMail;
use App\Mail\PaymentSuccessMail;
use App\Mail\PriceDropNotificationMail;
use App\Mail\WelcomeMail;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\ProductPriceHistory;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestAllMails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:all-mails {email} {--type=all : Mail türü (all, welcome, order, payment, cargo, price-drop)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tüm mail türlerini test et';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $type = $this->option('type');
        
        $this->info("🧪 Mail testleri başlıyor...");
        $this->info("📧 Test email: {$email}");
        $this->info("📋 Test türü: {$type}");
        
        // Test kullanıcısı oluştur
        $user = $this->createTestUser($email);
        
        $successCount = 0;
        $totalCount = 0;
        
        if ($type === 'all' || $type === 'welcome') {
            $totalCount++;
            if ($this->testWelcomeMail($user)) {
                $successCount++;
            }
        }
        
        if ($type === 'all' || $type === 'order') {
            $totalCount++;
            if ($this->testOrderConfirmationMail($user)) {
                $successCount++;
            }
        }
        
        if ($type === 'all' || $type === 'payment') {
            $totalCount++;
            if ($this->testPaymentSuccessMail($user)) {
                $successCount++;
            }
        }
        
        if ($type === 'all' || $type === 'cargo') {
            $totalCount++;
            if ($this->testCargoNotificationMail($user)) {
                $successCount++;
            }
            
            $totalCount++;
            if ($this->testCargoStatusUpdateMail($user)) {
                $successCount++;
            }
        }
        
        if ($type === 'all' || $type === 'price-drop') {
            $totalCount++;
            if ($this->testPriceDropNotificationMail($user)) {
                $successCount++;
            }
        }
        
        // Sonuçları göster
        $this->info("\n📊 Test Sonuçları:");
        $this->line("   ✅ Başarılı: {$successCount}");
        $this->line("   ❌ Başarısız: " . ($totalCount - $successCount));
        $this->line("   📧 Toplam: {$totalCount}");
        
        if ($successCount === $totalCount) {
            $this->info("🎉 Tüm mail testleri başarılı!");
        } else {
            $this->warn("⚠️  Bazı mail testleri başarısız oldu.");
        }
    }
    
    /**
     * Test kullanıcısı oluştur
     */
    private function createTestUser($email)
    {
        $user = new User();
        $user->id = 999;
        $user->name = 'Test Kullanıcı';
        $user->email = $email;
        $user->phone = '0555 123 45 67';
        return $user;
    }
    
    /**
     * Hoş geldin maili test et
     */
    private function testWelcomeMail($user)
    {
        try {
            $this->info("📧 Welcome Mail test ediliyor...");
            Mail::to($user->email)->send(new WelcomeMail($user));
            $this->line("   ✅ Welcome Mail başarılı");
            return true;
        } catch (\Exception $e) {
            $this->error("   ❌ Welcome Mail hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Sipariş onay maili test et
     */
    private function testOrderConfirmationMail($user)
    {
        try {
            $this->info("📧 Order Confirmation Mail test ediliyor...");
            
            // Test siparişi oluştur
            $order = $this->createTestOrder($user);
            
            Mail::to($user->email)->send(new OrderConfirmationMail($order, $user));
            $this->line("   ✅ Order Confirmation Mail başarılı");
            return true;
        } catch (\Exception $e) {
            $this->error("   ❌ Order Confirmation Mail hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Ödeme başarı maili test et
     */
    private function testPaymentSuccessMail($user)
    {
        try {
            $this->info("📧 Payment Success Mail test ediliyor...");
            
            // Test siparişi ve ödeme oluştur
            $order = $this->createTestOrder($user);
            $payment = $this->createTestPayment($order);
            
            Mail::to($user->email)->send(new PaymentSuccessMail($order, $payment, $user));
            $this->line("   ✅ Payment Success Mail başarılı");
            return true;
        } catch (\Exception $e) {
            $this->error("   ❌ Payment Success Mail hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Kargo bildirim maili test et
     */
    private function testCargoNotificationMail($user)
    {
        try {
            $this->info("📧 Cargo Notification Mail test ediliyor...");
            
            // Test siparişi ve kargo takibi oluştur
            $order = $this->createTestOrder($user);
            $cargoTracking = $this->createTestCargoTracking($order);
            
            Mail::to($user->email)->send(new CargoNotificationMail($order, $cargoTracking));
            $this->line("   ✅ Cargo Notification Mail başarılı");
            return true;
        } catch (\Exception $e) {
            $this->error("   ❌ Cargo Notification Mail hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Kargo durum güncelleme maili test et
     */
    private function testCargoStatusUpdateMail($user)
    {
        try {
            $this->info("📧 Cargo Status Update Mail test ediliyor...");
            
            // Test siparişi ve kargo takibi oluştur
            $order = $this->createTestOrder($user);
            $cargoTracking = $this->createTestCargoTracking($order);
            
            Mail::to($user->email)->send(new CargoStatusUpdateMail($order, $cargoTracking, 'created', 'in_transit'));
            $this->line("   ✅ Cargo Status Update Mail başarılı");
            return true;
        } catch (\Exception $e) {
            $this->error("   ❌ Cargo Status Update Mail hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Fiyat düşüşü bildirimi test et
     */
    private function testPriceDropNotificationMail($user)
    {
        try {
            $this->info("📧 Price Drop Notification Mail test ediliyor...");
            
            // Test ürünü ve fiyat geçmişi oluştur
            $product = $this->createTestProduct();
            $priceHistory = $this->createTestPriceHistory($product);
            
            Mail::to($user->email)->send(new PriceDropNotificationMail($user, $product, $priceHistory));
            $this->line("   ✅ Price Drop Notification Mail başarılı");
            return true;
        } catch (\Exception $e) {
            $this->error("   ❌ Price Drop Notification Mail hatası: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Test siparişi oluştur
     */
    private function createTestOrder($user)
    {
        $order = new Order();
        $order->id = 999;
        $order->user_id = $user->id;
        $order->order_number = 'TEST-999';
        $order->total_amount = 150.00;
        $order->status = 'confirmed';
        $order->shipping_address = 'Test Adres, Test Mahallesi, Test Şehir';
        $order->billing_address = 'Test Fatura Adresi, Test Mahallesi, Test Şehir';
        $order->created_at = now();
        
        // Test sipariş kalemleri
        $orderItem = new OrderItem();
        $orderItem->id = 999;
        $orderItem->order_id = $order->id;
        $orderItem->product_kod = 'TEST001';
        $orderItem->product_name = 'Test Ürün';
        $orderItem->quantity = 2;
        $orderItem->price = 75.00;
        $orderItem->total = 150.00;
        
        // Test ürünü oluştur ve ilişkilendir
        $product = new Product();
        $product->id = 999;
        $product->kod = 'TEST001';
        $product->ad = 'Test Ürün';
        $product->name = 'Test Ürün'; // Template için
        $orderItem->product = $product;
        
        $order->items = collect([$orderItem]);
        $order->currency = 'TL';
        $order->shipping_name = 'Test Kullanıcı';
        $order->shipping_address = 'Test Adres, Test Mahallesi, Test Şehir';
        $order->shipping_city = 'Test Şehir';
        $order->shipping_postal_code = '34000';
        $order->shipping_phone = '0555 123 45 67';
        
        return $order;
    }
    
    /**
     * Test ödeme oluştur
     */
    private function createTestPayment($order)
    {
        $payment = new PaymentTransaction();
        $payment->id = 999;
        $payment->order_id = $order->id;
        $payment->transaction_id = 'TEST-TXN-999';
        $payment->amount = $order->total_amount;
        $payment->status = 'completed';
        $payment->payment_method = 'credit_card';
        $payment->provider = 'test_provider';
        $payment->created_at = now();
        
        return $payment;
    }
    
    /**
     * Test ürünü oluştur
     */
    private function createTestProduct()
    {
        $product = new Product();
        $product->id = 999;
        $product->kod = 'TEST001';
        $product->ad = 'Test Ürün';
        $product->marka = 'Test Marka';
        $product->kategori = 'Test Kategori';
        $product->doviz = 'TL';
        $product->aciklama = 'Bu bir test ürünüdür.';
        $product->fiyat_ozel = 80.00;
        
        return $product;
    }
    
    /**
     * Test fiyat geçmişi oluştur
     */
    private function createTestPriceHistory($product)
    {
        $priceHistory = new ProductPriceHistory();
        $priceHistory->id = 999;
        $priceHistory->product_kod = $product->kod;
        $priceHistory->old_price_ozel = 100.00;
        $priceHistory->new_price_ozel = 80.00;
        $priceHistory->price_difference = -20.00;
        $priceHistory->discount_percentage = 20.00;
        $priceHistory->is_discount = true;
        $priceHistory->changed_at = now();
        
        return $priceHistory;
    }
    
    /**
     * Test kargo takibi oluştur
     */
    private function createTestCargoTracking($order)
    {
        $cargoTracking = new \App\Models\CargoTracking();
        $cargoTracking->id = 999;
        $cargoTracking->order_id = $order->id;
        $cargoTracking->tracking_number = 'TEST-TRACK-999';
        $cargoTracking->cargo_company = 'Test Kargo';
        $cargoTracking->status = 'shipped';
        $cargoTracking->shipped_at = now();
        $cargoTracking->estimated_delivery = now()->addDays(2);
        
        return $cargoTracking;
    }
}
