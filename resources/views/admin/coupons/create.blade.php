@extends('layouts.admin')

@section('title', 'Yeni Kupon Oluştur - Basital.com')
@section('page-title', 'Yeni Kupon Oluştur')

@section('content')

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Kupon Bilgileri</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.coupons.store') }}" id="couponForm">
                    @csrf
                    
                    <div class="row">
                        <!-- Kupon Kodu -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kupon Kodu *</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                       name="code" value="{{ old('code') }}" required maxlength="50">
                                <button type="button" class="btn btn-outline-secondary" id="generateCode">
                                    <i class="fas fa-random"></i>
                                </button>
                            </div>
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Büyük harflerle otomatik dönüştürülür.</small>
                        </div>

                        <!-- Kupon Adı -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kupon Adı *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" required maxlength="255">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Açıklama -->
                        <div class="col-12 mb-3">
                            <label class="form-label">Açıklama</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- İndirim Tipi -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">İndirim Tipi *</label>
                            <select class="form-control @error('type') is-invalid @enderror" name="type" id="discountType" required>
                                <option value="">Seçiniz</option>
                                <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Yüzde (%)</option>
                                <option value="fixed_amount" {{ old('type') == 'fixed_amount' ? 'selected' : '' }}>Sabit Tutar (TL)</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- İndirim Değeri -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">İndirim Değeri *</label>
                            <div class="input-group">
                                <input type="number" class="form-control @error('value') is-invalid @enderror" 
                                       name="value" value="{{ old('value') }}" step="0.01" min="0" required>
                                <span class="input-group-text" id="valueUnit">TL</span>
                            </div>
                            @error('value')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Minimum Tutar -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Minimum Sepet Tutarı (TL)</label>
                            <input type="number" class="form-control @error('minimum_amount') is-invalid @enderror" 
                                   name="minimum_amount" value="{{ old('minimum_amount') }}" step="0.01" min="0">
                            @error('minimum_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Bu tutarın üzerinde alışveriş yapılması gerekir.</small>
                        </div>

                        <!-- Maksimum İndirim -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Maksimum İndirim Tutarı (TL)</label>
                            <input type="number" class="form-control @error('maximum_discount') is-invalid @enderror" 
                                   name="maximum_discount" value="{{ old('maximum_discount') }}" step="0.01" min="0">
                            @error('maximum_discount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Yüzde indirimlerde maksimum limit.</small>
                        </div>

                        <!-- Kullanım Limitleri -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Toplam Kullanım Limiti</label>
                            <input type="number" class="form-control @error('usage_limit') is-invalid @enderror" 
                                   name="usage_limit" value="{{ old('usage_limit') }}" min="1">
                            @error('usage_limit')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Boş bırakılırsa sınırsız kullanım.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kullanıcı Başına Kullanım Limiti</label>
                            <input type="number" class="form-control @error('usage_limit_per_user') is-invalid @enderror" 
                                   name="usage_limit_per_user" value="{{ old('usage_limit_per_user') }}" min="1">
                            @error('usage_limit_per_user')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Bir kullanıcının kaç kez kullanabileceği.</small>
                        </div>

                        <!-- Tarih Aralığı -->
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Başlangıç Tarihi</label>
                            <input type="datetime-local" class="form-control @error('starts_at') is-invalid @enderror" 
                                   name="starts_at" value="{{ old('starts_at') }}">
                            @error('starts_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bitiş Tarihi</label>
                            <input type="datetime-local" class="form-control @error('expires_at') is-invalid @enderror" 
                                   name="expires_at" value="{{ old('expires_at') }}">
                            @error('expires_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Özel Seçenekler -->
                        <div class="col-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="free_shipping" value="1" 
                                       id="freeShipping" {{ old('free_shipping') ? 'checked' : '' }}>
                                <label class="form-check-label" for="freeShipping">
                                    Ücretsiz Kargo
                                </label>
                            </div>
                        </div>

                        <div class="col-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" value="1" 
                                       id="isActive" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="isActive">
                                    Aktif
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>İptal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Kupon Oluştur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Yardım Paneli -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Yardım</h5>
            </div>
            <div class="card-body">
                <h6>İndirim Tipleri:</h6>
                <ul class="list-unstyled">
                    <li><strong>Yüzde:</strong> Sepet tutarının belirli yüzdesi kadar indirim</li>
                    <li><strong>Sabit Tutar:</strong> Belirli miktarda TL indirim</li>
                </ul>

                <h6 class="mt-3">Kullanım Limitleri:</h6>
                <ul class="list-unstyled">
                    <li><strong>Toplam Limit:</strong> Kuponun toplam kaç kez kullanılabileceği</li>
                    <li><strong>Kullanıcı Başına:</strong> Bir kullanıcının kaç kez kullanabileceği</li>
                </ul>

                <h6 class="mt-3">Özel Seçenekler:</h6>
                <ul class="list-unstyled">
                    <li><strong>Ücretsiz Kargo:</strong> Kargo ücretini sıfırlar</li>
                    <li><strong>Aktif:</strong> Kuponun kullanılabilir olup olmadığı</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // İndirim tipi değiştiğinde birim güncelle
    $('#discountType').on('change', function() {
        var type = $(this).val();
        var $unit = $('#valueUnit');
        var $valueInput = $('input[name="value"]');
        
        if (type === 'percentage') {
            $unit.text('%');
            $valueInput.attr('max', '100');
        } else {
            $unit.text('TL');
            $valueInput.removeAttr('max');
        }
    });

    // Kupon kodu oluştur
    $('#generateCode').on('click', function() {
        var $button = $(this);
        var $input = $('input[name="code"]');
        
        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: '{{ route("admin.coupons.generate-code") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                length: 8
            },
            success: function(response) {
                $input.val(response.code);
                $button.prop('disabled', false).html('<i class="fas fa-random"></i>');
            },
            error: function() {
                $button.prop('disabled', false).html('<i class="fas fa-random"></i>');
                showAlert('danger', 'Kupon kodu oluşturulurken bir hata oluştu.');
            }
        });
    });

    // Form gönderimi
    $('#couponForm').on('submit', function(e) {
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Oluşturuluyor...');
    });

    // Kupon kodunu büyük harfe çevir
    $('input[name="code"]').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });
});
</script>
@endsection
