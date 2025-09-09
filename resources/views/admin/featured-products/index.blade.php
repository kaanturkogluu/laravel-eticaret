@extends('layouts.admin')

@section('title', 'Öne Çıkan Ürünler Yönetimi')

@section('page-title', 'Öne Çıkan Ürünler')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-star me-2"></i>Öne Çıkan Ürünler Yönetimi
                </h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Arama ve Filtreleme -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <form method="GET" action="{{ route('admin.featured-products.index') }}" class="d-flex">
                            <div class="input-group">
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="Ürün adı, marka, kategori veya kod ile ara..." 
                                       value="{{ $search }}"
                                       id="searchInput">
                                <button class="btn btn-outline-primary" type="submit">
                                    <i class="fas fa-search"></i> Ara
                                </button>
                                @if($search)
                                    <a href="{{ route('admin.featured-products.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times"></i> Temizle
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <form method="GET" action="{{ route('admin.featured-products.index') }}" class="d-flex">
                            @if($search)
                                <input type="hidden" name="search" value="{{ $search }}">
                            @endif
                            <select name="per_page" class="form-select" onchange="this.form.submit()">
                                <option value="12" {{ $perPage == 12 ? 'selected' : '' }}>12 ürün/göster</option>
                                <option value="24" {{ $perPage == 24 ? 'selected' : '' }}>24 ürün/göster</option>
                                <option value="48" {{ $perPage == 48 ? 'selected' : '' }}>48 ürün/göster</option>
                                <option value="96" {{ $perPage == 96 ? 'selected' : '' }}>96 ürün/göster</option>
                            </select>
                        </form>
                    </div>
                </div>

                @if($search)
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>"{{ $search }}"</strong> için arama sonuçları gösteriliyor.
                        <a href="{{ route('admin.featured-products.index') }}" class="alert-link">Tüm ürünleri göster</a>
                    </div>
                @endif

                <!-- Öne Çıkan Ürünler -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-star text-warning me-2"></i>Öne Çıkan Ürünler ({{ $featuredProducts->count() }})
                    </h6>
                    
                    @if($featuredProducts->count() > 0)
                        <div class="row" id="featured-products-list">
                            @foreach($featuredProducts as $product)
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-3" data-product-id="{{ $product->id }}">
                                <div class="card product-card h-100">
                                    <div class="position-relative">
                                        @if($product->images->count() > 0)
                                            <img src="{{ $product->images->first()->resim_url }}" 
                                                 class="card-img-top" 
                                                 alt="{{ $product->ad }}"
                                                 style="height: 150px; object-fit: cover;"
                                                 onerror="this.src='{{ asset('images/no-product-image.svg') }}'">
                                        @else
                                            <img src="{{ asset('images/no-product-image.svg') }}" 
                                                 class="card-img-top" 
                                                 alt="{{ $product->ad }}"
                                                 style="height: 150px; object-fit: cover;">
                                        @endif
                                        
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-warning">
                                                <i class="fas fa-star me-1"></i>Sıra: {{ $product->featured_order ?? 'Belirsiz' }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body d-flex flex-column">
                                        <h6 class="card-title">{{ Str::limit($product->ad, 40) }}</h6>
                                        <p class="card-text text-muted small mb-2">{{ $product->marka }}</p>
                                        <div class="price mb-2 text-danger fw-bold">
                                            {{ $product->formatted_price }}
                                        </div>
                                        <div class="mt-auto">
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="editOrder({{ $product->id }}, {{ $product->featured_order ?? 0 }})">
                                                    <i class="fas fa-edit"></i> Sıra
                                                </button>
                                                <form method="POST" action="{{ route('admin.featured-products.remove', $product->id) }}" 
                                                      class="d-inline" onsubmit="return confirm('Bu ürünü öne çıkan listesinden çıkarmak istediğinizden emin misiniz?')">
                                                    @csrf
                                                    @if($search)
                                                        <input type="hidden" name="search" value="{{ $search }}">
                                                    @endif
                                                    @if($perPage != 12)
                                                        <input type="hidden" name="per_page" value="{{ $perPage }}">
                                                    @endif
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-times"></i> Çıkar
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-3">
                            <button class="btn btn-warning" onclick="resetOrder()">
                                <i class="fas fa-sort me-2"></i>Sıralamayı Sıfırla
                            </button>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Henüz öne çıkan ürün bulunmuyor. Aşağıdaki listeden ürün ekleyebilirsiniz.
                        </div>
                    @endif
                </div>

                <!-- Tüm Ürünler -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-box me-2"></i>Tüm Ürünler 
                        @if($search)
                            ({{ $allProducts->total() }} sonuç bulundu)
                        @else
                            ({{ $allProducts->total() }} ürün)
                        @endif
                    </h6>
                    
                    @if($allProducts->count() > 0)
                        <div class="row">
                            @foreach($allProducts as $product)
                            <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                                <div class="card product-card h-100">
                                    <div class="position-relative">
                                        @if($product->images->count() > 0)
                                            <img src="{{ $product->images->first()->resim_url }}" 
                                                 class="card-img-top" 
                                                 alt="{{ $product->ad }}"
                                                 style="height: 150px; object-fit: cover;"
                                                 onerror="this.src='{{ asset('images/no-product-image.svg') }}'">
                                        @else
                                            <img src="{{ asset('images/no-product-image.svg') }}" 
                                                 class="card-img-top" 
                                                 alt="{{ $product->ad }}"
                                                 style="height: 150px; object-fit: cover;">
                                        @endif
                                        
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-success">Stok: {{ $product->miktar }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body d-flex flex-column">
                                        <h6 class="card-title">{{ Str::limit($product->ad, 40) }}</h6>
                                        <p class="card-text text-muted small mb-2">{{ $product->marka }}</p>
                                        <div class="price mb-2 text-danger fw-bold">
                                            {{ $product->formatted_price }}
                                        </div>
                                        <div class="mt-auto">
                                            <form method="POST" action="{{ route('admin.featured-products.add') }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                @if($search)
                                                    <input type="hidden" name="search" value="{{ $search }}">
                                                @endif
                                                @if($perPage != 12)
                                                    <input type="hidden" name="per_page" value="{{ $perPage }}">
                                                @endif
                                                <div class="input-group input-group-sm">
                                                    <input type="number" name="featured_order" class="form-control" 
                                                           placeholder="Sıra" min="1" style="width: 80px;">
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="fas fa-star me-1"></i>Ekle
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Sayfalama -->
                        @if($allProducts->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $allProducts->links() }}
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            @if($search)
                                "{{ $search }}" için ürün bulunamadı.
                            @else
                                Öne çıkan olarak eklenebilecek ürün bulunmuyor.
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sıra Düzenleme Modal -->
<div class="modal fade" id="editOrderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sıra Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editOrderForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Yeni Sıra</label>
                        <input type="number" id="newOrder" class="form-control" min="1" required>
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
let currentProductId = null;
let searchTimeout = null;

