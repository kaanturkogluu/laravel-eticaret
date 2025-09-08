<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use SimpleXMLElement;
use Illuminate\Support\Facades\Storage;

class XMLImportController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'xml_file' => 'required|file|mimes:xml|max:10240',
            'xml_type' => 'required|in:gunes_bilgisayar,standard,custom',
            'update_mode' => 'required|in:replace,merge',
            'stock_control' => 'required|boolean'
        ]);

        try {
            $xmlContent = file_get_contents($request->file('xml_file')->getPathname());
            $xml = new SimpleXMLElement($xmlContent);

            \Log::info('XML Import başladı', [
                'xml_type' => $request->xml_type,
                'update_mode' => $request->update_mode,
                'stock_control' => $request->stock_control,
                'xml_content_length' => strlen($xmlContent),
                'xml_root_element' => $xml->getName(),
                'xml_children_count' => count($xml->children())
            ]);

            // XML yapısını debug et
            \Log::info('XML Yapısı', [
                'root_element' => $xml->getName(),
                'children' => array_keys((array)$xml->children()),
                'xml_content_preview' => substr($xmlContent, 0, 500)
            ]);

            $importedCount = 0;
            $updatedCount = 0;
            $skippedCount = 0;

            // XML tipine göre işleme mantığını belirle
            $products = $this->getProductsByXmlType($xml, $request->xml_type);
            
            \Log::info('XML Ürün Döngüsü', [
                'xml_type' => $request->xml_type,
                'products_count' => count($products),
                'first_product_structure' => $products ? array_keys((array)$products[0]) : []
            ]);

            foreach ($products as $urun) {
                // XML tipine göre ürün verilerini işle
                $productData = $this->processProductDataByXmlType($urun, $request->xml_type);
                $imageUrls = $this->processImageDataByXmlType($urun, $request->xml_type);

                \Log::info('Ürün verisi işleniyor', [
                    'kod' => $productData['kod'],
                    'ad' => $productData['ad'],
                    'miktar' => $productData['miktar'],
                    'resim_sayisi' => count($imageUrls),
                    'resim_urls' => $imageUrls
                ]);

                // Stok kontrolü
                if ($request->stock_control && $productData['miktar'] < 2) {
                    $skippedCount++;
                    continue;
                }

                $existingProduct = Product::where('kod', $productData['kod'])->first();

                if ($existingProduct) {
                    if ($request->update_mode === 'replace') {
                        $existingProduct->update($productData);
                        $updatedCount++;
                        \Log::info('Ürün güncellendi (replace)', ['kod' => $productData['kod']]);
                        
                        // Resimleri güncelle (replace mode'da mevcut resimleri sil ve yenilerini ekle)
                        if (!empty($imageUrls)) {
                            $existingProduct->images()->delete();
                            $this->saveProductImages($existingProduct, $imageUrls);
                        }
                    } else {
                        // Merge mode - sadece boş alanları güncelle
                        $updateData = [];
                        foreach ($productData as $key => $value) {
                            if (empty($existingProduct->$key) && !empty($value)) {
                                $updateData[$key] = $value;
                            }
                        }
                        if (!empty($updateData)) {
                            $existingProduct->update($updateData);
                            $updatedCount++;
                            \Log::info('Ürün güncellendi (merge)', ['kod' => $productData['kod'], 'updated_fields' => array_keys($updateData)]);
                        } else {
                            \Log::info('Ürün güncellenmedi (merge - değişiklik yok)', ['kod' => $productData['kod']]);
                        }
                        
                        // Merge mode'da sadece resim yoksa ekle
                        if (!empty($imageUrls) && $existingProduct->images()->count() == 0) {
                            $this->saveProductImages($existingProduct, $imageUrls);
                        }
                    }
                } else {
                    $product = Product::create($productData);
                    $importedCount++;
                    \Log::info('Yeni ürün eklendi', ['kod' => $productData['kod']]);
                    
                    // Yeni ürün için resimleri kaydet
                    if (!empty($imageUrls)) {
                        $this->saveProductImages($product, $imageUrls);
                    }
                }
            }

            $message = "XML içe aktarma tamamlandı! ";
            $message .= "Yeni ürün: {$importedCount}, ";
            $message .= "Güncellenen: {$updatedCount}, ";
            $message .= "Atlanan: {$skippedCount}";

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'XML dosyası işlenirken hata oluştu: ' . $e->getMessage());
        }
    }

    public function export()
    {
        $products = Product::with('images')->get();
        $xml = new SimpleXMLElement('<urunler></urunler>');

        foreach ($products as $product) {
            $urun = $xml->addChild('urun');
            $urun->addChild('kod', htmlspecialchars($product->kod));
            $urun->addChild('ad', htmlspecialchars($product->ad));
            $urun->addChild('marka', htmlspecialchars($product->marka));
            $urun->addChild('kategori', htmlspecialchars($product->kategori));
            $urun->addChild('fiyat_ozel', $product->fiyat_ozel);
            $urun->addChild('fiyat_bayi', $product->fiyat_bayi);
            $urun->addChild('fiyat_sk', $product->fiyat_sk);
            $urun->addChild('miktar', $product->miktar);
            $urun->addChild('doviz', $product->doviz);
            $urun->addChild('aciklama', htmlspecialchars($product->aciklama));
            
            // Resim bilgilerini ekle
            if ($product->images()->count() > 0) {
                $urunResimleri = $urun->addChild('urunResimleri');
                foreach ($product->images as $index => $image) {
                    $urunResim = $urunResimleri->addChild('UrunResimler');
                    $urunResim->addChild('UrunKodu', $product->kod);
                    $urunResim->addChild('Resim', htmlspecialchars($image->resim_url));
                }
            }
        }

        $filename = 'products_export_' . date('Y-m-d_H-i-s') . '.xml';
        
        return response($xml->asXML(), 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    /**
     * Stok kontrol işlemi
     */
    public function stockControl(Request $request)
    {
        try {
            $request->validate([
                'min_stock' => 'required|integer|min:0'
            ]);

            $minStock = $request->min_stock;
            $products = Product::where('miktar', '<', $minStock)->get();

            \Log::info('Stok kontrol işlemi', [
                'min_stock' => $minStock,
                'low_stock_products_count' => $products->count()
            ]);

            $message = "Stok kontrolü tamamlandı! ";
            $message .= "Minimum stok: {$minStock}, ";
            $message .= "Stokta az ürün sayısı: {$products->count()}";

            // AJAX isteği ise JSON döndür
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'data' => [
                        'min_stock' => $minStock,
                        'low_stock_count' => $products->count(),
                        'products' => $products->map(function($product) {
                            return [
                                'id' => $product->id,
                                'kod' => $product->kod,
                                'ad' => $product->ad,
                                'miktar' => $product->miktar,
                                'marka' => $product->marka
                            ];
                        })
                    ]
                ]);
            }

            // Normal form submit ise redirect yap
            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Stok kontrol hatası', [
                'error' => $e->getMessage()
            ]);

            $errorMessage = 'Stok kontrol işlemi sırasında hata oluştu: ' . $e->getMessage();

            // AJAX isteği ise JSON döndür
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            // Normal form submit ise redirect yap
            return redirect()->back()->with('error', $errorMessage);
        }
    }

    /**
     * Liste.xml dosyasını import et
     */
    public function importListeXml(Request $request)
    {
        try {
            $xmlPath = base_path('liste.xml');
            
            if (!file_exists($xmlPath)) {
                return redirect()->back()->with('error', 'liste.xml dosyası bulunamadı!');
            }

            $xmlContent = file_get_contents($xmlPath);
            $xml = new SimpleXMLElement($xmlContent);

            \Log::info('Liste.xml Import başladı', [
                'xml_content_length' => strlen($xmlContent),
                'xml_root_element' => $xml->getName()
            ]);

            $importedCount = 0;
            $updatedCount = 0;
            $skippedCount = 0;

            // Güneş Bilgisayar formatı için işleme
            $products = $xml->XMLUrunView ?? [];
            
            foreach ($products as $urun) {
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
                    'is_active' => true
                ];

                // Stok kontrolü
                if ($productData['miktar'] < 2) {
                    $skippedCount++;
                    continue;
                }

                $existingProduct = Product::where('kod', $productData['kod'])->first();

                if ($existingProduct) {
                    $existingProduct->update($productData);
                    $updatedCount++;
                } else {
                    $product = Product::create($productData);
                    $importedCount++;
                    
                    // Resim bilgilerini işle
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
                        $this->saveProductImages($product, $imageUrls);
                    }
                }
            }

            $message = "Liste.xml import tamamlandı! ";
            $message .= "Yeni ürün: {$importedCount}, ";
            $message .= "Güncellenen: {$updatedCount}, ";
            $message .= "Atlanan: {$skippedCount}";

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Liste.xml import hatası', [
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()->with('error', 'Liste.xml dosyası işlenirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * XML import sayfası
     */
    public function index()
    {
        return view('admin.xml-import');
    }

    /**
     * XML tipine göre ürün listesini al
     */
    private function getProductsByXmlType($xml, $xmlType)
    {
        switch ($xmlType) {
            case 'gunes_bilgisayar':
                // Güneş Bilgisayar formatı: ArrayOfXMLUrunView > XMLUrunView
                return $xml->XMLUrunView ?? [];
                
            case 'standard':
                // Standart format: products > product veya urunler > urun
                return $xml->product ?? $xml->urun ?? $xml->products ?? $xml->urunler ?? $xml->children();
                
            case 'custom':
                // Özel format: tüm çocuk elementleri
                return $xml->children();
                
            default:
                return $xml->children();
        }
    }

    /**
     * XML tipine göre ürün verilerini işle
     */
    private function processProductDataByXmlType($urun, $xmlType)
    {
        switch ($xmlType) {
            case 'gunes_bilgisayar':
                return [
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
                    'is_active' => true
                ];
                
            case 'standard':
                return [
                    'kod' => (string) ($urun->kod ?? $urun->code ?? ''),
                    'ad' => (string) ($urun->ad ?? $urun->name ?? ''),
                    'marka' => (string) ($urun->marka ?? $urun->brand ?? ''),
                    'kategori' => (string) ($urun->kategori ?? $urun->category ?? ''),
                    'fiyat_ozel' => (float) ($urun->fiyat_ozel ?? $urun->price_special ?? 0),
                    'fiyat_bayi' => (float) ($urun->fiyat_bayi ?? $urun->price_dealer ?? 0),
                    'fiyat_sk' => (float) ($urun->fiyat_sk ?? $urun->price_retail ?? 0),
                    'miktar' => (int) ($urun->miktar ?? $urun->quantity ?? $urun->stock ?? 0),
                    'doviz' => (string) ($urun->doviz ?? $urun->currency ?? 'TL'),
                    'aciklama' => (string) ($urun->aciklama ?? $urun->description ?? ''),
                    'is_active' => true
                ];
                
            case 'custom':
                // Özel format için esnek okuma
                return [
                    'kod' => (string) ($urun->kod ?? $urun->code ?? $urun->Kod ?? ''),
                    'ad' => (string) ($urun->ad ?? $urun->name ?? $urun->Ad ?? ''),
                    'marka' => (string) ($urun->marka ?? $urun->brand ?? $urun->Marka ?? ''),
                    'kategori' => (string) ($urun->kategori ?? $urun->category ?? $urun->Kategori ?? ''),
                    'fiyat_ozel' => (float) ($urun->fiyat_ozel ?? $urun->price_special ?? $urun->FiyatOzel ?? 0),
                    'fiyat_bayi' => (float) ($urun->fiyat_bayi ?? $urun->price_dealer ?? $urun->FiyatBayi ?? 0),
                    'fiyat_sk' => (float) ($urun->fiyat_sk ?? $urun->price_retail ?? $urun->FiyatSK ?? 0),
                    'miktar' => (int) ($urun->miktar ?? $urun->quantity ?? $urun->stock ?? $urun->Miktar ?? 0),
                    'doviz' => (string) ($urun->doviz ?? $urun->currency ?? $urun->Doviz ?? 'TL'),
                    'aciklama' => (string) ($urun->aciklama ?? $urun->description ?? $urun->Aciklama ?? ''),
                    'is_active' => true
                ];
                
            default:
                return [];
        }
    }

    /**
     * XML tipine göre resim verilerini işle
     */
    private function processImageDataByXmlType($urun, $xmlType)
    {
        $imageUrls = [];
        
        switch ($xmlType) {
            case 'gunes_bilgisayar':
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
                break;
                
            case 'standard':
                // Standart format resimleri
                $images = $urun->images ?? $urun->resimler ?? null;
                if ($images) {
                    $imageList = $images->image ?? $images->resim ?? [];
                    if (!is_array($imageList)) {
                        $imageList = [$imageList];
                    }
                    
                    foreach ($imageList as $image) {
                        $imageUrl = (string) ($image->url ?? $image->src ?? $image ?? '');
                        if (!empty($imageUrl) && !in_array($imageUrl, $imageUrls)) {
                            $imageUrls[] = $imageUrl;
                        }
                    }
                }
                break;
                
            case 'custom':
                // Özel format için esnek resim okuma
                $anaResim = (string) ($urun->anaResim ?? $urun->AnaResim ?? $urun->main_image ?? '');
                if (!empty($anaResim)) {
                    $imageUrls[] = $anaResim;
                }
                
                $urunResimleri = $urun->urunResimleri ?? $urun->product_images ?? null;
                if ($urunResimleri) {
                    $resimler = $urunResimleri->UrunResimler ?? $urunResimleri->product_image ?? [];
                    if (!is_array($resimler)) {
                        $resimler = [$resimler];
                    }
                    
                    foreach ($resimler as $resim) {
                        $resimUrl = (string) ($resim->Resim ?? $resim->image ?? '');
                        if (!empty($resimUrl) && !in_array($resimUrl, $imageUrls)) {
                            $imageUrls[] = $resimUrl;
                        }
                    }
                }
                break;
        }
        
        return $imageUrls;
    }

    /**
     * Ürün resimlerini kaydet
     */
    private function saveProductImages($product, $imageUrls)
    {
        try {
            foreach ($imageUrls as $index => $imageUrl) {
                if (!empty($imageUrl)) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'urun_kodu' => $product->kod,
                        'resim_url' => $imageUrl,
                        'sort_order' => $index
                    ]);
                    
                    \Log::info('Ürün resmi kaydedildi', [
                        'product_id' => $product->id,
                        'urun_kodu' => $product->kod,
                        'resim_url' => $imageUrl,
                        'sort_order' => $index
                    ]);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Ürün resmi kaydetme hatası', [
                'product_id' => $product->id,
                'urun_kodu' => $product->kod,
                'error' => $e->getMessage()
            ]);
        }
    }
}