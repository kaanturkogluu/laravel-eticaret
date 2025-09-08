<?php

namespace App\Console\Commands;

use App\Services\GunesXmlService;
use Illuminate\Console\Command;

class GunesXmlImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gunes:xml-import {--manual : Manuel çalıştırma}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Güneş Bilgisayar XML import işlemini çalıştır';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Güneş Bilgisayar XML import işlemi başlatılıyor...');
        
        $startTime = now();
        
        try {
            $gunesService = new GunesXmlService();
            $result = $gunesService->fullProcess();

            if ($result['success']) {
                $this->info('✅ XML import işlemi başarıyla tamamlandı!');
                
                $processResult = $result['process_result'];
                $this->table(
                    ['İşlem', 'Sayı'],
                    [
                        ['Yeni Ürün', $processResult['imported_count']],
                        ['Güncellenen Ürün', $processResult['updated_count']],
                        ['Atlanan Ürün', $processResult['skipped_count']],
                        ['Hata Alan Ürün', $processResult['error_count']],
                        ['Toplam İşlenen', $processResult['total_processed']]
                    ]
                );

                $duration = $startTime->diffInSeconds(now());
                $this->info("⏱️  İşlem süresi: {$duration} saniye");
                $this->info("📁 Dosya: {$result['download_result']['filename']}");
                $this->info("💾 Dosya boyutu: {$result['download_result']['size']} byte");
                
                return 0;
            } else {
                $this->error('❌ XML import işlemi başarısız!');
                $this->error('Hata: ' . $result['error']);
                return 1;
            }

        } catch (\Exception $e) {
            $this->error('❌ XML import işlemi sırasında hata oluştu!');
            $this->error('Hata: ' . $e->getMessage());
            
            if ($this->option('manual')) {
                $this->error('Stack trace:');
                $this->line($e->getTraceAsString());
            }
            
            return 1;
        }
    }
}
