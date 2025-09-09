<?php

namespace App\Console\Commands;

use App\Mail\PriceDropNotificationMail;
use App\Models\Product;
use App\Models\ProductPriceHistory;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestPriceDropMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:price-drop-mail {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test fiyat düşüşü mail gönderimi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Test mail gönderiliyor: {$email}");
        
        try {
            // Test kullanıcısı oluştur
            $user = new User();
            $user->id = 1;
            $user->name = 'Test Kullanıcı';
            $user->email = $email;
            
            // Test ürünü oluştur
            $product = new Product();
            $product->id = 1;
            $product->kod = 'TEST001';
            $product->ad = 'Test Ürün';
            $product->marka = 'Test Marka';
            $product->kategori = 'Test Kategori';
            $product->doviz = 'TL';
            $product->aciklama = 'Bu bir test ürünüdür.';
            
            // Test fiyat geçmişi oluştur
            $priceHistory = new ProductPriceHistory();
            $priceHistory->id = 1;
            $priceHistory->product_kod = 'TEST001';
            $priceHistory->old_price_ozel = 100.00;
            $priceHistory->new_price_ozel = 80.00;
            $priceHistory->price_difference = -20.00;
            $priceHistory->discount_percentage = 20.00;
            $priceHistory->is_discount = true;
            $priceHistory->changed_at = now();
            
            // Mail gönder
            Mail::to($email)->send(new PriceDropNotificationMail($user, $product, $priceHistory));
            
            $this->info('✅ Test mail başarıyla gönderildi!');
            
        } catch (\Exception $e) {
            $this->error('❌ Mail gönderim hatası: ' . $e->getMessage());
            $this->error('Hata detayı: ' . $e->getTraceAsString());
        }
    }
}