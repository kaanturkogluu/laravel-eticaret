@extends('emails.layout')

@section('content')
<h2>Hoş Geldiniz!</h2>

<p>Merhaba {{ $user->name }},</p>

<p>{{ config('app.name') }} ailesine hoş geldiniz! Hesabınız başarıyla oluşturulmuştur.</p>

<div class="order-details">
    <h3>Hesap Bilgileriniz</h3>
    <div class="order-item">
        <strong>E-posta:</strong>
        <span>{{ $user->email }}</span>
    </div>
    <div class="order-item">
        <strong>Kayıt Tarihi:</strong>
        <span>{{ $user->created_at ? $user->created_at->format('d.m.Y H:i') : now()->format('d.m.Y H:i') }}</span>
    </div>
</div>

<h3>Neler Yapabilirsiniz?</h3>
<ul>
    <li>Geniş ürün yelpazemizden alışveriş yapabilirsiniz</li>
    <li>Siparişlerinizi takip edebilirsiniz</li>
    <li>Favori ürünlerinizi kaydedebilirsiniz</li>
    <li>Kampanyalarımızdan haberdar olabilirsiniz</li>
    <li>Güvenli ödeme seçenekleri ile alışveriş yapabilirsiniz</li>
</ul>

<p>İlk alışverişinizde %10 indirim kazanmak için aşağıdaki butona tıklayın:</p>

<a href="{{ config('app.url') }}/products" class="button">Alışverişe Başla</a>

<div class="order-details">
    <h3>Özel Kampanyalar</h3>
    <p>🎉 Yeni üyelerimize özel %10 indirim!</p>
    <p>🚚 150 TL ve üzeri alışverişlerde ücretsiz kargo!</p>
    <p>💳 Güvenli ödeme seçenekleri</p>
</div>

<p>Herhangi bir sorunuz olması durumunda müşteri hizmetlerimizle iletişime geçebilirsiniz.</p>

<p>İyi alışverişler dileriz!</p>

<p>Saygılarımızla,<br>
{{ config('app.name') }} Ekibi</p>
@endsection
