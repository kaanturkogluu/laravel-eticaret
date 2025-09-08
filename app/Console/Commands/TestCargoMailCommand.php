<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\CargoCompany;
use App\Models\CargoTracking;
use App\Models\User;
use App\Mail\CargoNotificationMail;
use App\Mail\CargoStatusUpdateMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestCargoMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:cargo-mail {email} {--type=notification}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test cargo notification and status update emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $type = $this->option('type');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Geçerli bir e-posta adresi giriniz.');
            return 1;
        }

        try {
            // Test verileri oluştur
            $user = User::first();
            $cargoCompany = CargoCompany::first();
            
            if (!$user || !$cargoCompany) {
                $this->error('Test için gerekli veriler bulunamadı. Lütfen önce kullanıcı ve kargo şirketi ekleyin.');
                return 1;
            }

            // Test siparişi oluştur
            $order = Order::create([
                'order_number' => 'TEST-' . date('Ymd') . '-' . rand(1000, 9999),
                'user_id' => $user->id,
                'status' => 'processing',
                'subtotal' => 100.00,
                'subtotal_tl' => 100.00,
                'shipping_cost' => 0,
                'shipping_cost_tl' => 0,
                'total' => 100.00,
                'total_tl' => 100.00,
                'currency' => 'TRY',
                'customer_name' => 'Test Müşteri',
                'customer_email' => $email,
                'customer_phone' => '05551234567',
                'shipping_address' => 'Test Adres, Test Mahallesi, Test Sokak No:1',
                'shipping_city' => 'İstanbul',
                'shipping_district' => 'Kadıköy',
                'shipping_postal_code' => '34710',
                'payment_status' => 'paid',
                'payment_method' => 'credit_card',
                'payment_reference' => 'TEST-' . time(),
                'paid_at' => now(),
                'cargo_company_id' => $cargoCompany->id,
                'cargo_tracking_number' => 'TEST' . rand(100000, 999999),
            ]);

            // Test kargo takip kaydı oluştur
            $cargoTracking = CargoTracking::create([
                'order_id' => $order->id,
                'cargo_company_id' => $cargoCompany->id,
                'tracking_number' => 'TEST' . rand(100000, 999999),
                'status' => 'created',
                'description' => 'Test kargo kaydı oluşturuldu',
                'location' => 'İstanbul Merkez',
                'event_date' => now(),
                'is_delivered' => false,
            ]);

            if ($type === 'notification') {
                // Kargo bilgilendirme maili gönder (queue olmadan)
                try {
                    Mail::to($email)->send(new CargoNotificationMail($order, $cargoTracking));
                    $this->info("Kargo bilgilendirme maili {$email} adresine gönderildi.");
                } catch (\Exception $e) {
                    $this->error("Mail gönderim hatası: " . $e->getMessage());
                    return 1;
                }
            } elseif ($type === 'status-update') {
                // Kargo durumu güncelleme maili gönder (queue olmadan)
                try {
                    Mail::to($email)->send(new CargoStatusUpdateMail($order, $cargoTracking, 'created', 'in_transit'));
                    $this->info("Kargo durumu güncelleme maili {$email} adresine gönderildi.");
                } catch (\Exception $e) {
                    $this->error("Mail gönderim hatası: " . $e->getMessage());
                    return 1;
                }
            } else {
                $this->error('Geçersiz mail tipi. Kullanılabilir tipler: notification, status-update');
                return 1;
            }

            // Test verilerini temizle
            $cargoTracking->delete();
            $order->delete();

            $this->info('Test başarıyla tamamlandı!');
            return 0;

        } catch (\Exception $e) {
            $this->error('Test sırasında hata oluştu: ' . $e->getMessage());
            return 1;
        }
    }
}
