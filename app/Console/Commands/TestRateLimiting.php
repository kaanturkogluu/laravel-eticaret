<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TestRateLimiting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rate-limit:test {--endpoint=login : Test edilecek endpoint} {--attempts=10 : Test deneme sayısı}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rate limiting sistemini test eder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $endpoint = $this->option('endpoint');
        $attempts = (int) $this->option('attempts');
        
        $this->info("Rate limiting testi başlatılıyor...");
        $this->info("Endpoint: {$endpoint}");
        $this->info("Deneme sayısı: {$attempts}");
        $this->newLine();
        
        $baseUrl = config('app.url', 'http://127.0.0.1:8000');
        $testUrl = $baseUrl . '/' . $endpoint;
        
        $successCount = 0;
        $rateLimitedCount = 0;
        
        for ($i = 1; $i <= $attempts; $i++) {
            $response = $this->makeRequest($testUrl, $endpoint);
            
            if ($response['status'] === 200) {
                $successCount++;
                $this->line("Deneme {$i}: ✅ Başarılı");
            } elseif ($response['status'] === 429) {
                $rateLimitedCount++;
                $this->line("Deneme {$i}: ⚠️  Rate Limited (429)");
                break; // Rate limit'e takıldığında dur
            } else {
                $this->line("Deneme {$i}: ❌ Hata ({$response['status']})");
            }
            
            // Kısa bir bekleme
            usleep(100000); // 0.1 saniye
        }
        
        $this->newLine();
        $this->info("Test tamamlandı!");
        $this->info("Başarılı istekler: {$successCount}");
        $this->info("Rate limited istekler: {$rateLimitedCount}");
        
        if ($rateLimitedCount > 0) {
            $this->info("✅ Rate limiting çalışıyor!");
        } else {
            $this->warn("⚠️  Rate limiting çalışmıyor olabilir!");
        }
    }
    
    private function makeRequest($url, $endpoint)
    {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_COOKIEJAR, tempnam(sys_get_temp_dir(), 'cookies'));
        curl_setopt($ch, CURLOPT_COOKIEFILE, tempnam(sys_get_temp_dir(), 'cookies'));
        
        if ($endpoint === 'login') {
            // Önce login sayfasını al ve CSRF token'ı çıkar
            curl_setopt($ch, CURLOPT_POST, false);
            $loginPage = curl_exec($ch);
            
            // CSRF token'ı bul
            preg_match('/name="_token" value="([^"]+)"/', $loginPage, $matches);
            $csrfToken = $matches[1] ?? 'test-token';
            
            // Login isteği gönder
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'email' => 'test@example.com',
                'password' => 'wrongpassword',
                '_token' => $csrfToken
            ]));
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'status' => $httpCode,
            'body' => $response
        ];
    }
}
