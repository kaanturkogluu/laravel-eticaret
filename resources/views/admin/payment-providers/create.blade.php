@extends('layouts.admin')

@section('title', 'Yeni Ödeme Sağlayıcısı - Admin Panel')
@section('page-title', 'Yeni Ödeme Sağlayıcısı')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-plus me-2"></i>Yeni Ödeme Sağlayıcısı Ekle
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.payment-providers.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <!-- Temel Bilgiler -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>Temel Bilgiler
                            </h6>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Sağlayıcı Adı *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="code" class="form-label">Sağlayıcı Kodu *</label>
                                <select class="form-control @error('code') is-invalid @enderror" 
                                        id="code" name="code" required onchange="updateProviderInfo()">
                                    <option value="">Sağlayıcı seçin</option>
                                    @foreach($supportedProviders as $providerCode)
                                        <option value="{{ $providerCode }}" {{ old('code') == $providerCode ? 'selected' : '' }}>
                                            {{ ucfirst($providerCode) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Açıklama</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="logo_url" class="form-label">Logo URL</label>
                                <input type="url" class="form-control @error('logo_url') is-invalid @enderror" 
                                       id="logo_url" name="logo_url" value="{{ old('logo_url') }}">
                                @error('logo_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Ayarlar -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-cog me-2"></i>Ayarlar
                            </h6>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                           value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Aktif
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="test_mode" name="test_mode" 
                                           value="1" {{ old('test_mode', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="test_mode">
                                        Test Modu
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Sıralama</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                       id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                                @error('sort_order')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Desteklenen Para Birimleri -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-coins me-2"></i>Desteklenen Para Birimleri *
                            </h6>
                            <div class="row">
                                @foreach($currencies as $currency)
                                <div class="col-md-3 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="currency_{{ $currency }}" name="supported_currencies[]" 
                                               value="{{ $currency }}" 
                                               {{ in_array($currency, old('supported_currencies', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="currency_{{ $currency }}">
                                            {{ $currency }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @error('supported_currencies')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Desteklenen Ödeme Yöntemleri -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-credit-card me-2"></i>Desteklenen Ödeme Yöntemleri *
                            </h6>
                            <div class="row">
                                @foreach($paymentMethods as $method => $label)
                                <div class="col-md-3 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="method_{{ $method }}" name="supported_payment_methods[]" 
                                               value="{{ $method }}" 
                                               {{ in_array($method, old('supported_payment_methods', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="method_{{ $method }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @error('supported_payment_methods')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Tutar Limitleri -->
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-chart-line me-2"></i>Tutar Limitleri
                            </h6>
                            
                            <div class="mb-3">
                                <label for="min_amount" class="form-label">Minimum Tutar</label>
                                <input type="number" class="form-control @error('min_amount') is-invalid @enderror" 
                                       id="min_amount" name="min_amount" value="{{ old('min_amount') }}" 
                                       step="0.01" min="0">
                                @error('min_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="max_amount" class="form-label">Maksimum Tutar</label>
                                <input type="number" class="form-control @error('max_amount') is-invalid @enderror" 
                                       id="max_amount" name="max_amount" value="{{ old('max_amount') }}" 
                                       step="0.01" min="0">
                                @error('max_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-percentage me-2"></i>Komisyon Ayarları
                            </h6>
                            
                            <div class="mb-3">
                                <label for="commission_rate" class="form-label">Komisyon Oranı (%)</label>
                                <input type="number" class="form-control @error('commission_rate') is-invalid @enderror" 
                                       id="commission_rate" name="commission_rate" value="{{ old('commission_rate', 0) }}" 
                                       step="0.01" min="0" max="100">
                                @error('commission_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="commission_fixed" class="form-label">Sabit Komisyon (₺)</label>
                                <input type="number" class="form-control @error('commission_fixed') is-invalid @enderror" 
                                       id="commission_fixed" name="commission_fixed" value="{{ old('commission_fixed', 0) }}" 
                                       step="0.01" min="0">
                                @error('commission_fixed')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.payment-providers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Geri Dön
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function updateProviderInfo() {
    const code = document.getElementById('code').value;
    const nameField = document.getElementById('name');
    const descriptionField = document.getElementById('description');
    
    if (code) {
        // Provider koduna göre otomatik bilgi doldur
        const providerInfo = {
            'payu': {
                name: 'PayU',
                description: 'PayU ödeme sistemi - Kredi kartı ve banka havalesi desteği'
            },
            'sipay': {
                name: 'Sipay',
                description: 'Sipay ödeme sistemi - Türkiye\'nin güvenilir ödeme çözümü'
            },
            'mokapos': {
                name: 'Mokapos',
                description: 'Mokapos ödeme sistemi - Modern ödeme teknolojileri'
            },
            'paynet': {
                name: 'Paynet',
                description: 'Paynet ödeme sistemi - Hızlı ve güvenli ödeme'
            },
            'odeal': {
                name: 'Ödeal',
                description: 'Ödeal ödeme sistemi - Türkiye\'nin önde gelen ödeme sağlayıcısı'
            },
            'papara': {
                name: 'Papara',
                description: 'Papara ödeme sistemi - Dijital cüzdan çözümü'
            },
            'paycell': {
                name: 'Paycell',
                description: 'Paycell ödeme sistemi - Turkcell\'in ödeme çözümü'
            },
            'hepsipay': {
                name: 'Hepsipay',
                description: 'Hepsipay ödeme sistemi - Hepsiburada\'nın ödeme çözümü'
            },
            'esnekpos': {
                name: 'Esnekpos',
                description: 'Esnekpos ödeme sistemi - Esnek ödeme çözümleri'
            },
            'ininal': {
                name: 'Ininal',
                description: 'Ininal ödeme sistemi - Dijital cüzdan ve ödeme kartı'
            },
            'paytrek': {
                name: 'Paytrek',
                description: 'Paytrek ödeme sistemi - Güvenilir ödeme teknolojileri'
            }
        };
        
        if (providerInfo[code]) {
            nameField.value = providerInfo[code].name;
            descriptionField.value = providerInfo[code].description;
        }
    } else {
        nameField.value = '';
        descriptionField.value = '';
    }
}
</script>
@endsection
