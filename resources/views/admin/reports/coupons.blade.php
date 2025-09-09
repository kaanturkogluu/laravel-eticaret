@extends('layouts.admin')

@section('title', 'Kupon Raporları')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-ticket-alt"></i> Kupon Raporları
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Filtreler -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('admin.reports.coupons') }}" class="form-inline">
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
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filtrele
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Kupon Kullanım Grafiği -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Kupon Kullanım Dağılımı</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="couponUsageChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kupon Kullanım Detayları -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Kupon Kullanım Detayları</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Kupon Kodu</th>
                                                    <th>Kullanım Sayısı</th>
                                                    <th>Toplam İndirim (₺)</th>
                                                    <th>Toplam Satış (₺)</th>
                                                    <th>Ortalama İndirim (₺)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($couponUsageReport as $coupon)
                                                <tr>
                                                    <td>
                                                        <span class="badge badge-success">{{ $coupon->coupon_code }}</span>
                                                    </td>
                                                    <td>{{ number_format($coupon->usage_count) }}</td>
                                                    <td>{{ number_format($coupon->total_discount_tl, 2) }}</td>
                                                    <td>{{ number_format($coupon->total_sales_tl, 2) }}</td>
                                                    <td>{{ number_format($coupon->avg_discount_tl, 2) }}</td>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Kupon kullanım grafiği
    const couponUsageData = @json($couponUsageReport);
    const ctx = document.getElementById('couponUsageChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: couponUsageData.map(item => item.coupon_code),
            datasets: [{
                label: 'Kullanım Sayısı',
                data: couponUsageData.map(item => item.usage_count),
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'Toplam İndirim (₺)',
                data: couponUsageData.map(item => item.total_discount_tl),
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
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
