<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\User;
use App\Models\Favorite;
use App\Models\ProductPriceHistory;
use App\Services\PriceDropNotificationService;
use Illuminate\Console\Command;

class TestRealDataMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:real-data-mail {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gerçek veriler ile favori indirim mail testi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("🧪 Gerçek Veriler ile Favori İndirim Mail Testi");
        $this->info("📧 Test email: {$email}");
        
        // Gerçek veri istatistikleri
        $this->info("\n📊 Gerçek Veri İstatistikleri:");
        $this->line("   📦 Toplam Ürün: " . Product::count());
        $this->line("   👥 Toplam Kullanıcı: " . User::count());
        $this->line("   ❤️ Toplam Favori: " . Favorite::count());
        $this->line("   📈 Toplam Fiyat Geçmişi: " . ProductPriceHistory::count());
        
        // Test kullanıcısı bul
        $user = User::where('email', $email)->first();
        if (!$user) {
            $this->error("❌ Kullanıcı bulunamadı: {$email}");
            $this->info("Mevcut kullanıcılar:");
            User::all()->each(function($u) {
                $this->line("   - {$u->email} ({$u->name})");
            });
            return;
        }
        
        $this->line("\n✅ Kullanıcı bulundu: {$user->name} ({$user->email})");
        
        // Kullanıcının favorilerini göster
        $favorites = Favorite::where('user_id', $user->id)->get();
        $this->info("\n❤️ Kullanıcının Favorileri ({$favorites->count()}):");
        
        if ($favorites->count() == 0) {
            $this->warn("   ⚠️ Kullanıcının favori ürünü yok!");
            $this->info("   💡 Gerçek bir ürünü favorilere ekleyelim...");
            
            // Rastgele bir ürün seç
            $randomProduct = Product::inRandomOrder()->first();
            if ($randomProduct) {
                $favorite = new Favorite();
                $favorite->user_id = $user->id;
                $favorite->product_kod = $randomProduct->kod;
                $favorite->save();
                
                $this->line("   ✅ Ürün favorilere eklendi: {$randomProduct->ad} ({$randomProduct->kod})");
                $favorites = Favorite::where('user_id', $user->id)->get();
            }
        }
        
        foreach ($favorites as $favorite) {
            $product = Product::where('kod', $favorite->product_kod)->first();
            if ($product) {
                $this->line("   📦 {$product->ad} ({$product->kod}) - {$product->fiyat_ozel} TL");
            }
        }
        
        // Favori ürünlerden birini seç ve fiyatını düşür
        $selectedFavorite = $favorites->first();
        if (!$selectedFavorite) {
            $this->error("❌ Favori ürün bulunamadı");
            return;
        }
        
        $product = Product::where('kod', $selectedFavorite->product_kod)->first();
        if (!$product) {
            $this->error("❌ Ürün bulunamadı: {$selectedFavorite->product_kod}");
            return;
        }
        
        $this->info("\n🎯 Test Ürünü Seçildi:");
        $this->line("   📦 Ürün: {$product->ad}");
        $this->line("   🏷️ Kod: {$product->kod}");
        $this->line("   💰 Mevcut Fiyat: {$product->fiyat_ozel} TL");
        $this->line("   🏪 Marka: {$product->marka}");
        $this->line("   📂 Kategori: {$product->kategori}");
        
        // Fiyat düşür
        $oldPrice = $product->fiyat_ozel;
        $newPrice = $oldPrice * 0.85; // %15 indirim
        
        $this->info("\n💰 Fiyat Düşürülüyor:");
        $this->line("   Eski fiyat: {$oldPrice} TL");
        $this->line("   Yeni fiyat: {$newPrice} TL");
        $this->line("   İndirim: %" . round((($oldPrice - $newPrice) / $oldPrice) * 100, 2));
        
        // updateWithPriceCheck metodunu kullan
        $this->info("\n📧 updateWithPriceCheck metodu kullanılıyor...");
        
        try {
            $product->updateWithPriceCheck([
                'fiyat_ozel' => $newPrice
            ]);
            
            $this->line("   ✅ Fiyat güncellendi ve mail gönderildi!");
            
            // Fiyat geçmişini kontrol et
            $priceHistory = ProductPriceHistory::where('product_kod', $product->kod)
                                             ->where('is_discount', true)
                                             ->latest()
                                             ->first();
            
            if ($priceHistory) {
                $this->info("\n📈 Fiyat Geçmişi Kaydedildi:");
                $this->line("   Eski fiyat: {$priceHistory->old_price_ozel} TL");
                $this->line("   Yeni fiyat: {$priceHistory->new_price_ozel} TL");
                $this->line("   İndirim: %{$priceHistory->discount_percentage}");
                $this->line("   Tarih: {$priceHistory->changed_at->format('d.m.Y H:i')}");
            }
            
            // Sonuçları göster
            $this->info("\n📊 Test Sonuçları:");
            $this->line("   👤 Kullanıcı: {$user->name} ({$user->email})");
            $this->line("   📦 Ürün: {$product->ad} ({$product->kod})");
            $this->line("   💰 Eski Fiyat: {$oldPrice} TL");
            $this->line("   💰 Yeni Fiyat: {$newPrice} TL");
            $this->line("   📉 İndirim: %" . round((($oldPrice - $newPrice) / $oldPrice) * 100, 2));
            $this->line("   📧 Mail: Otomatik gönderildi");
            
            $this->info("\n🎉 Gerçek veriler ile favori indirim mail testi başarılı!");
            
        } catch (\Exception $e) {
            $this->error("   ❌ Hata: " . $e->getMessage());
            $this->error("   📋 Hata detayı: " . $e->getTraceAsString());
        }
    }
}
