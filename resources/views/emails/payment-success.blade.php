@extends('emails.layout')

@section('content')
<h2>Ödemeniz Başarıyla Alındı!</h2>

<p>Merhaba {{ $user->name }},</p>

<p>Siparişiniz için yaptığınız ödeme başarıyla işlenmiştir. Ödeme detaylarınız aşağıda yer almaktadır:</p>

<div class="order-details">
    <h3>Ödeme Bilgileri</h3>
    <div class="order-item">
        <strong>Sipariş Numarası:</strong>
        <span>#{{ $order->id }}</span>
    </div>
    <div class="order-item">
        <strong>Ödeme Tarihi:</strong>
        <span>{{ $payment->created_at ? $payment->created_at->format('d.m.Y H:i') : now()->format('d.m.Y H:i') }}</span>
    </div>
    <div class="order-item">
        <strong>Ödeme Tutarı:</strong>
        <span>{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</span>
    </div>
    <div class="order-item">
        <strong>Ödeme Yöntemi:</strong>
        <span>{{ $payment->paymentProvider->name }}</span>
    </div>
    <div class="order-item">
        <strong>İşlem Numarası:</strong>
        <span>{{ $payment->transaction_id }}</span>
    </div>
    <div class="order-item">
        <strong>Ödeme Durumu:</strong>
        <span class="status-badge status-confirmed">{{ ucfirst($payment->status) }}</span>
    </div>
</div>

<div class="order-details">
    <h3>Sipariş Özeti</h3>
    @foreach($order->items as $item)
    <div class="order-item">
        <div>
            <strong>{{ $item->product->name }}</strong><br>
            <small>Miktar: {{ $item->quantity }}</small>
        </div>
        <div>
            {{ number_format($item->price * $item->quantity, 2) }} {{ $order->currency }}
        </div>
    </div>
    @endforeach
    <div class="order-item" style="border-top: 2px solid #007bff; font-weight: bold;">
        <strong>Toplam:</strong>
        <span>{{ number_format($order->total_amount, 2) }} {{ $order->currency }}</span>
    </div>
</div>

<p>Siparişiniz şimdi hazırlanma aşamasına geçmiştir. Sipariş durumunuz hakkında güncellemeler e-posta ile size iletilecektir.</p>

<a href="{{ route('orders.show', $order->id) }}" class="button">Siparişi Görüntüle</a>

<p>Ödemeniz için teşekkür ederiz!</p>

<p>Saygılarımızla,<br>
{{ config('app.name') }} Ekibi</p>
@endsection
