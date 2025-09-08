@extends('layouts.app')

@section('title', 'Favori Ürünlerim - Basital.com')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-heart text-danger me-2"></i>Favori Ürünlerim</h2>
            <div class="text-muted">
                <span id="favorite-count">{{ $favorites->total() }}</span> ürün
            </div>
        </div>

        @if($favorites->count() > 0)
            <div class="row">
                @foreach($favorites as $product)
                <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                    <div class="card product-card h-100">
                        <div class="position-relative">
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

                            <!-- Favori Butonu -->
                            <button class="btn btn-sm btn-outline-danger position-absolute" 
                                    style="top: 10px; left: 10px; z-index: 10;"
                                    onclick="toggleFavorite('{{ $product->kod }}', this)"
                                    data-product-kod="{{ $product->kod }}">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title text-truncate" title="{{ $product->ad }}">
                                {{ $product->ad }}
                            </h6>
                            
                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-barcode me-1"></i>{{ $product->kod }}
                                </small>
                            </div>
                            
                            @if($product->marka)
                            <div class="mb-2">
                                <span class="badge bg-secondary">{{ $product->marka }}</span>
                            </div>
                            @endif
                            
                            <div class="mb-3">
                                <div class="price">{{ $product->formatted_price }}</div>
                                @if($product->miktar > 0)
                                    <small class="text-muted">Stok: {{ $product->miktar }} adet</small>
                                @endif
                            </div>
                            
                            <div class="mt-auto">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('products.show', $product->kod) }}" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>Detayları Gör
                                    </a>
                                    
                                    @if($product->miktar > 0)
                                    <button class="btn btn-primary btn-sm" 
                                            onclick="addToCart('{{ $product->kod }}', 1)">
                                        <i class="fas fa-shopping-cart me-1"></i>Sepete Ekle
                                    </button>
                                    @else
                                    <button class="btn btn-secondary btn-sm" disabled>
                                        <i class="fas fa-times me-1"></i>Stok Yok
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $favorites->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-heart-broken text-muted" style="font-size: 4rem;"></i>
                </div>
                <h4 class="text-muted">Henüz favori ürününüz yok</h4>
                <p class="text-muted mb-4">Beğendiğiniz ürünleri favorilere ekleyerek burada görebilirsiniz.</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    <i class="fas fa-shopping-bag me-2"></i>Ürünleri İncele
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
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
                    
                    // Favoriler sayfasındaysa ürünü kaldır
                    setTimeout(() => {
                        $button.closest('.col-lg-3, .col-md-4, .col-sm-6').fadeOut(300, function() {
                            $(this).remove();
                            updateFavoriteCount();
                        });
                    }, 1000);
                }
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

// Sepete ekleme fonksiyonu
function addToCart(productKod, quantity = 1) {
    $.ajax({
        url: '{{ route("cart.add") }}',
        method: 'POST',
        data: {
            product_kod: productKod,
            quantity: quantity
        },
        success: function(response) {
            if (response.success) {
                showToast('success', response.message);
                updateCartCount(response.cart_count);
            }
        },
        error: function(xhr) {
            const response = xhr.responseJSON;
            showToast('error', response?.message || 'Sepete eklenirken bir hata oluştu.');
        }
    });
}

// Favori sayısını güncelle
function updateFavoriteCount() {
    $.ajax({
        url: '{{ route("favorites.count") }}',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                $('#favorite-count').text(response.count);
            }
        }
    });
}

// Toast bildirimi
function showToast(type, message) {
    const toastClass = type === 'success' ? 'bg-success' : 
                      type === 'error' ? 'bg-danger' : 'bg-info';
    
    const toast = $(`
        <div class="toast align-items-center text-white ${toastClass} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `);
    
    // Toast container oluştur
    if (!$('.toast-container').length) {
        $('body').append('<div class="toast-container position-fixed top-0 end-0 p-3"></div>');
    }
    
    $('.toast-container').append(toast);
    
    const bsToast = new bootstrap.Toast(toast[0]);
    bsToast.show();
    
    // Toast kapandıktan sonra DOM'dan kaldır
    toast.on('hidden.bs.toast', function() {
        $(this).remove();
    });
}

// Sayfa yüklendiğinde favori sayısını güncelle
$(document).ready(function() {
    updateFavoriteCount();
});
</script>
@endsection
