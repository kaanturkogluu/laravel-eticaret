<?php

namespace App\Jobs;

use App\Services\GunesXmlService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GunesXmlImportJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 600; // 10 dakika timeout
    public $tries = 3; // 3 kez dene

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Güneş Bilgisayar otomatik XML import job başladı');

        try {
            $gunesService = new GunesXmlService();
            $result = $gunesService->fullProcess();

            if ($result['success']) {
                Log::info('Güneş Bilgisayar otomatik XML import job başarıyla tamamlandı', [
                    'history_id' => $result['history_id']
                ]);
            } else {
                Log::error('Güneş Bilgisayar otomatik XML import job başarısız', [
                    'error' => $result['error']
                ]);
                
                // Job'u başarısız olarak işaretle
                throw new \Exception($result['error']);
            }

        } catch (\Exception $e) {
            Log::error('Güneş Bilgisayar otomatik XML import job hatası', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Job'u tekrar denemek için exception'ı yeniden fırlat
            throw $e;
        }
    }

    /**
     * Job başarısız olduğunda çalışır
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Güneş Bilgisayar otomatik XML import job tamamen başarısız', [
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);
    }
}
