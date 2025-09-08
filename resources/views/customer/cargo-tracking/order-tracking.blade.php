@extends('layouts.app')

@section('title', 'Kargo Takip - ' . $order->order_number)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kargo Takip - {{ $order->order_number }}</h3>
                </div>
                <div class="card-body">
                    <!-- Sipariş Bilgileri -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Sipariş Bilgileri</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Sipariş No:</strong></td>
                                    <td>{{ $order->order_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Sipariş Tarihi:</strong></td>
                                    <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Durum:</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $order->status_color }}">
                                            {{ $order->status_label }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Toplam:</strong></td>
                                    <td>{{ $order->formatted_total }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Kargo Bilgileri</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Kargo Şirketi:</strong></td>
                                    <td>{{ $order->cargoCompany->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Takip No:</strong></td>
                                    <td>
                                        @if($order->cargo_tracking_number)
                                            <code>{{ $order->cargo_tracking_number }}</code>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @if($order->cargo_created_at)
                                    <tr>
                                        <td><strong>Kargo Oluşturulma:</strong></td>
                                        <td>{{ $order->cargo_created_at->format('d.m.Y H:i') }}</td>
                                    </tr>
                                @endif
                                @if($order->cargo_picked_up_at)
                                    <tr>
                                        <td><strong>Kargo Alınma:</strong></td>
                                        <td>{{ $order->cargo_picked_up_at->format('d.m.Y H:i') }}</td>
                                    </tr>
                                @endif
                                @if($order->cargo_delivered_at)
                                    <tr>
                                        <td><strong>Teslim Tarihi:</strong></td>
                                        <td>{{ $order->cargo_delivered_at->format('d.m.Y H:i') }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>

                    <!-- Sipariş Kalemleri -->
                    <div class="mb-4">
                        <h5>Sipariş Kalemleri</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Ürün</th>
                                        <th>Miktar</th>
                                        <th>Birim Fiyat</th>
                                        <th>Toplam</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($order->items as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($item->product->images->count() > 0)
                                                        <img src="{{ asset('storage/' . $item->product->images->first()->image_path) }}" 
                                                             alt="{{ $item->product_name }}" class="me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                    @endif
                                                    <div>
                                                        <strong>{{ $item->product_name }}</strong>
                                                        @if($item->product_code)
                                                            <br><small class="text-muted">{{ $item->product_code }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ $item->formatted_unit_price }}</td>
                                            <td>{{ $item->formatted_total_price }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Kargo Takip Geçmişi -->
                    @if($trackingHistory->count() > 0)
                        <div class="mb-4">
                            <h5>Kargo Takip Geçmişi</h5>
                            <div class="timeline">
                                @foreach($trackingHistory as $event)
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-{{ $event->status_color }}"></div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-title">{{ $event->status_label }}</h6>
                                            <p class="timeline-text">
                                                @if($event->description)
                                                    {{ $event->description }}
                                                @endif
                                                @if($event->location)
                                                    <br><small class="text-muted"><i class="fas fa-map-marker-alt"></i> {{ $event->location }}</small>
                                                @endif
                                            </p>
                                            <small class="text-muted">{{ $event->formatted_event_date }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Bu sipariş için henüz kargo takip bilgisi bulunmuyor.
                        </div>
                    @endif

                    <!-- Kargo Şirketi Linki -->
                    @if($order->cargo_tracking_number && $order->cargoCompany->tracking_url)
                        <div class="text-center">
                            <a href="{{ $order->cargoCompany->getTrackingUrl($order->cargo_tracking_number) }}" 
                               target="_blank" class="btn btn-primary">
                                <i class="fas fa-external-link-alt"></i> Kargo Şirketi Sitesinde Görüntüle
                            </a>
                        </div>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('customer.cargo-tracking.orders') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Geri
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}

.timeline-title {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-text {
    margin-bottom: 5px;
}
</style>
@endsection