// Dinamik arama
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value;
    
    // Önceki timeout'u temizle
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    
    // 500ms sonra arama yap
    searchTimeout = setTimeout(() => {
        if (searchTerm.length >= 2 || searchTerm.length === 0) {
            performSearch(searchTerm);
        }
    }, 500);
});

function performSearch(searchTerm) {
    const url = new URL(window.location);
    if (searchTerm) {
        url.searchParams.set('search', searchTerm);
    } else {
        url.searchParams.delete('search');
    }
    url.searchParams.delete('page'); // Sayfa numarasını sıfırla
    
    window.location.href = url.toString();
}

function editOrder(productId, currentOrder) {
    currentProductId = productId;
    document.getElementById('newOrder').value = currentOrder;
    new bootstrap.Modal(document.getElementById('editOrderModal')).show();
}

document.getElementById('editOrderForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const newOrder = document.getElementById('newOrder').value;
    
    fetch('{{ route("admin.featured-products.update-order") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            products: [{
                id: currentProductId,
                order: newOrder
            }]
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Hata: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Bir hata oluştu.');
    });
});

function resetOrder() {
    if (confirm('Tüm öne çıkan ürünlerin sırasını sıfırlamak istediğinizden emin misiniz?')) {
        window.location.href = '{{ route("admin.featured-products.reset-order") }}';
    }
}

// Enter tuşu ile arama
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const searchTerm = this.value;
        performSearch(searchTerm);
    }
});

// Sayfa yüklendiğinde arama kutusuna odaklan
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput && !searchInput.value) {
        searchInput.focus();
    }
});
</script>
@endsection
