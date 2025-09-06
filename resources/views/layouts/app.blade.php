<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Basital.com - Teknoloji ve Elektronik')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .product-card {
            transition: transform 0.2s;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .product-image {
            height: 200px;
            object-fit: cover;
        }
        .price {
            font-size: 1.2rem;
            font-weight: bold;
            color: #28a745;
        }
        .stock-badge {
            position: absolute;
            top: 10px;
            right: 10px;
        }
        .filter-sidebar {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }
        .search-box {
            border-radius: 25px;
        }
        .btn-search {
            border-radius: 0 25px 25px 0;
        }
        .footer {
            background-color: #343a40;
            color: white;
            padding: 40px 0;
            margin-top: 50px;
        }
        .loading {
            display: none;
        }
        .loading.show {
            display: block;
        }
        
        /* Categories Navigation Styles */
        .categories-nav {
            background-color: var(--light-bg, #f8f9fa);
            border-bottom: 1px solid var(--border-color, #e5e7eb);
        }
        
        .categories-nav .nav-link {
            color: var(--text-color, #1f2937);
            font-weight: 500;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0 2px;
        }
        
        .categories-nav .nav-link:hover {
            color: var(--primary-color, #2563eb);
            background-color: rgba(37, 99, 235, 0.1);
            transform: translateY(-1px);
        }
        
        .categories-nav .nav-link.active {
            color: var(--primary-color, #2563eb);
            background-color: rgba(37, 99, 235, 0.15);
            font-weight: 600;
        }
        
        /* Search Input Styles */
        .search-input {
            min-width: 250px;
            border-radius: 25px 0 0 25px;
            border: 1px solid rgba(255,255,255,0.3);
            background-color: rgba(255,255,255,0.1);
            color: white;
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            background-color: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.5);
            box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.25);
            color: white;
        }
        
        .search-input::placeholder {
            color: rgba(255,255,255,0.7);
        }
        
        .search-input::-webkit-search-cancel-button {
            -webkit-appearance: none;
            appearance: none;
            height: 16px;
            width: 16px;
            background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='white'%3E%3Cpath d='M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z'/%3E%3C/svg%3E") no-repeat center;
            background-size: contain;
            cursor: pointer;
        }
        
        .btn-outline-light:hover {
            background-color: rgba(255,255,255,0.2);
            border-color: rgba(255,255,255,0.5);
        }
        @media (max-width: 768px) {
            .filter-sidebar {
                margin-bottom: 20px;
            }
            .product-image {
                height: 150px;
            }
            .categories-nav .navbar-nav {
                justify-content: center;
            }
            .categories-nav .nav-link {
                font-size: 0.9rem;
                padding: 8px 12px;
            }
            .search-input {
                min-width: 200px;
            }
        }
        
        @media (max-width: 576px) {
            .search-input {
                min-width: 150px;
                font-size: 0.9rem;
            }
            .navbar-nav .nav-link {
                padding: 0.5rem 0.75rem;
            }
        }
        
        @media (max-width: 576px) {
            .categories-nav .nav-link {
                font-size: 0.8rem;
                padding: 6px 8px;
            }
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-laptop me-2"></i>Basital.com
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Ana Sayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index') }}">Ürünler</a>
                    </li>
                </ul>
                
                <!-- Arama Alanı -->
                <form class="d-flex me-3" action="{{ route('products.index') }}" method="GET">
                    <div class="input-group">
                        <input class="form-control search-input" type="search" name="search" 
                               placeholder="Ürün ara..." value="{{ request('search') }}" 
                               aria-label="Search">
                        <button class="btn btn-outline-light" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('customer.dashboard') }}">
                            <i class="fas fa-user me-1"></i>Hesabım
                        </a>
                    </li>
                    @auth
                        @if(auth()->user()->is_admin)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                    <i class="fas fa-cog me-1"></i>Admin
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-1"></i>Çıkış
                            </a>
                        </li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt me-1"></i>Giriş
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i>Kayıt
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Categories Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light categories-nav">
        <div class="container">
            <div class="navbar-nav flex-row flex-wrap">
                @php
                    $categories = \App\Models\Product::active()
                        ->inStock()
                        ->selectRaw('kategori, COUNT(*) as product_count')
                        ->whereNotNull('kategori')
                        ->groupBy('kategori')
                        ->orderBy('product_count', 'desc')
                        ->take(8)
                        ->get();
                @endphp
                @foreach($categories as $category)
                <a class="nav-link px-3 py-2 text-decoration-none" href="{{ route('products.index', ['category' => $category->kategori]) }}">
                    {{ $category->kategori }}
                </a>
                @endforeach
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container my-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Basital.com</h5>
                    <p>Teknoloji ve elektronik ürünlerde en uygun fiyatlar.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; {{ date('Y') }} Tüm hakları saklıdır.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script>
        // CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>
