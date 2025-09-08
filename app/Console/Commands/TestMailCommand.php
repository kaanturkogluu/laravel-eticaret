<?php

namespace App\Console\Commands;

use App\Mail\WelcomeMail;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:test {email} {type=welcome}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test mail gönderimi - welcome, order, payment, status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $type = $this->argument('type');
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Geçerli bir e-posta adresi girin.');
            return 1;
        }

        try {
            $this->info("Test maili gönderiliyor... (Tip: {$type})");
            
            switch ($type) {
                case 'welcome':
                    $this->sendWelcomeMail($email);
                    break;
                case 'order':
                    $this->sendOrderConfirmationMail($email);
                    break;
                case 'payment':
                    $this->sendPaymentSuccessMail($email);
                    break;
                case 'status':
                    $this->sendOrderStatusUpdateMail($email);
                    break;
                default:
                    $this->error('Geçersiz mail tipi. Kullanılabilir tipler: welcome, order, payment, status');
                    return 1;
            }
            
            $this->info('Test maili başarıyla gönderildi!');
            $this->info('E-posta adresi: ' . $email);
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Mail gönderimi başarısız: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Hoş geldin maili gönder
     */
    private function sendWelcomeMail($email)
    {
        $user = new User([
            'name' => 'Test Kullanıcı',
            'email' => $email,
        ]);

        Mail::to($email)->send(new WelcomeMail($user));
    }

    /**
     * Sipariş onay maili gönder
     */
    private function sendOrderConfirmationMail($email)
    {
        $user = new User([
            'name' => 'Test Kullanıcı',
            'email' => $email,
        ]);

        // Test siparişi oluştur
        $order = new \App\Models\Order();
        $order->id = 12345;
        $order->order_number = 'ORD-2024-001';
        $order->status = 'confirmed';
        $order->total_amount = 299.99;
        $order->currency = 'TRY';
        $order->customer_name = 'Test Kullanıcı';
        $order->customer_email = $email;
        $order->customer_phone = '0555 123 45 67';
        $order->shipping_address = 'Test Mahallesi, Test Sokak No:1';
        $order->shipping_city = 'İstanbul';
        $order->shipping_postal_code = '34000';
        $order->created_at = now();

        // Test sipariş kalemleri
        $order->setRelation('items', collect([
            (object) [
                'product' => (object) [
                    'name' => 'Test Ürün 1',
                ],
                'quantity' => 2,
                'price' => 149.99,
            ],
            (object) [
                'product' => (object) [
                    'name' => 'Test Ürün 2',
                ],
                'quantity' => 1,
                'price' => 199.99,
            ],
        ]));

        Mail::to($email)->send(new \App\Mail\OrderConfirmationMail($order, $user));
    }

    /**
     * Ödeme başarılı maili gönder
     */
    private function sendPaymentSuccessMail($email)
    {
        $user = new User([
            'name' => 'Test Kullanıcı',
            'email' => $email,
        ]);

        // Test siparişi oluştur
        $order = new \App\Models\Order();
        $order->id = 12345;
        $order->order_number = 'ORD-2024-001';
        $order->status = 'confirmed';
        $order->total_amount = 299.99;
        $order->currency = 'TRY';
        $order->customer_name = 'Test Kullanıcı';
        $order->customer_email = $email;
        $order->created_at = now();

        // Test sipariş kalemleri
        $order->setRelation('items', collect([
            (object) [
                'product' => (object) [
                    'name' => 'Test Ürün 1',
                ],
                'quantity' => 2,
                'price' => 149.99,
            ],
        ]));

        // Test ödeme işlemi
        $payment = new \App\Models\PaymentTransaction([
            'id' => 1,
            'transaction_id' => 'TXN-' . time(),
            'amount' => 299.99,
            'currency' => 'TRY',
            'status' => 'completed',
            'created_at' => now(),
        ]);

        // Test ödeme sağlayıcısı
        $payment->setRelation('paymentProvider', (object) [
            'name' => 'Test Ödeme Sağlayıcısı',
        ]);

        Mail::to($email)->send(new \App\Mail\PaymentSuccessMail($order, $payment, $user));
    }

    /**
     * Sipariş durumu güncelleme maili gönder
     */
    private function sendOrderStatusUpdateMail($email)
    {
        $user = new User([
            'name' => 'Test Kullanıcı',
            'email' => $email,
        ]);

        // Test siparişi oluştur
        $order = new \App\Models\Order();
        $order->id = 12345;
        $order->order_number = 'ORD-2024-001';
        $order->status = 'shipped';
        $order->total_amount = 299.99;
        $order->currency = 'TRY';
        $order->customer_name = 'Test Kullanıcı';
        $order->customer_email = $email;
        $order->tracking_number = 'TRK123456789';
        $order->shipping_company = 'Test Kargo';
        $order->created_at = now();

        Mail::to($email)->send(new \App\Mail\OrderStatusUpdateMail($order, $user, 'confirmed', 'shipped'));
    }
}
