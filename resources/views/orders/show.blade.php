@extends('layouts.app')

@section('title', 'Sipariş Detayı - Basital.com')

@section('content')
<div class="container-fluid px-0">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Ana Sayfa</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('customer.orders') }}">Siparişlerim</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Sipariş Detayı</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <i class="fas fa-receipt me-2"></i>Sipariş Detayı
                            </h4>
                            <span class="badge bg-light text-dark fs-6">
                                {{ $order->order_number }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Sipariş Durumu -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">Sipariş Durumu</h6>
                                <span class="badge bg-{{ $order->status_color }} fs-6">
                                    {{ $order->status_label }}
                                </span>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Ödeme Durumu</h6>
                                <span class="badge bg-{{ $order->payment_status_color }} fs-6">
                                    {{ $order->payment_status_label }}
                                </span>
                            </div>
                        </div>

                        <!-- Sipariş Tarihi -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted">Sipariş Tarihi</h6>
                                <p class="mb-0">{{ $order->created_at->format('d.m.Y H:i') }}</p>
                            </div>
                            @if($order->paid_at)
                            <div class="col-md-6">
                                <h6 class="text-muted">Ödeme Tarihi</h6>
                                <p class="mb-0">{{ $order->paid_at->format('d.m.Y H:i') }}</p>
                            </div>
                            @endif
                        </div>

                        <!-- Sipariş Kalemleri -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-shopping-bag me-2"></i>Sipariş Kalemleri
                                </h5>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Ürün</th>
                                                <th>Kod</th>
                                                <th>Adet</th>
                                                <th>Birim Fiyat</th>
                                                <th>Toplam</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($order->items as $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($item->product && $item->product->images->first())
                                                            <img src="{{ $item->product->images->first()->resim_url }}" 
                                                                 alt="{{ $item->product_name }}" 
                                                                 class="me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                                        @endif
                                                        <div>
                                                            <h6 class="mb-0">{{ $item->product_name }}</h6>
                                                            @if($item->product)
                                                                <small class="text-muted">
                                                                    <a href="{{ route('products.show', $item->product->kod) }}" 
                                                                       class="text-decoration-none">
                                                                        Ürünü Görüntüle
                                                                    </a>
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $item->product_code }}</td>
                                                <td>{{ $item->quantity }}</td>
                                                <td>{{ $item->formatted_unit_price }}</td>
                                                <td><strong>{{ $item->formatted_total_price }}</strong></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Fiyat Özeti -->
                        <div class="row mb-4">
                            <div class="col-md-6 offset-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        @php
                                            $currencyGroups = $order->items->groupBy('currency');
                                            $hasMultipleCurrencies = $currencyGroups->count() > 1;
                                        @endphp
                                        
                                        @if($hasMultipleCurrencies)
                                            {{-- Çoklu para birimi durumunda her para birimi için ayrı toplam --}}
                                            @foreach($currencyGroups as $currency => $items)
                                                @php
                                                    $currencyTotal = $items->sum('total_price');
                                                    $currencySymbol = \App\Models\Product::getCurrencySymbolFor($currency);
                                                @endphp
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>{{ $currency }} Toplam:</span>
                                                    <span>{{ number_format($currencyTotal, 2) }} {{ $currencySymbol }}</span>
                                                </div>
                                            @endforeach
                                            
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Kargo:</span>
                                                <span class="text-success">
                                                    {{ $order->formatted_shipping_cost }}
                                                </span>
                                            </div>
                                            
                                            <hr>
                                            
                                            <div class="d-flex justify-content-between mb-2">
                                                <strong>Toplam:</strong>
                                                <strong class="text-primary">{{ $order->formatted_total }}</strong>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between">
                                                <strong>TL Karşılığı:</strong>
                                                <strong class="text-success">{{ $order->formatted_total_tl }}</strong>
                                            </div>
                                        @else
                                            {{-- Tek para birimi durumunda normal gösterim --}}
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Kargo:</span>
                                                <span class="text-success">
                                                    {{ $order->formatted_shipping_cost }}
                                                </span>
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-between mb-2">
                                                <strong>Toplam:</strong>
                                                <strong class="text-primary">{{ $order->formatted_total }}</strong>
                                            </div>
                                            
                                            @if($order->currency !== 'TRY')
                                            <div class="d-flex justify-content-between">
                                                <strong>TL Karşılığı:</strong>
                                                <strong class="text-success">{{ $order->formatted_total_tl }}</strong>
                                            </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Müşteri Bilgileri -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>Müşteri Bilgileri
                                </h5>
                                <p><strong>Ad Soyad:</strong> {{ $order->customer_name }}</p>
                                <p><strong>E-posta:</strong> {{ $order->customer_email }}</p>
                                @if($order->customer_phone)
                                    <p><strong>Telefon:</strong> {{ $order->customer_phone }}</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-truck me-2"></i>Teslimat Adresi
                                </h5>
                                <p>{{ $order->shipping_address }}</p>
                                @if($order->shipping_city)
                                    <p>{{ $order->shipping_city }}</p>
                                @endif
                                @if($order->shipping_district)
                                    <p>{{ $order->shipping_district }}</p>
                                @endif
                                @if($order->shipping_postal_code)
                                    <p>Posta Kodu: {{ $order->shipping_postal_code }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Ödeme Bilgileri -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-credit-card me-2"></i>Ödeme Bilgileri
                                </h5>
                                <p><strong>Ödeme Yöntemi:</strong> 
                                    @switch($order->payment_method)
                                        @case('credit_card')
                                            Kredi Kartı
                                            @break
                                        @case('bank_transfer')
                                            Banka Havalesi
                                            @break
                                        @case('cash_on_delivery')
                                            Kapıda Ödeme
                                            @break
                                        @default
                                            {{ $order->payment_method }}
                                    @endswitch
                                </p>
                                @if($order->payment_reference)
                                    <p><strong>Ödeme Referansı:</strong> {{ $order->payment_reference }}</p>
                                @endif
                            </div>
                            @if($order->tracking_number)
                            <div class="col-md-6">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-shipping-fast me-2"></i>Kargo Bilgileri
                                </h5>
                                <p><strong>Takip Numarası:</strong> {{ $order->tracking_number }}</p>
                                @if($order->shipped_at)
                                    <p><strong>Kargoya Verilme Tarihi:</strong> {{ $order->shipped_at->format('d.m.Y H:i') }}</p>
                                @endif
                            </div>
                            @endif
                        </div>

                        <!-- Notlar -->
                        @if($order->notes)
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-sticky-note me-2"></i>Sipariş Notları
                                </h5>
                                <p class="bg-light p-3 rounded">{{ $order->notes }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Sipariş İşlemleri -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-2 flex-wrap">
                                    @if(in_array($order->status, ['pending', 'processing']))
                                        <form action="{{ route('orders.cancel', $order->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger" 
                                                    onclick="return confirm('Bu siparişi iptal etmek istediğinizden emin misiniz?')">
                                                <i class="fas fa-times me-2"></i>Siparişi İptal Et
                                            </button>
                                        </form>
                                    @endif

                                    @if($order->status === 'shipped')
                                        <form action="{{ route('orders.delivered', $order->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success" 
                                                    onclick="return confirm('Siparişinizi teslim aldığınızı onaylıyor musunuz?')">
                                                <i class="fas fa-check me-2"></i>Teslim Aldım
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('customer.orders') }}" class="btn btn-outline-primary">
                                        <i class="fas fa-arrow-left me-2"></i>Siparişlerime Dön
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
