@extends('layouts.admin')

@section('title', 'Genel Kar Ayarları')
@section('page-title', 'Genel Kar Ayarları')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line me-2"></i>Genel Kar Ayarları
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.global-profit.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-4">
                                <h6 class="text-primary">
                                    <i class="fas fa-cog me-2"></i>Genel Kar Sistemi
                                </h6>
                                <p class="text-muted">
                                    Bu ayarlar, özel kar ayarlanmamış tüm ürünler için geçerli olacaktır. 
                                    Ürün özel kar ayarları varsa, o ürün için genel ayarlar geçersiz olur.
                                </p>
                            </div>
                            
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_enabled" 
                                           name="is_enabled" value="1" 
                                           {{ old('is_enabled', $settings->is_enabled) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_enabled">
                                        <strong>Genel Kar Sistemi Aktif</strong>
                                    </label>
                                </div>
                                <small class="text-muted">
                                    Aktif edildiğinde, özel kar ayarlanmamış tüm ürünlerde genel kar oranı uygulanır.
                                </small>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="profit_type" class="form-label">Kar Türü</label>
                                        <select class="form-select @error('profit_type') is-invalid @enderror" 
                                                id="profit_type" name="profit_type">
                                            <option value="0" {{ old('profit_type', $settings->profit_type) == 0 ? 'selected' : '' }}>
                                                Kar Yok
                                            </option>
                                            <option value="1" {{ old('profit_type', $settings->profit_type) == 1 ? 'selected' : '' }}>
                                                Yüzde Kar
                                            </option>
                                            <option value="2" {{ old('profit_type', $settings->profit_type) == 2 ? 'selected' : '' }}>
                                                Sabit Kar
                                            </option>
                                        </select>
                                        @error('profit_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="profit_value" class="form-label">
                                            <span id="profit_label">Kar Değeri</span>
                                        </label>
                                        <input type="number" class="form-control @error('profit_value') is-invalid @enderror" 
                                               id="profit_value" name="profit_value" 
                                               value="{{ old('profit_value', $settings->profit_value) }}" 
                                               min="0" step="0.01" required>
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
                                        <span id="original_price_display">100.00 TL</span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Genel Kar Dahil Fiyat:</strong> 
                                        <span id="profit_price_display" class="text-success"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-info-circle me-2"></i>Bilgi
                                    </h6>
                                    <ul class="list-unstyled mb-0">
                                        <li class="mb-2">
                                            <strong>Yüzde Kar:</strong> Orijinal fiyatın üzerine belirtilen yüzde kadar eklenir.
                                        </li>
                                        <li class="mb-2">
                                            <strong>Sabit Kar:</strong> Orijinal fiyatın üzerine belirtilen sabit tutar eklenir.
                                        </li>
                                        <li class="mb-2">
                                            <strong>Özel Ayarlar:</strong> Ürün bazında özel kar ayarları varsa, genel ayarlar geçersiz olur.
                                        </li>
                                        <li>
                                            <strong>Örnek:</strong> %20 kar ile 100 TL → 120 TL
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Ayarları Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Kategori Kar Ayarları -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-tags me-2"></i>Kategori Bazlı Kar Ayarları
                </h5>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryProfitModal">
                    <i class="fas fa-plus me-1"></i>Yeni Kategori Kar Ayarı
                </button>
            </div>
            <div class="card-body">
                @if($categoryProfits->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Kategori</th>
                                    <th>Durum</th>
                                    <th>Kar Türü</th>
                                    <th>Kar Değeri</th>
                                    <th>Açıklama</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categoryProfits as $categoryProfit)
                                    <tr>
                                        <td>
                                            <strong>{{ $categoryProfit->category_name }}</strong>
                                        </td>
                                        <td>
                                            @if($categoryProfit->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Pasif</span>
                                            @endif
                                        </td>
                                        <td>{{ $categoryProfit->profit_type_description }}</td>
                                        <td>{{ $categoryProfit->formatted_profit_value }}</td>
                                        <td>{{ $categoryProfit->description ?? '-' }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button type="button" class="btn btn-outline-primary" 
                                                        onclick="editCategoryProfit({{ $categoryProfit->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <form action="{{ route('admin.category-profits.toggle', $categoryProfit) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-{{ $categoryProfit->is_active ? 'warning' : 'success' }}">
                                                        <i class="fas fa-{{ $categoryProfit->is_active ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.category-profits.destroy', $categoryProfit) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Bu kategori kar ayarını silmek istediğinizden emin misiniz?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Henüz kategori kar ayarı eklenmemiş</h5>
                        <p class="text-muted">Farklı kategoriler için farklı kar oranları belirleyebilirsiniz.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Yeni Kategori Kar Ayarı Modal -->
<div class="modal fade" id="addCategoryProfitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.category-profits.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Yeni Kategori Kar Ayarı</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="category_name" class="form-label">Kategori Adı</label>
                        <select class="form-select @error('category_name') is-invalid @enderror" 
                                id="category_name" name="category_name" required>
                            <option value="">Kategori Seçin</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ old('category_name') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" 
                                   name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Aktif
                            </label>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="profit_type" class="form-label">Kar Türü</label>
                                <select class="form-select @error('profit_type') is-invalid @enderror" 
                                        id="profit_type" name="profit_type" required>
                                    <option value="0" {{ old('profit_type') == 0 ? 'selected' : '' }}>Kar Yok</option>
                                    <option value="1" {{ old('profit_type') == 1 ? 'selected' : '' }}>Yüzde Kar</option>
                                    <option value="2" {{ old('profit_type') == 2 ? 'selected' : '' }}>Sabit Kar</option>
                                </select>
                                @error('profit_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="profit_value" class="form-label">Kar Değeri</label>
                                <input type="number" class="form-control @error('profit_value') is-invalid @enderror" 
                                       id="profit_value" name="profit_value" 
                                       value="{{ old('profit_value') }}" 
                                       min="0" step="0.01" required>
                                @error('profit_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Açıklama</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Düzenleme Modal -->
<div class="modal fade" id="editCategoryProfitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editCategoryProfitForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Kategori Kar Ayarını Düzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kategori Adı</label>
                        <input type="text" class="form-control" id="edit_category_name" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_is_active" 
                                   name="is_active" value="1">
                            <label class="form-check-label" for="edit_is_active">
                                Aktif
                            </label>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_profit_type" class="form-label">Kar Türü</label>
                                <select class="form-select" id="edit_profit_type" name="profit_type" required>
                                    <option value="0">Kar Yok</option>
                                    <option value="1">Yüzde Kar</option>
                                    <option value="2">Sabit Kar</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_profit_value" class="form-label">Kar Değeri</label>
                                <input type="number" class="form-control" id="edit_profit_value" 
                                       name="profit_value" min="0" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
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
        $('#is_enabled').on('change', function() {
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
                profitLabel.text('Sabit Kar Tutarı (TL)');
                break;
            default:
                profitLabel.text('Kar Değeri');
        }
    }
    
    function calculateProfit() {
        const profitEnabled = $('#is_enabled').is(':checked');
        const profitType = $('#profit_type').val();
        const profitValue = parseFloat($('#profit_value').val()) || 0;
        const basePrice = 100; // Örnek fiyat
        
        if (!profitEnabled || profitType == '0') {
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
        $('#profit_price_display').text(profitPrice.toFixed(2) + ' TL +KDV');
        $('#profit_preview').show();
    }
    
    // Kategori kar ayarı düzenleme fonksiyonu
    function editCategoryProfit(categoryProfitId) {
        // AJAX ile kategori kar ayarı verilerini getir
        fetch(`/admin/category-profits/${categoryProfitId}`)
            .then(response => response.json())
            .then(data => {
                $('#edit_category_name').val(data.category_name);
                $('#edit_is_active').prop('checked', data.is_active);
                $('#edit_profit_type').val(data.profit_type);
                $('#edit_profit_value').val(data.profit_value);
                $('#edit_description').val(data.description);
                
                // Form action URL'ini güncelle
                $('#editCategoryProfitForm').attr('action', `/admin/category-profits/${categoryProfitId}`);
                
                // Modal'ı göster
                $('#editCategoryProfitModal').modal('show');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Kategori kar ayarı verileri yüklenirken hata oluştu.');
            });
    }
</script>
@endsection
