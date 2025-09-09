<?php

namespace App\Console\Commands;

use App\Models\Favorite;
use App\Models\ProductPriceHistory;
use Illuminate\Console\Command;

class ShowFavoriteUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'show:favorite-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Favori kullanıcıları ve indirimli ürünleri göster';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('👥 Favori Kullanıcılar ve İndirimli Ürünler');
        
        // İndirimli ürünleri al
        $discounts = ProductPriceHistory::where('is_discount', true)
            ->with('product')
            ->get();
            
        foreach ($discounts as $discount) {
            $this->info("\n💰 İndirimli Ürün: {$discount->product->ad} ({$discount->product_kod})");
            $this->line("   İndirim: %{$discount->discount_percentage}");
            $this->line("   Tarih: {$discount->changed_at->format('d.m.Y H:i')}");
            
            // Bu ürünü favorilere ekleyen kullanıcıları bul
            $favorites = Favorite::where('product_kod', $discount->product_kod)
                ->with('user')
                ->get();
                
            if ($favorites->count() > 0) {
                $this->info("   📧 Mail gönderilen kullanıcılar:");
                foreach ($favorites as $favorite) {
                    $this->line("      - {$favorite->user->email} ({$favorite->user->name})");
                }
            } else {
                $this->warn("   ⚠️  Bu ürünü favorilere ekleyen kullanıcı yok!");
            }
        }
        
        // Tüm favorileri göster
        $this->info("\n📋 Tüm Favoriler:");
        $allFavorites = Favorite::with(['user', 'product'])->get();
        
        foreach ($allFavorites as $favorite) {
            $this->line("   👤 {$favorite->user->email} → {$favorite->product->ad} ({$favorite->product_kod})");
        }
    }
}
