@extends('layouts.app')

@section('title', 'Sipariş Geçmişi Raporu')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list"></i> Sipariş Geçmişi Raporu
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Filtreler -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('customer.reports.orders') }}" class="form-inline">
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

                    <!-- Özet İstatistikler -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-shopping-cart"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Toplam Sipariş</span>
                                    <span class="info-box-number">{{ $orderStats->total_orders ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-lira-sign"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Toplam Harcama</span>
                                    <span class="info-box-number">{{ number_format($orderStats->total_spent_tl ?? 0, 0) }} ₺</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-chart-line"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Ortalama Sipariş</span>
                                    <span class="info-box-number">{{ number_format($orderStats->avg_order_value_tl ?? 0, 0) }} ₺</span>
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
                                    <span class="info-box-number">{{ $orderStats->delivered_orders ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sipariş Listesi -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Sipariş Detayları</h3>
                                </div>
                                <div class="card-body">
                                    @if($orders->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Sipariş No</th>
                                                        <th>Tarih</th>
                                                        <th>Ürünler</th>
                                                        <th>Tutar</th>
                                                        <th>Durum</th>
                                                        <th>Ödeme</th>
                                                        <th>İşlemler</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($orders as $order)
                                                    <tr>
                                                        <td>{{ $order->order_number }}</td>
                                                        <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                                                        <td>
                                                            <small>
                                                                @foreach($order->items->take(2) as $item)
                                                                    {{ $item->product->ad ?? 'Ürün Bulunamadı' }}<br>
                                                                @endforeach
                                                                @if($order->items->count() > 2)
                                                                    <em>+{{ $order->items->count() - 2 }} ürün daha</em>
                                                                @endif
                                                            </small>
                                                        </td>
                                                        <td>{{ number_format($order->total_tl, 2) }} ₺</td>
                                                        <td>
                                                            <span class="badge badge-{{ $order->status_color }}">
                                                                {{ $order->status_label }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-{{ $order->payment_status_color }}">
                                                                {{ $order->payment_status_label }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-eye"></i> Detay
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        
                                        <!-- Sayfalama -->
                                        <div class="d-flex justify-content-center">
                                            {{ $orders->appends(request()->query())->links() }}
                                        </div>
                                    @else
                                        <div class="alert alert-info text-center">
                                            <i class="fas fa-info-circle"></i>
                                            Seçilen tarih aralığında sipariş bulunamadı.
                                        </div>
                                    @endif
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
