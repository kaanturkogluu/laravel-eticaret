@extends('layouts.app')

@section('title', $product->ad)

@section('content')
<div class="container-fluid px-0">
    <div class="container">
        <div class="row">
    <div class="col-lg-8">
        <!-- Ürün Resimleri -->
        <div class="mb-4">
            @if($product->images->count() > 0)
                <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @foreach($product->images as $index => $image)
                            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                <div class="image-container" style="height: 400px;">
                                    <img src="{{ $image->resim_url }}" 
                                         class="d-block w-100 rounded product-image" 
                                         alt="{{ $product->ad }}"
                                         style="height: 100%; object-fit: cover;"
                                         loading="{{ $index === 0 ? 'eager' : 'lazy' }}"
                                         onerror="this.src='{{ asset('images/no-product-image.svg') }}'">
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($product->images->count() > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon"></span>
                        </button>
                    @endif
                </div>
            @else
                <img src="{{ asset('images/no-product-image.svg') }}" 
                     class="img-fluid rounded" 
                     alt="{{ $product->ad }}"
                     style="height: 400px; object-fit: cover;">
            @endif
        </div>

        <!-- Teknik Özellikler -->
        @if($product->specifications->count() > 0)
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>Teknik Özellikler
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($product->specifications as $spec)
                        <div class="col-md-6 mb-2">
                            <strong>{{ $spec->ozellik }}:</strong> {{ $spec->deger }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">{{ $product->ad }}</h2>
                
                <div class="mb-3">
                    <span class="badge bg-primary fs-6">{{ $product->kod }}</span>
                </div>

                <div class="mb-3">
                    <h4 class="price text-success">{{ $product->formatted_price_with_profit_in_try }}</h4>
                    @if($product->doviz !== 'TRY')
                        <small class="text-muted">
                            ({{ $product->formatted_price_with_profit }})
                        </small>
                    @endif
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span><strong>Marka:</strong></span>
                        <span>{{ $product->marka }}</span>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span><strong>Kategori:</strong></span>
                        <span>{{ $product->kategori }}</span>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span><strong>Stok Durumu:</strong></span>
                        <span class="badge bg-success fs-6">
                            <i class="fas fa-box me-1"></i>{{ $product->miktar }} Adet
                        </span>
                    </div>
                </div>

                @if($product->barkod)
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span><strong>Barkod:</strong></span>
                        <span>{{ $product->barkod }}</span>
                    </div>
                </div>
                @endif

                @if($product->desi > 0)
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span><strong>Desi:</strong></span>
                        <span>{{ $product->desi }}</span>
                    </div>
                </div>
                @endif

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span><strong>KDV:</strong></span>
                        <span>%{{ $product->kdv }}</span>
                    </div>
                </div>

                @if($product->aciklama)
                <div class="mb-3">
                    <h6><strong>Açıklama:</strong></h6>
                    <p class="text-muted">{{ $product->aciklama }}</p>
                </div>
                @endif

                @if($product->detay)
                <div class="mb-3">
                    <h6><strong>Detay:</strong></h6>
                    <div class="text-muted">
                        {!! $product->detay !!}
                    </div>
                </div>
                @endif

                <div class="d-grid gap-2">
                    @if($product->miktar > 0)
                        <button class="btn btn-success btn-lg add-to-cart-btn" 
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $product->ad }}">
                            <i class="fas fa-shopping-cart me-2"></i>Sepete Ekle
                        </button>
                    @else
                        <button class="btn btn-secondary btn-lg" disabled>
                            <i class="fas fa-times me-2"></i>Stok Yok
                        </button>
                    @endif
                    
                    @auth
                    <button class="btn btn-outline-danger favorite-btn" 
                            data-product-kod="{{ $product->kod }}"
                            onclick="toggleFavorite('{{ $product->kod }}', this)">
                        <i class="fas fa-heart me-2"></i>Favorilere Ekle
                    </button>
                    @else
                    <a href="{{ route('login') }}" class="btn btn-outline-danger">
                        <i class="fas fa-heart me-2"></i>Favorilere Ekle
                    </a>
                    @endauth
                </div>

                <div class="mt-3 text-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Son güncelleme: {{ $product->last_updated ? $product->last_updated->format('d.m.Y H:i') : 'Bilinmiyor' }}
                    </small>
                </div>
            </div>
        </div>

        <!-- Fiyat Detayları -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-tags me-2"></i>Fiyat Detayları
                </h6>
            </div>
            <div class="card-body">
                @if($product->fiyat_ozel)
                <div class="d-flex justify-content-between mb-2">
                    <span>Özel Fiyat:</span>
                    <span class="text-success fw-bold">{{ number_format($product->fiyat_ozel, 2) }} {{ $product->doviz }}</span>
                </div>
                @endif
                
                @if($product->fiyat_bayi)
                <div class="d-flex justify-content-between mb-2">
                    <span>Bayi Fiyatı:</span>
                    <span>{{ number_format($product->fiyat_bayi, 2) }} {{ $product->doviz }}</span>
                </div>
                @endif
                
                @if($product->fiyat_sk)
                <div class="d-flex justify-content-between mb-2">
                    <span>Satış Fiyatı:</span>
                    <span>{{ number_format($product->fiyat_sk, 2) }} {{ $product->doviz }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

        <!-- Geri Dön -->
        <div class="mt-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-arrow-left me-2"></i>Ürün Listesine Dön
                    </a>
                </div>
                <div class="col-md-6">
                    <a href="{{ route('favorites.index') }}" class="btn btn-outline-danger w-100">
                        <i class="fas fa-heart me-2"></i>Favorilerim
                    </a>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Sepete ekle butonu
    $('.add-to-cart-btn').on('click', function() {
        const productId = $(this).data('product-id');
        const productName = $(this).data('product-name');
        const $button = $(this);
        
        // Butonu devre dışı bırak
        $button.prop('disabled', true);
        $button.html('<i class="fas fa-spinner fa-spin me-2"></i>Ekleniyor...');
        
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
                    $button.html('<i class="fas fa-check me-2"></i>Eklendi!');
                    $button.removeClass('btn-success').addClass('btn-success');
                    
                    // Navbar'daki sepet sayacını güncelle
                    updateCartCount(data.cart_count);
                    
                    // 2 saniye sonra butonu eski haline getir
                    setTimeout(() => {
                        $button.prop('disabled', false);
                        $button.html('<i class="fas fa-cart-plus me-2"></i>Sepete Ekle');
                    }, 2000);
                    
                    // Toast mesajı göster
                    showToast('success', data.message);
                } else {
                    // Hata mesajı göster
                    $button.prop('disabled', false);
                    $button.html('<i class="fas fa-cart-plus me-2"></i>Sepete Ekle');
                    showToast('error', data.message);
                }
            },
            error: function() {
                $button.prop('disabled', false);
                $button.html('<i class="fas fa-cart-plus me-2"></i>Sepete Ekle');
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
                    $button.html('<i class="fas fa-heart me-2"></i>Favorilerden Çıkar');
                    showToast('success', response.message);
                } else {
                    $button.removeClass('btn-danger').addClass('btn-outline-danger');
                    $button.html('<i class="fas fa-heart me-2"></i>Favorilere Ekle');
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
