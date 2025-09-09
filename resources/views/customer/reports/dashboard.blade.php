@extends('layouts.app')

@section('title', 'Raporlarım')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-line"></i> Raporlarım
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Özet Kartları -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3>{{ $userOrders->total_orders ?? 0 }}</h3>
                                    <p>Toplam Sipariş</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3>{{ number_format($userOrders->total_spent_tl ?? 0, 0) }} ₺</h3>
                                    <p>Toplam Harcama</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-lira-sign"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{ number_format($userOrders->avg_order_value_tl ?? 0, 0) }} ₺</h3>
                                    <p>Ortalama Sipariş</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-6">
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3>{{ $userOrders->last_order_date ? \Carbon\Carbon::parse($userOrders->last_order_date)->diffForHumans() : 'Hiç' }}</h3>
                                    <p>Son Sipariş</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Aylık Trend Grafiği -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Son 12 Aylık Sipariş Trendi</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="monthlyTrendChart" height="100"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- En Çok Sipariş Verilen Ürünler -->
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">En Çok Sipariş Verilen Ürünler</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Ürün</th>
                                                    <th>Marka</th>
                                                    <th>Miktar</th>
                                                    <th>Sipariş</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($topProducts as $product)
                                                <tr>
                                                    <td>{{ $product->name }}</td>
                                                    <td>{{ $product->brand }}</td>
                                                    <td>{{ $product->total_quantity }}</td>
                                                    <td>{{ $product->order_count }}</td>
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
                                    <h3 class="card-title">Son Siparişlerim</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Sipariş No</th>
                                                    <th>Tarih</th>
                                                    <th>Tutar</th>
                                                    <th>Durum</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($recentOrders as $order)
                                                <tr>
                                                    <td>{{ $order->order_number }}</td>
                                                    <td>{{ $order->created_at->format('d.m.Y') }}</td>
                                                    <td>{{ number_format($order->total_tl, 2) }} ₺</td>
                                                    <td>
                                                        <span class="badge badge-{{ $order->status_color }}">
                                                            {{ $order->status_label }}
                                                        </span>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hızlı Erişim Linkleri -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Detaylı Raporlar</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-6">
                                            <a href="{{ route('customer.reports.orders') }}" class="btn btn-outline-primary btn-block">
                                                <i class="fas fa-list"></i> Sipariş Geçmişi
                                            </a>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <a href="{{ route('customer.reports.spending') }}" class="btn btn-outline-success btn-block">
                                                <i class="fas fa-chart-pie"></i> Harcama Analizi
                                            </a>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <a href="{{ route('customer.reports.favorites') }}" class="btn btn-outline-warning btn-block">
                                                <i class="fas fa-heart"></i> Favori Ürünler
                                            </a>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <a href="{{ route('customer.orders') }}" class="btn btn-outline-info btn-block">
                                                <i class="fas fa-shopping-bag"></i> Tüm Siparişler
                                            </a>
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
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Aylık trend grafiği
    const monthlyTrendData = @json($monthlyTrend);
    const ctx = document.getElementById('monthlyTrendChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: monthlyTrendData.map(item => {
                const date = new Date(item.month + '-01');
                return date.toLocaleDateString('tr-TR', { year: 'numeric', month: 'short' });
            }),
            datasets: [{
                label: 'Sipariş Sayısı',
                data: monthlyTrendData.map(item => item.order_count),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }, {
                label: 'Harcama (₺)',
                data: monthlyTrendData.map(item => item.total_spent_tl),
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
