@extends('layouts.admin')

@section('title', 'Ürün Raporları')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-box"></i> Ürün Raporları
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Filtreler -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('admin.reports.products') }}" class="form-inline">
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
                                    <label for="limit" class="mr-2">Limit:</label>
                                    <select class="form-control" id="limit" name="limit">
                                        <option value="25" {{ $limit == 25 ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ $limit == 100 ? 'selected' : '' }}>100</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filtrele
                                </button>
                                <a href="{{ route('admin.reports.export', ['type' => 'products', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                                   class="btn btn-success ml-2">
                                    <i class="fas fa-download"></i> Excel İndir
                                </a>
                            </form>
                        </div>
                    </div>

                    <!-- Kategori ve Marka Grafikleri -->
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Kategori Bazlı Satış</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="categoryChart"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Marka Bazlı Satış</h3>
                                </div>
                                <div class="card-body">
                                    <canvas id="brandChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- En Çok Satan Ürünler -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">En Çok Satan Ürünler</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Kod</th>
                                                    <th>Ürün Adı</th>
                                                    <th>Marka</th>
                                                    <th>Kategori</th>
                                                    <th>Satılan Miktar</th>
                                                    <th>Toplam Satış (₺)</th>
                                                    <th>Sipariş Sayısı</th>
                                                    <th>Ortalama Fiyat (₺)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($productSalesReport as $product)
                                                <tr>
                                                    <td>{{ $product->kod }}</td>
                                                    <td>{{ $product->product_name }}</td>
                                                    <td>{{ $product->brand }}</td>
                                                    <td>{{ $product->category }}</td>
                                                    <td>{{ number_format($product->total_quantity) }}</td>
                                                    <td>{{ number_format($product->total_sales_tl, 2) }}</td>
                                                    <td>{{ number_format($product->order_count) }}</td>
                                                    <td>{{ number_format($product->avg_price_tl, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
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
                                                    <th>Toplam Satış (₺)</th>
                                                    <th>Ortalama Fiyat (₺)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($categorySalesReport as $category)
                                                <tr>
                                                    <td>{{ $category->category }}</td>
                                                    <td>{{ number_format($category->order_count) }}</td>
                                                    <td>{{ number_format($category->total_quantity) }}</td>
                                                    <td>{{ number_format($category->total_sales_tl, 2) }}</td>
                                                    <td>{{ number_format($category->avg_price_tl, 2) }}</td>
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
                    <div class="row mb-4">
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
                                                    <th>Toplam Satış (₺)</th>
                                                    <th>Ortalama Fiyat (₺)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($brandSalesReport as $brand)
                                                <tr>
                                                    <td>{{ $brand->brand }}</td>
                                                    <td>{{ number_format($brand->order_count) }}</td>
                                                    <td>{{ number_format($brand->total_quantity) }}</td>
                                                    <td>{{ number_format($brand->total_sales_tl, 2) }}</td>
                                                    <td>{{ number_format($brand->avg_price_tl, 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stok Durumu -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Stok Durumu Raporu</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Kategori</th>
                                                    <th>Marka</th>
                                                    <th>Toplam Ürün</th>
                                                    <th>Stokta</th>
                                                    <th>Az Stok</th>
                                                    <th>Stok Yok</th>
                                                    <th>Ortalama Stok</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($stockReport as $stock)
                                                <tr>
                                                    <td>{{ $stock->category }}</td>
                                                    <td>{{ $stock->brand }}</td>
                                                    <td>{{ number_format($stock->total_products) }}</td>
                                                    <td class="text-success">{{ number_format($stock->in_stock) }}</td>
                                                    <td class="text-warning">{{ number_format($stock->low_stock) }}</td>
                                                    <td class="text-danger">{{ number_format($stock->out_of_stock) }}</td>
                                                    <td>{{ number_format($stock->avg_stock, 1) }}</td>
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
    // Kategori grafiği
    const categoryData = @json($categorySalesReport);
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: categoryData.map(item => item.category),
            datasets: [{
                label: 'Satış (₺)',
                data: categoryData.map(item => item.total_sales_tl),
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Marka grafiği
    const brandData = @json($brandSalesReport);
    const brandCtx = document.getElementById('brandChart').getContext('2d');
    
    new Chart(brandCtx, {
        type: 'bar',
        data: {
            labels: brandData.map(item => item.brand),
            datasets: [{
                label: 'Satış (₺)',
                data: brandData.map(item => item.total_sales_tl),
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endpush
