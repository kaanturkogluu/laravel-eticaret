@extends('layouts.app')

@section('title', 'Basital.com - Teknoloji ve Elektronik')

@section('styles')
<style>
    /* CSS Custom Properties - Theme Colors */
    :root {
        --primary-color: #2563eb;
        --secondary-color: #1e40af;
        --text-color: #1f2937;
        --bg-color: #ffffff;
        --light-bg: #f3f4f6;
        --border-color: #e5e7eb;
        --card-bg: #ffffff;
        --navbar-bg: rgba(255, 255, 255, 0.95);
        --shadow-color: rgba(0, 0, 0, 0.1);
    }
    
    [data-theme="dark"] {
        --primary-color: #3b82f6;
        --secondary-color: #60a5fa;
        --text-color: #f3f4f6;
        --bg-color: #111827;
        --light-bg: #1f2937;
        --border-color: #374151;
        --card-bg: #1f2937;
        --navbar-bg: rgba(17, 24, 39, 0.95);
        --shadow-color: rgba(0, 0, 0, 0.3);
    }

    /* Hero Section with CSS Variables */
    .hero-section {
        position: relative;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        min-height: 50vh;
        display: flex;
        align-items: center;
        overflow: hidden;
    }
    
    .hero-background {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    }
    
    .hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.3);
    }
    
    .hero-content {
        position: relative;
        z-index: 2;
    }
    
    .hero-title {
        font-size: 3.5rem;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 1.5rem;
    }
    
    .hero-subtitle {
        font-size: 1.25rem;
        opacity: 0.9;
        margin-bottom: 2rem;
    }
    
    .hero-buttons .btn {
        border-radius: 50px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }
    
    .hero-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    
    .hero-intro {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .hero-features {
        margin: 2rem 0;
    }
    
    .feature-item {
        padding: 15px;
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 10px;
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .feature-item:hover {
        background: rgba(255,255,255,0.15);
        transform: translateY(-5px);
    }
    
    .feature-item h5 {
        color: white;
        font-weight: 600;
        margin-bottom: 10px;
    }
    
    .feature-item p {
        color: rgba(255,255,255,0.8);
        margin: 0;
    }
    
    .hero-stats {
        margin: 2rem 0;
    }
    
    .hero-stats .stat-item {
        text-align: center;
        padding: 15px;
        background: rgba(255,255,255,0.1);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    
    .hero-stats .stat-item:hover {
        background: rgba(255,255,255,0.15);
        transform: translateY(-5px);
    }
    
    .hero-stats .stat-number {
        font-size: 2.5rem;
        font-weight: 800;
        color: #ffc107;
        margin-bottom: 5px;
    }
    
    .hero-stats .stat-item p {
        color: rgba(255,255,255,0.8);
        margin: 0;
        font-weight: 500;
    }
    
    /* Responsive Hero Section */
    @media (max-width: 768px) {
        .hero-title {
            font-size: 2.5rem;
        }
        
        .hero-subtitle {
            font-size: 1rem;
        }
        
        .hero-features {
            margin: 2rem 0;
        }
        
        .feature-item {
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .hero-stats {
            margin: 2rem 0;
        }
        
        .hero-stats .stat-item {
            padding: 15px;
            margin-bottom: 15px;
        }
        
        .hero-stats .stat-number {
            font-size: 2rem;
        }
        
        .hero-buttons .btn {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }
    }
    
    @media (max-width: 576px) {
        .hero-title {
            font-size: 2rem;
        }
        
        .hero-subtitle {
            font-size: 0.9rem;
        }
        
        .feature-item h5 {
            font-size: 1rem;
        }
        
        .hero-stats .stat-number {
            font-size: 1.8rem;
        }
    }
    
    .min-vh-50 {
        min-height: 50vh;
    }
    
    /* Custom Button Styles with CSS Variables */
    .btn-outline-primary {
        border-color: var(--primary-color);
        color: var(--primary-color);
    }
    
    .btn-outline-primary:hover {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    
    .btn-primary:hover {
        background-color: var(--secondary-color);
        border-color: var(--secondary-color);
    }
    
    .campaign-card {
        border-radius: 15px;
        overflow: hidden;
        transition: transform 0.3s;
        height: 100%;
    }
    .campaign-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px var(--shadow-color);
    }
    .campaign-card img {
        height: 200px;
        object-fit: cover;
    }
    /* Modern Section Titles */
    .section-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-color);
        margin-bottom: 1.5rem;
        position: relative;
        text-align: center;
        letter-spacing: -0.5px;
    }
    
    .section-title::before {
        content: '';
        position: absolute;
        top: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        border-radius: 2px;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: -15px;
        left: 50%;
        transform: translateX(-50%);
        width: 40px;
        height: 2px;
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        border-radius: 1px;
    }
    
    .section-subtitle {
        text-align: center;
        color: #7f8c8d;
        font-size: 1.1rem;
        margin-bottom: 3rem;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
    /* Modern Product Cards */
    .product-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        height: 100%;
        box-shadow: 0 4px 15px var(--shadow-color);
        background: var(--card-bg);
        position: relative;
    }
    
    .product-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        transform: scaleX(0);
        transition: transform 0.3s ease;
        z-index: 1;
    }
    
    .product-card:hover::before {
        transform: scaleX(1);
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px var(--shadow-color);
    }
    .product-image {
        height: 180px;
        object-fit: cover;
    }
    .price {
        font-size: 1.3rem;
        font-weight: bold;
        color: var(--primary-color);
    }
    .old-price {
        text-decoration: line-through;
        color: #6c757d;
        font-size: 0.9rem;
    }
    .discount-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: #dc3545;
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: bold;
    }
    .stock-badge {
        position: absolute;
        top: 10px;
        left: 10px;
    }
    /* MediaMarkt Style Brand Cards */
    .brand-card {
        background: var(--card-bg);
        border-radius: 10px;
        padding: 20px 15px;
        text-align: center;
        box-shadow: 0 2px 10px var(--shadow-color);
        transition: all 0.3s ease;
        height: 100%;
        border: 1px solid var(--border-color);
        position: relative;
        overflow: hidden;
    }
    
    .brand-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }
    
    .brand-card:hover::before {
        transform: scaleX(1);
    }
    
    .brand-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 20px var(--shadow-color);
        border-color: var(--primary-color);
    }
    
    .brand-card h5 {
        color: var(--text-color);
        font-weight: 600;
        margin-bottom: 8px;
        font-size: 1.1rem;
    }
    
    .brand-card p {
        color: #6c757d;
        font-size: 0.9rem;
        margin: 0;
    }
    /* Stats Section - Compact */
    .stats-section {
        background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        color: white;
        padding: 40px 0;
        margin: 40px 0;
        position: relative;
        overflow: hidden;
    }
    
    .stats-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="10" cy="60" r="0.5" fill="rgba(255,255,255,0.05)"/><circle cx="90" cy="40" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }
    
    .stat-item {
        text-align: center;
        padding: 30px 20px;
        position: relative;
        z-index: 2;
        transition: transform 0.3s ease;
    }
    
    .stat-item:hover {
        transform: translateY(-10px);
    }
    
    .stat-number {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 15px;
        background: linear-gradient(45deg, #fff, #f8f9fa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .stat-item p {
        font-size: 1.1rem;
        font-weight: 500;
        opacity: 0.9;
        margin: 0;
    }
    .newsletter-section {
        background: #f8f9fa;
        padding: 60px 0;
        margin: 50px 0;
    }
    /* Main Slider Styles */
    .main-slider {
        height: 500px;
        overflow: hidden;
        position: relative;
    }
    
    .slider-image-container {
        position: relative;
        height: 500px;
        overflow: hidden;
    }
    
    .slider-image {
        height: 500px;
        object-fit: cover;
        width: 100%;
        transition: transform 0.5s ease;
    }
    
    .slider-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.2) 100%);
        z-index: 1;
    }
    
    .slider-caption {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 2;
        text-align: center;
        color: white;
        width: 100%;
    }
    
    .slider-title {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.7);
        animation: slideInUp 1s ease-out;
    }
    
    .slider-description {
        font-size: 1.3rem;
        margin-bottom: 2rem;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.7);
        animation: slideInUp 1s ease-out 0.3s both;
    }
    
    .slider-btn {
        border-radius: 50px;
        padding: 15px 40px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        animation: slideInUp 1s ease-out 0.6s both;
        transition: all 0.3s ease;
    }
    
    .slider-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.4);
    }
    
    .slider-control {
        width: 60px;
        height: 60px;
        background: rgba(0,0,0,0.5);
        border-radius: 50%;
        top: 50%;
        transform: translateY(-50%);
        transition: all 0.3s ease;
        border: 2px solid rgba(255,255,255,0.3);
        z-index: 10;
        position: absolute;
    }
    
    .slider-control:hover {
        background: rgba(0,0,0,0.7);
        border-color: rgba(255,255,255,0.6);
        transform: translateY(-50%) scale(1.1);
    }
    
    .slider-control .carousel-control-prev-icon,
    .slider-control .carousel-control-next-icon {
        width: 20px;
        height: 20px;
    }
    
    .carousel-control-prev {
        left: 20px;
    }
    
    .carousel-control-next {
        right: 20px;
    }
    
    .main-slider .carousel-indicators {
        bottom: 30px;
        z-index: 3;
    }
    
    .main-slider .carousel-indicators button {
        width: 15px;
        height: 15px;
        border-radius: 50%;
        margin: 0 8px;
        border: 3px solid white;
        background: transparent;
        transition: all 0.3s ease;
        opacity: 0.7;
    }
    
    .main-slider .carousel-indicators button.active {
        background: white;
        opacity: 1;
        transform: scale(1.3);
    }
    
    /* Animations */
    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

     @media (max-width: 768px) {
         .hero-banner {
             padding: 30px 0;
         }
         .main-slider {
             height: 300px;
         }
         .slider-image-container {
             height: 300px;
         }
         .slider-image {
             height: 300px;
         }
         .slider-title {
             font-size: 2rem;
         }
         .slider-description {
             font-size: 1rem;
         }
         .product-image {
             height: 150px;
         }
     }
     
     @media (max-width: 576px) {
         .slider-title {
             font-size: 1.5rem;
         }
         .slider-description {
             font-size: 0.9rem;
         }
     }
