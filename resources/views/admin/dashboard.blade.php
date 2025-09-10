@extends('layouts.admin')

@section('title', 'Admin Dashboard - Basital.com')
@section('page-title', 'Dashboard')

@section('content')

<!-- Hızlı İşlemler -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Hızlı İşlemler</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#addProductModal">
                            <i class="fas fa-plus me-2"></i>Manuel Ürün Ekle
                        </button>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.products.create') }}" class="btn btn-success w-100">
                            <i class="fas fa-plus me-2"></i>Yeni Ürün Ekle
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#marketplaceModal">
                            <i class="fas fa-store me-2"></i>Pazaryeri Entegrasyonu
                        </button>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('products.index') }}" class="btn btn-warning w-100">
                            <i class="fas fa-list me-2"></i>Ürünleri Yönet
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- İstatistik Kartları -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">{{ \App\Models\Product::count() }}</div>
                    <div class="stats-label">Toplam Ürün</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-box"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">{{ \App\Models\Product::active()->inStock()->count() }}</div>
                    <div class="stats-label">Aktif Ürün</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">{{ \App\Models\Product::where('miktar', '<', 2)->count() }}</div>
                    <div class="stats-label">Düşük Stok</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="stats-card info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">{{ \App\Models\User::count() }}</div>
                    <div class="stats-label">Toplam Kullanıcı</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- XML İşlemleri -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-file-code me-2"></i>XML İşlemleri
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('admin.xml-import') }}" class="btn btn-primary w-100">
                            <i class="fas fa-file-import me-2"></i>XML İçe Aktar
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <form method="POST" action="{{ route('admin.xml-import.liste') }}" class="d-inline w-100">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" 
                                    onclick="return confirm('Liste.xml import edilecek. Devam etmek istiyor musunuz?')">
                                <i class="fas fa-play me-2"></i>Liste.xml Import
                            </button>
                        </form>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('admin.xml-history') }}" class="btn btn-dark w-100">
                            <i class="fas fa-history me-2"></i>XML Geçmişi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Diğer İşlemler -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-tools me-2"></i>Diğer İşlemler
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <a href="{{ route('cart.index') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-shopping-cart me-2"></i>Sepet Yönetimi
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#stockControlModal">
                            <i class="fas fa-boxes me-2"></i>Stok Kontrolü
                        </button>
                    </div>
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-info w-100" data-bs-toggle="modal" data-bs-target="#marketplaceModal">
                            <i class="fas fa-store me-2"></i>Pazaryeri Entegrasyonu
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Son Aktiviteler -->
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>Son Güncellenen Ürünler
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Ürün Kodu</th>
                                <th>Ürün Adı</th>
                                <th>Stok</th>
                                <th>Son Güncelleme</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\Models\Product::orderBy('last_updated', 'desc')->limit(5)->get() as $product)
                            <tr>
                                <td>{{ $product->kod }}</td>
                                <td>{{ Str::limit($product->ad, 30) }}</td>
                                <td>
                                    <span class="badge {{ $product->miktar >= 2 ? 'bg-success' : 'bg-warning' }}">
                                        {{ $product->miktar }}
                                    </span>
                                </td>
                                <td>{{ $product->last_updated ? $product->last_updated->format('d.m.Y H:i') : '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Kategori Dağılımı
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Kategori</th>
                                <th>Ürün Sayısı</th>
                                <th>Aktif</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\Models\Product::selectRaw('kategori, COUNT(*) as total, SUM(CASE WHEN is_active = 1 AND miktar >= 2 THEN 1 ELSE 0 END) as active')
                                ->whereNotNull('kategori')
                                ->groupBy('kategori')
                                ->orderBy('total', 'desc')
                                ->limit(5)
                                ->get() as $category)
                            <tr>
                                <td>{{ $category->kategori }}</td>
                                <td>{{ $category->total }}</td>
                                <td>
                                    <span class="badge bg-success">{{ $category->active }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sistem Bilgileri -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Sistem Bilgileri
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Laravel Versiyonu:</strong><br>
                        <span class="text-muted">{{ app()->version() }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>PHP Versiyonu:</strong><br>
                        <span class="text-muted">{{ PHP_VERSION }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>Son XML Import:</strong><br>
                        <span class="text-muted">
                            @php
                                $lastImport = \App\Models\Product::whereNotNull('last_updated')->orderBy('last_updated', 'desc')->first();
                            @endphp
                            {{ $lastImport ? $lastImport->last_updated->format('d.m.Y H:i') : 'Henüz yapılmadı' }}
                        </span>
                    </div>
                    <div class="col-md-3">
                        <strong>Sunucu Zamanı:</strong><br>
                        <span class="text-muted">{{ now()->format('d.m.Y H:i:s') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>

<!-- Manuel Ürün Ekleme Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Manuel Ürün Ekle</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ürün Adı *</label>
                            <input type="text" class="form-control" name="ad" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ürün Kodu *</label>
                            <input type="text" class="form-control" name="kod" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Marka</label>
                            <input type="text" class="form-control" name="marka">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kategori</label>
                            <input type="text" class="form-control" name="kategori">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fiyat (TL) *</label>
                            <input type="number" step="0.01" class="form-control" name="fiyat_ozel" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Stok Miktarı *</label>
                            <input type="number" class="form-control" name="miktar" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Para Birimi</label>
                            <select class="form-control" name="doviz">
                                <option value="TL">TL</option>
                                <option value="USD">USD</option>
                                <option value="EUR">EUR</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Ürün Açıklaması</label>
                            <textarea class="form-control" name="aciklama" rows="3"></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Ürün Resmi</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Ürün Ekle</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Manuel ürün ekleme formu
    $('#addProductModal form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        var $form = $(this);
        var $submitBtn = $form.find('button[type="submit"]');
        
        // Butonu devre dışı bırak
        $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Ekleniyor...');
        
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Başarılı mesajı göster
                showAlert('success', 'Ürün başarıyla eklendi!');
                
                // Modal'ı kapat
                $('#addProductModal').modal('hide');
                
                // Formu temizle
                $form[0].reset();
                
                // Sayfayı yenile
                setTimeout(() => {
                    location.reload();
                }, 1000);
            },
            error: function(xhr) {
                var errors = xhr.responseJSON?.errors || {};
                var errorMessage = 'Bir hata oluştu!';
                
                if (Object.keys(errors).length > 0) {
                    errorMessage = Object.values(errors).flat().join('<br>');
                }
                
                showAlert('danger', errorMessage);
            },
            complete: function() {
                // Butonu eski haline getir
                $submitBtn.prop('disabled', false).html('Ürün Ekle');
            }
        });
    });
});
</script>
@endsection
