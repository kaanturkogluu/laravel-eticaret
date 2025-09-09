@extends('emails.layout')

@section('content')
<h2>🎉 Favori Ürününüzde İndirim!</h2>

<p>Merhaba {{ $user->name }},</p>

<p>Favorilerinize eklediğiniz ürünün fiyatında güzel bir indirim oldu! Kaçırmayın!</p>

<div class="order-details">
    <h3>Ürün Bilgileri</h3>
    <div class="order-item">
        <strong>Ürün Adı:</strong>
        <span>{{ $product->ad }}</span>
    </div>
    <div class="order-item">
        <strong>Ürün Kodu:</strong>
        <span>{{ $product->kod }}</span>
    </div>
    <div class="order-item">
        <strong>Marka:</strong>
        <span>{{ $product->marka }}</span>
    </div>
    @if($product->kategori)
    <div class="order-item">
        <strong>Kategori:</strong>
        <span>{{ $product->kategori }}</span>
    </div>
    @endif
</div>

<div class="order-details" style="background-color: #e8f5e8; border-left: 4px solid #28a745;">
    <h3 style="color: #28a745;">💰 Fiyat Bilgileri</h3>
    <div class="order-item">
        <strong>Eski Fiyat:</strong>
        <span style="text-decoration: line-through; color: #666;">{{ number_format($oldPrice, 2) }} {{ $currencySymbol }}</span>
    </div>
    <div class="order-item">
        <strong>Yeni Fiyat:</strong>
        <span style="color: #28a745; font-weight: bold; font-size: 18px;">{{ number_format($newPrice, 2) }} {{ $currencySymbol }}</span>
    </div>
    <div class="order-item">
        <strong>İndirim Tutarı:</strong>
        <span style="color: #dc3545; font-weight: bold;">-{{ number_format($discountAmount, 2) }} {{ $currencySymbol }}</span>
    </div>
    <div class="order-item">
        <strong>İndirim Oranı:</strong>
        <span style="color: #dc3545; font-weight: bold;">%{{ number_format($discountPercentage, 1) }}</span>
    </div>
</div>

@if($product->aciklama)
<div class="order-details">
    <h3>Ürün Açıklaması</h3>
    <p>{{ Str::limit($product->aciklama, 200) }}</p>
</div>
@endif

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ config('app.url') }}/products/{{ $product->kod }}" class="button" style="background-color: #28a745; font-size: 16px; padding: 15px 30px;">
        🛒 Hemen Satın Al
    </a>
</div>

<div class="order-details" style="background-color: #fff3cd; border-left: 4px solid #ffc107;">
    <h3 style="color: #856404;">⚠️ Önemli Bilgiler</h3>
    <ul style="margin: 0; padding-left: 20px;">
        <li>Bu indirim sınırlı süre için geçerlidir</li>
        <li>Stok durumu değişebilir, acele edin!</li>
        <li>150 TL ve üzeri alışverişlerde ücretsiz kargo</li>
        <li>Güvenli ödeme seçenekleri mevcuttur</li>
    </ul>
</div>

<p>Bu ürünü favorilerinize eklediğiniz için teşekkür ederiz. Fiyat değişikliklerinden haberdar olmak için favori ürünlerinizi takip etmeye devam edin!</p>

<p>İyi alışverişler dileriz!</p>

<p>Saygılarımızla,<br>
{{ config('app.name') }} Ekibi</p>

<div style="text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
    <p style="font-size: 12px; color: #666;">
        Bu e-postayı almak istemiyorsanız, 
        <a href="{{ config('app.url') }}/favorites">favori ürünlerinizi</a> 
        güncelleyebilir veya 
        <a href="{{ config('app.url') }}/unsubscribe">abonelikten çıkabilirsiniz</a>.
    </p>
</div>
@endsection
