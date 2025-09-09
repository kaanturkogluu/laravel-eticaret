@extends('emails.layout')

@section('content')
<h2>📦 Kargo Durumu Güncellendi!</h2>

<p>Merhaba {{ $order->customer_name ?? 'Değerli Müşterimiz' }},</p>

<p>Siparişinizin kargo durumu güncellenmiştir. Güncel durum bilgileri aşağıda yer almaktadır.</p>

<div class="order-details">
    <h3>📦 Sipariş Bilgileri</h3>
    <div class="order-item">
        <strong>Sipariş Numarası:</strong>
        <span>{{ $order->order_number ?? $order->id }}</span>
    </div>
    <div class="order-item">
        <strong>Kargo Takip Numarası:</strong>
        <span style="font-weight: bold; color: #007bff;">{{ $cargoTracking->tracking_number ?? 'TAKIP-001' }}</span>
    </div>
</div>

<div class="order-details" style="background-color: #e3f2fd; border-left: 4px solid #2196f3;">
    <h3 style="color: #1976d2;">🚛 Kargo Durum Güncellemesi</h3>
    <div class="order-item">
        <strong>Kargo Şirketi:</strong>
        <span>{{ $cargoTracking->cargo_company ?? 'Kargo Şirketi' }}</span>
    </div>
    <div class="order-item">
        <strong>Önceki Durum:</strong>
        <span class="status-badge status-{{ $oldStatus ?? 'created' }}">{{ ucfirst($oldStatus ?? 'Oluşturuldu') }}</span>
    </div>
    <div class="order-item">
        <strong>Yeni Durum:</strong>
        <span class="status-badge status-{{ $newStatus ?? 'in_transit' }}">{{ ucfirst($newStatus ?? 'Yolda') }}</span>
    </div>
    <div class="order-item">
        <strong>Güncelleme Tarihi:</strong>
        <span>{{ now()->format('d.m.Y H:i') }}</span>
    </div>
</div>

@php
$statusMessages = [
    'created' => 'Kargo oluşturuldu ve hazırlanıyor.',
    'picked_up' => 'Kargo alındı ve dağıtım merkezine gönderiliyor.',
    'in_transit' => 'Kargo yolda, hedef şehre doğru ilerliyor.',
    'out_for_delivery' => 'Kargo dağıtım aracında, teslimat için yolda.',
    'delivered' => 'Kargo başarıyla teslim edildi.',
    'exception' => 'Kargo teslimatında bir sorun oluştu.',
    'returned' => 'Kargo iade edildi.'
];

$currentMessage = $statusMessages[$newStatus] ?? 'Kargo durumu güncellendi.';
@endphp

<div class="order-details" style="background-color: #f8f9fa; border-left: 4px solid #6c757d;">
    <h3 style="color: #495057;">📋 Durum Açıklaması</h3>
    <p style="margin: 0; font-size: 16px; line-height: 1.6;">{{ $currentMessage }}</p>
</div>

@if($newStatus === 'delivered')
<div class="order-details" style="background-color: #d4edda; border-left: 4px solid #28a745;">
    <h3 style="color: #155724;">🎉 Teslimat Tamamlandı!</h3>
    <p style="margin: 0;">Siparişiniz başarıyla teslim edilmiştir. Memnuniyetiniz bizim için çok önemli!</p>
</div>
@elseif($newStatus === 'out_for_delivery')
<div class="order-details" style="background-color: #fff3cd; border-left: 4px solid #ffc107;">
    <h3 style="color: #856404;">🚚 Teslimat Yaklaşıyor!</h3>
    <p style="margin: 0;">Kargo dağıtım aracında, yakında kapınızda olacak. Evde bulunmanız önerilir.</p>
</div>
@elseif($newStatus === 'exception')
<div class="order-details" style="background-color: #f8d7da; border-left: 4px solid #dc3545;">
    <h3 style="color: #721c24;">⚠️ Teslimat Sorunu</h3>
    <p style="margin: 0;">Kargo teslimatında bir sorun oluştu. En kısa sürede sizinle iletişime geçeceğiz.</p>
</div>
@endif

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ config('app.url') }}/cargo-tracking" class="button">
        🔍 Kargo Takip Et
    </a>
</div>

<div class="order-details">
    <h3>🏠 Teslimat Adresi</h3>
    <p><strong>Ad Soyad:</strong> {{ $order->customer_name ?? 'Müşteri' }}</p>
    <p><strong>Adres:</strong> {{ $order->shipping_address ?? 'Teslimat Adresi' }}</p>
    @if($order->customer_phone)
    <p><strong>Telefon:</strong> {{ $order->customer_phone }}</p>
    @endif
</div>

<div class="order-details" style="background-color: #e7f3ff; border-left: 4px solid #007bff;">
    <h3 style="color: #004085;">ℹ️ Bilgilendirme</h3>
    <ul style="margin: 0; padding-left: 20px;">
        <li>Kargo durumunu takip etmek için yukarıdaki butonu kullanabilirsiniz</li>
        <li>Kargo şirketinin web sitesinden detaylı bilgi alabilirsiniz</li>
        <li>Herhangi bir sorunuz olursa bizimle iletişime geçebilirsiniz</li>
    </ul>
</div>

<p>Teşekkürler,<br>
{{ config('app.name') }} Ekibi</p>
@endsection
