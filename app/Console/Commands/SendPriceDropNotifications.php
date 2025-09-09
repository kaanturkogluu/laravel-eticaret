<?php

namespace App\Console\Commands;

use App\Services\PriceDropNotificationService;
use Illuminate\Console\Command;

class SendPriceDropNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'price-drop:send-notifications {--test : Test modunda çalıştır}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Favori ürünlerdeki fiyat düşüşleri için bildirim gönder';

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
        $this->info('Fiyat düşüşü bildirimleri gönderiliyor...');

        try {
            if ($this->option('test')) {
                $this->info('Test modunda çalışıyor...');
                $this->testPriceDropNotifications();
            } else {
                $this->priceDropService->sendBulkPriceDropNotifications();
                $this->info('Fiyat düşüşü bildirimleri başarıyla gönderildi!');
            }
        } catch (\Exception $e) {
            $this->error('Hata: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Test modunda çalıştır
     */
    protected function testPriceDropNotifications()
    {
        $this->info('Test bildirimleri gönderiliyor...');
        
        // Son indirimleri listele
        $recentDiscounts = \App\Models\ProductPriceHistory::where('is_discount', true)
            ->where('changed_at', '>=', now()->subDay())
            ->with('product')
            ->get();

        if ($recentDiscounts->isEmpty()) {
            $this->warn('Son 24 saatte indirim bulunamadı.');
            return;
        }

        $this->info('Son 24 saatte ' . $recentDiscounts->count() . ' indirim bulundu:');
        
        foreach ($recentDiscounts as $discount) {
            $this->line('- ' . $discount->product->ad . ' (%' . number_format($discount->discount_percentage, 1) . ' indirim)');
        }

        $this->info('Test tamamlandı.');
    }
}
