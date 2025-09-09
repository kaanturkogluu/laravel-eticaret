@extends('emails.layout')

@section('content')
<h2>Siparişiniz Onaylandı!</h2>

<p>Merhaba {{ $user->name }},</p>

<p>Siparişiniz başarıyla alınmış ve onaylanmıştır. Sipariş detaylarınız aşağıda yer almaktadır:</p>

<div class="order-details">
    <h3>Sipariş Bilgileri</h3>
    <div class="order-item">
        <strong>Sipariş Numarası:</strong>
        <span>#{{ $order->id }}</span>
    </div>
    <div class="order-item">
        <strong>Sipariş Tarihi:</strong>
        <span>{{ $order->created_at ? $order->created_at->format('d.m.Y H:i') : now()->format('d.m.Y H:i') }}</span>
    </div>
    <div class="order-item">
        <strong>Sipariş Durumu:</strong>
        <span class="status-badge status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
    </div>
    <div class="order-item">
        <strong>Toplam Tutar:</strong>
        <span>{{ number_format($order->total_amount, 2) }} {{ $order->currency }}</span>
    </div>
</div>

<h3>Sipariş Edilen Ürünler</h3>
@foreach($order->items as $item)
<div class="order-item">
    <div>
        <strong>{{ $item->product->name ?? $item->product_name ?? 'Ürün' }}</strong><br>
        <small>Miktar: {{ $item->quantity }}</small>
    </div>
    <div>
        {{ number_format($item->price * $item->quantity, 2) }} {{ $order->currency ?? 'TL' }}
    </div>
</div>
@endforeach

<div class="order-details">
    <h3>Teslimat Bilgileri</h3>
    <p><strong>Ad Soyad:</strong> {{ $order->shipping_name }}</p>
    <p><strong>Adres:</strong> {{ $order->shipping_address }}</p>
    <p><strong>Şehir:</strong> {{ $order->shipping_city }}</p>
    <p><strong>Posta Kodu:</strong> {{ $order->shipping_postal_code }}</p>
    <p><strong>Telefon:</strong> {{ $order->shipping_phone }}</p>
</div>

<p>Siparişinizin durumunu takip etmek için aşağıdaki butona tıklayabilirsiniz:</p>

<a href="{{ route('orders.show', $order->id) }}" class="button">Siparişi Görüntüle</a>

<p>Herhangi bir sorunuz olması durumunda bizimle iletişime geçebilirsiniz.</p>

<p>Teşekkürler,<br>
{{ config('app.name') }} Ekibi</p>
@endsection
