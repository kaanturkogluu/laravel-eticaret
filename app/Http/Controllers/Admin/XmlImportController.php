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
            'update_mode' => 'required|in:replace,merge',
            'stock_control' => 'required|boolean'
        ]);

        try {
            $xmlContent = file_get_contents($request->file('xml_file')->getPathname());
            $xml = new SimpleXMLElement($xmlContent);

            $importedCount = 0;
            $updatedCount = 0;
            $skippedCount = 0;

            foreach ($xml->urun as $urun) {
                $productData = [
                    'kod' => (string) $urun->kod,
                    'ad' => (string) $urun->ad,
                    'marka' => (string) $urun->marka,
                    'kategori' => (string) $urun->kategori,
                    'fiyat_ozel' => (float) $urun->fiyat_ozel,
                    'fiyat_bayi' => (float) $urun->fiyat_bayi,
                    'fiyat_sk' => (float) $urun->fiyat_sk,
                    'miktar' => (int) $urun->miktar,
                    'doviz' => (string) $urun->doviz,
                    'aciklama' => (string) $urun->aciklama,
                    'is_active' => true
                ];

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
                        }
                    }
                } else {
                    Product::create($productData);
                    $importedCount++;
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
        $products = Product::all();
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
        }

        $filename = 'products_export_' . date('Y-m-d_H-i-s') . '.xml';
        
        return response($xml->asXML(), 200, [
            'Content-Type' => 'application/xml',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }
}