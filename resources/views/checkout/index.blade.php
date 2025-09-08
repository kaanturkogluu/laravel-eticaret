@extends('layouts.app')

@section('title', 'Ödeme - Basital.com')

@section('content')
<div class="container-fluid px-0">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">Ana Sayfa</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Sepet</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Ödeme</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>Ödeme Bilgileri
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('checkout.store') }}" method="POST">
                            @csrf
                            
                            <!-- Müşteri Bilgileri -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-user me-2"></i>Müşteri Bilgileri
                                    </h5>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="customer_name" class="form-label">Ad Soyad *</label>
                                    <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                           id="customer_name" name="customer_name" 
                                           value="{{ old('customer_name', $user->name ?? '') }}" required>
                                    @error('customer_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="customer_email" class="form-label">E-posta *</label>
                                    <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                                           id="customer_email" name="customer_email" 
                                           value="{{ old('customer_email', $user->email ?? '') }}" required>
                                    @error('customer_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="customer_phone" class="form-label">Telefon</label>
                                    <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" 
                                           id="customer_phone" name="customer_phone" 
                                           value="{{ old('customer_phone', $user->phone ?? '') }}">
                                    @error('customer_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Teslimat Adresi -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-truck me-2"></i>Teslimat Adresi
                                    </h5>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="shipping_address" class="form-label">Adres *</label>
                                    <textarea class="form-control @error('shipping_address') is-invalid @enderror" 
                                              id="shipping_address" name="shipping_address" rows="3" required>{{ old('shipping_address', $user->address ?? '') }}</textarea>
                                    @error('shipping_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="shipping_city" class="form-label">Şehir</label>
                                    <input type="text" class="form-control @error('shipping_city') is-invalid @enderror" 
                                           id="shipping_city" name="shipping_city" 
                                           value="{{ old('shipping_city') }}">
                                    @error('shipping_city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="shipping_district" class="form-label">İlçe</label>
                                    <input type="text" class="form-control @error('shipping_district') is-invalid @enderror" 
                                           id="shipping_district" name="shipping_district" 
                                           value="{{ old('shipping_district') }}">
                                    @error('shipping_district')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="shipping_postal_code" class="form-label">Posta Kodu</label>
                                    <input type="text" class="form-control @error('shipping_postal_code') is-invalid @enderror" 
                                           id="shipping_postal_code" name="shipping_postal_code" 
                                           value="{{ old('shipping_postal_code') }}">
                                    @error('shipping_postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Fatura Adresi -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="checkbox" id="different_billing" 
                                               onchange="toggleBillingAddress()">
                                        <label class="form-check-label" for="different_billing">
                                            Fatura adresi teslimat adresinden farklı
                                        </label>
                                    </div>
                                </div>
                                <div id="billing_address_section" style="display: none;">
                                    <div class="col-12 mb-3">
                                        <label for="billing_address" class="form-label">Fatura Adresi</label>
                                        <textarea class="form-control @error('billing_address') is-invalid @enderror" 
                                                  id="billing_address" name="billing_address" rows="3">{{ old('billing_address') }}</textarea>
                                        @error('billing_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="billing_city" class="form-label">Şehir</label>
                                        <input type="text" class="form-control @error('billing_city') is-invalid @enderror" 
                                               id="billing_city" name="billing_city" 
                                               value="{{ old('billing_city') }}">
                                        @error('billing_city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="billing_district" class="form-label">İlçe</label>
                                        <input type="text" class="form-control @error('billing_district') is-invalid @enderror" 
                                               id="billing_district" name="billing_district" 
                                               value="{{ old('billing_district') }}">
                                        @error('billing_district')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label for="billing_postal_code" class="form-label">Posta Kodu</label>
                                        <input type="text" class="form-control @error('billing_postal_code') is-invalid @enderror" 
                                               id="billing_postal_code" name="billing_postal_code" 
                                               value="{{ old('billing_postal_code') }}">
                                        @error('billing_postal_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Ödeme Yöntemi -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-credit-card me-2"></i>Ödeme Yöntemi
                                    </h5>
                                </div>
                                <div class="col-12">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="credit_card" value="credit_card" checked onchange="loadPaymentProviders()">
                                        <label class="form-check-label" for="credit_card">
                                            <i class="fas fa-credit-card me-2"></i>Kredi Kartı
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="bank_transfer" value="bank_transfer" onchange="loadPaymentProviders()">
                                        <label class="form-check-label" for="bank_transfer">
                                            <i class="fas fa-university me-2"></i>Banka Havalesi
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="wallet" value="wallet" onchange="loadPaymentProviders()">
                                        <label class="form-check-label" for="wallet">
                                            <i class="fas fa-wallet me-2"></i>Cüzdan
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input" type="radio" name="payment_method" 
                                               id="cash_on_delivery" value="cash_on_delivery" onchange="loadPaymentProviders()">
                                        <label class="form-check-label" for="cash_on_delivery">
                                            <i class="fas fa-money-bill-wave me-2"></i>Kapıda Ödeme
                                        </label>
                                    </div>
                                    @error('payment_method')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Ödeme Sağlayıcıları -->
                            <div class="row mb-4" id="payment-providers-section" style="display: none;">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-shield-alt me-2"></i>Ödeme Sağlayıcısı Seçin
                                    </h5>
                                    <div id="payment-providers-list" class="row">
                                        <!-- Payment providers will be loaded here -->
                                    </div>
                                    <input type="hidden" name="payment_provider" id="selected_payment_provider">
                                    @error('payment_provider')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Taksit Seçenekleri -->
                            <div class="row mb-4" id="installment-section" style="display: none;">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-calendar-alt me-2"></i>Taksit Seçenekleri
                                    </h5>
                                    <div class="row" id="installment-options">
                                        <!-- Installment options will be loaded here -->
                                    </div>
                                    <input type="hidden" name="installment" id="selected_installment" value="1">
                                </div>
                            </div>

                            <!-- Notlar -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <label for="notes" class="form-label">Sipariş Notları</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3" 
                                              placeholder="Siparişinizle ilgili özel notlarınız...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-check me-2"></i>Siparişi Tamamla
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Sipariş Özeti -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-receipt me-2"></i>Sipariş Özeti
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($cartItems as $item)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                @if($item->product->images->first())
                                    <img src="{{ $item->product->images->first()->resim_url }}" 
                                         alt="{{ $item->product->ad }}" 
                                         class="me-2" style="width: 50px; height: 50px; object-fit: cover;">
                                @endif
                                <div>
                                    <h6 class="mb-0">{{ $item->product->ad }}</h6>
                                    <small class="text-muted">Adet: {{ $item->quantity }}</small>
                                </div>
                            </div>
                            <div class="text-end">
                                <strong>{{ $item->formatted_total_price }}</strong>
                            </div>
                        </div>
                        @endforeach

                        <hr>
                        
                        <!-- Kupon Bilgileri -->
                        @if($appliedCoupon)
                        <div class="alert alert-success mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $appliedCoupon['name'] }}</strong>
                                    <br><small>{{ $appliedCoupon['code'] }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="text-success">-{{ number_format($discountAmount, 2) }} ₺</span>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Kargo:</span>
                            <span class="text-success">{{ $hasFreeShipping ? 'Ücretsiz (Kupon)' : 'Ücretsiz' }}</span>
                        </div>
                        <hr>
                        @php
                            $currencyGroups = $cartItems->groupBy('product.doviz');
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
                            
                            <hr class="my-3">
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>TL Karşılığı Ara Toplam:</span>
                                <span>{{ number_format($cartTotalInTry, 2) }} ₺</span>
                            </div>
                            @if($discountAmount > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-success">İndirim:</span>
                                <span class="text-success">-{{ number_format($discountAmount, 2) }} ₺</span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between mb-3">
                                <strong>TL Karşılığı Toplam:</strong>
                                <strong class="text-success">{{ number_format($finalTotal, 2) }} ₺</strong>
                            </div>
                        @else
                            {{-- Tek para birimi durumunda normal gösterim --}}
                            <div class="d-flex justify-content-between mb-2">
                                <span>Ara Toplam:</span>
                                <span>{{ number_format($cartTotal, 2) }} {{ $cartItems->first() ? $cartItems->first()->product->getCurrencySymbol() : '₺' }}</span>
                            </div>
                            @if($discountAmount > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-success">İndirim:</span>
                                <span class="text-success">-{{ number_format($discountAmount, 2) }} ₺</span>
                            </div>
                            @endif
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Toplam:</strong>
                                <strong class="text-primary">{{ number_format($finalTotal, 2) }} {{ $cartItems->first() ? $cartItems->first()->product->getCurrencySymbol() : '₺' }}</strong>
                            </div>
                            
                            @if($cartItems->first() && $cartItems->first()->product->doviz !== 'TRY')
                            <div class="d-flex justify-content-between mb-3">
                                <strong>TL Karşılığı:</strong>
                                <strong class="text-success">{{ number_format($finalTotal, 2) }} ₺</strong>
                            </div>
                            @endif
                        @endif
                        
                        <a href="{{ route('cart.index') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-arrow-left me-2"></i>Sepete Dön
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleBillingAddress() {
    const checkbox = document.getElementById('different_billing');
    const section = document.getElementById('billing_address_section');
    
    if (checkbox.checked) {
        section.style.display = 'block';
    } else {
        section.style.display = 'none';
    }
}

// Payment providers yükleme
function loadPaymentProviders() {
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
    const providersSection = document.getElementById('payment-providers-section');
    const providersList = document.getElementById('payment-providers-list');
    const installmentSection = document.getElementById('installment-section');
    
    // Kapıda ödeme için provider gerekmez
    if (paymentMethod === 'cash_on_delivery') {
        providersSection.style.display = 'none';
        installmentSection.style.display = 'none';
        document.getElementById('selected_payment_provider').value = '';
        return;
    }
    
    // Loading göster
    providersList.innerHTML = '<div class="col-12 text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Yükleniyor...</span></div></div>';
    providersSection.style.display = 'block';
    
    // API'den provider'ları al
    fetch(`/payment/providers?payment_method=${paymentMethod}&currency=TRY&amount={{ $cartTotalInTry }}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data.length > 0) {
                displayPaymentProviders(data.data);
            } else {
                providersList.innerHTML = '<div class="col-12"><div class="alert alert-warning">Bu ödeme yöntemi için uygun sağlayıcı bulunamadı.</div></div>';
            }
        })
        .catch(error => {
            console.error('Error loading payment providers:', error);
            providersList.innerHTML = '<div class="col-12"><div class="alert alert-danger">Ödeme sağlayıcıları yüklenirken hata oluştu.</div></div>';
        });
}

// Payment providers göster
function displayPaymentProviders(providers) {
    const providersList = document.getElementById('payment-providers-list');
    let html = '';
    
    providers.forEach(provider => {
        const commissionText = provider.commission_amount > 0 ? 
            `<small class="text-muted">+${provider.commission_amount.toFixed(2)} ₺ komisyon</small>` : 
            '<small class="text-success">Komisyonsuz</small>';
        
        html += `
            <div class="col-md-6 mb-3">
                <div class="card payment-provider-card" onclick="selectPaymentProvider('${provider.code}', this)">
                    <div class="card-body text-center">
                        <img src="${provider.logo_url}" alt="${provider.name}" class="mb-2" style="height: 40px; object-fit: contain;" 
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="d-flex align-items-center justify-content-center mb-2" style="height: 40px; display: none;">
                            <i class="fas fa-credit-card text-muted" style="font-size: 24px;"></i>
                        </div>
                        <h6 class="card-title">${provider.name}</h6>
                        <p class="card-text small">${provider.description || ''}</p>
                        ${commissionText}
                        ${provider.test_mode ? '<span class="badge bg-warning">Test Modu</span>' : ''}
                    </div>
                </div>
            </div>
        `;
    });
    
    providersList.innerHTML = html;
}

// Payment provider seç
function selectPaymentProvider(providerCode, element) {
    // Tüm kartları seçili olmayan duruma getir
    document.querySelectorAll('.payment-provider-card').forEach(card => {
        card.classList.remove('border-primary', 'bg-light');
    });
    
    // Seçili kartı işaretle
    element.classList.add('border-primary', 'bg-light');
    
    // Hidden input'a değeri ata
    document.getElementById('selected_payment_provider').value = providerCode;
    
    // Taksit seçeneklerini yükle (kredi kartı için)
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
    if (paymentMethod === 'credit_card') {
        loadInstallmentOptions(providerCode);
    } else {
        document.getElementById('installment-section').style.display = 'none';
    }
}

// Taksit seçeneklerini yükle
function loadInstallmentOptions(providerCode) {
    const installmentSection = document.getElementById('installment-section');
    const installmentOptions = document.getElementById('installment-options');
    
    installmentSection.style.display = 'block';
    
    // Basit taksit seçenekleri (gerçek uygulamada API'den gelecek)
    const installments = [1, 2, 3, 6, 9, 12];
    let html = '';
    
    installments.forEach(installment => {
        const isSelected = installment === 1 ? 'checked' : '';
        html += `
            <div class="col-md-2 mb-2">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="installment_option" 
                           id="installment_${installment}" value="${installment}" ${isSelected} 
                           onchange="selectInstallment(${installment})">
                    <label class="form-check-label" for="installment_${installment}">
                        ${installment === 1 ? 'Peşin' : installment + ' Taksit'}
                    </label>
                </div>
            </div>
        `;
    });
    
    installmentOptions.innerHTML = html;
    document.getElementById('selected_installment').value = '1';
}

// Taksit seç
function selectInstallment(installment) {
    document.getElementById('selected_installment').value = installment;
}

// Sayfa yüklendiğinde payment providers'ı yükle
document.addEventListener('DOMContentLoaded', function() {
    loadPaymentProviders();
});
</script>

<style>
.payment-provider-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.payment-provider-card:hover {
    border-color: #0d6efd;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.payment-provider-card.border-primary {
    border-color: #0d6efd !important;
    background-color: #f8f9fa !important;
}
</style>
@endsection
