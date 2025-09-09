<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\User;
use App\Models\Favorite;
use App\Models\ProductPriceHistory;
use App\Services\PriceDropNotificationService;
use Illuminate\Console\Command;

class TestFavoriteMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:favorite-mail {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Favori ürün için mail test et';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("🧪 Favori Ürün Mail Testi");
        $this->info("📧 Test email: {$email}");
        
        // Test kullanıcısı oluştur veya bul
        $user = User::where('email', $email)->first();
        if (!$user) {
            $user = new User();
            $user->name = 'Test Kullanıcı';
            $user->email = $email;
            $user->phone = '0555 123 45 67';
            $user->save();
            $this->line("   ✅ Test kullanıcısı oluşturuldu");
        } else {
            $this->line("   ✅ Mevcut kullanıcı bulundu: {$user->name}");
        }
        
        // Test ürünü oluştur
        $product = Product::where('kod', 'TEST002')->first();
        if (!$product) {
            $product = new Product();
            $product->kod = 'TEST002';
            $product->ad = 'Test Favori Ürün';
            $product->marka = 'Test Marka';
            $product->kategori = 'Test Kategori';
            $product->doviz = 'TL';
            $product->fiyat_ozel = 100.00;
            $product->save();
            $this->line("   ✅ Test ürünü oluşturuldu: {$product->kod}");
        } else {
            $this->line("   ✅ Mevcut ürün bulundu: {$product->ad}");
        }
        
        // Favori ekle
        $favorite = Favorite::where('user_id', $user->id)
                           ->where('product_kod', $product->kod)
                           ->first();
        
        if (!$favorite) {
            $favorite = new Favorite();
            $favorite->user_id = $user->id;
            $favorite->product_kod = $product->kod;
            $favorite->save();
            $this->line("   ✅ Ürün favorilere eklendi");
        } else {
            $this->line("   ✅ Ürün zaten favorilerde");
        }
        
        // Fiyat düşür
        $oldPrice = $product->fiyat_ozel;
        $newPrice = 80.00; // %20 indirim
        
        $this->info("💰 Fiyat düşürülüyor...");
        $this->line("   Eski fiyat: {$oldPrice} TL");
        $this->line("   Yeni fiyat: {$newPrice} TL");
        $this->line("   İndirim: %" . round((($oldPrice - $newPrice) / $oldPrice) * 100, 2));
        
        // Fiyat geçmişi oluştur
        $priceHistory = new ProductPriceHistory();
        $priceHistory->product_kod = $product->kod;
        $priceHistory->old_price_ozel = $oldPrice;
        $priceHistory->new_price_ozel = $newPrice;
        $priceHistory->price_difference = $newPrice - $oldPrice;
        $priceHistory->discount_percentage = round((($oldPrice - $newPrice) / $oldPrice) * 100, 2);
        $priceHistory->is_discount = true;
        $priceHistory->changed_at = now();
        $priceHistory->save();
        
        $this->line("   ✅ Fiyat geçmişi kaydedildi");
        
        // Ürün fiyatını güncelle
        $product->fiyat_ozel = $newPrice;
        $product->save();
        
        $this->line("   ✅ Ürün fiyatı güncellendi");
        
        // Mail gönder
        $this->info("📧 Mail gönderiliyor...");
        
        try {
            $priceDropService = app(PriceDropNotificationService::class);
            $priceDropService->sendBulkPriceDropNotifications();
            
            $this->line("   ✅ Mail başarıyla gönderildi!");
            
            // Sonuçları göster
            $this->info("\n📊 Test Sonuçları:");
            $this->line("   👤 Kullanıcı: {$user->name} ({$user->email})");
            $this->line("   📦 Ürün: {$product->ad} ({$product->kod})");
            $this->line("   💰 Eski Fiyat: {$oldPrice} TL");
            $this->line("   💰 Yeni Fiyat: {$newPrice} TL");
            $this->line("   📉 İndirim: %" . round((($oldPrice - $newPrice) / $oldPrice) * 100, 2));
            $this->line("   📧 Mail: Gönderildi");
            
            $this->info("\n🎉 Favori ürün mail testi başarılı!");
            
        } catch (\Exception $e) {
            $this->error("   ❌ Mail gönderim hatası: " . $e->getMessage());
        }
    }
}