</style>
@endsection

@section('content')
<!-- Hero Section with Company Introduction -->
<section class="hero-section">
    <div class="hero-background">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="row align-items-center min-vh-50">
                <div class="col-lg-10 mx-auto text-center">
                    <div class="hero-content text-white">
                        <h1 class="hero-title display-3 fw-bold mb-4">
                            Basital.com'a Hoş Geldiniz
                        </h1>
                        <div class="hero-intro">
                            <p class="hero-subtitle lead mb-4">
                                Türkiye'nin önde gelen teknoloji ve elektronik ürünleri platformu olarak, 
                                müşterilerimize en kaliteli ürünleri en uygun fiyatlarla sunuyoruz.
                            </p>
                            
                            <div class="hero-features mb-4">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <div class="feature-item">
                                            <i class="fas fa-shipping-fast fa-2x mb-2 text-warning"></i>
                                            <h6>Hızlı Teslimat</h6>
                                            <p class="small mb-0">24 saat içinde teslimat</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="feature-item">
                                            <i class="fas fa-shield-alt fa-2x mb-2 text-warning"></i>
                                            <h6>Güvenli Alışveriş</h6>
                                            <p class="small mb-0">SSL korumalı ödeme</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <div class="feature-item">
                                            <i class="fas fa-headset fa-2x mb-2 text-warning"></i>
                                            <h6>7/24 Destek</h6>
                                            <p class="small mb-0">Uzman ekip desteği</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="hero-stats mb-4">
                                <div class="row">
                                    <div class="col-md-3 col-6 mb-2">
                                        <div class="stat-item">
                                            <div class="stat-number">{{ $stats['totalProducts'] }}+</div>
                                            <p class="small mb-0">Ürün</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6 mb-2">
                                        <div class="stat-item">
                                            <div class="stat-number">{{ $stats['totalBrands'] }}+</div>
                                            <p class="small mb-0">Marka</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6 mb-2">
                                        <div class="stat-item">
                                            <div class="stat-number">{{ $stats['totalCategories'] }}+</div>
                                            <p class="small mb-0">Kategori</p>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-6 mb-2">
                                        <div class="stat-item">
                                            <div class="stat-number">10K+</div>
                                            <p class="small mb-0">Müşteri</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="hero-buttons">
                                <a href="{{ route('products.index') }}" class="btn btn-warning btn-lg me-3 px-5 py-3 fw-bold">
                                    <i class="fas fa-shopping-bag me-2"></i>Alışverişe Başla
                                </a>
                                <a href="#featured-products" class="btn btn-outline-light btn-lg px-5 py-3 fw-bold">
                                    <i class="fas fa-star me-2"></i>Öne Çıkan Ürünler
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="container-fluid px-0">
    <!-- Main Slider -->
    @if($sliders->count() > 0)
    <section class="mb-4 py-4">
        <div class="container-fluid px-0">
            <div id="mainSlider" class="carousel slide main-slider" data-bs-ride="carousel" data-bs-interval="5000">
                <div class="carousel-indicators">
                    @foreach($sliders as $index => $slider)
                        <button type="button" data-bs-target="#mainSlider" data-bs-slide-to="{{ $index }}" 
                                class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}" 
                                aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>
                <div class="carousel-inner">
                    @foreach($sliders as $index => $slider)
                        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                            <div class="slider-image-container">
                                <img src="{{ asset('storage/' . $slider->image_url) }}" 
                                     class="d-block w-100 slider-image" 
                                     alt="{{ $slider->title }}">
                                <div class="slider-overlay"></div>
                            </div>
                            <div class="carousel-caption slider-caption">
                                <div class="container">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-8 text-center">
                                            <h2 class="slider-title">{{ $slider->title }}</h2>
                                            @if($slider->description)
                                                <p class="slider-description">{{ $slider->description }}</p>
                                            @endif
                                            @if($slider->link_url && $slider->link_text)
                                                <a href="{{ $slider->link_url }}" class="btn btn-warning btn-lg slider-btn">
                                                    {{ $slider->link_text }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($sliders->count() > 1)
                    <button class="carousel-control-prev slider-control" type="button" data-bs-target="#mainSlider" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next slider-control" type="button" data-bs-target="#mainSlider" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                @endif
            </div>
        </div>
    </section>
    @endif


    <!-- Kampanyalar -->
    @if($campaigns->count() > 0)
    <section class="mb-4 py-4">
        <div class="container">
            <h2 class="section-title">🔥 Özel Kampanyalar</h2>
            <p class="section-subtitle">Size özel indirimli fırsatları kaçırmayın</p>
            <div class="row">
            @foreach($campaigns as $campaign)
            <div class="col-md-4 mb-4">
                <div class="card campaign-card">
                    <img src="{{ $campaign->image_url }}" class="card-img-top" alt="{{ $campaign->title }}">
                    <div class="card-body">
                        <h5 class="card-title">{{ $campaign->title }}</h5>
                        @if($campaign->description)
                        <p class="card-text">{{ Str::limit($campaign->description, 100) }}</p>
                        @endif
                        @if($campaign->link_url)
                        <a href="{{ $campaign->link_url }}" class="btn btn-primary fw-bold">Detayları Gör</a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
            </div>
        </div>
    </section>
    @endif


    <!-- Öne Çıkan Ürünler -->
    <section class="mb-4 py-4" style="background-color: var(--light-bg);">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="section-title mb-0">⭐ Öne Çıkan Ürünler</h2>
                <a href="{{ route('products.index') }}" class="btn btn-outline-primary fw-bold">Tümünü Gör</a>
            </div>
            <div class="row">
            @foreach($featuredProducts as $product)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card product-card position-relative">
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
                        <div class="position-absolute top-0 end-0 m-2">
                            <span class="badge bg-success">Stokta</span>
                        </div>
                        
                        @auth
                        <!-- Favori Butonu -->
                        <button class="btn btn-sm btn-outline-danger position-absolute" 
                                style="top: 10px; left: 10px; z-index: 10;"
                                onclick="toggleFavorite('{{ $product->kod }}', this)"
                                data-product-kod="{{ $product->kod }}">
                            <i class="fas fa-heart"></i>
                        </button>
                        @endauth
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ Str::limit($product->ad, 50) }}</h5>
                        <p class="card-text text-muted">{{ $product->marka }}</p>
                        <div class="price mb-3">
                            <div class="fw-bold text-success fs-5">
                                {{ $product->formatted_price_with_profit_in_try }}
                            </div>
                            @if($product->doviz !== 'TRY')
                                <small class="text-muted">
                                    ({{ $product->formatted_price_with_profit }})
                                </small>
                            @endif
                            @if($product->fiyat_sk && $product->fiyat_ozel && $product->fiyat_sk > $product->fiyat_ozel)
                            <br><small class="old-price">{{ number_format($product->fiyat_sk, 2) }} {{ $product->doviz }}</small>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Stok: {{ $product->miktar }}</small>
                            <a href="{{ route('products.show', $product->kod) }}" 
                               class="btn btn-primary btn-sm w-100 fw-bold">
                                <i class="fas fa-eye me-1"></i>Detayları Gör
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            </div>
        </div>
    </section>

    <!-- Yeni Ürünler -->
    <section class="mb-4 py-4">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="section-title mb-0">🆕 Yeni Ürünler</h2>
                <a href="{{ route('products.index', ['sort' => 'created_at', 'direction' => 'desc']) }}" class="btn btn-outline-primary fw-bold">Tümünü Gör</a>
            </div>
            <div class="row">
            @foreach($newProducts as $product)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="card product-card position-relative">
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
                        
                        @auth
                        <!-- Favori Butonu -->
                        <button class="btn btn-sm btn-outline-danger position-absolute" 
                                style="top: 10px; left: 10px; z-index: 10;"
                                onclick="toggleFavorite('{{ $product->kod }}', this)"
                                data-product-kod="{{ $product->kod }}">
                            <i class="fas fa-heart"></i>
                        </button>
                        @endauth
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ Str::limit($product->ad, 50) }}</h5>
                        <p class="card-text text-muted">{{ $product->marka }}</p>
                        <div class="price mb-3">
                            <div class="fw-bold text-success fs-5">
                                {{ $product->formatted_price_with_profit_in_try }}
                            </div>
                            @if($product->doviz !== 'TRY')
                                <small class="text-muted">
                                    ({{ $product->formatted_price_with_profit }})
                                </small>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">Stok: {{ $product->miktar }}</small>
                            <a href="{{ route('products.show', $product->kod) }}" 
                               class="btn btn-primary btn-sm w-100 fw-bold">
                                <i class="fas fa-eye me-1"></i>Detayları Gör
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            </div>
        </div>
    </section>

    <!-- Markalar -->
    <section class="mb-4 py-4" style="background-color: var(--light-bg);">
        <div class="container">
            <h2 class="section-title">🏆 Popüler Markalar</h2>
            <p class="section-subtitle">Güvenilir markaların kaliteli ürünleri</p>
            <div class="row">
            @foreach($brands as $brand)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <a href="{{ route('products.index', ['brand' => $brand->marka]) }}" class="text-decoration-none">
                    <div class="brand-card">
                        <h5>{{ $brand->marka }}</h5>
                        <p class="text-muted mb-0">{{ $brand->product_count }} ürün</p>
                    </div>
                </a>
            </div>
            @endforeach
            </div>
        </div>
    </section>
