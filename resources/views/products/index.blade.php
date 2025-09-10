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
                            <div class="position-relative overflow-hidden">
                                <a href="{{ route('products.show', $product->kod) }}" class="d-block">
                                    @if($product->images->count() > 0)
                                        <img src="{{ $product->images->first()->resim_url }}" 
                                             class="card-img-top product-image" 
                                             alt="{{ $product->ad }}"
                                             onerror="handleImageError(this)">
                                    @else
                                        <img src="{{ asset('images/no-product-image.svg') }}" 
                                             class="card-img-top product-image" 
                                             alt="{{ $product->ad }}">
                                    @endif
                                </a>
                                
                                <!-- Stok Badge -->
                                @if($product->miktar >= 2)
                                    <span class="badge bg-success stock-badge">
                                        <i class="fas fa-check me-1"></i>Stokta
                                    </span>
                                @elseif($product->miktar == 1)
                                    <span class="badge bg-warning stock-badge">
                                        <i class="fas fa-exclamation-triangle me-1"></i>Son 1 Adet
                                    </span>
                                @else
                                    <span class="badge bg-danger stock-badge">
                                        <i class="fas fa-times me-1"></i>Stok Yok
                                    </span>
                                @endif

                                @auth
                                <!-- Favori Butonu -->
                                <button class="btn btn-sm btn-outline-danger position-absolute favorite-btn" 
                                        style="top: 10px; left: 10px; z-index: 10;"
                                        data-product-kod="{{ $product->kod }}"
                                        onclick="toggleFavorite('{{ $product->kod }}', this)">
                                    <i class="fas fa-heart"></i>
                                </button>
                                @endauth
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
                                    <div class="fw-bold text-success fs-5">
                                        {{ $product->formatted_price_with_profit_in_try }}
                                        Y
                                    </div>
                                    @if($product->doviz !== 'TRY')
                                        <small class="text-muted">
                                            ({{ $product->formatted_price_with_profit }})
                                        </small>
                                    @endif
                                </div>
                                
                                <div class="mt-auto">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <a href="{{ route('products.show', $product->kod) }}" 
                                               class="btn btn-outline-primary btn-sm w-100">
                                                <i class="fas fa-eye me-1"></i>Detay
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <button class="btn btn-success btn-sm w-100 add-to-cart-btn" 
                                                    data-product-id="{{ $product->id }}"
                                                    data-product-name="{{ $product->ad }}">
                                                <i class="fas fa-cart-plus me-1"></i>Sepete Ekle
                                            </button>
                                        </div>
                                    </div>
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

    // Sepete ekle butonları
    $('.add-to-cart-btn').on('click', function() {
        const productId = $(this).data('product-id');
        const productName = $(this).data('product-name');
        const $button = $(this);
        
        // Butonu devre dışı bırak
        $button.prop('disabled', true);
        $button.html('<i class="fas fa-spinner fa-spin me-1"></i>Ekleniyor...');
        
        $.ajax({
            url: '{{ route("cart.add") }}',
            method: 'POST',
            data: {
                product_id: productId,
                quantity: 1,
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                if (data.success) {
                    // Başarılı mesajı göster
                    $button.html('<i class="fas fa-check me-1"></i>Eklendi!');
                    $button.removeClass('btn-success').addClass('btn-success');
                    
                    // Navbar'daki sepet sayacını güncelle
                    updateCartCount(data.cart_count);
                    
                    // 2 saniye sonra butonu eski haline getir
                    setTimeout(() => {
                        $button.prop('disabled', false);
                        $button.html('<i class="fas fa-cart-plus me-1"></i>Sepete Ekle');
                    }, 2000);
                    
                    // Toast mesajı göster
                    showToast('success', data.message);
                } else {
                    // Hata mesajı göster
                    $button.prop('disabled', false);
                    $button.html('<i class="fas fa-cart-plus me-1"></i>Sepete Ekle');
                    showToast('error', data.message);
                }
            },
            error: function() {
                $button.prop('disabled', false);
                $button.html('<i class="fas fa-cart-plus me-1"></i>Sepete Ekle');
                showToast('error', 'Bir hata oluştu');
            }
        });
    });
});

// Sepet sayacını güncelle
function updateCartCount(count) {
    const $cartCount = $('.cart-count');
    if ($cartCount.length) {
        $cartCount.text(count);
        $cartCount.toggle(count > 0);
    }
}

// Toast mesajı göster
function showToast(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    const toast = $(`
        <div class="alert ${alertClass} position-fixed" style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="fas ${iconClass} me-2"></i>${message}
        </div>
    `);
    
    $('body').append(toast);
    
    // 3 saniye sonra kaldır
    setTimeout(() => {
        toast.fadeOut(() => toast.remove());
    }, 3000);
}

// Favori toggle fonksiyonu
function toggleFavorite(productKod, button) {
    const $button = $(button);
    const $icon = $button.find('i');
    
    // Loading state
    $button.prop('disabled', true);
    $icon.removeClass('fas fa-heart').addClass('fas fa-spinner fa-spin');
    
    $.ajax({
        url: '{{ route("favorites.toggle") }}',
        method: 'POST',
        data: {
            product_kod: productKod
        },
        success: function(response) {
            if (response.success) {
                if (response.is_favorite) {
                    $button.removeClass('btn-outline-danger').addClass('btn-danger');
                    $icon.removeClass('fa-spinner fa-spin').addClass('fas fa-heart');
                    showToast('success', response.message);
                } else {
                    $button.removeClass('btn-danger').addClass('btn-outline-danger');
                    $icon.removeClass('fa-spinner fa-spin').addClass('fas fa-heart');
                    showToast('info', response.message);
                }
                
                // Favori sayacını güncelle
                updateFavoriteCount();
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showToast('error', response?.message || 'Bir hata oluştu.');
            $icon.removeClass('fa-spinner fa-spin').addClass('fas fa-heart');
        },
        complete: function() {
            $button.prop('disabled', false);
        }
    });
}

// Favori sayacını güncelle
function updateFavoriteCount() {
    $.ajax({
        url: '{{ route("favorites.count") }}',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const $favoriteCount = $('.favorite-count');
                if ($favoriteCount.length) {
                    $favoriteCount.text(response.count);
                    $favoriteCount.toggle(response.count > 0);
                }
            }
        }
    });
}

</script>
@endsection
