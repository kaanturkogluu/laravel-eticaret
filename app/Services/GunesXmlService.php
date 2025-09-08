<?php

namespace App\Services;

use App\Models\XmlImportHistory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;

class GunesXmlService
{
    private $apiUrl = 'https://api.gunes.net/api/Urunler/XmlUrunListesi/17656';
    private $localDir = 'xml/';
    
    /**
     * XML dosyasını API'den indir ve kaydet
     */
    public function downloadXml()
    {
        try {
            Log::info('Güneş Bilgisayar XML indirme işlemi başladı', [
                'api_url' => $this->apiUrl
            ]);

            // Klasörün var olup olmadığını kontrol et, yoksa oluştur
            $fullPath = storage_path('app/' . $this->localDir);
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0755, true);
            }

            // Dosya adı oluştur
            $filename = 'gunes_urun_listesi_' . date('Y-m-d_H-i-s') . '.xml';
            $localPath = $this->localDir . $filename;

            // Dosyayı indirme işlemi
            $ch = curl_init($this->apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300); // 5 dakika timeout
            curl_setopt($ch, CURLOPT_USERAGENT, 'Laravel XML Importer/1.0');
            
            $data = curl_exec($ch);

            // Hata kontrolü
            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
                throw new \Exception('CURL Hatası: ' . $error);
            }

            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 200) {
                throw new \Exception('HTTP Hatası: ' . $httpCode);
            }

            // XML verisinin geçerliliğini kontrol et
            $xml = null;
            try {
                $xml = new SimpleXMLElement($data);
            } catch (\Exception $e) {
                // XML parse hatası - veriyi logla
                Log::error('XML parse hatası', [
                    'error' => $e->getMessage(),
                    'data_preview' => substr($data, 0, 500),
                    'data_length' => strlen($data)
                ]);
                throw new \Exception('Geçersiz XML formatı: ' . $e->getMessage());
            }

            // Dosyayı kaydet
            if (Storage::put($localPath, $data)) {
                Log::info('XML dosyası başarıyla indirildi', [
                    'filename' => $filename,
                    'size' => strlen($data)
                ]);

                return [
                    'success' => true,
                    'filename' => $filename,
                    'path' => $localPath,
                    'size' => strlen($data),
                    'xml' => $xml
                ];
            } else {
                throw new \Exception('Dosya kaydedilirken bir hata oluştu');
            }

        } catch (\Exception $e) {
            Log::error('XML indirme hatası', [
                'error' => $e->getMessage(),
                'api_url' => $this->apiUrl
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * İndirilen XML'i işle ve ürünleri güncelle
     */
    public function processXml($xmlData)
    {
        try {
            $importedCount = 0;
            $updatedCount = 0;
            $skippedCount = 0;
            $errorCount = 0;

            // XML yapısını kontrol et
            $products = $xmlData->XMLUrunView ?? [];
            
            if (empty($products)) {
                throw new \Exception('XML\'de ürün verisi bulunamadı');
            }

            Log::info('XML işleme başladı', [
                'product_count' => count($products)
            ]);

            foreach ($products as $urun) {
                try {
                    $productData = [
                        'kod' => (string) ($urun->Kod ?? ''),
                        'ad' => (string) ($urun->Ad ?? ''),
                        'marka' => (string) ($urun->Marka ?? ''),
                        'kategori' => (string) ($urun->Kategori ?? ''),
                        'fiyat_ozel' => (float) ($urun->Fiyat_Ozel ?? 0),
                        'fiyat_bayi' => (float) ($urun->Fiyat_Bayi ?? 0),
                        'fiyat_sk' => (float) ($urun->Fiyat_SK ?? 0),
                        'miktar' => (int) ($urun->Miktar ?? 0),
                        'doviz' => (string) ($urun->Doviz ?? 'TL'),
                        'aciklama' => (string) ($urun->Aciklama ?? ''),
                        'is_active' => true,
                        'last_updated' => now()
                    ];

                    // Boş kod kontrolü
                    if (empty($productData['kod'])) {
                        $skippedCount++;
                        continue;
                    }

                    $existingProduct = \App\Models\Product::where('kod', $productData['kod'])->first();

                    if ($existingProduct) {
                        // Mevcut ürünü güncelle
                        $existingProduct->update($productData);
                        $updatedCount++;
                    } else {
                        // Yeni ürün oluştur
                        $product = \App\Models\Product::create($productData);
                        $importedCount++;
                        
                        // Resim bilgilerini işle
                        $this->processProductImages($product, $urun);
                    }

                } catch (\Exception $e) {
                    $errorCount++;
                    Log::warning('Ürün işleme hatası', [
                        'product_code' => $urun->Kod ?? 'unknown',
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $result = [
                'success' => true,
                'imported_count' => $importedCount,
                'updated_count' => $updatedCount,
                'skipped_count' => $skippedCount,
                'error_count' => $errorCount,
                'total_processed' => count($products)
            ];

            Log::info('XML işleme tamamlandı', $result);

            return $result;

        } catch (\Exception $e) {
            Log::error('XML işleme hatası', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Ürün resimlerini işle
     */
    private function processProductImages($product, $urun)
    {
        try {
            $imageUrls = [];
            
            // Ana resim
            $anaResim = (string) ($urun->AnaResim ?? '');
            if (!empty($anaResim)) {
                $imageUrls[] = $anaResim;
            }
            
            // Ek resimler
            $urunResimleri = $urun->urunResimleri ?? null;
            if ($urunResimleri) {
                $resimler = $urunResimleri->UrunResimler ?? [];
                if (!is_array($resimler)) {
                    $resimler = [$resimler];
                }
                
                foreach ($resimler as $resim) {
                    $resimUrl = (string) ($resim->Resim ?? '');
                    if (!empty($resimUrl) && !in_array($resimUrl, $imageUrls)) {
                        $imageUrls[] = $resimUrl;
                    }
                }
            }
            
            // Resimleri kaydet
            if (!empty($imageUrls)) {
                foreach ($imageUrls as $index => $imageUrl) {
                    \App\Models\ProductImage::create([
                        'product_id' => $product->id,
                        'urun_kodu' => $product->kod,
                        'resim_url' => $imageUrl,
                        'sort_order' => $index
                    ]);
                }
            }

        } catch (\Exception $e) {
            Log::warning('Ürün resmi işleme hatası', [
                'product_id' => $product->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Tam otomatik işlem: İndir, işle ve kaydet
     */
    public function fullProcess()
    {
        $startTime = now();
        
        try {
            // XML'i indir
            $downloadResult = $this->downloadXml();
            
            if (!$downloadResult['success']) {
                throw new \Exception($downloadResult['error']);
            }

            // XML'i işle
            $processResult = $this->processXml($downloadResult['xml']);
            
            if (!$processResult['success']) {
                throw new \Exception($processResult['error']);
            }

            // Geçmişe kaydet
            $history = XmlImportHistory::create([
                'filename' => $downloadResult['filename'],
                'file_path' => $downloadResult['path'],
                'file_size' => $downloadResult['size'],
                'status' => 'completed',
                'imported_count' => $processResult['imported_count'],
                'updated_count' => $processResult['updated_count'],
                'skipped_count' => $processResult['skipped_count'],
                'error_count' => $processResult['error_count'],
                'total_processed' => $processResult['total_processed'],
                'started_at' => $startTime,
                'completed_at' => now(),
                'error_message' => null
            ]);

            Log::info('Güneş Bilgisayar otomatik işlem tamamlandı', [
                'history_id' => $history->id,
                'duration' => $startTime->diffInSeconds(now()) . ' saniye'
            ]);

            return [
                'success' => true,
                'history_id' => $history->id,
                'download_result' => $downloadResult,
                'process_result' => $processResult
            ];

        } catch (\Exception $e) {
            // Hata durumunda da geçmişe kaydet
            XmlImportHistory::create([
                'filename' => 'error_' . date('Y-m-d_H-i-s') . '.xml',
                'file_path' => null,
                'file_size' => 0,
                'status' => 'failed',
                'imported_count' => 0,
                'updated_count' => 0,
                'skipped_count' => 0,
                'error_count' => 0,
                'total_processed' => 0,
                'started_at' => $startTime,
                'completed_at' => now(),
                'error_message' => $e->getMessage()
            ]);

            Log::error('Güneş Bilgisayar otomatik işlem hatası', [
                'error' => $e->getMessage(),
                'duration' => $startTime->diffInSeconds(now()) . ' saniye'
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
