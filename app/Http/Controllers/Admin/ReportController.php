<?php

namespace App\Http\Controllers\Admin;

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
     * Dashboard raporları
     */
    public function dashboard()
    {
        $data = $this->reportService->getDashboardData();
        $salesTrend = $this->reportService->getSalesTrend(30);
        $topProducts = $this->reportService->getTopSellingProducts(10);
        $topCustomers = $this->reportService->getTopCustomers(10);

        return view('admin.reports.dashboard', compact('data', 'salesTrend', 'topProducts', 'topCustomers'));
    }

    /**
     * Satış raporları
     */
    public function sales(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $groupBy = $request->get('group_by', 'day');

        $salesReport = $this->reportService->getSalesReport($startDate, $endDate, $groupBy);
        $orderStatusReport = $this->reportService->getOrderStatusReport($startDate, $endDate);
        $paymentStatusReport = $this->reportService->getPaymentStatusReport($startDate, $endDate);

        return view('admin.reports.sales', compact(
            'salesReport', 
            'orderStatusReport', 
            'paymentStatusReport',
            'startDate',
            'endDate',
            'groupBy'
        ));
    }

    /**
     * Ürün raporları
     */
    public function products(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $limit = $request->get('limit', 50);

        $productSalesReport = $this->reportService->getProductSalesReport($startDate, $endDate, $limit);
        $categorySalesReport = $this->reportService->getCategorySalesReport($startDate, $endDate);
        $brandSalesReport = $this->reportService->getBrandSalesReport($startDate, $endDate);
        $stockReport = $this->reportService->getStockReport();

        return view('admin.reports.products', compact(
            'productSalesReport',
            'categorySalesReport',
            'brandSalesReport',
            'stockReport',
            'startDate',
            'endDate',
            'limit'
        ));
    }

    /**
     * Müşteri raporları
     */
    public function customers(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $limit = $request->get('limit', 50);

        $customerReport = $this->reportService->getCustomerReport($startDate, $endDate, $limit);
        $topCustomers = $this->reportService->getTopCustomers(20, $startDate, $endDate);

        return view('admin.reports.customers', compact(
            'customerReport',
            'topCustomers',
            'startDate',
            'endDate',
            'limit'
        ));
    }

    /**
     * Kargo raporları
     */
    public function cargo(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $cargoStatusReport = $this->reportService->getCargoStatusReport($startDate, $endDate);

        return view('admin.reports.cargo', compact(
            'cargoStatusReport',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Kupon raporları
     */
    public function coupons(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        $couponUsageReport = $this->reportService->getCouponUsageReport($startDate, $endDate);

        return view('admin.reports.coupons', compact(
            'couponUsageReport',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Raporları Excel olarak export et
     */
    public function export(Request $request)
    {
        $type = $request->get('type');
        $startDate = $request->get('start_date', Carbon::now()->subMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));

        switch ($type) {
            case 'sales':
                $data = $this->reportService->getSalesReport($startDate, $endDate);
                $filename = 'satis_raporu_' . $startDate . '_' . $endDate . '.xlsx';
                break;
            case 'products':
                $data = $this->reportService->getProductSalesReport($startDate, $endDate);
                $filename = 'urun_raporu_' . $startDate . '_' . $endDate . '.xlsx';
                break;
            case 'customers':
                $data = $this->reportService->getCustomerReport($startDate, $endDate);
                $filename = 'musteri_raporu_' . $startDate . '_' . $endDate . '.xlsx';
                break;
            default:
                return redirect()->back()->with('error', 'Geçersiz rapor türü');
        }

        // Excel export işlemi burada yapılacak
        // Şimdilik JSON olarak döndürüyoruz
        return response()->json($data);
    }

    /**
     * API endpoint - Dashboard verileri
     */
    public function apiDashboard()
    {
        $data = $this->reportService->getDashboardData();
        return response()->json($data);
    }

    /**
     * API endpoint - Satış trendi
     */
    public function apiSalesTrend(Request $request)
    {
        $days = $request->get('days', 30);
        $data = $this->reportService->getSalesTrend($days);
        return response()->json($data);
    }

    /**
     * API endpoint - En çok satan ürünler
     */
    public function apiTopProducts(Request $request)
    {
        $limit = $request->get('limit', 10);
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $data = $this->reportService->getTopSellingProducts($limit, $startDate, $endDate);
        return response()->json($data);
    }

    /**
     * API endpoint - En değerli müşteriler
     */
    public function apiTopCustomers(Request $request)
    {
        $limit = $request->get('limit', 10);
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $data = $this->reportService->getTopCustomers($limit, $startDate, $endDate);
        return response()->json($data);
    }
}