</div>

<!-- İstatistikler -->
<section class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-item">
                    <div class="stat-number">{{ $stats['activeProducts'] }}</div>
                    <p>Aktif Ürün</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-item">
                    <div class="stat-number">{{ $stats['totalBrands'] }}</div>
                    <p>Marka</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-item">
                    <div class="stat-number">{{ $stats['totalCategories'] }}</div>
                    <p>Kategori</p>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-item">
                    <div class="stat-number">{{ $stats['totalProducts'] }}</div>
                    <p>Toplam Ürün</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
// Slider butonları debug
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('mainSlider');
    if (slider) {
        console.log('Slider found:', slider);
        
        const prevBtn = slider.querySelector('.carousel-control-prev');
        const nextBtn = slider.querySelector('.carousel-control-next');
        
        if (prevBtn) {
            console.log('Prev button found:', prevBtn);
            prevBtn.addEventListener('click', function() {
                console.log('Prev button clicked');
            });
        }
        
        if (nextBtn) {
            console.log('Next button found:', nextBtn);
            nextBtn.addEventListener('click', function() {
                console.log('Next button clicked');
            });
        }
    } else {
        console.log('Slider not found');
    }
});

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

// Toast mesajı göster
function showToast(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 
                      type === 'error' ? 'alert-danger' : 'alert-info';
    const iconClass = type === 'success' ? 'fa-check-circle' : 
                     type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';
    
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

</script>
@endsection
