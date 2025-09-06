<?php

namespace App\Console\Commands;

use App\Services\XmlImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class XmlImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xml:import {--file= : XML dosya yolu}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'XML dosyasını import eder ve stok kontrolü yapar';

    protected $xmlImportService;

    public function __construct(XmlImportService $xmlImportService)
    {
        parent::__construct();
        $this->xmlImportService = $xmlImportService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('XML Import işlemi başlatılıyor...');

        try {
            // XML dosya yolu belirle
            $filePath = $this->option('file') ?: base_path('liste.xml');

            if (!file_exists($filePath)) {
                $this->error("XML dosyası bulunamadı: {$filePath}");
                return 1;
            }

            $this->info("XML dosyası okunuyor: {$filePath}");

            // XML import işlemi
            $results = $this->xmlImportService->importFromFile($filePath);

            if ($results['success']) {
                $this->info("✅ Import başarılı!");
                $this->info("📦 {$results['imported']} ürün eklendi");
                $this->info("🔄 {$results['updated']} ürün güncellendi");

                if (!empty($results['errors'])) {
                    $this->warn("⚠️  Bazı hatalar oluştu:");
                    foreach ($results['errors'] as $error) {
                        $this->error("   - {$error}");
                    }
                }
            } else {
                $this->error("❌ Import başarısız!");
                foreach ($results['errors'] as $error) {
                    $this->error("   - {$error}");
                }
                return 1;
            }

            // Stok kontrolü
            $this->info("\n📊 Stok kontrolü yapılıyor...");
            
            $deactivated = $this->xmlImportService->deactivateOutOfStockProducts();
            $activated = $this->xmlImportService->activateInStockProducts();

            $this->info("🔴 {$deactivated} ürün pasif yapıldı (stok < 2)");
            $this->info("🟢 {$activated} ürün aktif yapıldı (stok >= 2)");

            // İstatistikler
            $totalProducts = \App\Models\Product::count();
            $activeProducts = \App\Models\Product::active()->inStock()->count();
            $lowStockProducts = \App\Models\Product::where('miktar', '<', 2)->count();

            $this->info("\n📈 İstatistikler:");
            $this->info("   Toplam ürün: {$totalProducts}");
            $this->info("   Aktif ürün: {$activeProducts}");
            $this->info("   Düşük stok: {$lowStockProducts}");

            $this->info("\n✅ XML import işlemi tamamlandı!");
            
            // Log kaydet
            Log::info('XML Import Command Completed', [
                'imported' => $results['imported'],
                'updated' => $results['updated'],
                'deactivated' => $deactivated,
                'activated' => $activated,
                'total_products' => $totalProducts,
                'active_products' => $activeProducts
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Hata oluştu: " . $e->getMessage());
            Log::error('XML Import Command Failed', ['error' => $e->getMessage()]);
            return 1;
        }
    }
}
