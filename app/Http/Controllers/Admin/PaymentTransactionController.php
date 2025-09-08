<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentTransaction;
use App\Models\PaymentProvider;
use App\Services\Payment\PaymentServiceFactory;
use Illuminate\Http\Request;

class PaymentTransactionController extends Controller
{
    /**
     * Payment transaction listesi
     */
    public function index(Request $request)
    {
        $query = PaymentTransaction::with(['order', 'paymentProvider']);

        // Filtreleme
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_provider_id')) {
            $query->where('payment_provider_id', $request->payment_provider_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhere('external_transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('order', function ($orderQuery) use ($search) {
                      $orderQuery->where('order_number', 'like', "%{$search}%")
                                ->orWhere('customer_email', 'like', "%{$search}%");
                  });
            });
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);
        $providers = PaymentProvider::where('is_active', true)->get();
        $statuses = ['pending', 'processing', 'completed', 'failed', 'cancelled', 'refunded', 'partially_refunded'];

        return view('admin.payment-transactions.index', compact('transactions', 'providers', 'statuses'));
    }

    /**
     * Payment transaction detayı
     */
    public function show(PaymentTransaction $paymentTransaction)
    {
        $paymentTransaction->load(['order.items', 'paymentProvider']);
        
        return view('admin.payment-transactions.show', compact('paymentTransaction'));
    }

    /**
     * Payment durumunu kontrol et
     */
    public function checkStatus(PaymentTransaction $paymentTransaction)
    {
        try {
            $service = PaymentServiceFactory::create($paymentTransaction->paymentProvider);
            $result = $service->checkPaymentStatus($paymentTransaction);

            if ($result['success']) {
                return back()->with('success', 'Ödeme durumu güncellendi.');
            } else {
                return back()->with('error', 'Ödeme durumu kontrol edilemedi: ' . $result['message']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * İade işlemi
     */
    public function refund(Request $request, PaymentTransaction $paymentTransaction)
    {
        $request->validate([
            'amount' => 'nullable|numeric|min:0.01|max:' . $paymentTransaction->amount,
            'reason' => 'nullable|string|max:255'
        ]);

        if (!$paymentTransaction->isRefundable()) {
            return back()->with('error', 'Bu işlem iade edilemez.');
        }

        try {
            $service = PaymentServiceFactory::create($paymentTransaction->paymentProvider);
            $refundAmount = $request->amount ?? ($paymentTransaction->amount - $paymentTransaction->refund_amount);
            
            $result = $service->refund($paymentTransaction, $refundAmount);

            if ($result['success']) {
                return back()->with('success', 'İade işlemi başarıyla gerçekleştirildi.');
            } else {
                return back()->with('error', 'İade işlemi başarısız: ' . $result['message']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'İade işlemi sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * İşlemi iptal et
     */
    public function cancel(PaymentTransaction $paymentTransaction)
    {
        if (!in_array($paymentTransaction->status, ['pending', 'processing'])) {
            return back()->with('error', 'Bu işlem iptal edilemez.');
        }

        try {
            $service = PaymentServiceFactory::create($paymentTransaction->paymentProvider);
            
            // Service'de cancel metodu yoksa, manuel olarak iptal et
            if (method_exists($service, 'cancel')) {
                $result = $service->cancel($paymentTransaction);
                if (!$result['success']) {
                    return back()->with('error', 'İptal işlemi başarısız: ' . $result['message']);
                }
            } else {
                $paymentTransaction->markAsCancelled();
            }

            return back()->with('success', 'İşlem başarıyla iptal edildi.');
        } catch (\Exception $e) {
            return back()->with('error', 'İptal işlemi sırasında hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Ödeme istatistikleri
     */
    public function statistics(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth();
        $dateTo = $request->date_to ?? now()->endOfMonth();

        $query = PaymentTransaction::whereBetween('created_at', [$dateFrom, $dateTo]);

        // Toplam işlem sayısı
        $totalTransactions = $query->count();

        // Başarılı işlemler
        $successfulTransactions = $query->clone()->where('status', 'completed')->count();

        // Başarısız işlemler
        $failedTransactions = $query->clone()->where('status', 'failed')->count();

        // Bekleyen işlemler
        $pendingTransactions = $query->clone()->whereIn('status', ['pending', 'processing'])->count();

        // Toplam tutar
        $totalAmount = $query->clone()->where('status', 'completed')->sum('amount');

        // Toplam komisyon
        $totalCommission = $query->clone()->where('status', 'completed')->sum('commission_amount');

        // Provider bazında istatistikler
        $providerStats = PaymentTransaction::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'completed')
            ->selectRaw('payment_provider_id, COUNT(*) as transaction_count, SUM(amount) as total_amount, SUM(commission_amount) as total_commission')
            ->with('paymentProvider')
            ->groupBy('payment_provider_id')
            ->get();

        // Günlük istatistikler
        $dailyStats = PaymentTransaction::whereBetween('created_at', [$dateFrom, $dateTo])
            ->where('status', 'completed')
            ->selectRaw('DATE(created_at) as date, COUNT(*) as transaction_count, SUM(amount) as total_amount')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('admin.payment-transactions.statistics', compact(
            'totalTransactions',
            'successfulTransactions',
            'failedTransactions',
            'pendingTransactions',
            'totalAmount',
            'totalCommission',
            'providerStats',
            'dailyStats',
            'dateFrom',
            'dateTo'
        ));
    }

    /**
     * Export işlemleri
     */
    public function export(Request $request)
    {
        $query = PaymentTransaction::with(['order', 'paymentProvider']);

        // Filtreleme (index ile aynı)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_provider_id')) {
            $query->where('payment_provider_id', $request->payment_provider_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('created_at', 'desc')->get();

        $filename = 'payment_transactions_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            
            // CSV başlıkları
            fputcsv($file, [
                'Transaction ID',
                'External Transaction ID',
                'Order Number',
                'Customer Email',
                'Payment Provider',
                'Amount',
                'Currency',
                'Status',
                'Commission Amount',
                'Net Amount',
                'Created At',
                'Processed At'
            ]);

            // Veriler
            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->transaction_id,
                    $transaction->external_transaction_id,
                    $transaction->order->order_number ?? '',
                    $transaction->order->customer_email ?? '',
                    $transaction->paymentProvider->name ?? '',
                    $transaction->amount,
                    $transaction->currency,
                    $transaction->status,
                    $transaction->commission_amount,
                    $transaction->net_amount,
                    $transaction->created_at->format('Y-m-d H:i:s'),
                    $transaction->processed_at ? $transaction->processed_at->format('Y-m-d H:i:s') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
