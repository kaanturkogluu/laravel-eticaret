@extends('layouts.admin')

@section('title', 'Yeni Ürün Ekle')
@section('page-title', 'Yeni Ürün Ekle')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-plus me-2"></i>Yeni Ürün Ekle
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="ad" class="form-label">Ürün Adı <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('ad') is-invalid @enderror" 
                                       id="ad" name="ad" value="{{ old('ad') }}" required>
                                @error('ad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="kod" class="form-label">Ürün Kodu <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('kod') is-invalid @enderror" 
                                               id="kod" name="kod" value="{{ old('kod') }}" required>
                                        @error('kod')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="marka" class="form-label">Marka</label>
                                        <input type="text" class="form-control @error('marka') is-invalid @enderror" 
                                               id="marka" name="marka" value="{{ old('marka') }}">
                                        @error('marka')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="kategori" class="form-label">Kategori</label>
                                        <input type="text" class="form-control @error('kategori') is-invalid @enderror" 
                                               id="kategori" name="kategori" value="{{ old('kategori') }}">
                                        @error('kategori')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="doviz" class="form-label">Para Birimi <span class="text-danger">*</span></label>
                                        <select class="form-control @error('doviz') is-invalid @enderror" 
                                                id="doviz" name="doviz" required>
                                            <option value="">Seçiniz</option>
                                            <option value="TL" {{ old('doviz') === 'TL' ? 'selected' : '' }}>TL</option>
                                            <option value="USD" {{ old('doviz') === 'USD' ? 'selected' : '' }}>USD</option>
                                            <option value="EUR" {{ old('doviz') === 'EUR' ? 'selected' : '' }}>EUR</option>
                                        </select>
                                        @error('doviz')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Fiyat Ayarları -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-money-bill me-2"></i>Fiyat Ayarları</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="fiyat_ozel" class="form-label">Özel Fiyat <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control @error('fiyat_ozel') is-invalid @enderror" 
                                                       id="fiyat_ozel" name="fiyat_ozel" value="{{ old('fiyat_ozel') }}" 
                                                       min="0" step="0.01" required>
                                                @error('fiyat_ozel')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="fiyat_bayi" class="form-label">Bayii Fiyatı</label>
                                                <input type="number" class="form-control @error('fiyat_bayi') is-invalid @enderror" 
                                                       id="fiyat_bayi" name="fiyat_bayi" value="{{ old('fiyat_bayi') }}" 
                                                       min="0" step="0.01">
                                                @error('fiyat_bayi')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="fiyat_sk" class="form-label">SK Fiyatı</label>
                                                <input type="number" class="form-control @error('fiyat_sk') is-invalid @enderror" 
                                                       id="fiyat_sk" name="fiyat_sk" value="{{ old('fiyat_sk') }}" 
                                                       min="0" step="0.01">
                                                @error('fiyat_sk')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Kar Ayarları -->
                                    <div class="mt-4">
                                        <h6 class="text-primary">
                                            <i class="fas fa-chart-line me-2"></i>Kar Ayarları
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" id="profit_enabled" 
                                                               name="profit_enabled" value="1" 
                                                               {{ old('profit_enabled') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="profit_enabled">
                                                            Kar Hesaplaması Aktif
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="profit_type" class="form-label">Kar Türü</label>
                                                    <select class="form-select @error('profit_type') is-invalid @enderror" 
                                                            id="profit_type" name="profit_type">
                                                        <option value="0" {{ old('profit_type') == 0 ? 'selected' : '' }}>
                                                            Kar Yok
                                                        </option>
                                                        <option value="1" {{ old('profit_type') == 1 ? 'selected' : '' }}>
                                                            Yüzde Kar
                                                        </option>
                                                        <option value="2" {{ old('profit_type') == 2 ? 'selected' : '' }}>
                                                            Sabit Kar
                                                        </option>
                                                    </select>
                                                    @error('profit_type')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="profit_value" class="form-label">
                                                        <span id="profit_label">Kar Değeri</span>
                                                    </label>
                                                    <input type="number" class="form-control @error('profit_value') is-invalid @enderror" 
                                                           id="profit_value" name="profit_value" 
                                                           value="{{ old('profit_value') }}" 
                                                           min="0" step="0.01">
                                                    @error('profit_value')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Kar Önizleme -->
                                        <div class="alert alert-info" id="profit_preview" style="display: none;">
                                            <h6>Fiyat Önizleme:</h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>Orijinal Fiyat:</strong> 
                                                    <span id="original_price_display"></span>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Kar Dahil Fiyat:</strong> 
                                                    <span id="profit_price_display" class="text-success"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="miktar" class="form-label">Stok Miktarı <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('miktar') is-invalid @enderror" 
                                               id="miktar" name="miktar" value="{{ old('miktar') }}" 
                                               min="0" step="1" required>
                                        @error('miktar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="barkod" class="form-label">Barkod</label>
                                        <input type="text" class="form-control @error('barkod') is-invalid @enderror" 
                                               id="barkod" name="barkod" value="{{ old('barkod') }}">
                                        @error('barkod')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="aciklama" class="form-label">Açıklama</label>
                                <textarea class="form-control @error('aciklama') is-invalid @enderror" 
                                          id="aciklama" name="aciklama" rows="4">{{ old('aciklama') }}</textarea>
                                @error('aciklama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                           {{ old('is_active') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Ürünü aktif yap
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="detay" class="form-label">Detay</label>
                                <textarea class="form-control @error('detay') is-invalid @enderror" 
                                          id="detay" name="detay" rows="6">{{ old('detay') }}</textarea>
                                @error('detay')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="images" class="form-label">Ürün Resimleri</label>
                                <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                                       id="images" name="images[]" accept="image/*" multiple onchange="previewMultipleImages(this)">
                                @error('images.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">
                                    JPG, PNG, GIF, WebP formatları desteklenir. Maksimum 5MB per resim.<br>
                                    <strong>Birden fazla resim seçmek için Ctrl tuşuna basılı tutun.</strong>
                                </small>
                                
                                <div id="images-preview" class="row mt-3" style="display: none;"></div>
                            </div>
                            
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Fiyat Bilgileri</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li><strong>Özel Fiyat:</strong> Müşterilere gösterilen fiyat</li>
                                        <li><strong>Bayii Fiyatı:</strong> Bayiilere özel fiyat</li>
                                        <li><strong>SK Fiyatı:</strong> Satış kanalları fiyatı</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div class="card bg-light mt-3">
                                <div class="card-body">
                                    <h6 class="card-title">Stok Bilgileri</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li><strong>0-10:</strong> Düşük stok (Kırmızı)</li>
                                        <li><strong>11-50:</strong> Normal stok (Sarı)</li>
                                        <li><strong>50+:</strong> Yüksek stok (Yeşil)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>İptal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Ürünü Kaydet
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
    function previewMultipleImages(input) {
        const previewContainer = $('#images-preview');
        previewContainer.empty();
        
        if (input.files && input.files.length > 0) {
            Array.from(input.files).forEach((file, index) => {
                if (file.type.match('image.*')) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const previewCol = `
                            <div class="col-6 mb-2">
                                <div class="position-relative">
                                    <img src="${e.target.result}" alt="Önizleme ${index + 1}" 
                                         class="img-thumbnail" style="width: 100%; height: 80px; object-fit: cover;">
                                    <span class="badge bg-primary position-absolute top-0 start-0" style="margin: 2px;">
                                        ${index + 1}
                                    </span>
                                </div>
                            </div>
                        `;
                        previewContainer.append(previewCol);
                    }
                    
                    reader.readAsDataURL(file);
                }
            });
            
            previewContainer.show();
        } else {
            previewContainer.hide();
        }
    }
    
    // Fiyat alanları için para birimi sembolü gösterimi
    $('#doviz').on('change', function() {
        var currency = $(this).val();
        var symbol = '';
        
        switch(currency) {
            case 'TL':
                symbol = '₺';
                break;
            case 'USD':
                symbol = '$';
                break;
            case 'EUR':
                symbol = '€';
                break;
        }
        
        // Fiyat alanlarına sembol ekle (opsiyonel)
        $('input[name^="fiyat_"]').each(function() {
            var currentValue = $(this).val();
            if (currentValue && !currentValue.includes(symbol)) {
                // Sadece gösterim için, gerçek değeri değiştirme
            }
        });
        
        // Kar hesaplama fonksiyonlarını başlat
        initProfitCalculation();
    });
    
    // Kar hesaplama fonksiyonları
    function initProfitCalculation() {
        // Kar türü değiştiğinde
        $('#profit_type').on('change', function() {
            updateProfitLabel();
            calculateProfit();
        });
        
        // Kar değeri değiştiğinde
        $('#profit_value').on('input', function() {
            calculateProfit();
        });
        
        // Kar aktif checkbox değiştiğinde
        $('#profit_enabled').on('change', function() {
            calculateProfit();
        });
        
        // Fiyat değiştiğinde
        $('input[name^="fiyat_"]').on('input', function() {
            calculateProfit();
        });
        
        // İlk yüklemede hesapla
        updateProfitLabel();
        calculateProfit();
    }
    
    function updateProfitLabel() {
        const profitType = $('#profit_type').val();
        const profitLabel = $('#profit_label');
        
        switch(profitType) {
            case '1':
                profitLabel.text('Kar Yüzdesi (%)');
                break;
            case '2':
                profitLabel.text('Sabit Kar Tutarı');
                break;
            default:
                profitLabel.text('Kar Değeri');
        }
    }
    
    function calculateProfit() {
        const profitEnabled = $('#profit_enabled').is(':checked');
        const profitType = $('#profit_type').val();
        const profitValue = parseFloat($('#profit_value').val()) || 0;
        const doviz = $('#doviz').val() || 'TRY';
        
        if (!profitEnabled || profitType == '0') {
            $('#profit_preview').hide();
            return;
        }
        
        // En düşük fiyatı bul
        const fiyatOzel = parseFloat($('#fiyat_ozel').val()) || 0;
        const fiyatBayi = parseFloat($('#fiyat_bayi').val()) || 0;
        const fiyatSk = parseFloat($('#fiyat_sk').val()) || 0;
        
        const prices = [fiyatOzel, fiyatBayi, fiyatSk].filter(p => p > 0);
        const basePrice = Math.min(...prices);
        
        if (basePrice <= 0) {
            $('#profit_preview').hide();
            return;
        }
        
        let profitPrice = basePrice;
        
        switch(profitType) {
            case '1': // Yüzde kar
                profitPrice = basePrice * (1 + (profitValue / 100));
                break;
            case '2': // Sabit kar
                profitPrice = basePrice + profitValue;
                break;
        }
        
        // Önizleme göster
        $('#original_price_display').text(basePrice.toFixed(2) + ' ' + doviz + ' +KDV');
        $('#profit_price_display').text(profitPrice.toFixed(2) + ' ' + doviz + ' +KDV');
        $('#profit_preview').show();
    }
</script>
@endsection
