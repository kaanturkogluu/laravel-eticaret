@extends('layouts.admin')

@section('title', 'Kargo Raporları')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-shipping-fast"></i> Kargo Raporları
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Filtreler -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('admin.reports.cargo') }}" class="form-inline">
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

                    <!-- Kargo Durumu Grafiği -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Kargo Durumu Dağılımı</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="cargoStatusChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kargo Durumu Detayları -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Kargo Durumu Detayları</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Durum</th>
                                                    <th>Sayı</th>
                                                    <th>Toplam Değer (₺)</th>
                                                    <th>Ortalama Değer (₺)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($cargoStatusReport as $status)
                                                <tr>
                                                    <td>
                                                        <span class="badge badge-info">{{ $status->status }}</span>
                                                    </td>
                                                    <td>{{ number_format($status->count) }}</td>
                                                    <td>{{ number_format($status->total_value_tl, 2) }}</td>
                                                    <td>{{ number_format($status->total_value_tl / $status->count, 2) }}</td>
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
    // Kargo durumu grafiği
    const cargoStatusData = @json($cargoStatusReport);
    const ctx = document.getElementById('cargoStatusChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: cargoStatusData.map(item => item.status),
            datasets: [{
                data: cargoStatusData.map(item => item.count),
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
