@extends('layouts.app')

@section('title', 'Siparişlerim - Basital.com')

@section('content')
<div class="container-fluid px-0">
    <div class="container">
        <div class="row">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>Hesap Menüsü
                </h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="{{ route('customer.dashboard') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a href="{{ route('customer.profile') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-user-edit me-2"></i>Profil Bilgileri
                </a>
                <a href="{{ route('customer.orders') }}" class="list-group-item list-group-item-action active">
                    <i class="fas fa-shopping-bag me-2"></i>Siparişlerim
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-shopping-bag me-2"></i>Siparişlerim
                </h4>
            </div>
            <div class="card-body">
                @if($orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Sipariş No</th>
                                    <th>Tarih</th>
                                    <th>Durum</th>
                                    <th>Ödeme</th>
                                    <th>Tutar</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
                                <tr>
                                    <td>
                                        <strong>{{ $order->order_number }}</strong>
                                    </td>
                                    <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $order->status_color }}">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $order->payment_status_color }}">
                                            {{ $order->payment_status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <!-- <div class="text-muted small">
                                            {{ $order->formatted_total }}
                                        </div> -->
                                        <div>
                                            <strong>{{ $order->formatted_total_tl }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('orders.show', $order->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>Detay
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $orders->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                        <h4 class="text-muted">Henüz sipariş vermediniz</h4>
                        <p class="text-muted">İlk siparişinizi vermek için ürünlerimizi inceleyin.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-primary">
                            <i class="fas fa-shopping-bag me-2"></i>Alışverişe Başla
                        </a>
                    </div>
                @endif
                <!--
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Sipariş No</th>
                                <th>Tarih</th>
                                <th>Ürünler</th>
                                <th>Tutar</th>
                                <th>Durum</th>
                                <th>İşlemler</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    Henüz sipariş vermediniz
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                -->
            </div>
        </div>
    </div>
    </div>
</div>
@endsection
