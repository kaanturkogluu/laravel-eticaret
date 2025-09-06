<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\XmlImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class XmlImportController extends Controller
{
    protected $xmlImportService;

    public function __construct(XmlImportService $xmlImportService)
    {
        $this->xmlImportService = $xmlImportService;
    }

    /**
     * XML import sayfasını göster
     */
    public function index()
    {
        return view('admin.xml-import');
    }

    /**
     * XML dosyasını import et
     */
    public function import(Request $request)
    {
        $request->validate([
            'xml_file' => 'required|file|mimes:xml|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('xml_file');
            $filePath = $file->store('temp', 'local');
            $fullPath = storage_path('app/' . $filePath);

            $results = $this->xmlImportService->importFromFile($fullPath);

            // Temp dosyayı sil
            Storage::delete($filePath);

            if ($results['success']) {
                return redirect()->back()->with('success', 
                    "Import başarılı! {$results['imported']} ürün eklendi, {$results['updated']} ürün güncellendi."
                );
            } else {
                return redirect()->back()->withErrors($results['errors']);
            }

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Import hatası: ' . $e->getMessage()]);
        }
    }

    /**
     * Liste.xml dosyasını import et
     */
    public function importListeXml()
    {
        try {
            $filePath = base_path('liste.xml');
            
            if (!file_exists($filePath)) {
                return redirect()->back()->withErrors(['liste.xml dosyası bulunamadı!']);
            }

            $results = $this->xmlImportService->importFromFile($filePath);

            if ($results['success']) {
                return redirect()->back()->with('success', 
                    "Liste.xml import başarılı! {$results['imported']} ürün eklendi, {$results['updated']} ürün güncellendi."
                );
            } else {
                return redirect()->back()->withErrors($results['errors']);
            }

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Import hatası: ' . $e->getMessage()]);
        }
    }

    /**
     * Stok kontrolü yap
     */
    public function stockControl()
    {
        try {
            $deactivated = $this->xmlImportService->deactivateOutOfStockProducts();
            $activated = $this->xmlImportService->activateInStockProducts();

            return redirect()->back()->with('success', 
                "Stok kontrolü tamamlandı! {$deactivated} ürün pasif yapıldı, {$activated} ürün aktif yapıldı."
            );

        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Stok kontrolü hatası: ' . $e->getMessage()]);
        }
    }
}
