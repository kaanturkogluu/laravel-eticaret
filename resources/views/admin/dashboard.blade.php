@extends('layouts.app')

@section('title', 'Admin Dashboard - Basital.com')

@section('content')
<div class="container-fluid px-0">
    <div class="container">
        <div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
            </h2>
            <div class="text-muted">
                Hoş geldiniz, {{ auth()->user()->name }}
            </div>
        </div>
    </div>
</div>

<!-- Hızlı İşlemler -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Hızlı İşlemler</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#addProductModal">
                            <i class="fas fa-plus me-2"></i>Manuel Ürün Ekle
                        </button>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button class="btn btn-outline-success w-100" data-bs-toggle="modal" data-bs-target="#xmlImportModal">
                            <i class="fas fa-file-import me-2"></i>XML İçe Aktar
                        </button>
                    </div>
                    <div class="col-md-3 mb-3">
                        <button class="btn btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#marketplaceModal">
                            <i class="fas fa-store me-2"></i>Pazaryeri Entegrasyonu
                        </button>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-warning w-100">
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
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ \App\Models\Product::count() }}</h4>
                        <p class="mb-0">Toplam Ürün</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-box fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ \App\Models\Product::active()->inStock()->count() }}</h4>
                        <p class="mb-0">Aktif Ürün</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ \App\Models\Product::where('miktar', '<', 2)->count() }}</h4>
                        <p class="mb-0">Düşük Stok</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ \App\Models\User::count() }}</h4>
                        <p class="mb-0">Toplam Kullanıcı</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hızlı İşlemler -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Hızlı İşlemler
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('admin.xml-import') }}" class="btn btn-primary w-100">
                            <i class="fas fa-upload me-2"></i>XML Import
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <form method="POST" action="{{ route('admin.xml-import.liste') }}" class="d-inline w-100">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" 
                                    onclick="return confirm('Liste.xml import edilecek. Devam etmek istiyor musunuz?')">
                                <i class="fas fa-file-code me-2"></i>Liste.xml Import
                            </button>
                        </form>
                    </div>
                    <div class="col-md-3 mb-3">
                        <form method="POST" action="{{ route('admin.stock-control') }}" class="d-inline w-100">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100" 
                                    onclick="return confirm('Stok kontrolü yapılacak. Devam etmek istiyor musunuz?')">
                                <i class="fas fa-boxes me-2"></i>Stok Kontrolü
                            </button>
                        </form>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ route('products.index') }}" class="btn btn-info w-100">
                            <i class="fas fa-eye me-2"></i>Ürünleri Görüntüle
                        </a>
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

<!-- XML İçe Aktarma Modal -->
<div class="modal fade" id="xmlImportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-file-import me-2"></i>XML İçe Aktar</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.xml.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        XML dosyası yükleyerek ürünleri toplu olarak içe aktarabilirsiniz.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">XML Dosyası *</label>
                        <input type="file" class="form-control" name="xml_file" accept=".xml" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Güncelleme Modu</label>
                            <select class="form-control" name="update_mode">
                                <option value="replace">Mevcut verileri değiştir</option>
                                <option value="merge">Mevcut verilerle birleştir</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok Kontrolü</label>
                            <select class="form-control" name="stock_control">
                                <option value="1">Stok kontrolü yap</option>
                                <option value="0">Stok kontrolü yapma</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-success">XML İçe Aktar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Pazaryeri Entegrasyonu Modal -->
<div class="modal fade" id="marketplaceModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-store me-2"></i>Pazaryeri Entegrasyonu</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Hepsiburada</h6>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Hepsiburada pazaryerine ürünlerinizi otomatik olarak aktarın.</p>
                                <div class="mb-3">
                                    <label class="form-label">API Anahtarı</label>
                                    <input type="password" class="form-control" placeholder="Hepsiburada API anahtarınız">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Satıcı ID</label>
                                    <input type="text" class="form-control" placeholder="Satıcı ID'niz">
                                </div>
                                <button class="btn btn-warning w-100">Hepsiburada Bağlantısını Test Et</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Trendyol</h6>
                            </div>
                            <div class="card-body">
                                <p class="card-text">Trendyol pazaryerine ürünlerinizi otomatik olarak aktarın.</p>
                                <div class="mb-3">
                                    <label class="form-label">API Anahtarı</label>
                                    <input type="password" class="form-control" placeholder="Trendyol API anahtarınız">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Satıcı ID</label>
                                    <input type="text" class="form-control" placeholder="Satıcı ID'niz">
                                </div>
                                <button class="btn btn-primary w-100">Trendyol Bağlantısını Test Et</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0"><i class="fas fa-sync me-2"></i>Otomatik Senkronizasyon</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Senkronizasyon Sıklığı</label>
                                        <select class="form-control">
                                            <option value="30">30 dakikada bir</option>
                                            <option value="60">1 saatte bir</option>
                                            <option value="120">2 saatte bir</option>
                                            <option value="240">4 saatte bir</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Stok Güncelleme</label>
                                        <select class="form-control">
                                            <option value="1">Otomatik güncelle</option>
                                            <option value="0">Manuel güncelle</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Fiyat Güncelleme</label>
                                        <select class="form-control">
                                            <option value="1">Otomatik güncelle</option>
                                            <option value="0">Manuel güncelle</option>
                                        </select>
                                    </div>
                                </div>
                                <button class="btn btn-success">Senkronizasyon Ayarlarını Kaydet</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
