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
                                <img src="{{ $image->resim_url }}" 
                                     class="d-block w-100 rounded" 
                                     alt="{{ $product->ad }}"
                                     style="height: 400px; object-fit: cover;"
                                     onerror="this.src='https://via.placeholder.com/600x400?text=Resim+Yok'">
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
                <img src="https://via.placeholder.com/600x400?text=Resim+Yok" 
                     class="img-fluid rounded" 
                     alt="{{ $product->ad }}">
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
                    <h4 class="price text-success">{{ $product->formatted_price }}</h4>
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
                    <button class="btn btn-success btn-lg" disabled>
                        <i class="fas fa-shopping-cart me-2"></i>Sepete Ekle
                    </button>
                    <button class="btn btn-outline-primary" disabled>
                        <i class="fas fa-heart me-2"></i>Favorilere Ekle
                    </button>
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
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Ürün Listesine Dön
            </a>
        </div>
        </div>
    </div>
</div>
@endsection
