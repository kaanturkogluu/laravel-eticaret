@extends('emails.layout')

@section('content')
<h2>🚚 Siparişiniz Kargoya Verildi!</h2>

<p>Merhaba {{ $order->customer_name ?? 'Değerli Müşterimiz' }},</p>

<p>Siparişiniz başarıyla kargoya verilmiştir. Kargo takip bilgileriniz aşağıda yer almaktadır.</p>

<div class="order-details">
    <h3>📦 Sipariş Bilgileri</h3>
    <div class="order-item">
        <strong>Sipariş Numarası:</strong>
        <span>{{ $order->order_number ?? $order->id }}</span>
    </div>
    <div class="order-item">
        <strong>Sipariş Tarihi:</strong>
        <span>{{ $order->created_at ? $order->created_at->format('d.m.Y H:i') : now()->format('d.m.Y H:i') }}</span>
    </div>
    <div class="order-item">
        <strong>Toplam Tutar:</strong>
        <span>{{ number_format($order->total_amount ?? 0, 2) }} TL</span>
    </div>
</div>

<div class="order-details" style="background-color: #e8f5e8; border-left: 4px solid #28a745;">
    <h3 style="color: #28a745;">🚛 Kargo Takip Bilgileri</h3>
    <div class="order-item">
        <strong>Kargo Şirketi:</strong>
        <span>{{ $cargoTracking->cargo_company ?? 'Kargo Şirketi' }}</span>
    </div>
    <div class="order-item">
        <strong>Takip Numarası:</strong>
        <span style="font-weight: bold; color: #28a745;">{{ $cargoTracking->tracking_number ?? 'TAKIP-001' }}</span>
    </div>
    <div class="order-item">
        <strong>Kargo Durumu:</strong>
        <span class="status-badge status-shipped">{{ ucfirst($cargoTracking->status ?? 'Kargoya Verildi') }}</span>
    </div>
    @if($cargoTracking->shipped_at)
    <div class="order-item">
        <strong>Kargoya Verilme Tarihi:</strong>
        <span>{{ $cargoTracking->shipped_at->format('d.m.Y H:i') }}</span>
    </div>
    @endif
    @if($cargoTracking->estimated_delivery)
    <div class="order-item">
        <strong>Tahmini Teslimat:</strong>
        <span>{{ $cargoTracking->estimated_delivery->format('d.m.Y') }}</span>
    </div>
    @endif
</div>

@if(isset($order->items) && $order->items->count() > 0)
<h3>📋 Sipariş Kalemleri</h3>
@foreach($order->items as $item)
<div class="order-item">
    <div>
        <strong>{{ $item->product_name ?? 'Ürün' }}</strong><br>
        <small>Miktar: {{ $item->quantity ?? 1 }} adet</small>
    </div>
    <div>
        {{ number_format(($item->price ?? 0) * ($item->quantity ?? 1), 2) }} TL
    </div>
</div>
@endforeach
@endif

<div class="order-details">
    <h3>🏠 Teslimat Adresi</h3>
    <p><strong>Ad Soyad:</strong> {{ $order->customer_name ?? 'Müşteri' }}</p>
    <p><strong>Adres:</strong> {{ $order->shipping_address ?? 'Teslimat Adresi' }}</p>
    @if($order->customer_phone)
    <p><strong>Telefon:</strong> {{ $order->customer_phone }}</p>
    @endif
</div>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ config('app.url') }}/cargo-tracking" class="button" style="background-color: #28a745;">
        🔍 Kargo Takip Et
    </a>
</div>

<div class="order-details" style="background-color: #fff3cd; border-left: 4px solid #ffc107;">
    <h3 style="color: #856404;">ℹ️ Önemli Bilgiler</h3>
    <ul style="margin: 0; padding-left: 20px;">
        <li>Kargo takip numaranızı not almayı unutmayın</li>
        <li>Kargo şirketinin web sitesinden detaylı takip yapabilirsiniz</li>
        <li>Teslimat sırasında evde bulunmanız önerilir</li>
        <li>Herhangi bir sorunuz olursa bizimle iletişime geçebilirsiniz</li>
    </ul>
</div>

<p>Teşekkürler,<br>
{{ config('app.name') }} Ekibi</p>
@endsection
