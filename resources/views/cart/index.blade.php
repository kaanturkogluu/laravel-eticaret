@extends('layouts.app')

@section('title', 'Sepetim')

@section('content')
<div class="container-fluid px-0">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>
                        <i class="fas fa-shopping-cart me-2"></i>Sepetim
                        <small class="text-muted">({{ $cartCount }} ürün)</small>
                    </h2>
                    @if($cartItems->count() > 0)
                    <button class="btn btn-outline-danger" onclick="clearCart()">
                        <i class="fas fa-trash me-2"></i>Sepeti Temizle
                    </button>
                    @endif
                </div>

                @if($cartItems->count() > 0)
                <div class="row">
                    <!-- Sepet Ürünleri -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-body p-0">
                                @foreach($cartItems as $item)
                                <div class="cart-item border-bottom p-4" data-product-id="{{ $item->product_id }}">
                                    <div class="row align-items-center">
                                        <!-- Ürün Resmi -->
                                        <div class="col-md-2">
                                            @if($item->product->images->count() > 0)
                                                <img src="{{ $item->product->images->first()->resim_url }}" 
                                                     class="img-fluid rounded cart-product-image" 
                                                     alt="{{ $item->product->ad }}">
                                            @else
                                                <div class="cart-product-image bg-light d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-image text-muted fa-2x"></i>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Ürün Bilgileri -->
                                        <div class="col-md-4">
                                            <h6 class="mb-1">
                                                <a href="{{ route('products.show', $item->product->kod) }}" 
                                                   class="text-decoration-none text-dark">
                                                    {{ $item->product->ad }}
                                                </a>
                                            </h6>
                                            <p class="text-muted mb-1 small">{{ $item->product->marka }}</p>
                                            <p class="text-muted mb-0 small">{{ $item->product->kategori }}</p>
                                        </div>

                                        <!-- Miktar -->
                                        <div class="col-md-2">
                                            <div class="input-group input-group-sm">
                                                <button class="btn btn-outline-secondary" type="button" 
                                                        onclick="updateQuantity({{ $item->product_id }}, {{ $item->quantity - 1 }})">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                                <input type="number" class="form-control text-center quantity-input" 
                                                       value="{{ $item->quantity }}" min="1" max="10"
                                                       onchange="updateQuantity({{ $item->product_id }}, this.value)">
                                                <button class="btn btn-outline-secondary" type="button" 
                                                        onclick="updateQuantity({{ $item->product_id }}, {{ $item->quantity + 1 }})">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Fiyat -->
                                        <div class="col-md-2">
                                            <div class="text-end">
                                                <div class="fw-bold text-primary">
                                                    {{ $item->formatted_unit_price }}
                                                </div>
                                                <div class="text-muted small">
                                                    Toplam: {{ $item->formatted_total_price }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Sil Butonu -->
                                        <div class="col-md-2">
                                            <button class="btn btn-outline-danger btn-sm" 
                                                    onclick="removeFromCart({{ $item->product_id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Sepet Özeti -->
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-receipt me-2"></i>Sipariş Özeti
                                </h5>
                            </div>
                            <div class="card-body">
                                <!-- Kupon Uygulama -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Kupon Kodu</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="couponCode" placeholder="Kupon kodunuzu girin">
                                        <button class="btn btn-outline-primary" type="button" id="applyCoupon">
                                            <i class="fas fa-ticket-alt"></i>
                                        </button>
                                    </div>
                                    <div id="couponMessage" class="mt-2"></div>
                                </div>

                                <!-- Uygulanan Kupon -->
                                <div id="appliedCoupon" class="mb-3" style="display: none;">
                                    <div class="alert alert-success d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong id="couponName"></strong>
                                            <br><small id="couponCode"></small>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-danger" id="removeCoupon">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between mb-2">
                                    <span>Kargo:</span>
                                    <span class="text-success" id="shippingCost">Ücretsiz</span>
                                </div>
                                <hr>
                                @php
                                    $currencyGroups = $cartItems->groupBy('product.doviz');
                                    $hasMultipleCurrencies = $currencyGroups->count() > 1;
                                @endphp
                                
                                @if($hasMultipleCurrencies)
                                    {{-- Çoklu para birimi durumunda her para birimi için ayrı toplam --}}
                                    @foreach($currencyGroups as $currency => $items)
                                        @php
                                            $currencyTotal = $items->sum('total_price');
                                            $currencySymbol = \App\Models\Product::getCurrencySymbolFor($currency);
                                        @endphp
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>{{ $currency }} Toplam:</span>
                                            <span>{{ number_format($currencyTotal, 2) }} {{ $currencySymbol }}</span>
                                        </div>
                                    @endforeach
                                    
                                    <hr class="my-3">
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>TL Karşılığı Ara Toplam:</span>
                                        <span id="subtotalAmount">{{ number_format($cartTotalInTry, 2) }} ₺</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2" id="discountRow" style="display: none;">
                                        <span class="text-success">İndirim:</span>
                                        <span class="text-success" id="discountAmount">-0.00 ₺</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <strong>TL Karşılığı Toplam:</strong>
                                        <strong class="text-success" id="finalTotal">{{ number_format($cartTotalInTry, 2) }} ₺</strong>
                                    </div>
                                @else
                                    {{-- Tek para birimi durumunda normal gösterim --}}
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Ara Toplam:</span>
                                        <span id="subtotalAmount">{{ number_format($cartTotal, 2) }} {{ $cartItems->first() ? $cartItems->first()->product->getCurrencySymbol() : '₺' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2" id="discountRow" style="display: none;">
                                        <span class="text-success">İndirim:</span>
                                        <span class="text-success" id="discountAmount">-0.00 ₺</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <strong>Toplam:</strong>
                                        <strong class="text-primary" id="finalTotal">{{ number_format($cartTotal, 2) }} {{ $cartItems->first() ? $cartItems->first()->product->getCurrencySymbol() : '₺' }}</strong>
                                    </div>
                                    
                                    @if($cartItems->first() && $cartItems->first()->product->doviz !== 'TRY')
                                    <div class="d-flex justify-content-between mb-3">
                                        <strong>TL Karşılığı:</strong>
                                        <strong class="text-success" id="finalTotalTl">{{ number_format($cartTotalInTry, 2) }} ₺</strong>
                                    </div>
                                    @endif
                                @endif
                                
                                <button class="btn btn-success w-100 mb-3" onclick="proceedToCheckout()">
                                    <i class="fas fa-credit-card me-2"></i>Ödemeye Geç
                                </button>
                                
                                <a href="{{ route('products.index') }}" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-arrow-left me-2"></i>Alışverişe Devam Et
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <!-- Boş Sepet -->
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">Sepetiniz boş</h4>
                    <p class="text-muted mb-4">Alışverişe başlamak için ürünleri sepete ekleyin</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">
                        <i class="fas fa-shopping-bag me-2"></i>Alışverişe Başla
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.cart-product-image {
    width: 80px;
    height: 80px;
    object-fit: cover;
}

.cart-item:hover {
    background-color: #f8f9fa;
}

.quantity-input {
    max-width: 60px;
}

@media (max-width: 768px) {
    .cart-item {
        padding: 1rem !important;
    }
    
    .cart-product-image {
        width: 60px;
        height: 60px;
    }
}
</style>

<script>
// Miktar güncelleme
function updateQuantity(productId, quantity) {
    if (quantity < 1) {
        removeFromCart(productId);
        return;
    }
    
    if (quantity > 10) {
        quantity = 10;
    }
    
    fetch('{{ route("cart.update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu');
    });
}

// Sepetten çıkar
function removeFromCart(productId) {
    if (confirm('Bu ürünü sepetten çıkarmak istediğinizden emin misiniz?')) {
        fetch('{{ route("cart.remove") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu');
        });
    }
}

