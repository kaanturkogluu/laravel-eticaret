<?php

namespace App\Console\Commands;

use App\Services\CurrencyService;
use Illuminate\Console\Command;

class UpdateCurrencyRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update {--test : Test kurları kullan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Döviz kurlarını TCMB\'den çek ve güncelle';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Döviz kurları güncelleniyor...');

        $currencyService = new CurrencyService();

        if ($this->option('test')) {
            $this->info('Test kurları kullanılıyor...');
            $currencyService->updateTestRates();
            $this->info('Test kurları başarıyla güncellendi!');
        } else {
            $result = $currencyService->updateExchangeRates();
            
            if ($result['success']) {
                $this->info('✅ ' . $result['message']);
                $this->info('Güncellenen para birimi sayısı: ' . $result['updated_count']);
            } else {
                $this->error('❌ ' . $result['message']);
                $this->error('Hata: ' . $result['error']);
                return 1;
            }
        }

        // Güncel kurları göster
        $this->showCurrentRates();

        return 0;
    }

    /**
     * Güncel kurları göster
     */
    private function showCurrentRates()
    {
        $this->info("\n📊 Güncel Döviz Kurları:");
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $currencies = \App\Models\Currency::orderBy('code')->get();
        
        foreach ($currencies as $currency) {
            $this->line(sprintf(
                '%-3s: %8s TRY (%s)',
                $currency->code,
                $currency->formatted_rate,
                $currency->last_updated->format('H:i')
            ));
        }
        
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
    }
}
