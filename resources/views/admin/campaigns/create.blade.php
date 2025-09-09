@extends('layouts.admin')

@section('title', 'Yeni Kampanya Oluştur')
@section('page-title', 'Yeni Kampanya Oluştur')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-plus me-2"></i>Yeni Kampanya Oluştur
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.campaigns.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="title" class="form-label">Kampanya Başlığı <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Açıklama</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Kampanya Tipi <span class="text-danger">*</span></label>
                                        <select class="form-control @error('type') is-invalid @enderror" 
                                                id="type" name="type" required>
                                            <option value="">Seçiniz</option>
                                            <option value="banner" {{ old('type') === 'banner' ? 'selected' : '' }}>Banner</option>
                                            <option value="campaign" {{ old('type') === 'campaign' ? 'selected' : '' }}>Kampanya</option>
                                            <option value="promotion" {{ old('type') === 'promotion' ? 'selected' : '' }}>Promosyon</option>
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="link_url" class="form-label">Bağlantı URL'si</label>
                                        <input type="url" class="form-control @error('link_url') is-invalid @enderror" 
                                               id="link_url" name="link_url" value="{{ old('link_url') }}" 
                                               placeholder="https://example.com">
                                        @error('link_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="start_date" class="form-label">Başlangıç Tarihi</label>
                                        <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror" 
                                               id="start_date" name="start_date" value="{{ old('start_date') }}">
                                        @error('start_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="end_date" class="form-label">Bitiş Tarihi</label>
                                        <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror" 
                                               id="end_date" name="end_date" value="{{ old('end_date') }}">
                                        @error('end_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <!-- İndirim Ayarları -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-percentage me-2"></i>İndirim Ayarları</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="discount_type" class="form-label">İndirim Tipi</label>
                                                <select class="form-control @error('discount_type') is-invalid @enderror" 
                                                        id="discount_type" name="discount_type">
                                                    <option value="">İndirim Yok</option>
                                                    <option value="percentage" {{ old('discount_type') === 'percentage' ? 'selected' : '' }}>Yüzde (%)</option>
                                                    <option value="fixed" {{ old('discount_type') === 'fixed' ? 'selected' : '' }}>Sabit Tutar (TL)</option>
                                                </select>
                                                @error('discount_type')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="discount_value" class="form-label">İndirim Değeri</label>
                                                <input type="number" class="form-control @error('discount_value') is-invalid @enderror" 
                                                       id="discount_value" name="discount_value" value="{{ old('discount_value') }}" 
                                                       min="0" step="0.01">
                                                @error('discount_value')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Yüzde için 0-100, sabit tutar için TL cinsinden</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="minimum_amount" class="form-label">Minimum Sepet Tutarı</label>
                                                <input type="number" class="form-control @error('minimum_amount') is-invalid @enderror" 
                                                       id="minimum_amount" name="minimum_amount" value="{{ old('minimum_amount') }}" 
                                                       min="0" step="0.01">
                                                @error('minimum_amount')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Bu tutarın üzerinde alışveriş yapılmalı</small>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="maximum_discount" class="form-label">Maksimum İndirim</label>
                                                <input type="number" class="form-control @error('maximum_discount') is-invalid @enderror" 
                                                       id="maximum_discount" name="maximum_discount" value="{{ old('maximum_discount') }}" 
                                                       min="0" step="0.01">
                                                @error('maximum_discount')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="form-text text-muted">Yüzde indirimlerde maksimum TL tutarı</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="sort_order" class="form-label">Sıralama</label>
                                        <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                               id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" 
                                               min="0" step="1">
                                        @error('sort_order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Düşük sayılar önce görünür</small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                                   {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Kampanyayı aktif yap
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="image" class="form-label">Kampanya Resmi</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       id="image" name="image" accept="image/*" onchange="previewImage(this)">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">JPG, PNG, GIF formatları desteklenir. Maksimum 2MB.</small>
                                
                                <div id="image-preview" class="mt-3" style="display: none;">
                                    <img id="preview-img" src="" alt="Önizleme" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                            </div>
                            
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Kampanya Tipleri</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li><strong>Banner:</strong> Ana sayfa banner'ları için</li>
                                        <li><strong>Kampanya:</strong> Özel kampanya sayfaları için</li>
                                        <li><strong>Promosyon:</strong> Promosyon duyuruları için</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>İptal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Kampanyayı Kaydet
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
    
    // Başlangıç tarihi değiştiğinde bitiş tarihini güncelle
    $('#start_date').on('change', function() {
        var startDate = $(this).val();
        if (startDate) {
            $('#end_date').attr('min', startDate);
        }
    });
    
    // Bitiş tarihi değiştiğinde başlangıç tarihini kontrol et
    $('#end_date').on('change', function() {
        var endDate = $(this).val();
        var startDate = $('#start_date').val();
        
        if (startDate && endDate && endDate <= startDate) {
            alert('Bitiş tarihi başlangıç tarihinden sonra olmalıdır.');
            $(this).val('');
        }
    });
</script>
@endsection
