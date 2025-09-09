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

        .btn-outline-success {
            color: #28a745;
            background-color: transparent;
            background-image: none;
            border-color: #28a745;
        }

        .btn-outline-success:hover {
            color: #fff;
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-outline-warning {
            color: #ffc107;
            background-color: transparent;
            background-image: none;
            border-color: #ffc107;
        }

        .btn-outline-warning:hover {
            color: #212529;
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .btn-outline-info {
            color: #17a2b8;
            background-color: transparent;
            background-image: none;
            border-color: #17a2b8;
        }

        .btn-outline-info:hover {
            color: #fff;
            background-color: #17a2b8;
            border-color: #17a2b8;
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
        /* Sticky Footer Implementation */
        html, body {
            height: 100%;
        }
        
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        main {
            flex: 1 0 auto;
        }
        
        .footer {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            padding: 50px 0 30px 0;
            margin-top: auto;
            position: relative;
            flex-shrink: 0;
            box-shadow: 0 -5px 15px rgba(0,0,0,0.1);
            min-height: auto;
        }
        
        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #3498db, #e74c3c, #f39c12, #2ecc71);
        }
        
        .footer-content {
            position: relative;
            z-index: 2;
            width: 100%;
            overflow: visible;
        }
        
        .footer-section h5 {
            color: #ecf0f1;
            font-weight: 600;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 12px;
            font-size: 1.1rem;
        }
        
        .footer-section h5::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 2px;
            background: #3498db;
            border-radius: 1px;
        }
        
        .footer-section ul {
            list-style: none;
            padding: 0;
        }
        
        .footer-section ul li {
            margin-bottom: 8px;
        }
        
        .footer-section ul li a {
            color: #bdc3c7;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            padding: 4px 0;
            font-size: 0.95rem;
        }
        
        .footer-section ul li a:hover {
            color: #3498db;
            transform: translateX(5px);
            text-decoration: none;
        }
        
        .footer-section ul li a i {
            margin-right: 10px;
            width: 16px;
            text-align: center;
            font-size: 0.9rem;
        }
        
        .footer-bottom {
            border-top: 1px solid #34495e;
            margin-top: 40px;
            padding-top: 20px;
            padding-bottom: 10px;
            text-align: center;
            width: 100%;
            overflow: visible;
        }
        
        .payment-methods {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 25px;
            padding: 15px 0;
        }
        
        .payment-method {
            background: rgba(255,255,255,0.1);
            padding: 10px 15px;
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255,255,255,0.1);
            font-size: 0.9rem;
        }
        
        .payment-method:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-3px);
            border-color: rgba(255,255,255,0.3);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .payment-method i {
            font-size: 1.3rem;
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .social-link {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .social-link:hover {
            background: #3498db;
            transform: translateY(-3px);
            color: white;
        }
        
        .company-info {
            background: rgba(255,255,255,0.05);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .company-info h6 {
            color: #3498db;
            margin-bottom: 12px;
            font-weight: 600;
            font-size: 1rem;
        }
        
        .company-info p {
            color: #bdc3c7;
            margin: 0;
            line-height: 1.7;
            font-size: 0.95rem;
        }
        
        @media (max-width: 768px) {
            .footer {
                padding: 40px 0 25px 0;
            }
            
            .footer-section h5 {
                font-size: 1rem;
                margin-bottom: 15px;
            }
            
            .footer-section ul li a {
                font-size: 0.9rem;
                padding: 3px 0;
            }
            
            .payment-methods {
                gap: 8px;
                margin-bottom: 20px;
            }
            
            .payment-method {
                padding: 8px 12px;
                font-size: 0.85rem;
            }
            
            .payment-method i {
                font-size: 1.1rem;
            }
            
            .social-links {
                gap: 10px;
                margin-bottom: 15px;
            }
            
            .social-link {
                width: 35px;
                height: 35px;
            }
            
            .company-info {
                padding: 15px;
                margin-bottom: 15px;
            }
            
            .company-info p {
                font-size: 0.9rem;
            }
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
    <!-- Currency Rates Bar -->
    <div class="bg-light border-bottom py-2">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-chart-line text-primary me-2"></i>
                        <span class="text-muted small me-3">Güncel Döviz Kurları:</span>
                        <div class="currency-rates">
                            @php
                                $currencies = \App\Models\Currency::getRatesFor(['USD', 'EUR', 'GBP']);
                            @endphp
                            @foreach($currencies as $currency)
                                <span class="badge bg-secondary me-2">
                                    {{ $currency->code }}: {{ $currency->formatted_rate }} ₺
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        Son güncelleme: {{ $currencies->first() ? $currencies->first()->last_updated->format('H:i') : 'Bilinmiyor' }}
                    </small>
                </div>
            </div>
        </div>
    </div>

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
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cargo-tracking.track') }}">Kargo Takip</a>
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
                    <!-- Sepet İkonu -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ route('cart.index') }}">
                            <i class="fas fa-shopping-cart me-1"></i>Sepet
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count" 
                                  style="display: none;">
                                0
                            </span>
                        </a>
                    </li>
                    
                    @auth
                    <!-- Favoriler İkonu -->
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="{{ route('favorites.index') }}">
                            <i class="fas fa-heart me-1"></i>Favoriler
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger favorite-count" 
                                  style="display: none;">
                                0
                            </span>
                        </a>
                    </li>
                    @endauth
                    
                    @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('customer.dashboard') }}">
                            <i class="fas fa-user me-1"></i>Hesabım
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('customer.reports.dashboard') }}">
                            <i class="fas fa-chart-line me-1"></i>Raporlarım
                        </a>
                    </li>
                    @endauth
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
            <div class="footer-content">
                <div class="row">
                    <!-- Company Info -->
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="footer-section">
                            <h5><i class="fas fa-laptop me-2"></i>Basital.com</h5>
                            <div class="company-info">
                                <h6>Hakkımızda</h6>
                                <p>Türkiye'nin önde gelen teknoloji ve elektronik ürünleri platformu olarak, müşterilerimize en kaliteli ürünleri en uygun fiyatlarla sunuyoruz.</p>
                            </div>
                            <div class="social-links">
                                <a href="#" class="social-link" title="Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="#" class="social-link" title="Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                                <a href="#" class="social-link" title="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                                <a href="#" class="social-link" title="LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                                <a href="#" class="social-link" title="YouTube">
                                    <i class="fab fa-youtube"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="col-lg-2 col-md-6 mb-4">
                        <div class="footer-section">
                            <h5>Hızlı Linkler</h5>
                            <ul>
                                <li><a href="{{ route('home') }}"><i class="fas fa-home"></i>Ana Sayfa</a></li>
                                <li><a href="{{ route('products.index') }}"><i class="fas fa-box"></i>Ürünler</a></li>
                                <li><a href="{{ route('cargo-tracking.track') }}"><i class="fas fa-truck"></i>Kargo Takip</a></li>
                                @auth
                                <li><a href="{{ route('customer.dashboard') }}"><i class="fas fa-user"></i>Hesabım</a></li>
                                <li><a href="{{ route('customer.orders') }}"><i class="fas fa-shopping-bag"></i>Siparişlerim</a></li>
                                <li><a href="{{ route('favorites.index') }}"><i class="fas fa-heart"></i>Favorilerim</a></li>
                                @endauth
                            </ul>
                        </div>
                    </div>

                    <!-- Customer Service -->
                    <div class="col-lg-2 col-md-6 mb-4">
                        <div class="footer-section">
                            <h5>Müşteri Hizmetleri</h5>
                            <ul>
                                <li><a href="#"><i class="fas fa-question-circle"></i>SSS</a></li>
                                <li><a href="#"><i class="fas fa-phone"></i>İletişim</a></li>
                                <li><a href="#"><i class="fas fa-shipping-fast"></i>Kargo Bilgileri</a></li>
                                <li><a href="#"><i class="fas fa-undo"></i>İade & Değişim</a></li>
                                <li><a href="#"><i class="fas fa-shield-alt"></i>Gizlilik Politikası</a></li>
                                <li><a href="#"><i class="fas fa-file-contract"></i>Kullanım Şartları</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="col-lg-2 col-md-6 mb-4">
                        <div class="footer-section">
                            <h5>Kategoriler</h5>
                            <ul>
                                @php
                                    $footerCategories = \App\Models\Product::active()
                                        ->inStock()
                                        ->selectRaw('kategori, COUNT(*) as product_count')
                                        ->whereNotNull('kategori')
                                        ->groupBy('kategori')
                                        ->orderBy('product_count', 'desc')
                                        ->take(6)
                                        ->get();
                                @endphp
                                @foreach($footerCategories as $category)
                                <li><a href="{{ route('products.index', ['category' => $category->kategori]) }}"><i class="fas fa-tag"></i>{{ $category->kategori }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="col-lg-2 col-md-6 mb-4">
                        <div class="footer-section">
                            <h5>İletişim</h5>
                            <ul>
                                <li><a href="tel:+905551234567"><i class="fas fa-phone"></i>+90 555 123 45 67</a></li>
                                <li><a href="mailto:info@basital.com"><i class="fas fa-envelope"></i>info@basital.com</a></li>
                                <li><a href="#"><i class="fas fa-map-marker-alt"></i>İstanbul, Türkiye</a></li>
                                <li><a href="#"><i class="fas fa-clock"></i>7/24 Destek</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Footer Bottom -->
                <div class="footer-bottom">
                    <!-- Payment Methods -->
                    <div class="payment-methods">
                        <div class="payment-method">
                            <i class="fab fa-cc-visa"></i>
                            <span>Visa</span>
                        </div>
                        <div class="payment-method">
                            <i class="fab fa-cc-mastercard"></i>
                            <span>Mastercard</span>
                        </div>
                        <div class="payment-method">
                            <i class="fas fa-credit-card"></i>
                            <span>American Express</span>
                        </div>
                        <div class="payment-method">
                            <i class="fas fa-university"></i>
                            <span>Banka Kartı</span>
                        </div>
                        <div class="payment-method">
                            <i class="fas fa-mobile-alt"></i>
                            <span>Mobil Ödeme</span>
                        </div>
                        <div class="payment-method">
                            <i class="fas fa-barcode"></i>
                            <span>Kapıda Ödeme</span>
                        </div>
                    </div>

                    <!-- Copyright -->
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <p class="mb-0" style="color: #95a5a6; font-size: 0.9rem;">
                                &copy; {{ date('Y') }} Basital.com - Tüm hakları saklıdır.
                            </p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-0" style="color: #95a5a6; font-size: 0.9rem;">
                                <i class="fas fa-shield-alt me-1" style="color: #2ecc71;"></i>
                                SSL ile güvenli alışveriş
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

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

        // Sayfa yüklendiğinde sepet ve favori sayacını güncelle
        $(document).ready(function() {
            updateCartCountOnLoad();
            updateFavoriteCountOnLoad();
        });

        // Sepet sayacını güncelle
        function updateCartCount(count) {
            const $cartCount = $('.cart-count');
            if ($cartCount.length) {
                $cartCount.text(count);
                $cartCount.toggle(count > 0);
            }
        }

        // Sayfa yüklendiğinde sepet sayacını getir
        function updateCartCountOnLoad() {
            $.ajax({
                url: '{{ route("cart.count") }}',
                method: 'GET',
                success: function(data) {
                    updateCartCount(data.count);
                },
                error: function() {
                    updateCartCount(0);
                }
            });
        }

        // Favori sayacını güncelle
        function updateFavoriteCount(count) {
            const $favoriteCount = $('.favorite-count');
            if ($favoriteCount.length) {
                $favoriteCount.text(count);
                $favoriteCount.toggle(count > 0);
            }
        }

        // Sayfa yüklendiğinde favori sayacını getir
        function updateFavoriteCountOnLoad() {
            @auth
            $.ajax({
                url: '{{ route("favorites.count") }}',
                method: 'GET',
                success: function(data) {
                    if (data.success) {
                        updateFavoriteCount(data.count);
                    }
                },
                error: function() {
                    updateFavoriteCount(0);
                }
            });
            @endauth
        }

        // Global resim yükleme hatası yönetimi
        function handleImageError(img) {
            // Sadece bir kez değiştir, sonsuz döngüyü engelle
            if (!img.dataset.errorHandled) {
                img.src = '{{ asset("images/no-product-image.svg") }}';
                img.dataset.errorHandled = 'true';
            }
        }
    </script>
    
    @yield('scripts')
</body>
</html>
