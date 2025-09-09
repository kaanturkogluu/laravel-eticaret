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
                                <label for="image" class="form-label">Ürün Resmi</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       id="image" name="image" accept="image/*" onchange="previewImage(this)">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">JPG, PNG, GIF, WebP formatları desteklenir. Maksimum 5MB.</small>
                                
                                <div id="image-preview" class="mt-3" style="display: none;">
                                    <img id="preview-img" src="" alt="Önizleme" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
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
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                $('#preview-img').attr('src', e.target.result);
                $('#image-preview').show();
            }
            
            reader.readAsDataURL(input.files[0]);
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
    });
</script>
@endsection
