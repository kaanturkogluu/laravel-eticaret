@extends('layouts.admin')

@section('title', 'Raporlama Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i> Raporlama Dashboard
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Özet Kartları -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ number_format($data['today']['orders']) }}</h3>
                                    <p>Bugünkü Siparişler</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div class="small-box-footer">
                                    <span class="text-white">
                                        @if($data['growth']['orders'] > 0)
                                            <i class="fas fa-arrow-up"></i> +{{ $data['growth']['orders'] }}%
                                        @elseif($data['growth']['orders'] < 0)
                                            <i class="fas fa-arrow-down"></i> {{ $data['growth']['orders'] }}%
                                        @else
                                            <i class="fas fa-minus"></i> 0%
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ number_format($data['today']['sales_tl'], 0) }} ₺</h3>
                                    <p>Bugünkü Satış</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-lira-sign"></i>
                                </div>
                                <div class="small-box-footer">
                                    <span class="text-white">
                                        @if($data['growth']['sales_tl'] > 0)
                                            <i class="fas fa-arrow-up"></i> +{{ $data['growth']['sales_tl'] }}%
                                        @elseif($data['growth']['sales_tl'] < 0)
                                            <i class="fas fa-arrow-down"></i> {{ $data['growth']['sales_tl'] }}%
                                        @else
                                            <i class="fas fa-minus"></i> 0%
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ number_format($data['today']['customers']) }}</h3>
                                    <p>Yeni Müşteriler</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-plus"></i>
                                </div>
                                <div class="small-box-footer">
                                    <span class="text-white">
                                        @if($data['growth']['customers'] > 0)
                                            <i class="fas fa-arrow-up"></i> +{{ $data['growth']['customers'] }}%
                                        @elseif($data['growth']['customers'] < 0)
                                            <i class="fas fa-arrow-down"></i> {{ $data['growth']['customers'] }}%
                                        @else
                                            <i class="fas fa-minus"></i> 0%
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ number_format($data['total']['products']) }}</h3>
                                    <p>Toplam Ürün</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-box"></i>
                                </div>
                                <div class="small-box-footer">
                                    <span class="text-white">Aktif Ürünler</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Grafikler -->
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Son 30 Günlük Satış Trendi</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="salesTrendChart" height="100"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Bu Ay vs Geçen Ay</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="description-block border-right">
                                                <span class="description-percentage text-success">
                                                    <i class="fas fa-caret-up"></i> {{ $data['this_month']['orders'] }}
                                                </span>
                                                <h5 class="description-header">Bu Ay Sipariş</h5>
                                                <span class="description-text">Toplam: {{ $data['total']['orders'] }}</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="description-block">
                                                <span class="description-percentage text-warning">
                                                    <i class="fas fa-caret-up"></i> {{ number_format($data['this_month']['sales_tl'], 0) }} ₺
                                                </span>
                                                <h5 class="description-header">Bu Ay Satış</h5>
                                                <span class="description-text">Toplam: {{ number_format($data['total']['sales_tl'], 0) }} ₺</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- En Çok Satan Ürünler ve Müşteriler -->
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">En Çok Satan Ürünler</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Ürün</th>
                                                    <th>Marka</th>
                                                    <th>Satılan</th>
                                                    <th>Ciro</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($topProducts as $product)
                                                <tr>
                                                    <td>{{ $product->name }}</td>
                                                    <td>{{ $product->brand }}</td>
                                                    <td>{{ $product->quantity_sold }}</td>
                                                    <td>{{ number_format($product->revenue_tl, 0) }} ₺</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">En Değerli Müşteriler</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Müşteri</th>
                                                    <th>Sipariş</th>
                                                    <th>Toplam Harcama</th>
                                                    <th>Ortalama</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($topCustomers as $customer)
                                                <tr>
                                                    <td>{{ $customer->name }}</td>
                                                    <td>{{ $customer->order_count }}</td>
                                                    <td>{{ number_format($customer->total_spent_tl, 0) }} ₺</td>
                                                    <td>{{ number_format($customer->total_spent_tl / $customer->order_count, 0) }} ₺</td>
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
    const salesTrendData = @json($salesTrend);
    const ctx = document.getElementById('salesTrendChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: salesTrendData.map(item => item.date),
            datasets: [{
                label: 'Satış (₺)',
                data: salesTrendData.map(item => item.sales_tl),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }, {
                label: 'Sipariş Sayısı',
                data: salesTrendData.map(item => item.orders),
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
});
</script>
@endpush
