<?php

namespace App\Console\Commands;

use App\Models\Favorite;
use App\Models\Product;
use App\Models\ProductPriceHistory;
use App\Services\PriceDropNotificationService;
use Illuminate\Console\Command;

class CheckPriceDropStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'price-drop:check-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fiyat düşüşü durumunu kontrol et ve bildirim gönder';

    protected PriceDropNotificationService $priceDropService;

    public function __construct(PriceDropNotificationService $priceDropService)
    {
        parent::__construct();
        $this->priceDropService = $priceDropService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Fiyat düşüşü durumu kontrol ediliyor...');
        
        // İstatistikler
        $totalFavorites = Favorite::count();
        $totalPriceHistory = ProductPriceHistory::count();
        $totalDiscounts = ProductPriceHistory::where('is_discount', true)->count();
        
        $this->info("📊 İstatistikler:");
        $this->line("   - Toplam favori: {$totalFavorites}");
        $this->line("   - Toplam fiyat geçmişi: {$totalPriceHistory}");
        $this->line("   - Toplam indirim: {$totalDiscounts}");
        
        if ($totalDiscounts == 0) {
            $this->warn('⚠️  Hiç indirim bulunamadı!');
            return;
        }
        
        // Son indirimleri listele
        $recentDiscounts = ProductPriceHistory::where('is_discount', true)
            ->with('product')
            ->orderBy('changed_at', 'desc')
            ->get();
            
        $this->info("\n💰 Son İndirimler:");
        foreach ($recentDiscounts as $discount) {
            $product = $discount->product;
            $favoriteCount = Favorite::where('product_kod', $discount->product_kod)->count();
            
            $this->line("   - {$product->ad} ({$discount->product_kod})");
            $this->line("     İndirim: %{$discount->discount_percentage} ({$discount->changed_at->format('d.m.Y H:i')})");
            $this->line("     Favori kullanıcı: {$favoriteCount}");
            
            if ($favoriteCount > 0) {
                $favorites = Favorite::where('product_kod', $discount->product_kod)->with('user')->get();
                foreach ($favorites as $favorite) {
                    $this->line("       👤 {$favorite->user->email}");
                }
            }
            $this->line('');
        }
        
        // Son 24 saat içindeki indirimleri kontrol et
        $recentDiscounts24h = ProductPriceHistory::where('is_discount', true)
            ->where('changed_at', '>=', now()->subDay())
            ->with('product')
            ->get();
            
        $this->info("📅 Son 24 Saat İndirimleri: {$recentDiscounts24h->count()}");
        
        // Bildirim gönder
        $this->info('📧 Bildirimler gönderiliyor...');
        try {
            $this->priceDropService->sendBulkPriceDropNotifications();
            $this->info('✅ Bildirimler başarıyla gönderildi!');
        } catch (\Exception $e) {
            $this->error('❌ Bildirim hatası: ' . $e->getMessage());
        }
    }
}