// Sepeti temizle
function clearCart() {
    if (confirm('Sepeti tamamen temizlemek istediğinizden emin misiniz?')) {
        fetch('{{ route("cart.clear") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Bir hata oluştu');
        });
    }
}

// Ödemeye geç
function proceedToCheckout() {
    window.location.href = '{{ route("checkout.index") }}';
}

// Kupon uygula
function applyCoupon() {
    const couponCode = document.getElementById('couponCode').value.trim();
    if (!couponCode) {
        showCouponMessage('Lütfen kupon kodunu girin.', 'danger');
        return;
    }

    const button = document.getElementById('applyCoupon');
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    fetch('{{ route("cart.coupon.apply") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            coupon_code: couponCode
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showCouponMessage(data.message, 'success');
            showAppliedCoupon(data.coupon);
            updateCartTotals(data);
            document.getElementById('couponCode').value = '';
        } else {
            showCouponMessage(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showCouponMessage('Bir hata oluştu.', 'danger');
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

// Kupon kaldır
function removeCoupon() {
    fetch('{{ route("cart.coupon.remove") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showCouponMessage(data.message, 'success');
            hideAppliedCoupon();
            updateCartTotals(data);
        } else {
            showCouponMessage(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showCouponMessage('Bir hata oluştu.', 'danger');
    });
}

// Kupon mesajını göster
function showCouponMessage(message, type) {
    const messageDiv = document.getElementById('couponMessage');
    messageDiv.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>`;
    
    // 5 saniye sonra mesajı otomatik kapat
    setTimeout(() => {
        const alert = messageDiv.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}

// Uygulanan kuponu göster
function showAppliedCoupon(coupon) {
    document.getElementById('couponName').textContent = coupon.name;
    document.getElementById('couponCode').textContent = coupon.code;
    document.getElementById('appliedCoupon').style.display = 'block';
}

// Uygulanan kuponu gizle
function hideAppliedCoupon() {
    document.getElementById('appliedCoupon').style.display = 'none';
}

// Sepet toplamlarını güncelle
function updateCartTotals(data) {
    // İndirim satırını göster/gizle
    const discountRow = document.getElementById('discountRow');
    if (data.discount_amount > 0) {
        discountRow.style.display = 'flex';
        document.getElementById('discountAmount').textContent = `-${data.discount_amount.toFixed(2)} ₺`;
    } else {
        discountRow.style.display = 'none';
    }

    // Final toplamı güncelle
    document.getElementById('finalTotal').textContent = `${data.final_total.toFixed(2)} ₺`;
    
    // TL karşılığı varsa onu da güncelle
    const finalTotalTl = document.getElementById('finalTotalTl');
    if (finalTotalTl) {
        finalTotalTl.textContent = `${data.final_total.toFixed(2)} ₺`;
    }

    // Kargo ücretini güncelle
    const shippingCost = document.getElementById('shippingCost');
    if (data.free_shipping) {
        shippingCost.textContent = 'Ücretsiz';
        shippingCost.className = 'text-success';
    } else {
        shippingCost.textContent = 'Ücretsiz';
        shippingCost.className = 'text-success';
    }
}

// Sayfa yüklendiğinde uygulanan kuponu kontrol et
document.addEventListener('DOMContentLoaded', function() {
    // Uygulanan kuponu kontrol et
    fetch('{{ route("cart.coupon.applied") }}')
    .then(response => response.json())
    .then(data => {
        if (data.success && data.coupon) {
            showAppliedCoupon(data.coupon);
            updateCartTotals(data);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });

    // Kupon uygula butonuna event listener ekle
    document.getElementById('applyCoupon').addEventListener('click', applyCoupon);
    
    // Kupon kaldır butonuna event listener ekle
    document.getElementById('removeCoupon').addEventListener('click', removeCoupon);
    
    // Enter tuşu ile kupon uygula
    document.getElementById('couponCode').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            applyCoupon();
        }
    });
});
</script>
@endsection
