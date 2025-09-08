<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\XmlImportHistory;
use App\Services\GunesXmlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class XmlHistoryController extends Controller
{
    /**
     * XML import geçmişi sayfası
     */
    public function index(Request $request)
    {
        $query = XmlImportHistory::query();

        // Filtreleme
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sıralama
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $histories = $query->paginate(20);

        // İstatistikler
        $stats = [
            'total' => XmlImportHistory::count(),
            'completed' => XmlImportHistory::completed()->count(),
            'failed' => XmlImportHistory::failed()->count(),
            'today' => XmlImportHistory::today()->count(),
            'last_7_days' => XmlImportHistory::lastDays(7)->count(),
        ];

        return view('admin.xml-history', compact('histories', 'stats'));
    }

    /**
     * XML import geçmişi detayı
     */
    public function show(XmlImportHistory $xmlHistory)
    {
        return view('admin.xml-history-detail', compact('xmlHistory'));
    }

    /**
     * Manuel XML import çalıştır
     */
    public function runManualImport(Request $request)
    {
        try {
            Log::info('Manuel XML import başlatıldı', [
                'user_id' => auth()->id()
            ]);

            $gunesService = new GunesXmlService();
            $result = $gunesService->fullProcess();

            if ($result['success']) {
                $message = "Manuel XML import başarıyla tamamlandı! ";
                $message .= "Yeni ürün: {$result['process_result']['imported_count']}, ";
                $message .= "Güncellenen: {$result['process_result']['updated_count']}, ";
                $message .= "Atlanan: {$result['process_result']['skipped_count']}";

                return redirect()->back()->with('success', $message);
            } else {
                return redirect()->back()->with('error', 'XML import başarısız: ' . $result['error']);
            }

        } catch (\Exception $e) {
            Log::error('Manuel XML import hatası', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'XML import sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * XML import geçmişi sil
     */
    public function destroy(XmlImportHistory $xmlHistory)
    {
        try {
            // Dosyayı da sil
            if ($xmlHistory->file_path && \Storage::exists($xmlHistory->file_path)) {
                \Storage::delete($xmlHistory->file_path);
            }

            $xmlHistory->delete();

            return redirect()->back()->with('success', 'XML import geçmişi başarıyla silindi!');

        } catch (\Exception $e) {
            Log::error('XML import geçmişi silme hatası', [
                'history_id' => $xmlHistory->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'XML import geçmişi silinirken hata oluştu!');
        }
    }

    /**
     * XML dosyasını indir
     */
    public function download(XmlImportHistory $xmlHistory)
    {
        try {
            if (!$xmlHistory->file_path || !\Storage::exists($xmlHistory->file_path)) {
                return redirect()->back()->with('error', 'XML dosyası bulunamadı!');
            }

            return \Storage::download($xmlHistory->file_path, $xmlHistory->filename);

        } catch (\Exception $e) {
            Log::error('XML dosyası indirme hatası', [
                'history_id' => $xmlHistory->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->with('error', 'XML dosyası indirilirken hata oluştu!');
        }
    }

    /**
     * İstatistikler API
     */
    public function stats()
    {
        $stats = [
            'total' => XmlImportHistory::count(),
            'completed' => XmlImportHistory::completed()->count(),
            'failed' => XmlImportHistory::failed()->count(),
            'today' => XmlImportHistory::today()->count(),
            'last_7_days' => XmlImportHistory::lastDays(7)->count(),
            'last_30_days' => XmlImportHistory::lastDays(30)->count(),
        ];

        // Son 7 günün günlük istatistikleri
        $dailyStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dailyStats[] = [
                'date' => $date,
                'completed' => XmlImportHistory::completed()->whereDate('created_at', $date)->count(),
                'failed' => XmlImportHistory::failed()->whereDate('created_at', $date)->count(),
            ];
        }

        $stats['daily'] = $dailyStats;

        return response()->json($stats);
    }

    /**
     * Hata analizi API
     */
    public function errorAnalysis()
    {
        $failedHistories = XmlImportHistory::failed()->orderBy('created_at', 'desc')->get();
        $totalErrors = XmlImportHistory::sum('error_count');
        $totalProcessed = XmlImportHistory::sum('total_processed');
        $errorRate = $totalProcessed > 0 ? round(($totalErrors / $totalProcessed) * 100, 1) : 0;

        // Hata türlerine göre gruplama
        $errorTypes = [];
        foreach ($failedHistories as $history) {
            if ($history->error_message) {
                $errorType = $this->categorizeError($history->error_message);
                if (!isset($errorTypes[$errorType])) {
                    $errorTypes[$errorType] = 0;
                }
                $errorTypes[$errorType]++;
            }
        }

        // Son 7 günün hata istatistikleri
        $dailyErrors = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dailyErrors[] = [
                'date' => $date,
                'errors' => XmlImportHistory::whereDate('created_at', $date)->sum('error_count'),
                'failed_imports' => XmlImportHistory::failed()->whereDate('created_at', $date)->count(),
            ];
        }

        return response()->json([
            'total_failed' => $failedHistories->count(),
            'total_errors' => $totalErrors,
            'error_rate' => $errorRate,
            'error_types' => $errorTypes,
            'daily_errors' => $dailyErrors,
            'recent_failures' => $failedHistories->take(5)->map(function($history) {
                return [
                    'id' => $history->id,
                    'filename' => $history->filename,
                    'error_message' => $history->error_message,
                    'created_at' => $history->created_at->format('d.m.Y H:i'),
                    'error_count' => $history->error_count
                ];
            })
        ]);
    }

    /**
     * Hata mesajını kategorize et
     */
    private function categorizeError($errorMessage)
    {
        $errorMessage = strtolower($errorMessage);
        
        if (strpos($errorMessage, 'xml') !== false || strpos($errorMessage, 'parse') !== false) {
            return 'XML Parse Hatası';
        } elseif (strpos($errorMessage, 'curl') !== false || strpos($errorMessage, 'http') !== false) {
            return 'Network Hatası';
        } elseif (strpos($errorMessage, 'database') !== false || strpos($errorMessage, 'sql') !== false) {
            return 'Veritabanı Hatası';
        } elseif (strpos($errorMessage, 'file') !== false || strpos($errorMessage, 'storage') !== false) {
            return 'Dosya Hatası';
        } elseif (strpos($errorMessage, 'timeout') !== false) {
            return 'Timeout Hatası';
        } elseif (strpos($errorMessage, 'memory') !== false) {
            return 'Bellek Hatası';
        } else {
            return 'Diğer Hatalar';
        }
    }
}
