<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSpecification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class XmlImportService
{
    /**
     * XML dosyasını import et
     */
    public function importFromFile(string $filePath): array
    {
        $results = [
            'success' => false,
            'imported' => 0,
            'updated' => 0,
            'errors' => []
        ];

        try {
            if (!file_exists($filePath)) {
                throw new \Exception("XML dosyası bulunamadı: {$filePath}");
            }

            $xmlContent = file_get_contents($filePath);
            if ($xmlContent === false) {
                throw new \Exception("XML dosyası okunamadı");
            }

            // XML'i parse et
            $xml = new SimpleXMLElement($xmlContent);
            
            if (!isset($xml->XMLUrunView)) {
                throw new \Exception("XML formatı geçersiz - XMLUrunView elementleri bulunamadı");
            }

            DB::beginTransaction();

            foreach ($xml->XMLUrunView as $productXml) {
                try {
                    $this->importProduct($productXml, $results);
                } catch (\Exception $e) {
                    $results['errors'][] = "Ürün import hatası: " . $e->getMessage();
                    Log::error("XML Import Error", [
                        'product' => $productXml->Kod ?? 'Unknown',
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();
            $results['success'] = true;

        } catch (\Exception $e) {
            DB::rollBack();
            $results['errors'][] = $e->getMessage();
            Log::error("XML Import Failed", ['error' => $e->getMessage()]);
        }

        return $results;
    }

    /**
     * Tek bir ürünü import et
     */
    private function importProduct(SimpleXMLElement $productXml, array &$results): void
    {
        $kod = (string) $productXml->Kod;
        
        if (empty($kod)) {
            throw new \Exception("Ürün kodu boş olamaz");
        }

        // Ürün verilerini hazırla
        $productData = [
            'kod' => $kod,
            'ad' => (string) $productXml->Ad,
            'miktar' => (int) $productXml->Miktar,
            'fiyat_sk' => $this->parseDecimal($productXml->Fiyat_SK),
            'fiyat_bayi' => $this->parseDecimal($productXml->Fiyat_Bayi),
            'fiyat_ozel' => $this->parseDecimal($productXml->Fiyat_Ozel),
            'doviz' => $this->normalizeCurrency((string) ($productXml->Doviz ?? '')),
            'marka' => (string) $productXml->Marka,
            'kategori' => (string) $productXml->Kategori,
            'ana_grup_kod' => (string) $productXml->AnaGrup_Kod,
            'ana_grup_ad' => (string) $productXml->AnaGrup_Ad,
            'alt_grup_kod' => (string) $productXml->AltGrup_Kod,
            'alt_grup_ad' => (string) $productXml->AltGrup_Ad,
            'ana_resim' => (string) $productXml->AnaResim,
            'barkod' => (string) $productXml->Barkod,
            'aciklama' => (string) $productXml->Aciklama,
            'detay' => (string) $productXml->Detay,
            'desi' => $this->parseDecimal($productXml->Desi),
            'kdv' => (int) $productXml->Kdv ?: 20,
            'is_active' => true,
            'last_updated' => now(),
        ];

        // Ürünü bul veya oluştur
        $product = Product::updateOrCreate(
            ['kod' => $kod],
            $productData
        );

        if ($product->wasRecentlyCreated) {
            $results['imported']++;
        } else {
            $results['updated']++;
        }

        // Ürün resimlerini import et
        $this->importProductImages($product, $productXml);

        // Ürün teknik özelliklerini import et
        $this->importProductSpecifications($product, $productXml);
    }

    /**
     * Ürün resimlerini import et
     */
    private function importProductImages(Product $product, SimpleXMLElement $productXml): void
    {
        // Mevcut resimleri sil
        $product->images()->delete();

        $imageOptimizationService = app(\App\Services\ImageOptimizationService::class);

        // Ana resim
        if (!empty($productXml->AnaResim)) {
            $imageUrl = (string) $productXml->AnaResim;
            $result = $imageOptimizationService->optimizeFromUrl($imageUrl, 'products');
            
            if ($result['success']) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'urun_kodu' => $product->kod,
                    'resim_url' => $result['original_url'],
                    'sort_order' => 0,
                ]);
            } else {
                // Fallback to original URL if optimization fails
                ProductImage::create([
                    'product_id' => $product->id,
                    'urun_kodu' => $product->kod,
                    'resim_url' => $imageUrl,
                    'sort_order' => 0,
                ]);
            }
        }

        // Diğer resimler
        if (isset($productXml->urunResimleri->UrunResimler)) {
            $sortOrder = 1;
            foreach ($productXml->urunResimleri->UrunResimler as $imageXml) {
                $imageUrl = (string) $imageXml->Resim;
                $result = $imageOptimizationService->optimizeFromUrl($imageUrl, 'products');
                
                if ($result['success']) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'urun_kodu' => (string) $imageXml->UrunKodu,
                        'resim_url' => $result['original_url'],
                        'sort_order' => $sortOrder++,
                    ]);
                } else {
                    // Fallback to original URL if optimization fails
                    ProductImage::create([
                        'product_id' => $product->id,
                        'urun_kodu' => (string) $imageXml->UrunKodu,
                        'resim_url' => $imageUrl,
                        'sort_order' => $sortOrder++,
                    ]);
                }
            }
        }
    }

    /**
     * Ürün teknik özelliklerini import et
     */
    private function importProductSpecifications(Product $product, SimpleXMLElement $productXml): void
    {
        // Mevcut özellikleri sil
        $product->specifications()->delete();

        if (isset($productXml->TeknikOzellikler->UrunTeknikOzellikler)) {
            $sortOrder = 0;
            foreach ($productXml->TeknikOzellikler->UrunTeknikOzellikler as $specXml) {
                ProductSpecification::create([
                    'product_id' => $product->id,
                    'urun_kodu' => (string) $specXml->UrunKodu,
                    'ozellik' => (string) $specXml->Ozellik,
                    'deger' => (string) $specXml->Deger,
                    'sort_order' => $sortOrder++,
                ]);
            }
        }
    }

    /**
     * Decimal değeri parse et
     */
    private function parseDecimal($value): ?float
    {
        if (empty($value)) {
            return null;
        }

        $value = (string) $value;
        $value = str_replace(',', '.', $value);
        
        return is_numeric($value) ? (float) $value : null;
    }

    /**
     * Stokta olmayan ürünleri pasif yap
     */
    public function deactivateOutOfStockProducts(): int
    {
        return Product::where('miktar', '<', 2)
            ->where('is_active', true)
            ->update(['is_active' => false]);
    }

    /**
     * Stokta olan ürünleri aktif yap
     */
    public function activateInStockProducts(): int
    {
        return Product::where('miktar', '>=', 2)
            ->where('is_active', false)
            ->update(['is_active' => true]);
    }

    /**
     * Para birimini normalize et - boş veya geçersiz değerler için TL döndür
     */
    private function normalizeCurrency(string $currency): string
    {
        // Boş veya sadece boşluk karakteri içeren değerler
        if (empty(trim($currency))) {
            return 'TL';
        }

        // Geçerli para birimlerini kontrol et
        $validCurrencies = ['TL', 'USD', 'EUR'];
        $normalizedCurrency = strtoupper(trim($currency));

        // Eğer geçerli bir para birimi değilse TL döndür
        if (!in_array($normalizedCurrency, $validCurrencies)) {
            \Log::warning('Geçersiz para birimi, TL olarak ayarlandı', [
                'original_currency' => $currency,
                'normalized_currency' => $normalizedCurrency
            ]);
            return 'TL';
        }

        return $normalizedCurrency;
    }
}
