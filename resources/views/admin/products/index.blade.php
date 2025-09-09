s@extends('layouts.admin')

@section('title', 'Ürün Yönetimi')
@section('page-title', 'Ürün Yönetimi')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-box me-2"></i>Ürünler
                </h5>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Yeni Ürün
                </a>
            </div>
            <div class="card-body">
                <!-- Filtreler -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3">
                            <div class="col-md-3">
                                <input type="text" class="form-control" name="search" 
                                       value="{{ request('search') }}" placeholder="Ürün ara...">
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" name="brand">
                                    <option value="">Tüm Markalar</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>
                                            {{ $brand }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" name="category">
                                    <option value="">Tüm Kategoriler</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" name="status">
                                    <option value="">Tüm Durumlar</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Pasif</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select class="form-control" name="sort">
                                    <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Tarih</option>
                                    <option value="ad" {{ request('sort') == 'ad' ? 'selected' : '' }}>İsim</option>
                                    <option value="fiyat_ozel" {{ request('sort') == 'fiyat_ozel' ? 'selected' : '' }}>Fiyat</option>
                                    <option value="miktar" {{ request('sort') == 'miktar' ? 'selected' : '' }}>Stok</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                @if($products->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Resim</th>
                                    <th>Ürün Bilgileri</th>
                                    <th>Fiyat</th>
                                    <th>Stok</th>
                                    <th>Durum</th>
                                    <th>Tarih</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>
                                            @if($product->images->count() > 0)
                                                <img src="{{ $product->images->first()->resim_url }}" 
                                                     alt="{{ $product->ad }}" 
                                                     class="img-thumbnail" 
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 60px; height: 60px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $product->ad }}</strong>
                                                <br><small class="text-muted">Kod: {{ $product->kod }}</small>
                                                @if($product->marka)
                                                    <br><small class="text-muted">Marka: {{ $product->marka }}</small>
                                                @endif
                                                @if($product->kategori)
                                                    <br><small class="text-muted">Kategori: {{ $product->kategori }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div>
                                                @if($product->fiyat_ozel)
                                                    <strong>{{ number_format($product->fiyat_ozel, 2) }} {{ $product->getCurrencySymbol() }}</strong>
                                                    @if($product->fiyat_bayi || $product->fiyat_sk)
                                                        <br><small class="text-muted">
                                                            @if($product->fiyat_bayi)Bayii: {{ number_format($product->fiyat_bayi, 2) }} {{ $product->getCurrencySymbol() }}@endif
                                                            @if($product->fiyat_sk) | SK: {{ number_format($product->fiyat_sk, 2) }} {{ $product->getCurrencySymbol() }}@endif
                                                        </small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Fiyat belirtilmemiş</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $product->miktar > 10 ? 'bg-success' : ($product->miktar > 0 ? 'bg-warning' : 'bg-danger') }}">
                                                {{ $product->miktar }} adet
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $product->is_active ? 'Aktif' : 'Pasif' }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $product->created_at->format('d.m.Y H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.products.edit', $product) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Düzenle">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <form action="{{ route('admin.products.destroy', $product) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Bu ürünü silmek istediğinizden emin misiniz?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Sil">
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
                    
                    <div class="d-flex justify-content-center">
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-box fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Henüz ürün bulunmuyor</h5>
                        <p class="text-muted">İlk ürününüzü eklemek için yukarıdaki butona tıklayın.</p>
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Ürün Ekle
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Filtre formu otomatik gönderim
    $('select[name="brand"], select[name="category"], select[name="status"], select[name="sort"]').on('change', function() {
        $(this).closest('form').submit();
    });
</script>
@endsection
