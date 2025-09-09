@extends('layouts.admin')

@section('title', 'Satış Raporları')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i> Satış Raporları
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtreler -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('admin.reports.sales') }}" class="form-inline">
                                <div class="form-group mr-3">
                                    <label for="start_date" class="mr-2">Başlangıç:</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                           value="{{ $startDate }}">
                                </div>
                                <div class="form-group mr-3">
                                    <label for="end_date" class="mr-2">Bitiş:</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" 
                                           value="{{ $endDate }}">
                                </div>
                                <div class="form-group mr-3">
                                    <label for="group_by" class="mr-2">Grupla:</label>
                                    <select class="form-control" id="group_by" name="group_by">
                                        <option value="day" {{ $groupBy == 'day' ? 'selected' : '' }}>Günlük</option>
                                        <option value="week" {{ $groupBy == 'week' ? 'selected' : '' }}>Haftalık</option>
                                        <option value="month" {{ $groupBy == 'month' ? 'selected' : '' }}>Aylık</option>
                                        <option value="year" {{ $groupBy == 'year' ? 'selected' : '' }}>Yıllık</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filtrele
                                </button>
                                <a href="{{ route('admin.reports.export', ['type' => 'sales', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                                   class="btn btn-success ml-2">
                                    <i class="fas fa-download"></i> Excel İndir
                                </a>
                            </form>
                        </div>
                    </div>

                    <!-- Satış Grafiği -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Satış Trendi</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="salesChart" height="100"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Özet İstatistikler -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-shopping-cart"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Toplam Sipariş</span>
                                    <span class="info-box-number">{{ $salesReport->sum('order_count') }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-lira-sign"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Toplam Satış (₺)</span>
                                    <span class="info-box-number">{{ number_format($salesReport->sum('total_sales_tl'), 0) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-chart-line"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Ortalama Sipariş (₺)</span>
                                    <span class="info-box-number">{{ number_format($salesReport->avg('avg_order_value_tl'), 0) }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Teslim Edilen</span>
                                    <span class="info-box-number">{{ number_format($salesReport->sum('delivered_sales_tl'), 0) }} ₺</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sipariş Durumu Grafiği -->
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Sipariş Durumu Dağılımı</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="orderStatusChart"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Ödeme Durumu Dağılımı</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="paymentStatusChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detaylı Satış Tablosu -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Detaylı Satış Raporu</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Dönem</th>
                                                    <th>Sipariş Sayısı</th>
                                                    <th>Toplam Satış (₺)</th>
                                                    <th>Ortalama Sipariş (₺)</th>
                                                    <th>Teslim Edilen (₺)</th>
                                                    <th>Ödenen (₺)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($salesReport as $report)
                                                <tr>
                                                    <td>{{ $report->period }}</td>
                                                    <td>{{ number_format($report->order_count) }}</td>
                                                    <td>{{ number_format($report->total_sales_tl, 2) }}</td>
                                                    <td>{{ number_format($report->avg_order_value_tl, 2) }}</td>
                                                    <td>{{ number_format($report->delivered_sales_tl, 2) }}</td>
                                                    <td>{{ number_format($report->paid_sales_tl, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Satış trendi grafiği
    const salesData = @json($salesReport);
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesData.map(item => item.period),
            datasets: [{
                label: 'Satış (₺)',
                data: salesData.map(item => item.total_sales_tl),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }, {
                label: 'Sipariş Sayısı',
                data: salesData.map(item => item.order_count),
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });

    // Sipariş durumu grafiği
    const orderStatusData = @json($orderStatusReport);
    const orderStatusCtx = document.getElementById('orderStatusChart').getContext('2d');
    
    new Chart(orderStatusCtx, {
        type: 'doughnut',
        data: {
            labels: orderStatusData.map(item => {
                const statusLabels = {
                    'pending': 'Beklemede',
                    'processing': 'İşleniyor',
                    'shipped': 'Kargoya Verildi',
                    'delivered': 'Teslim Edildi',
                    'cancelled': 'İptal Edildi'
                };
                return statusLabels[item.status] || item.status;
            }),
            datasets: [{
                data: orderStatusData.map(item => item.count),
                backgroundColor: [
                    '#ffc107',
                    '#17a2b8',
                    '#007bff',
                    '#28a745',
                    '#dc3545'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Ödeme durumu grafiği
    const paymentStatusData = @json($paymentStatusReport);
    const paymentStatusCtx = document.getElementById('paymentStatusChart').getContext('2d');
    
    new Chart(paymentStatusCtx, {
        type: 'doughnut',
        data: {
            labels: paymentStatusData.map(item => {
                const statusLabels = {
                    'pending': 'Beklemede',
                    'paid': 'Ödendi',
                    'failed': 'Başarısız',
                    'refunded': 'İade Edildi'
                };
                return statusLabels[item.payment_status] || item.payment_status;
            }),
            datasets: [{
                data: paymentStatusData.map(item => item.count),
                backgroundColor: [
                    '#ffc107',
                    '#28a745',
                    '#dc3545',
                    '#17a2b8'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
});
</script>
@endpush
