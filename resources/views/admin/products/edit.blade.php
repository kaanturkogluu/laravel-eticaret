@extends('layouts.admin')

@section('title', 'Ürün Düzenle')
@section('page-title', 'Ürün Düzenle')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Ürün Düzenle: {{ $product->ad }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="ad" class="form-label">Ürün Adı <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('ad') is-invalid @enderror" 
                                       id="ad" name="ad" value="{{ old('ad', $product->ad) }}" required>
                                @error('ad')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="kod" class="form-label">Ürün Kodu <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('kod') is-invalid @enderror" 
                                               id="kod" name="kod" value="{{ old('kod', $product->kod) }}" required>
                                        @error('kod')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="marka" class="form-label">Marka</label>
                                        <input type="text" class="form-control @error('marka') is-invalid @enderror" 
                                               id="marka" name="marka" value="{{ old('marka', $product->marka) }}">
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
                                               id="kategori" name="kategori" value="{{ old('kategori', $product->kategori) }}">
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
                                            <option value="TL" {{ old('doviz', $product->doviz) === 'TL' ? 'selected' : '' }}>TL</option>
                                            <option value="USD" {{ old('doviz', $product->doviz) === 'USD' ? 'selected' : '' }}>USD</option>
                                            <option value="EUR" {{ old('doviz', $product->doviz) === 'EUR' ? 'selected' : '' }}>EUR</option>
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
                                                       id="fiyat_ozel" name="fiyat_ozel" value="{{ old('fiyat_ozel', $product->fiyat_ozel) }}" 
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
                                                       id="fiyat_bayi" name="fiyat_bayi" value="{{ old('fiyat_bayi', $product->fiyat_bayi) }}" 
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
                                                       id="fiyat_sk" name="fiyat_sk" value="{{ old('fiyat_sk', $product->fiyat_sk) }}" 
                                                       min="0" step="0.01">
                                                @error('fiyat_sk')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Fiyat Geçmişi -->
                                    @if($product->priceHistory->count() > 0)
                                        <div class="mt-3">
                                            <h6>Son Fiyat Değişiklikleri</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Tarih</th>
                                                            <th>Eski Fiyat</th>
                                                            <th>Yeni Fiyat</th>
                                                            <th>Değişim</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($product->priceHistory->take(5) as $history)
                                                            <tr>
                                                                <td>{{ $history->changed_at->format('d.m.Y H:i') }}</td>
                                                                <td>{{ number_format($history->old_best_price, 2) }} {{ $product->getCurrencySymbol() }}</td>
                                                                <td>{{ number_format($history->new_best_price, 2) }} {{ $product->getCurrencySymbol() }}</td>
                                                                <td>
                                                                    @if($history->is_discount)
                                                                        <span class="text-success">
                                                                            -{{ number_format(abs($history->price_difference), 2) }} {{ $product->getCurrencySymbol() }}
                                                                            ({{ number_format($history->discount_percentage, 1) }}%)
                                                                        </span>
                                                                    @else
                                                                        <span class="text-danger">
                                                                            +{{ number_format($history->price_difference, 2) }} {{ $product->getCurrencySymbol() }}
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="miktar" class="form-label">Stok Miktarı <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('miktar') is-invalid @enderror" 
                                               id="miktar" name="miktar" value="{{ old('miktar', $product->miktar) }}" 
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
                                               id="barkod" name="barkod" value="{{ old('barkod', $product->barkod) }}">
                                        @error('barkod')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="aciklama" class="form-label">Açıklama</label>
                                <textarea class="form-control @error('aciklama') is-invalid @enderror" 
                                          id="aciklama" name="aciklama" rows="4">{{ old('aciklama', $product->aciklama) }}</textarea>
                                @error('aciklama')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="detay" class="form-label">Detay</label>
                                <textarea class="form-control @error('detay') is-invalid @enderror" 
                                          id="detay" name="detay" rows="6">{{ old('detay', $product->detay) }}</textarea>
                                @error('detay')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                           {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Ürünü aktif yap
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <!-- Mevcut Resimler -->
                            @if($product->images->count() > 0)
                                <div class="mb-3">
                                    <label class="form-label">Mevcut Resimler</label>
                                    <div class="row">
                                        @foreach($product->images as $image)
                                            <div class="col-6 mb-2">
                                                <img src="{{ $image->resim_url }}" alt="{{ $product->ad }}" 
                                                     class="img-thumbnail" style="width: 100%; height: 80px; object-fit: cover;">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            
                            <div class="mb-3">
                                <label for="image" class="form-label">Yeni Resim Ekle</label>
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
                            
                            <!-- Ürün İstatistikleri -->
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Ürün İstatistikleri</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li><strong>Oluşturulma:</strong> {{ $product->created_at->format('d.m.Y H:i') }}</li>
                                        <li><strong>Son Güncelleme:</strong> {{ $product->updated_at->format('d.m.Y H:i') }}</li>
                                        <li><strong>Resim Sayısı:</strong> {{ $product->images->count() }}</li>
                                        <li><strong>Fiyat Değişikliği:</strong> {{ $product->priceHistory->count() }} kez</li>
                                        @if($product->priceHistory->where('is_discount', true)->count() > 0)
                                            <li><strong>Son İndirim:</strong> 
                                                @php
                                                    $lastDiscount = $product->priceHistory->where('is_discount', true)->first();
                                                @endphp
                                                @if($lastDiscount)
                                                    %{{ number_format($lastDiscount->discount_percentage, 1) }}
                                                    ({{ $lastDiscount->changed_at->format('d.m.Y') }})
                                                @endif
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            
                            <!-- Fiyat Uyarısı -->
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Fiyat Değişikliği Uyarısı:</strong>
                                Fiyatları değiştirdiğinizde, bu ürünü favorilerine ekleyen müşterilere otomatik olarak e-posta bildirimi gönderilecektir.
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>İptal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Değişiklikleri Kaydet
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
    
    // Fiyat değişikliği uyarısı
    $('input[name^="fiyat_"]').on('change', function() {
        var originalValue = $(this).data('original-value') || $(this).val();
        var currentValue = $(this).val();
        
        if (originalValue !== currentValue) {
            $(this).addClass('border-warning');
        } else {
            $(this).removeClass('border-warning');
        }
    });
    
    // Sayfa yüklendiğinde orijinal değerleri kaydet
    $(document).ready(function() {
        $('input[name^="fiyat_"]').each(function() {
            $(this).data('original-value', $(this).val());
        });
    });
</script>
@endsection
