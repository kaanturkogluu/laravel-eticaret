@extends('layouts.app')

@section('title', 'Hesabım - Basital.com')

@section('content')
<div class="container-fluid px-0">
    <div class="container">
        <div class="row">
    <div class="col-lg-3">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>Hesap Menüsü
                </h5>
            </div>
            <div class="list-group list-group-flush">
                <a href="{{ route('customer.dashboard') }}" class="list-group-item list-group-item-action active">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
                <a href="{{ route('customer.profile') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-user-edit me-2"></i>Profil Bilgileri
                </a>
                <a href="{{ route('customer.orders') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-shopping-bag me-2"></i>Siparişlerim
                </a>
                <a href="{{ route('favorites.index') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-heart me-2"></i>Favori Ürünlerim
                </a>
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-tachometer-alt me-2"></i>Hoş Geldiniz, {{ $user->name }}
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <i class="fas fa-user fa-3x text-primary mb-3"></i>
                                <h5>Profil Bilgileri</h5>
                                <p class="text-muted">Hesap bilgilerinizi görüntüleyin ve güncelleyin</p>
                                <a href="{{ route('customer.profile') }}" class="btn btn-primary">Profili Görüntüle</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <i class="fas fa-shopping-bag fa-3x text-success mb-3"></i>
                                <h5>Siparişlerim</h5>
                                <p class="text-muted">Sipariş geçmişinizi görüntüleyin</p>
                                <a href="{{ route('customer.orders') }}" class="btn btn-success">Siparişleri Görüntüle</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <i class="fas fa-heart fa-3x text-danger mb-3"></i>
                                <h5>Favori Ürünlerim</h5>
                                <p class="text-muted">Beğendiğiniz ürünleri görüntüleyin</p>
                                <a href="{{ route('favorites.index') }}" class="btn btn-danger">Favorileri Görüntüle</a>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-md-12">
                        <h5>Hesap Bilgileri</h5>
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Ad Soyad:</strong></td>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>E-posta:</strong></td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Telefon:</strong></td>
                                    <td>{{ $user->phone ?: 'Belirtilmemiş' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Üyelik Tarihi:</strong></td>
                                    <td>{{ $user->created_at->format('d.m.Y') }}</td>
                                </tr>
                                @if($user->is_admin)
                                <tr>
                                    <td><strong>Yetki:</strong></td>
                                    <td><span class="badge bg-danger">Admin</span></td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection
