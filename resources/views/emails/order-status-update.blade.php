@extends('emails.layout')

@section('content')
<h2>Sipariş Durumunuz Güncellendi</h2>

<p>Merhaba {{ $user->name }},</p>

<p>Siparişinizin durumu güncellenmiştir. Güncel durum bilgileri aşağıda yer almaktadır:</p>

<div class="order-details">
    <h3>Sipariş Bilgileri</h3>
    <div class="order-item">
        <strong>Sipariş Numarası:</strong>
        <span>#{{ $order->id }}</span>
    </div>
    <div class="order-item">
        <strong>Önceki Durum:</strong>
        <span class="status-badge status-{{ $oldStatus }}">{{ ucfirst($oldStatus) }}</span>
    </div>
    <div class="order-item">
        <strong>Yeni Durum:</strong>
        <span class="status-badge status-{{ $newStatus }}">{{ ucfirst($newStatus) }}</span>
    </div>
    <div class="order-item">
        <strong>Güncelleme Tarihi:</strong>
        <span>{{ now()->format('d.m.Y H:i') }}</span>
    </div>
</div>

@if($newStatus === 'shipped')
<div class="order-details">
    <h3>Kargo Bilgileri</h3>
    <p>Siparişiniz kargoya verilmiştir. Kargo takip numaranız:</p>
    <p><strong>{{ $order->tracking_number ?? 'Henüz atanmamış' }}</strong></p>
    @if($order->shipping_company)
    <p><strong>Kargo Şirketi:</strong> {{ $order->shipping_company }}</p>
    @endif
</div>
@endif

@if($newStatus === 'delivered')
<div class="order-details">
    <h3>Teslimat Bilgileri</h3>
    <p>Siparişiniz başarıyla teslim edilmiştir!</p>
    <p>Ürünlerinizden memnun kaldıysanız, değerlendirmenizi yapabilirsiniz.</p>
</div>
@endif

@if($newStatus === 'cancelled')
<div class="order-details">
    <h3>İptal Bilgileri</h3>
    <p>Siparişiniz iptal edilmiştir.</p>
    @if($order->cancellation_reason)
    <p><strong>İptal Nedeni:</strong> {{ $order->cancellation_reason }}</p>
    @endif
    <p>Ödemeniz varsa, 3-5 iş günü içinde hesabınıza iade edilecektir.</p>
</div>
@endif

<p>Siparişinizin detaylarını görüntülemek için aşağıdaki butona tıklayabilirsiniz:</p>

<a href="{{ route('orders.show', $order->id) }}" class="button">Siparişi Görüntüle</a>

<p>Herhangi bir sorunuz olması durumunda bizimle iletişime geçebilirsiniz.</p>

<p>Saygılarımızla,<br>
{{ config('app.name') }} Ekibi</p>
@endsection
