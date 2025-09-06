@extends('layouts.app')

@section('title', 'Ürünler')

@section('content')
<div class="container-fluid px-0">
    <div class="container">
        <div class="row">
    <!-- Filtreler -->
    <div class="col-lg-3">
        <div class="filter-sidebar">
            <h5 class="mb-3">
                <i class="fas fa-filter me-2"></i>Filtreler
            </h5>
            
            <!-- Arama -->
            <form method="GET" action="{{ route('products.index') }}" class="mb-4">
                <div class="input-group">
                    <input type="text" name="search" class="form-control search-box" 
                           placeholder="Ürün ara..." value="{{ request('search') }}">
                    <button class="btn btn-primary btn-search" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>

            <!-- Marka Filtresi -->
            @if($brands->count() > 0)
            <div class="mb-3">
                <h6>Marka</h6>
                <form method="GET" action="{{ route('products.index') }}">
                    @foreach(request()->except('brand') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <select name="brand" class="form-select" onchange="this.form.submit()">
                        <option value="">Tüm Markalar</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>
                                {{ $brand }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            @endif

            <!-- Kategori Filtresi -->
            @if($categories->count() > 0)
            <div class="mb-3">
                <h6>Kategori</h6>
                <form method="GET" action="{{ route('products.index') }}">
                    @foreach(request()->except('category') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <select name="category" class="form-select" onchange="this.form.submit()">
                        <option value="">Tüm Kategoriler</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            @endif

            <!-- Sıralama -->
            <div class="mb-3">
                <h6>Sıralama</h6>
                <form method="GET" action="{{ route('products.index') }}">
                    @foreach(request()->except(['sort', 'direction']) as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach
                    <select name="sort" class="form-select mb-2">
                        <option value="ad" {{ request('sort') == 'ad' ? 'selected' : '' }}>İsim</option>
                        <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Fiyat</option>
                        <option value="stock" {{ request('sort') == 'stock' ? 'selected' : '' }}>Stok</option>
                        <option value="marka" {{ request('sort') == 'marka' ? 'selected' : '' }}>Marka</option>
                    </select>
                    <select name="direction" class="form-select">
                        <option value="asc" {{ request('direction') == 'asc' ? 'selected' : '' }}>Artan</option>
                        <option value="desc" {{ request('direction') == 'desc' ? 'selected' : '' }}>Azalan</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-primary mt-2 w-100">Sırala</button>
                </form>
            </div>

            <!-- Filtreleri Temizle -->
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                <i class="fas fa-times me-1"></i>Filtreleri Temizle
            </a>
        </div>
    </div>

    <!-- Ürün Listesi -->
    <div class="col-lg-9">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="fas fa-box me-2"></i>Ürünler
                @if(request('search'))
                    <small class="text-muted">- "{{ request('search') }}" için arama sonuçları</small>
                @endif
                <small class="text-muted">({{ $products->total() }} ürün)</small>
            </h2>
            
            <div class="loading">
                <i class="fas fa-spinner fa-spin me-2"></i>Yükleniyor...
            </div>
        </div>

        @if($products->count() > 0)
        <!-- Arama Sonucu Bulunamadı -->
        @if(request('search') && $products->count() == 0)
        <div class="alert alert-info text-center">
            <i class="fas fa-search fa-2x mb-3"></i>
            <h5>"{{ request('search') }}" için arama sonucu bulunamadı</h5>
            <p class="mb-0">Farklı anahtar kelimeler deneyebilir veya filtreleri kaldırabilirsiniz.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">Tüm Ürünleri Gör</a>
        </div>
        @endif

        <div class="row" id="products-container">
            @foreach($products as $product)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card product-card h-100">
                            <div class="position-relative">
                                @if($product->images->count() > 0)
                                    <img src="{{ $product->images->first()->resim_url }}" 
                                         class="card-img-top product-image" 
                                         alt="{{ $product->ad }}"
                                         onerror="this.src='https://via.placeholder.com/300x200?text=Resim+Yok'">
                                @else
                                    <img src="https://via.placeholder.com/300x200?text=Resim+Yok" 
                                         class="card-img-top product-image" 
                                         alt="{{ $product->ad }}">
                                @endif
                                
                                <span class="badge bg-success stock-badge">
                                    <i class="fas fa-box me-1"></i>{{ $product->miktar }} Adet
                                </span>
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <h6 class="card-title">{{ Str::limit($product->ad, 50) }}</h6>
                                
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-tag me-1"></i>{{ $product->marka }}
                                    </small>
                                </div>
                                
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-folder me-1"></i>{{ $product->kategori }}
                                    </small>
                                </div>
                                
                                <div class="price mb-3">
                                    {{ $product->formatted_price }}
                                </div>
                                
                                <div class="mt-auto">
                                    <a href="{{ route('products.show', $product->kod) }}" 
                                       class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-eye me-1"></i>Detayları Gör
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">Ürün bulunamadı</h4>
                <p class="text-muted">Arama kriterlerinizi değiştirerek tekrar deneyin.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    <i class="fas fa-refresh me-1"></i>Tüm Ürünleri Göster
                </a>
            </div>
        @endif
    </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto-submit forms on change
    $('select[onchange]').on('change', function() {
        $(this).closest('form').submit();
    });
});
</script>
@endsection
