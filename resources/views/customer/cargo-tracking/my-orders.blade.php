@extends('layouts.app')

@section('title', 'Kargo Takip - Siparişlerim')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kargo Takip - Siparişlerim</h3>
                </div>
                <div class="card-body">
                    @if($orders->count() > 0)
                        <div class="row">
                            @foreach($orders as $order)
                                <div class="col-md-6 mb-4">
                                    <div class="card border">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">{{ $order->order_number }}</h6>
                                            <span class="badge bg-{{ $order->status_color }}">
                                                {{ $order->status_label }}
                                            </span>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <small class="text-muted">Kargo Şirketi:</small>
                                                    <p class="mb-1">{{ $order->cargoCompany->name }}</p>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">Takip No:</small>
                                                    <p class="mb-1">
                                                        @if($order->cargo_tracking_number)
                                                            <code>{{ $order->cargo_tracking_number }}</code>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-6">
                                                    <small class="text-muted">Sipariş Tarihi:</small>
                                                    <p class="mb-1">{{ $order->created_at->format('d.m.Y H:i') }}</p>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">Toplam:</small>
                                                    <p class="mb-1">{{ $order->formatted_total }}</p>
                                                </div>
                                            </div>

                                            @if($order->cargo_tracking_number)
                                                <div class="mt-3">
                                                    <a href="{{ route('customer.cargo-tracking.order', $order->id) }}" 
                                                       class="btn btn-primary btn-sm">
                                                        <i class="fas fa-truck"></i> Takip Et
                                                    </a>
                                                    
                                                    @if($order->cargoCompany->tracking_url)
                                                        <a href="{{ $order->cargoCompany->getTrackingUrl($order->cargo_tracking_number) }}" 
                                                           target="_blank" class="btn btn-outline-primary btn-sm">
                                                            <i class="fas fa-external-link-alt"></i> Kargo Sitesi
                                                        </a>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Henüz kargoya verilmiş siparişiniz bulunmuyor.</h5>
                            <p class="text-muted">Siparişleriniz kargoya verildiğinde burada görüntüleyebilirsiniz.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary">
                                <i class="fas fa-shopping-cart"></i> Alışverişe Devam Et
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
