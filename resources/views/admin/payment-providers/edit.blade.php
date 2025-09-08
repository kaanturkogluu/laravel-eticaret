@extends('layouts.admin')

@section('title', 'Ödeme Sağlayıcısı Düzenle - Admin Panel')
@section('page-title', 'Ödeme Sağlayıcısı Düzenle')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>{{ $paymentProvider->name }} Düzenle
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.payment-providers.update', $paymentProvider) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Temel Bilgiler -->
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-info-circle me-2"></i>Temel Bilgiler
                            </h6>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Sağlayıcı Adı *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', $paymentProvider->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="code" class="form-label">Sağlayıcı Kodu *</label>
                                <select class="form-control @error('code') is-invalid @enderror" 
                                        id="code" name="code" required>
                                    @foreach($supportedProviders as $providerCode)
                                        <option value="{{ $providerCode }}" 
                                                {{ old('code', $paymentProvider->code) == $providerCode ? 'selected' : '' }}>
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
                                          id="description" name="description" rows="3">{{ old('description', $paymentProvider->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="logo_url" class="form-label">Logo URL</label>
                                <input type="url" class="form-control @error('logo_url') is-invalid @enderror" 
                                       id="logo_url" name="logo_url" value="{{ old('logo_url', $paymentProvider->logo_url) }}">
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
                                           value="1" {{ old('is_active', $paymentProvider->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Aktif
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="test_mode" name="test_mode" 
                                           value="1" {{ old('test_mode', $paymentProvider->test_mode) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="test_mode">
                                        Test Modu
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Sıralama</label>
                                <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                       id="sort_order" name="sort_order" value="{{ old('sort_order', $paymentProvider->sort_order) }}" min="0">
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
                                               {{ in_array($currency, old('supported_currencies', $paymentProvider->supported_currencies ?? [])) ? 'checked' : '' }}>
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
                                               {{ in_array($method, old('supported_payment_methods', $paymentProvider->supported_payment_methods ?? [])) ? 'checked' : '' }}>
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
                                       id="min_amount" name="min_amount" value="{{ old('min_amount', $paymentProvider->min_amount) }}" 
                                       step="0.01" min="0">
                                @error('min_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="max_amount" class="form-label">Maksimum Tutar</label>
                                <input type="number" class="form-control @error('max_amount') is-invalid @enderror" 
                                       id="max_amount" name="max_amount" value="{{ old('max_amount', $paymentProvider->max_amount) }}" 
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
                                       id="commission_rate" name="commission_rate" value="{{ old('commission_rate', $paymentProvider->commission_rate) }}" 
                                       step="0.01" min="0" max="100">
                                @error('commission_rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="commission_fixed" class="form-label">Sabit Komisyon (₺)</label>
                                <input type="number" class="form-control @error('commission_fixed') is-invalid @enderror" 
                                       id="commission_fixed" name="commission_fixed" value="{{ old('commission_fixed', $paymentProvider->commission_fixed) }}" 
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
                            <i class="fas fa-save me-2"></i>Güncelle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
