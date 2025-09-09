@extends('layouts.app')

@section('title', 'Harcama Analizi')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie"></i> Harcama Analizi
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Filtreler -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('customer.reports.spending') }}" class="form-inline">
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
                            </form>
                        </div>
                    </div>

                    <!-- Harcama Trendi Grafiği -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Harcama Trendi</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="spendingTrendChart" height="100"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kategori ve Marka Grafikleri -->
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Kategori Bazlı Harcama</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="categorySpendingChart"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Marka Bazlı Harcama</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="brandSpendingChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kategori Detayları -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Kategori Detayları</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Kategori</th>
                                                    <th>Sipariş Sayısı</th>
                                                    <th>Toplam Miktar</th>
                                                    <th>Toplam Harcama (₺)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($categorySpending as $category)
                                                <tr>
                                                    <td>{{ $category->category }}</td>
                                                    <td>{{ number_format($category->order_count) }}</td>
                                                    <td>{{ number_format($category->total_quantity) }}</td>
                                                    <td>{{ number_format($category->total_spent_tl, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Marka Detayları -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Marka Detayları</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Marka</th>
                                                    <th>Sipariş Sayısı</th>
                                                    <th>Toplam Miktar</th>
                                                    <th>Toplam Harcama (₺)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($brandSpending as $brand)
                                                <tr>
                                                    <td>{{ $brand->brand }}</td>
                                                    <td>{{ number_format($brand->order_count) }}</td>
                                                    <td>{{ number_format($brand->total_quantity) }}</td>
                                                    <td>{{ number_format($brand->total_spent_tl, 2) }}</td>
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
    // Harcama trendi grafiği
    const spendingTrendData = @json($spendingTrend);
    const ctx = document.getElementById('spendingTrendChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: spendingTrendData.map(item => item.period),
            datasets: [{
                label: 'Harcama (₺)',
                data: spendingTrendData.map(item => item.total_spent_tl),
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }, {
                label: 'Sipariş Sayısı',
                data: spendingTrendData.map(item => item.order_count),
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

    // Kategori harcama grafiği
    const categorySpendingData = @json($categorySpending);
    const categoryCtx = document.getElementById('categorySpendingChart').getContext('2d');
    
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: categorySpendingData.map(item => item.category),
            datasets: [{
                data: categorySpendingData.map(item => item.total_spent_tl),
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Marka harcama grafiği
    const brandSpendingData = @json($brandSpending);
    const brandCtx = document.getElementById('brandSpendingChart').getContext('2d');
    
    new Chart(brandCtx, {
        type: 'doughnut',
        data: {
            labels: brandSpendingData.map(item => item.brand),
            datasets: [{
                data: brandSpendingData.map(item => item.total_spent_tl),
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40'
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
