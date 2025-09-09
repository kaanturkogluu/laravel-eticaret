<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Müşteri dashboard raporları
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Müşterinin kendi verileri
        $userOrders = $user->orders()
            ->selectRaw("
                COUNT(*) as total_orders,
                SUM(total_tl) as total_spent_tl,
                SUM(total) as total_spent,
                AVG(total_tl) as avg_order_value_tl,
                AVG(total) as avg_order_value,
                MAX(created_at) as last_order_date
            ")
            ->first();

        // Son 30 günlük siparişler
        $recentOrders = $user->orders()
            ->with(['items.product'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // En çok sipariş verilen ürünler
        $topProducts = $user->orders()
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw("
                products.kod,
                products.ad as name,
                products.marka as brand,
                SUM(order_items.quantity) as total_quantity,
                COUNT(DISTINCT orders.id) as order_count
            ")
            ->groupBy('products.kod', 'products.ad', 'products.marka')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        // Aylık sipariş trendi (son 12 ay)
        $monthlyTrend = $user->orders()
            ->selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as order_count,
                SUM(total_tl) as total_spent_tl,
                SUM(total) as total_spent
            ")
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('customer.reports.dashboard', compact(
            'userOrders',
            'recentOrders',
            'topProducts',
            'monthlyTrend'
        ));
    }

    /**
     * Müşteri sipariş geçmişi raporu
     */
    public function orders(Request $request)
    {
        $user = auth()->user();
        $startDate = $request->get('start_date', Carbon::now()->subYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $orders = $user->orders()
            ->with(['items.product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Sipariş istatistikleri
        $orderStats = $user->orders()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("
                COUNT(*) as total_orders,
                SUM(total_tl) as total_spent_tl,
                SUM(total) as total_spent,
                AVG(total_tl) as avg_order_value_tl,
                AVG(total) as avg_order_value,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered_orders,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders
            ")
            ->first();

        return view('customer.reports.orders', compact(
            'orders',
            'orderStats',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Müşteri harcama analizi
     */
    public function spending(Request $request)
    {
        $user = auth()->user();
        $startDate = $request->get('start_date', Carbon::now()->subYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $groupBy = $request->get('group_by', 'month');

        $dateFormat = match($groupBy) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            'year' => '%Y',
            default => '%Y-%m'
        };

        $spendingTrend = $user->orders()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw("
                DATE_FORMAT(created_at, '{$dateFormat}') as period,
                COUNT(*) as order_count,
                SUM(total_tl) as total_spent_tl,
                SUM(total) as total_spent,
                AVG(total_tl) as avg_order_value_tl,
                AVG(total) as avg_order_value
            ")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        // Kategori bazlı harcama
        $categorySpending = $user->orders()
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->selectRaw("
                products.kategori as category,
                COUNT(DISTINCT orders.id) as order_count,
                SUM(order_items.quantity) as total_quantity,
                SUM(order_items.total_price_tl) as total_spent_tl,
                SUM(order_items.total_price) as total_spent
            ")
            ->groupBy('products.kategori')
            ->orderByDesc('total_spent_tl')
            ->get();

        // Marka bazlı harcama
        $brandSpending = $user->orders()
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->selectRaw("
                products.marka as brand,
                COUNT(DISTINCT orders.id) as order_count,
                SUM(order_items.quantity) as total_quantity,
                SUM(order_items.total_price_tl) as total_spent_tl,
                SUM(order_items.total_price) as total_spent
            ")
            ->groupBy('products.marka')
            ->orderByDesc('total_spent_tl')
            ->get();

        return view('customer.reports.spending', compact(
            'spendingTrend',
            'categorySpending',
            'brandSpending',
            'startDate',
            'endDate',
            'groupBy'
        ));
    }

    /**
     * Müşteri favori ürün analizi
     */
    public function favorites()
    {
        $user = auth()->user();
        
        // Favori ürünler
        $favoriteProducts = $user->favoriteProducts()
            ->with(['images'])
            ->get();

        // Favori kategoriler
        $favoriteCategories = $user->favoriteProducts()
            ->selectRaw("
                kategori as category,
                COUNT(*) as product_count
            ")
            ->groupBy('kategori')
            ->orderByDesc('product_count')
            ->get();

        // Favori markalar
        $favoriteBrands = $user->favoriteProducts()
            ->selectRaw("
                marka as brand,
                COUNT(*) as product_count
            ")
            ->groupBy('marka')
            ->orderByDesc('product_count')
            ->get();

        return view('customer.reports.favorites', compact(
            'favoriteProducts',
            'favoriteCategories',
            'favoriteBrands'
        ));
    }

    /**
     * API endpoint - Müşteri dashboard verileri
     */
    public function apiDashboard()
    {
        $user = auth()->user();
        
        $data = $user->orders()
            ->selectRaw("
                COUNT(*) as total_orders,
                SUM(total_tl) as total_spent_tl,
                SUM(total) as total_spent,
                AVG(total_tl) as avg_order_value_tl,
                AVG(total) as avg_order_value
            ")
            ->first();

        return response()->json($data);
    }

    /**
     * API endpoint - Müşteri aylık trend
     */
    public function apiMonthlyTrend(Request $request)
    {
        $user = auth()->user();
        $months = $request->get('months', 12);
        
        $data = $user->orders()
            ->selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as order_count,
                SUM(total_tl) as total_spent_tl,
                SUM(total) as total_spent
            ")
            ->where('created_at', '>=', Carbon::now()->subMonths($months))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json($data);
    }
}
