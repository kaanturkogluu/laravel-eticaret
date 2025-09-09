@extends('layouts.app')

@section('title', 'Favori Ürün Analizi')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-heart"></i> Favori Ürün Analizi
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Favori Ürünler -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Favori Ürünlerim ({{ $favoriteProducts->count() }})</h3>
                                </div>
                                <div class="card-body">
                                    @if($favoriteProducts->count() > 0)
                                        <div class="row">
                                            @foreach($favoriteProducts as $product)
                                            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                                                <div class="card h-100">
                                                    <div class="card-img-top" style="height: 200px; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                                        @if($product->images->count() > 0)
                                                            <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                                                                 alt="{{ $product->ad }}" 
                                                                 style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                                        @else
                                                            <i class="fas fa-image fa-3x text-muted"></i>
                                                        @endif
                                                    </div>
                                                    <div class="card-body d-flex flex-column">
                                                        <h6 class="card-title">{{ $product->ad }}</h6>
                                                        <p class="card-text text-muted small">{{ $product->marka }}</p>
                                                        <p class="card-text text-muted small">{{ $product->kategori }}</p>
                                                        <div class="mt-auto">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <span class="text-primary font-weight-bold">
                                                                    {{ number_format($product->best_price, 2) }} {{ $product->getCurrencySymbol() }}
                                                                </span>
                                                                <a href="{{ route('products.show', $product->kod) }}" 
                                                                   class="btn btn-sm btn-outline-primary">
                                                                    <i class="fas fa-eye"></i>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-info text-center">
                                            <i class="fas fa-heart-broken"></i>
                                            Henüz favori ürününüz bulunmuyor.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Favori Kategoriler ve Markalar -->
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Favori Kategoriler</h3>
                                </div>
                                <div class="card-body">
                                    @if($favoriteCategories->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Kategori</th>
                                                        <th>Ürün Sayısı</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($favoriteCategories as $category)
                                                    <tr>
                                                        <td>{{ $category->category }}</td>
                                                        <td>
                                                            <span class="badge badge-primary">{{ $category->product_count }}</span>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info text-center">
                                            <i class="fas fa-info-circle"></i>
                                            Favori kategoriniz bulunmuyor.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Favori Markalar</h3>
                                </div>
                                <div class="card-body">
                                    @if($favoriteBrands->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Marka</th>
                                                        <th>Ürün Sayısı</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($favoriteBrands as $brand)
                                                    <tr>
                                                        <td>{{ $brand->brand }}</td>
                                                        <td>
                                                            <span class="badge badge-success">{{ $brand->product_count }}</span>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info text-center">
                                            <i class="fas fa-info-circle"></i>
                                            Favori markanız bulunmuyor.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- İstatistikler -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Favori İstatistikleri</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-lg-3 col-md-6">
                                            <div class="description-block border-right">
                                                <span class="description-percentage text-primary">
                                                    <i class="fas fa-heart"></i> {{ $favoriteProducts->count() }}
                                                </span>
                                                <h5 class="description-header">Toplam Favori</h5>
                                                <span class="description-text">Ürün sayısı</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <div class="description-block border-right">
                                                <span class="description-percentage text-success">
                                                    <i class="fas fa-tags"></i> {{ $favoriteCategories->count() }}
                                                </span>
                                                <h5 class="description-header">Kategori</h5>
                                                <span class="description-text">Farklı kategori</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <div class="description-block border-right">
                                                <span class="description-percentage text-warning">
                                                    <i class="fas fa-star"></i> {{ $favoriteBrands->count() }}
                                                </span>
                                                <h5 class="description-header">Marka</h5>
                                                <span class="description-text">Farklı marka</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <div class="description-block">
                                                <span class="description-percentage text-info">
                                                    <i class="fas fa-chart-pie"></i> {{ $favoriteProducts->avg('best_price') ? number_format($favoriteProducts->avg('best_price'), 2) : '0' }}
                                                </span>
                                                <h5 class="description-header">Ortalama Fiyat</h5>
                                                <span class="description-text">Favori ürünler</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
