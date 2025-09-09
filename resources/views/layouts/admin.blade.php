<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - Basital.com')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #2c3e50;
            --sidebar-hover: #34495e;
            --sidebar-active: #3498db;
            --content-bg: #f8f9fa;
            --header-bg: #ffffff;
            --border-color: #e9ecef;
        }

        body {
            background-color: var(--content-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(135deg, var(--sidebar-bg) 0%, #34495e 100%);
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }

        .sidebar-header .logo {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
            display: block;
        }

        .sidebar-header .logo:hover {
            color: var(--sidebar-active);
            text-decoration: none;
        }

        .sidebar.collapsed .sidebar-header .logo-text {
            display: none;
        }

        .sidebar.collapsed .sidebar-header .logo-icon {
            font-size: 1.8rem;
        }

        /* Small Box Styles */
        .small-box {
            border-radius: 2px;
            position: relative;
            display: block;
            margin-bottom: 20px;
            box-shadow: 0 1px 1px rgba(0,0,0,0.1);
        }

        .small-box > .inner {
            padding: 10px;
        }

        .small-box .icon {
            -webkit-transition: all .3s linear;
            -o-transition: all .3s linear;
            transition: all .3s linear;
            position: absolute;
            top: -10px;
            right: 10px;
            z-index: 0;
            font-size: 90px;
            color: rgba(0,0,0,0.15);
        }

        .small-box:hover .icon {
            font-size: 95px;
        }

        .small-box > .small-box-footer {
            position: relative;
            text-align: center;
            padding: 3px 0;
            color: #fff;
            color: rgba(255,255,255,0.8);
            display: block;
            z-index: 10;
            background: rgba(0,0,0,0.1);
            text-decoration: none;
        }

        .small-box > .small-box-footer:hover {
            color: #fff;
            background: rgba(0,0,0,0.15);
        }

        .small-box h3, .small-box p {
            z-index: 5;
        }

        .small-box h3 {
            font-size: 38px;
            font-weight: bold;
            margin: 0 0 10px 0;
            white-space: nowrap;
            padding: 0;
        }

        .small-box p {
            font-size: 15px;
        }

        .small-box .inner h3 {
            margin-top: 0;
        }

        .small-box .inner p {
            margin-bottom: 0;
        }

        /* Small Box Colors */
        .small-box.bg-aqua {
            background-color: #00c0ef !important;
            color: #fff;
        }

        .small-box.bg-green {
            background-color: #00a65a !important;
            color: #fff;
        }

        .small-box.bg-yellow {
            background-color: #f39c12 !important;
            color: #fff;
        }

        .small-box.bg-red {
            background-color: #dd4b39 !important;
            color: #fff;
        }

        .small-box.bg-info {
            background-color: #17a2b8 !important;
            color: #fff;
        }

        .small-box.bg-success {
            background-color: #28a745 !important;
            color: #fff;
        }

        .small-box.bg-warning {
            background-color: #ffc107 !important;
            color: #212529;
        }

        .small-box.bg-danger {
            background-color: #dc3545 !important;
            color: #fff;
        }

        /* Info Box Styles */
        .info-box {
            display: block;
            min-height: 90px;
            background: #fff;
            width: 100%;
            box-shadow: 0 1px 1px rgba(0,0,0,0.1);
            border-radius: 2px;
            margin-bottom: 15px;
        }

        .info-box-icon {
            border-top-left-radius: 2px;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
            border-bottom-left-radius: 2px;
            display: block;
            float: left;
            height: 90px;
            width: 90px;
            text-align: center;
            font-size: 45px;
            line-height: 90px;
            background: rgba(0,0,0,0.2);
        }

        .info-box-content {
            padding: 5px 10px;
            margin-left: 90px;
        }

        .info-box-text {
            text-transform: uppercase;
            font-weight: bold;
            font-size: 14px;
        }

        .info-box-number {
            display: block;
            font-weight: bold;
            font-size: 18px;
        }

        .info-box-icon.bg-aqua {
            background-color: #00c0ef !important;
            color: #fff;
        }

        .info-box-icon.bg-green {
            background-color: #00a65a !important;
            color: #fff;
        }

        .info-box-icon.bg-yellow {
            background-color: #f39c12 !important;
            color: #fff;
        }

        .info-box-icon.bg-red {
            background-color: #dd4b39 !important;
            color: #fff;
        }

        .info-box-icon.bg-info {
            background-color: #17a2b8 !important;
            color: #fff;
        }

        .info-box-icon.bg-success {
            background-color: #28a745 !important;
            color: #fff;
        }

        .info-box-icon.bg-warning {
            background-color: #ffc107 !important;
            color: #212529;
        }

        .info-box-icon.bg-danger {
            background-color: #dc3545 !important;
            color: #fff;
        }

        /* Description Block Styles */
        .description-block {
            display: block;
            margin: 10px 0;
            text-align: center;
        }

        .description-block > .description-header {
            margin: 0;
            padding: 0;
            font-weight: 600;
            font-size: 16px;
        }

        .description-block > .description-text {
            text-transform: uppercase;
            font-weight: 500;
            font-size: 12px;
            color: #999;
        }

        .description-block > .description-percentage {
            font-size: 18px;
            font-weight: 600;
        }

        .description-block.border-right {
            border-right: 1px solid #f4f4f4;
        }

        /* Card Styles */
        .card {
            position: relative;
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: vertical;
            -webkit-box-direction: normal;
            -ms-flex-direction: column;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 1px solid rgba(0,0,0,.125);
            border-radius: .25rem;
        }

        .card-header {
            padding: .75rem 1.25rem;
            margin-bottom: 0;
            background-color: rgba(0,0,0,.03);
            border-bottom: 1px solid rgba(0,0,0,.125);
        }

        .card-header:first-child {
            border-radius: calc(.25rem - 1px) calc(.25rem - 1px) 0 0;
        }

        .card-body {
            -webkit-box-flex: 1;
            -ms-flex: 1 1 auto;
            flex: 1 1 auto;
            padding: 1.25rem;
        }

        .card-title {
            margin-bottom: .75rem;
            font-size: 1.25rem;
            font-weight: 500;
        }

        .card-tools {
            float: right;
            margin-right: -.625rem;
        }

        /* Table Styles */
        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }

        .table th,
        .table td {
            padding: .75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody + tbody {
            border-top: 2px solid #dee2e6;
        }

        .table-bordered {
            border: 1px solid #dee2e6;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0,0,0,.05);
        }

        .table-sm th,
        .table-sm td {
            padding: .3rem;
        }

        /* Badge Styles */
        .badge {
            display: inline-block;
            padding: .25em .4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25rem;
        }

        .badge:empty {
            display: none;
        }

        .badge-primary {
            color: #fff;
            background-color: #007bff;
        }

        .badge-secondary {
            color: #fff;
            background-color: #6c757d;
        }

        .badge-success {
            color: #fff;
            background-color: #28a745;
        }

        .badge-danger {
            color: #fff;
            background-color: #dc3545;
        }

        .badge-warning {
            color: #212529;
            background-color: #ffc107;
        }

        .badge-info {
            color: #fff;
            background-color: #17a2b8;
        }

        .badge-light {
            color: #212529;
            background-color: #f8f9fa;
        }

        .badge-dark {
            color: #fff;
            background-color: #343a40;
        }

        /* Alert Styles */
        .alert {
            position: relative;
            padding: .75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: .25rem;
        }

        .alert-info {
            color: #0c5460;
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }

        .alert-warning {
            color: #856404;
            background-color: #fff3cd;
            border-color: #ffeaa7;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        /* Form Styles */
        .form-inline {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex;
            -webkit-box-orient: horizontal;
            -webkit-box-direction: normal;
            -ms-flex-direction: row;
            flex-direction: row;
            -webkit-box-align: center;
            -ms-flex-align: center;
            align-items: center;
            -webkit-box-pack: start;
            -ms-flex-pack: start;
            justify-content: flex-start;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-control {
            display: block;
            width: 100%;
            padding: .375rem .75rem;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid #ced4da;
            border-radius: .25rem;
            transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }

        .form-control:focus {
            color: #495057;
            background-color: #fff;
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 .2rem rgba(0,123,255,.25);
        }

        /* Button Styles */
        .btn {
            display: inline-block;
            font-weight: 400;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            border: 1px solid transparent;
            padding: .375rem .75rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: .25rem;
            transition: color .15s ease-in-out,background-color .15s ease-in-out,border-color .15s ease-in-out,box-shadow .15s ease-in-out;
        }

        .btn-primary {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            color: #fff;
            background-color: #0069d9;
            border-color: #0062cc;
        }

        .btn-success {
            color: #fff;
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            color: #fff;
            background-color: #218838;
            border-color: #1e7e34;
        }

        .btn-sm {
            padding: .25rem .5rem;
            font-size: .875rem;
            line-height: 1.5;
            border-radius: .2rem;
        }

        .btn-outline-primary {
            color: #007bff;
            background-color: transparent;
            background-image: none;
            border-color: #007bff;
        }

        .btn-outline-primary:hover {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-block {
            display: block;
            width: 100%;
        }

        /* Text Colors */
        .text-primary {
            color: #007bff !important;
        }

        .text-success {
            color: #28a745 !important;
        }

        .text-warning {
            color: #ffc107 !important;
        }

        .text-danger {
            color: #dc3545 !important;
        }

        .text-info {
            color: #17a2b8 !important;
        }

        .text-white {
            color: #fff !important;
        }

        .text-muted {
            color: #6c757d !important;
        }

        /* Navigation Styles */
        .sidebar-nav {
            padding: 20px 0;
        }

        .nav-item {
            margin: 5px 15px;
        }

        .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            text-decoration: none;
            position: relative;
        }

        .nav-link:hover {
            color: white;
            background-color: var(--sidebar-hover);
            transform: translateX(5px);
        }

        .nav-link.active {
            color: white;
            background-color: var(--sidebar-active);
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }

        .nav-link i {
            width: 20px;
            margin-right: 12px;
            text-align: center;
        }

        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 12px;
        }

        .sidebar.collapsed .nav-link i {
            margin-right: 0;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 70px;
        }

        /* Header */
        .main-header {
            background: var(--header-bg);
            padding: 15px 30px;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .sidebar-toggle {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: #6c757d;
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .sidebar-toggle:hover {
            background-color: #f8f9fa;
            color: #495057;
        }

        .page-title {
            margin: 0;
            margin-left: 15px;
            color: #2c3e50;
            font-weight: 600;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #6c757d;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3498db, #2980b9);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        /* Content Area */
        .content-wrapper {
            padding: 30px;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
            transform: translateY(-2px);
        }

        .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid var(--border-color);
            border-radius: 12px 12px 0 0 !important;
            padding: 20px;
        }

        .card-header h5 {
            margin: 0;
            color: #2c3e50;
            font-weight: 600;
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
        }

        /* Tables */
        .table {
            border-radius: 8px;
            overflow: hidden;
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid var(--border-color);
            font-weight: 600;
            color: #2c3e50;
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .content-wrapper {
                padding: 20px 15px;
            }

            .main-header {
                padding: 15px 20px;
            }
        }

        /* Loading Animation */
        .loading {
            display: none;
        }

        .loading.show {
            display: block;
        }

        /* Custom Scrollbar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.5);
        }

        /* Stats Cards */
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .stats-card.primary {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        }

        .stats-card.success {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
        }

        .stats-card.warning {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
        }

        .stats-card.danger {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
        }

        .stats-card.info {
            background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);
        }

        .stats-icon {
            font-size: 2.5rem;
            opacity: 0.8;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            margin: 10px 0 5px 0;
        }

        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin.dashboard') }}" class="logo">
                <i class="fas fa-cogs logo-icon"></i>
                <span class="logo-text">Admin Panel</span>
            </a>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                        <i class="fas fa-box"></i>
                        <span>Ürün Yönetimi</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.xml-import') ? 'active' : '' }}" href="{{ route('admin.xml-import') }}">
                        <i class="fas fa-file-import"></i>
                        <span>XML İçe Aktar</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.xml-history*') ? 'active' : '' }}" href="{{ route('admin.xml-history') }}">
                        <i class="fas fa-history"></i>
                        <span>XML Geçmişi</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('cart.*') ? 'active' : '' }}" href="{{ route('cart.index') }}">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Sepet Yönetimi</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.coupons*') ? 'active' : '' }}" href="{{ route('admin.coupons.index') }}">
                        <i class="fas fa-ticket-alt"></i>
                        <span>Kupon Yönetimi</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.campaigns*') ? 'active' : '' }}" href="{{ route('admin.campaigns.index') }}">
                        <i class="fas fa-bullhorn"></i>
                        <span>Kampanya Yönetimi</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.featured-products*') ? 'active' : '' }}" href="{{ route('admin.featured-products.index') }}">
                        <i class="fas fa-star"></i>
                        <span>Öne Çıkan Ürünler</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#stockControlModal">
                        <i class="fas fa-boxes"></i>
                        <span>Stok Kontrolü</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#marketplaceModal">
                        <i class="fas fa-store"></i>
                        <span>Pazaryeri Entegrasyonu</span>
                    </a>
                </li>
                

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.payment-providers*') ? 'active' : '' }}" href="{{ route('admin.payment-providers.index') }}">
                        <i class="fas fa-credit-card"></i>
                        <span>Ödeme Sağlayıcıları</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.payment-transactions*') ? 'active' : '' }}" href="{{ route('admin.payment-transactions.index') }}">
                        <i class="fas fa-receipt"></i>
                        <span>Ödeme İşlemleri</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.cargo-companies*') ? 'active' : '' }}" href="{{ route('admin.cargo-companies.index') }}">
                        <i class="fas fa-truck"></i>
                        <span>Kargo Şirketleri</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.cargo-trackings*') ? 'active' : '' }}" href="{{ route('admin.cargo-trackings.index') }}">
                        <i class="fas fa-shipping-fast"></i>
                        <span>Kargo Takip</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reports*') ? 'active' : '' }}" href="{{ route('admin.reports.dashboard') }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Raporlama</span>
                    </a>
                </li>
                
                <li class="nav-item mt-3">
                    <hr style="border-color: rgba(255,255,255,0.2);">
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                        <span>Siteyi Görüntüle</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Çıkış Yap</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Header -->
        <header class="main-header">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            </div>
            
            <div class="header-right">
                <div class="user-info">
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-bold">{{ auth()->user()->name }}</div>
                        <small class="text-muted">Admin</small>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <div class="content-wrapper">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Logout Form -->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Stok Kontrol Modal -->
    <div class="modal fade" id="stockControlModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="fas fa-boxes me-2"></i>Stok Kontrolü</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.stock-control') }}" id="stockControlForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Belirtilen minimum stok miktarının altında kalan ürünleri kontrol edin.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Minimum Stok Miktarı</label>
                            <input type="number" name="min_stock" value="2" min="0" class="form-control" 
                                   placeholder="Minimum stok miktarını girin" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-search me-2"></i>Stok Kontrolü Yap
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Pazaryeri Entegrasyonu Modal -->
    <div class="modal fade" id="marketplaceModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-store me-2"></i>Pazaryeri Entegrasyonu</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Hepsiburada</h6>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">Hepsiburada pazaryerine ürünlerinizi otomatik olarak aktarın.</p>
                                    <div class="mb-3">
                                        <label class="form-label">API Anahtarı</label>
                                        <input type="password" class="form-control" placeholder="Hepsiburada API anahtarınız">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Satıcı ID</label>
                                        <input type="text" class="form-control" placeholder="Satıcı ID'niz">
                                    </div>
                                    <button class="btn btn-warning w-100">Hepsiburada Bağlantısını Test Et</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Trendyol</h6>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">Trendyol pazaryerine ürünlerinizi otomatik olarak aktarın.</p>
                                    <div class="mb-3">
                                        <label class="form-label">API Anahtarı</label>
                                        <input type="password" class="form-control" placeholder="Trendyol API anahtarınız">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Satıcı ID</label>
                                        <input type="text" class="form-control" placeholder="Satıcı ID'niz">
                                    </div>
                                    <button class="btn btn-primary w-100">Trendyol Bağlantısını Test Et</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="fas fa-sync me-2"></i>Otomatik Senkronizasyon</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Senkronizasyon Sıklığı</label>
                                            <select class="form-control">
                                                <option value="30">30 dakikada bir</option>
                                                <option value="60">1 saatte bir</option>
                                                <option value="120">2 saatte bir</option>
                                                <option value="240">4 saatte bir</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Stok Güncelleme</label>
                                            <select class="form-control">
                                                <option value="1">Otomatik güncelle</option>
                                                <option value="0">Manuel güncelle</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Fiyat Güncelleme</label>
                                            <select class="form-control">
                                                <option value="1">Otomatik güncelle</option>
                                                <option value="0">Manuel güncelle</option>
                                            </select>
                                        </div>
                                    </div>
                                    <button class="btn btn-success">Senkronizasyon Ayarlarını Kaydet</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
        // CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const sidebarToggle = document.getElementById('sidebarToggle');
            
            // Toggle sidebar
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            });

            // Mobile sidebar toggle
            if (window.innerWidth <= 768) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });
            }

            // Close sidebar on mobile when clicking outside
            document.addEventListener('click', function(e) {
                if (window.innerWidth <= 768) {
                    if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                        sidebar.classList.remove('show');
                    }
                }
            });
        });

        // Stok kontrol formu AJAX ile gönder
        $('#stockControlForm').on('submit', function(e) {
            e.preventDefault();
            
            var form = $(this);
            var formData = new FormData(this);
            var submitBtn = form.find('button[type="submit"]');
            
            // Butonu devre dışı bırak
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>İşleniyor...');
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        // Modal'ı kapat
                        $('#stockControlModal').modal('hide');
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function(xhr) {
                    var errorMessage = 'Stok kontrol işlemi sırasında hata oluştu.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showAlert('error', errorMessage);
                },
                complete: function() {
                    // Butonu eski haline getir
                    submitBtn.prop('disabled', false).html('<i class="fas fa-search me-2"></i>Stok Kontrolü Yap');
                }
            });
        });

        // Alert gösterme fonksiyonu
        function showAlert(type, message) {
            var alertClass = type === 'error' ? 'danger' : type;
            var alertHtml = `
                <div class="alert alert-${alertClass} alert-dismissible fade show position-fixed" 
                     style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            $('body').append(alertHtml);
            
            // 5 saniye sonra otomatik kapat
            setTimeout(() => {
                $('.alert').fadeOut();
            }, 5000);
        }
    </script>
    
    @yield('scripts')
</body>
</html>
