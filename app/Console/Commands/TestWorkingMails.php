<?php

namespace App\Console\Commands;

use App\Mail\WelcomeMail;
use App\Mail\PriceDropNotificationMail;
use App\Models\Product;
use App\Models\ProductPriceHistory;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestWorkingMails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:working-mails {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Çalışan mail türlerini test et';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("🧪 Çalışan Mail Testleri");
        $this->info("📧 Test email: {$email}");
        
        // Test kullanıcısı oluştur
        $user = new User();
        $user->id = 999;
        $user->name = 'Test Kullanıcı';
        $user->email = $email;
        
        $successCount = 0;
        $totalCount = 0;
        
        // 1. Welcome Mail Test
        $totalCount++;
        try {
            $this->info("📧 Welcome Mail test ediliyor...");
            Mail::to($email)->send(new WelcomeMail($user));
            $this->line("   ✅ Welcome Mail başarılı");
            $successCount++;
        } catch (\Exception $e) {
            $this->error("   ❌ Welcome Mail hatası: " . $e->getMessage());
        }
        
        // 2. Price Drop Notification Mail Test
        $totalCount++;
        try {
            $this->info("📧 Price Drop Notification Mail test ediliyor...");
            
            // Test ürünü oluştur
            $product = new Product();
            $product->id = 999;
            $product->kod = 'TEST001';
            $product->ad = 'Test Ürün';
            $product->marka = 'Test Marka';
            $product->kategori = 'Test Kategori';
            $product->doviz = 'TL';
            $product->aciklama = 'Bu bir test ürünüdür.';
            
            // Test fiyat geçmişi oluştur
            $priceHistory = new ProductPriceHistory();
            $priceHistory->id = 999;
            $priceHistory->product_kod = 'TEST001';
            $priceHistory->old_price_ozel = 100.00;
            $priceHistory->new_price_ozel = 80.00;
            $priceHistory->price_difference = -20.00;
            $priceHistory->discount_percentage = 20.00;
            $priceHistory->is_discount = true;
            $priceHistory->changed_at = now();
            
            Mail::to($email)->send(new PriceDropNotificationMail($user, $product, $priceHistory));
            $this->line("   ✅ Price Drop Notification Mail başarılı");
            $successCount++;
        } catch (\Exception $e) {
            $this->error("   ❌ Price Drop Notification Mail hatası: " . $e->getMessage());
        }
        
        // Sonuçları göster
        $this->info("\n📊 Test Sonuçları:");
        $this->line("   ✅ Başarılı: {$successCount}");
        $this->line("   ❌ Başarısız: " . ($totalCount - $successCount));
        $this->line("   📧 Toplam: {$totalCount}");
        
        if ($successCount === $totalCount) {
            $this->info("🎉 Tüm çalışan mail testleri başarılı!");
        } else {
            $this->warn("⚠️  Bazı mail testleri başarısız oldu.");
        }
        
        $this->info("\n📋 Mail Türleri:");
        $this->line("   ✅ Welcome Mail - Hoş geldin maili");
        $this->line("   ✅ Price Drop Notification - Fiyat düşüşü bildirimi");
        $this->line("   ⚠️  Order Confirmation - Sipariş onay maili (template sorunu)");
        $this->line("   ⚠️  Payment Success - Ödeme başarı maili (template sorunu)");
        $this->line("   ⚠️  Cargo Notification - Kargo bildirim maili (template sorunu)");
        $this->line("   ⚠️  Cargo Status Update - Kargo durum güncelleme (template sorunu)");
    }
}
