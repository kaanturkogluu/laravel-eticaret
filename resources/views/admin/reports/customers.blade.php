@extends('layouts.admin')

@section('title', 'Müşteri Raporları')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i> Müşteri Raporları
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Filtreler -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('admin.reports.customers') }}" class="form-inline">
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
                                <a href="{{ route('admin.reports.export', ['type' => 'customers', 'start_date' => $startDate, 'end_date' => $endDate]) }}" 
                                   class="btn btn-success ml-2">
                                    <i class="fas fa-download"></i> Excel İndir
                                </a>
                            </form>
                        </div>
                    </div>

                    <!-- En Değerli Müşteriler -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">En Değerli Müşteriler</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Müşteri</th>
                                                    <th>E-posta</th>
                                                    <th>Kayıt Tarihi</th>
                                                    <th>Toplam Sipariş</th>
                                                    <th>Toplam Harcama (₺)</th>
                                                    <th>Ortalama Sipariş (₺)</th>
                                                    <th>Son Sipariş</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($topCustomers as $customer)
                                                <tr>
                                                    <td>{{ $customer->name }}</td>
                                                    <td>{{ $customer->email }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($customer->registration_date)->format('d.m.Y') }}</td>
                                                    <td>{{ number_format($customer->total_orders) }}</td>
                                                    <td>{{ number_format($customer->total_spent_tl, 2) }}</td>
                                                    <td>{{ number_format($customer->avg_order_value_tl, 2) }}</td>
                                                    <td>{{ $customer->last_order_date ? \Carbon\Carbon::parse($customer->last_order_date)->format('d.m.Y') : '-' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Müşteri Detayları -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Müşteri Detayları</h3>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Müşteri</th>
                                                    <th>E-posta</th>
                                                    <th>Kayıt Tarihi</th>
                                                    <th>Toplam Sipariş</th>
                                                    <th>Toplam Harcama (₺)</th>
                                                    <th>Ortalama Sipariş (₺)</th>
                                                    <th>Son Sipariş</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($customerReport as $customer)
                                                <tr>
                                                    <td>{{ $customer->name }}</td>
                                                    <td>{{ $customer->email }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($customer->registration_date)->format('d.m.Y') }}</td>
                                                    <td>{{ number_format($customer->total_orders) }}</td>
                                                    <td>{{ number_format($customer->total_spent_tl, 2) }}</td>
                                                    <td>{{ number_format($customer->avg_order_value_tl, 2) }}</td>
                                                    <td>{{ $customer->last_order_date ? \Carbon\Carbon::parse($customer->last_order_date)->format('d.m.Y') : '-' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Müşteri Segmentasyonu -->
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Müşteri Segmentasyonu</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="description-block border-right">
                                                <span class="description-percentage text-success">
                                                    <i class="fas fa-caret-up"></i> {{ $customerReport->where('total_orders', '>=', 10)->count() }}
                                                </span>
                                                <h5 class="description-header">VIP Müşteriler</h5>
                                                <span class="description-text">10+ sipariş</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="description-block">
                                                <span class="description-percentage text-warning">
                                                    <i class="fas fa-caret-up"></i> {{ $customerReport->where('total_orders', '>=', 5)->where('total_orders', '<', 10)->count() }}
                                                </span>
                                                <h5 class="description-header">Sadık Müşteriler</h5>
                                                <span class="description-text">5-9 sipariş</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="description-block border-right">
                                                <span class="description-percentage text-info">
                                                    <i class="fas fa-caret-up"></i> {{ $customerReport->where('total_orders', '>=', 2)->where('total_orders', '<', 5)->count() }}
                                                </span>
                                                <h5 class="description-header">Düzenli Müşteriler</h5>
                                                <span class="description-text">2-4 sipariş</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="description-block">
                                                <span class="description-percentage text-danger">
                                                    <i class="fas fa-caret-up"></i> {{ $customerReport->where('total_orders', 1)->count() }}
                                                </span>
                                                <h5 class="description-header">Tek Sipariş</h5>
                                                <span class="description-text">1 sipariş</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Harcama Dağılımı</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="description-block border-right">
                                                <span class="description-percentage text-success">
                                                    <i class="fas fa-caret-up"></i> {{ $customerReport->where('total_spent_tl', '>=', 10000)->count() }}
                                                </span>
                                                <h5 class="description-header">Yüksek Harcama</h5>
                                                <span class="description-text">10.000+ ₺</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="description-block">
                                                <span class="description-percentage text-warning">
                                                    <i class="fas fa-caret-up"></i> {{ $customerReport->where('total_spent_tl', '>=', 5000)->where('total_spent_tl', '<', 10000)->count() }}
                                                </span>
                                                <h5 class="description-header">Orta Harcama</h5>
                                                <span class="description-text">5.000-9.999 ₺</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="description-block border-right">
                                                <span class="description-percentage text-info">
                                                    <i class="fas fa-caret-up"></i> {{ $customerReport->where('total_spent_tl', '>=', 1000)->where('total_spent_tl', '<', 5000)->count() }}
                                                </span>
                                                <h5 class="description-header">Düşük Harcama</h5>
                                                <span class="description-text">1.000-4.999 ₺</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="description-block">
                                                <span class="description-percentage text-danger">
                                                    <i class="fas fa-caret-up"></i> {{ $customerReport->where('total_spent_tl', '<', 1000)->count() }}
                                                </span>
                                                <h5 class="description-header">Minimal Harcama</h5>
                                                <span class="description-text">0-999 ₺</span>
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
</div>
@endsection
