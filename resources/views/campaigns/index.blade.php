@extends('layouts.app')

@section('title', 'Kampanyalar - Basital.com')

@section('content')
<div class="container py-5">
    <!-- Page Header -->
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 fw-bold text-primary mb-3">🔥 Özel Kampanyalar</h1>
            <p class="lead text-muted">Size özel indirimli fırsatları kaçırmayın</p>
        </div>
    </div>

    @if($campaigns->count() > 0)
        <div class="row">
            @foreach($campaigns as $campaign)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card campaign-card h-100 shadow-sm">
                        @if($campaign->image_url)
                            <img src="{{ $campaign->image_url }}" class="card-img-top" alt="{{ $campaign->title }}" style="height: 250px; object-fit: cover;">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                                <i class="fas fa-bullhorn fa-3x text-muted"></i>
                            </div>
                        @endif
                        
                        <div class="card-body d-flex flex-column">
                            <div class="mb-3">
                                <span class="badge bg-primary mb-2">
                                    @if($campaign->type === 'campaign') Kampanya
                                    @else Promosyon
                                    @endif
                                </span>
                                
                                <h5 class="card-title fw-bold">{{ $campaign->title }}</h5>
                                
                                @if($campaign->description)
                                    <p class="card-text text-muted">{{ Str::limit($campaign->description, 120) }}</p>
                                @endif
                            </div>
                            
                            <div class="mt-auto">
                                @if($campaign->start_date || $campaign->end_date)
                                    <div class="mb-3">
                                        @if($campaign->start_date)
                                            <small class="text-muted d-block">
                                                <i class="fas fa-calendar-alt me-1"></i>
                                                Başlangıç: {{ $campaign->start_date->format('d.m.Y H:i') }}
                                            </small>
                                        @endif
                                        @if($campaign->end_date)
                                            <small class="text-muted d-block">
                                                <i class="fas fa-calendar-times me-1"></i>
                                                Bitiş: {{ $campaign->end_date->format('d.m.Y H:i') }}
                                            </small>
                                        @endif
                                    </div>
                                @endif
                                
                                <div class="d-grid gap-2">
                                    <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-primary fw-bold">
                                        <i class="fas fa-eye me-2"></i>Detayları Gör
                                    </a>
                                    
                                    @if($campaign->link_url)
                                        <a href="{{ $campaign->link_url }}" class="btn btn-outline-primary" target="_blank">
                                            <i class="fas fa-external-link-alt me-2"></i>Kampanya Sayfası
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-5">
            {{ $campaigns->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-bullhorn fa-4x text-muted mb-4"></i>
            <h3 class="text-muted">Henüz aktif kampanya bulunmuyor</h3>
            <p class="text-muted">Yeni kampanyalar için bizi takip etmeye devam edin!</p>
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="fas fa-home me-2"></i>Ana Sayfaya Dön
            </a>
        </div>
    @endif
</div>
@endsection

@section('styles')
<style>
    .campaign-card {
        border: none;
        border-radius: 15px;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .campaign-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
    }
    
    .campaign-card .card-img-top {
        transition: transform 0.3s ease;
    }
    
    .campaign-card:hover .card-img-top {
        transform: scale(1.05);
    }
    
    .badge {
        font-size: 0.8rem;
        padding: 0.5rem 0.75rem;
    }
    
    .btn {
        border-radius: 8px;
        font-weight: 600;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        border: none;
    }
    
    .btn-primary:hover {
        background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
        transform: translateY(-1px);
    }
    
    .btn-outline-primary {
        border-color: #2563eb;
        color: #2563eb;
    }
    
    .btn-outline-primary:hover {
        background-color: #2563eb;
        border-color: #2563eb;
        transform: translateY(-1px);
    }
</style>
@endsection
