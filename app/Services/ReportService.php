<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\PaymentTransaction;
use App\Models\CargoTracking;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Satış raporları
     */
    public function getSalesReport($startDate = null, $endDate = null, $groupBy = 'day')
    {
        $query = Order::query();
        
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $dateFormat = match($groupBy) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            'year' => '%Y',
            default => '%Y-%m-%d'
        };

        return $query->selectRaw("
                DATE_FORMAT(created_at, '{$dateFormat}') as period,
                COUNT(*) as order_count,
                SUM(total_tl) as total_sales_tl,
                SUM(total) as total_sales,
                AVG(total_tl) as avg_order_value_tl,
                AVG(total) as avg_order_value,
                SUM(CASE WHEN status = 'delivered' THEN total_tl ELSE 0 END) as delivered_sales_tl,
                SUM(CASE WHEN status = 'delivered' THEN total ELSE 0 END) as delivered_sales,
                SUM(CASE WHEN payment_status = 'paid' THEN total_tl ELSE 0 END) as paid_sales_tl,
                SUM(CASE WHEN payment_status = 'paid' THEN total ELSE 0 END) as paid_sales
            ")
            ->groupBy('period')
            ->orderBy('period')
            ->get();
    }

    /**
     * Ürün satış raporları
     */
    public function getProductSalesReport($startDate = null, $endDate = null, $limit = 50)
    {
        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw("
                products.kod,
                products.ad as product_name,
                products.marka as brand,
                products.kategori as category,
                SUM(order_items.quantity) as total_quantity,
                SUM(order_items.total_price_tl) as total_sales_tl,
                SUM(order_items.total_price) as total_sales,
                COUNT(DISTINCT orders.id) as order_count,
                AVG(order_items.unit_price_tl) as avg_price_tl,
                AVG(order_items.unit_price) as avg_price
            ");

        if ($startDate) {
            $query->whereDate('orders.created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('orders.created_at', '<=', $endDate);
        }

        return $query->groupBy('products.kod', 'products.ad', 'products.marka', 'products.kategori')
            ->orderByDesc('total_sales_tl')
            ->limit($limit)
            ->get();
    }

    /**
     * Müşteri raporları
     */
    public function getCustomerReport($startDate = null, $endDate = null, $limit = 50)
    {
        $query = User::query()
            ->selectRaw("
                users.id,
                users.name,
                users.email,
                users.created_at as registration_date,
                COUNT(orders.id) as total_orders,
                SUM(orders.total_tl) as total_spent_tl,
                SUM(orders.total) as total_spent,
                AVG(orders.total_tl) as avg_order_value_tl,
                AVG(orders.total) as avg_order_value,
                MAX(orders.created_at) as last_order_date
            ")
            ->leftJoin('orders', 'users.id', '=', 'orders.user_id');

        if ($startDate) {
            $query->whereDate('orders.created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('orders.created_at', '<=', $endDate);
        }

        return $query->groupBy('users.id', 'users.name', 'users.email', 'users.created_at')
            ->having('total_orders', '>', 0)
            ->orderByDesc('total_spent_tl')
            ->limit($limit)
            ->get();
    }

    /**
     * Sipariş durumu raporları
     */
    public function getOrderStatusReport($startDate = null, $endDate = null)
    {
        $query = Order::query();
        
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query->selectRaw("
                status,
                COUNT(*) as count,
                SUM(total_tl) as total_value_tl,
                SUM(total) as total_value,
                AVG(total_tl) as avg_value_tl,
                AVG(total) as avg_value
            ")
            ->groupBy('status')
            ->get();
    }

    /**
     * Ödeme durumu raporları
     */
    public function getPaymentStatusReport($startDate = null, $endDate = null)
    {
        $query = Order::query();
        
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query->selectRaw("
                payment_status,
                COUNT(*) as count,
                SUM(total_tl) as total_value_tl,
                SUM(total) as total_value,
                AVG(total_tl) as avg_value_tl,
                AVG(total) as avg_value
            ")
            ->groupBy('payment_status')
            ->get();
    }

    /**
     * Kargo durumu raporları
     */
    public function getCargoStatusReport($startDate = null, $endDate = null)
    {
        $query = CargoTracking::query()
            ->join('orders', 'cargo_trackings.order_id', '=', 'orders.id');
        
        if ($startDate) {
            $query->whereDate('cargo_trackings.created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('cargo_trackings.created_at', '<=', $endDate);
        }

        return $query->selectRaw("
                cargo_trackings.status,
                COUNT(*) as count,
                SUM(orders.total_tl) as total_value_tl,
                SUM(orders.total) as total_value
            ")
            ->groupBy('cargo_trackings.status')
            ->get();
    }

    /**
     * Kategori bazlı satış raporları
     */
    public function getCategorySalesReport($startDate = null, $endDate = null)
    {
        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw("
                products.kategori as category,
                COUNT(DISTINCT orders.id) as order_count,
                SUM(order_items.quantity) as total_quantity,
                SUM(order_items.total_price_tl) as total_sales_tl,
                SUM(order_items.total_price) as total_sales,
                AVG(order_items.unit_price_tl) as avg_price_tl,
                AVG(order_items.unit_price) as avg_price
            ");

        if ($startDate) {
            $query->whereDate('orders.created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('orders.created_at', '<=', $endDate);
        }

        return $query->groupBy('products.kategori')
            ->orderByDesc('total_sales_tl')
            ->get();
    }

    /**
     * Marka bazlı satış raporları
     */
    public function getBrandSalesReport($startDate = null, $endDate = null)
    {
        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw("
                products.marka as brand,
                COUNT(DISTINCT orders.id) as order_count,
                SUM(order_items.quantity) as total_quantity,
                SUM(order_items.total_price_tl) as total_sales_tl,
                SUM(order_items.total_price) as total_sales,
                AVG(order_items.unit_price_tl) as avg_price_tl,
                AVG(order_items.unit_price) as avg_price
            ");

        if ($startDate) {
            $query->whereDate('orders.created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('orders.created_at', '<=', $endDate);
        }

        return $query->groupBy('products.marka')
            ->orderByDesc('total_sales_tl')
            ->get();
    }

    /**
     * Günlük dashboard verileri
     */
    public function getDashboardData($date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::today();
        
        $today = $date->format('Y-m-d');
        $thisMonth = $date->format('Y-m');
        $lastMonth = $date->copy()->subMonth()->format('Y-m');

        // Bugünkü veriler
        $todayData = [
            'orders' => Order::whereDate('created_at', $today)->count(),
            'sales_tl' => Order::whereDate('created_at', $today)->sum('total_tl'),
            'sales' => Order::whereDate('created_at', $today)->sum('total'),
            'customers' => User::whereDate('created_at', $today)->count(),
        ];

        // Bu ayın verileri
        $thisMonthData = [
            'orders' => Order::where('created_at', 'like', $thisMonth . '%')->count(),
            'sales_tl' => Order::where('created_at', 'like', $thisMonth . '%')->sum('total_tl'),
            'sales' => Order::where('created_at', 'like', $thisMonth . '%')->sum('total'),
            'customers' => User::where('created_at', 'like', $thisMonth . '%')->count(),
        ];

        // Geçen ayın verileri
        $lastMonthData = [
            'orders' => Order::where('created_at', 'like', $lastMonth . '%')->count(),
            'sales_tl' => Order::where('created_at', 'like', $lastMonth . '%')->sum('total_tl'),
            'sales' => Order::where('created_at', 'like', $lastMonth . '%')->sum('total'),
            'customers' => User::where('created_at', 'like', $lastMonth . '%')->count(),
        ];

        // Toplam veriler
        $totalData = [
            'orders' => Order::count(),
            'sales_tl' => Order::sum('total_tl'),
            'sales' => Order::sum('total'),
            'customers' => User::count(),
            'products' => Product::count(),
        ];

        return [
            'today' => $todayData,
            'this_month' => $thisMonthData,
            'last_month' => $lastMonthData,
            'total' => $totalData,
            'growth' => $this->calculateGrowth($thisMonthData, $lastMonthData)
        ];
    }

    /**
     * Büyüme oranı hesapla
     */
    private function calculateGrowth($current, $previous)
    {
        $growth = [];
        
        foreach ($current as $key => $value) {
            if ($previous[$key] > 0) {
                $growth[$key] = round((($value - $previous[$key]) / $previous[$key]) * 100, 2);
            } else {
                $growth[$key] = $value > 0 ? 100 : 0;
            }
        }
        
        return $growth;
    }

    /**
     * Son 30 günlük satış trendi
     */
    public function getSalesTrend($days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return Order::selectRaw("
                DATE(created_at) as date,
                COUNT(*) as orders,
                SUM(total_tl) as sales_tl,
                SUM(total) as sales
            ")
            ->whereDate('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * En çok satan ürünler
     */
    public function getTopSellingProducts($limit = 10, $startDate = null, $endDate = null)
    {
        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw("
                products.kod,
                products.ad as name,
                products.marka as brand,
                SUM(order_items.quantity) as quantity_sold,
                SUM(order_items.total_price_tl) as revenue_tl,
                SUM(order_items.total_price) as revenue
            ");

        if ($startDate) {
            $query->whereDate('orders.created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('orders.created_at', '<=', $endDate);
        }

        return $query->groupBy('products.kod', 'products.ad', 'products.marka')
            ->orderByDesc('quantity_sold')
            ->limit($limit)
            ->get();
    }

    /**
     * En değerli müşteriler
     */
    public function getTopCustomers($limit = 10, $startDate = null, $endDate = null)
    {
        $query = User::query()
            ->selectRaw("
                users.id,
                users.name,
                users.email,
                COUNT(orders.id) as order_count,
                SUM(orders.total_tl) as total_spent_tl,
                SUM(orders.total) as total_spent,
                MAX(orders.created_at) as last_order
            ")
            ->join('orders', 'users.id', '=', 'orders.user_id');

        if ($startDate) {
            $query->whereDate('orders.created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('orders.created_at', '<=', $endDate);
        }

        return $query->groupBy('users.id', 'users.name', 'users.email')
            ->orderByDesc('total_spent_tl')
            ->limit($limit)
            ->get();
    }

    /**
     * Stok durumu raporu
     */
    public function getStockReport()
    {
        return Product::selectRaw("
                kategori as category,
                marka as brand,
                COUNT(*) as total_products,
                SUM(CASE WHEN miktar > 10 THEN 1 ELSE 0 END) as in_stock,
                SUM(CASE WHEN miktar BETWEEN 1 AND 10 THEN 1 ELSE 0 END) as low_stock,
                SUM(CASE WHEN miktar = 0 THEN 1 ELSE 0 END) as out_of_stock,
                AVG(miktar) as avg_stock
            ")
            ->groupBy('kategori', 'marka')
            ->orderBy('kategori')
            ->orderBy('marka')
            ->get();
    }

    /**
     * Kupon kullanım raporu
     */
    public function getCouponUsageReport($startDate = null, $endDate = null)
    {
        $query = Order::whereNotNull('coupon_id');
        
        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query->selectRaw("
                coupon_code,
                COUNT(*) as usage_count,
                SUM(discount_amount_tl) as total_discount_tl,
                SUM(discount_amount) as total_discount,
                SUM(total_tl) as total_sales_tl,
                SUM(total) as total_sales,
                AVG(discount_amount_tl) as avg_discount_tl,
                AVG(discount_amount) as avg_discount
            ")
            ->groupBy('coupon_code')
            ->orderByDesc('usage_count')
            ->get();
    }
}
