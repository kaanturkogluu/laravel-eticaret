<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\User;
use App\Models\Favorite;
use Illuminate\Console\Command;

class TestManualPriceDrop extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:manual-price-drop {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manuel fiyat düşürme testi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("🧪 Manuel Fiyat Düşürme Testi");
        $this->info("📧 Test email: {$email}");
        
        // Test kullanıcısı bul
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("❌ Kullanıcı bulunamadı: {$email}");
            return;
        }
        
        $this->line("   ✅ Kullanıcı bulundu: {$user->name}");
        
        // Test ürünü bul
        $product = Product::where('kod', 'TEST002')->first();
        if (!$product) {
            $this->error("❌ Test ürünü bulunamadı: TEST002");
            return;
        }
        
        $this->line("   ✅ Ürün bulundu: {$product->ad}");
        
        // Favori kontrolü
        $favorite = Favorite::where('user_id', $user->id)
                           ->where('product_kod', $product->kod)
                           ->first();
        
        if (!$favorite) {
            $this->error("❌ Ürün favorilerde değil");
            return;
        }
        
        $this->line("   ✅ Ürün favorilerde");
        
        // Mevcut fiyat
        $currentPrice = $product->fiyat_ozel;
        $newPrice = $currentPrice * 0.8; // %20 daha indirim
        
        $this->info("💰 Fiyat düşürülüyor...");
        $this->line("   Mevcut fiyat: {$currentPrice} TL");
        $this->line("   Yeni fiyat: {$newPrice} TL");
        $this->line("   İndirim: %" . round((($currentPrice - $newPrice) / $currentPrice) * 100, 2));
        
        // updateWithPriceCheck metodunu kullan
        $this->info("📧 updateWithPriceCheck metodu kullanılıyor...");
        
        try {
            $product->updateWithPriceCheck([
                'fiyat_ozel' => $newPrice
            ]);
            
            $this->line("   ✅ Fiyat güncellendi ve mail gönderildi!");
            
            // Sonuçları göster
            $this->info("\n📊 Test Sonuçları:");
            $this->line("   👤 Kullanıcı: {$user->name} ({$user->email})");
            $this->line("   📦 Ürün: {$product->ad} ({$product->kod})");
            $this->line("   💰 Eski Fiyat: {$currentPrice} TL");
            $this->line("   💰 Yeni Fiyat: {$newPrice} TL");
            $this->line("   📉 İndirim: %" . round((($currentPrice - $newPrice) / $currentPrice) * 100, 2));
            $this->line("   📧 Mail: Otomatik gönderildi");
            
            $this->info("\n🎉 Manuel fiyat düşürme testi başarılı!");
            
        } catch (\Exception $e) {
            $this->error("   ❌ Hata: " . $e->getMessage());
        }
    }
}
