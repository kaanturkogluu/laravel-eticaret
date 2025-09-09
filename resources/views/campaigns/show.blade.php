@extends('layouts.app')

@section('title', $campaign->title . ' - Basital.com')

@section('content')
<div class="container py-5">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Ana Sayfa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('campaigns.index') }}">Kampanyalar</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $campaign->title }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Campaign Content -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                @if($campaign->image_url)
                    <img src="{{ $campaign->image_url }}" class="card-img-top" alt="{{ $campaign->title }}" style="height: 400px; object-fit: cover;">
                @endif
                
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <span class="badge bg-primary fs-6 mb-2">
                                @if($campaign->type === 'campaign') Kampanya
                                @else Promosyon
                                @endif
                            </span>
                            <h1 class="card-title fw-bold mb-3">{{ $campaign->title }}</h1>
                        </div>
                    </div>
                    
                    @if($campaign->description)
                        <div class="campaign-description mb-4">
                            <h5 class="fw-bold mb-3">Kampanya Detayları</h5>
                            <div class="text-muted" style="line-height: 1.8;">
                                {!! nl2br(e($campaign->description)) !!}
                            </div>
                        </div>
                    @endif
                    
                    @if($campaign->start_date || $campaign->end_date)
                        <div class="campaign-dates mb-4">
                            <h5 class="fw-bold mb-3">Kampanya Tarihleri</h5>
                            <div class="row">
                                @if($campaign->start_date)
                                    <div class="col-md-6 mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-calendar-alt text-primary me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Başlangıç Tarihi</small>
                                                <strong>{{ $campaign->start_date->format('d.m.Y H:i') }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($campaign->end_date)
                                    <div class="col-md-6 mb-2">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-calendar-times text-danger me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Bitiş Tarihi</small>
                                                <strong>{{ $campaign->end_date->format('d.m.Y H:i') }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                    
                    @if($campaign->link_url)
                        <div class="campaign-actions">
                            <a href="{{ $campaign->link_url }}" class="btn btn-primary btn-lg" target="_blank">
                                <i class="fas fa-external-link-alt me-2"></i>Kampanyaya Katıl
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Campaign Info -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Kampanya Bilgileri</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Kampanya Tipi:</strong>
                        <span class="badge bg-primary ms-2">
                            @if($campaign->type === 'campaign') Kampanya
                            @else Promosyon
                            @endif
                        </span>
                    </div>
                    
                    <div class="mb-3">
                        <strong>Durum:</strong>
                        <span class="badge bg-success ms-2">Aktif</span>
                    </div>
                    
                    @if($campaign->end_date)
                        <div class="mb-3">
                            <strong>Kalan Süre:</strong>
                            <div id="countdown" class="mt-2"></div>
                        </div>
                    @endif
                    
                    <div class="d-grid">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-shopping-bag me-2"></i>Ürünleri İncele
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Related Campaigns -->
            @if($relatedCampaigns->count() > 0)
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i>Diğer Kampanyalar</h5>
                    </div>
                    <div class="card-body">
                        @foreach($relatedCampaigns as $relatedCampaign)
                            <div class="d-flex mb-3">
                                @if($relatedCampaign->image_url)
                                    <img src="{{ $relatedCampaign->image_url }}" alt="{{ $relatedCampaign->title }}" 
                                         class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                                         style="width: 60px; height: 60px;">
                                        <i class="fas fa-bullhorn text-muted"></i>
                                    </div>
                                @endif
                                
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ route('campaigns.show', $relatedCampaign) }}" class="text-decoration-none">
                                            {{ Str::limit($relatedCampaign->title, 40) }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        @if($relatedCampaign->type === 'campaign') Kampanya
                                        @else Promosyon
                                        @endif
                                    </small>
                                </div>
                            </div>
                        @endforeach
                        
                        <div class="text-center">
                            <a href="{{ route('campaigns.index') }}" class="btn btn-sm btn-outline-primary">
                                Tüm Kampanyaları Gör
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .card {
        border: none;
        border-radius: 15px;
    }
    
    .card-header {
        border-radius: 15px 15px 0 0 !important;
    }
    
    .badge {
        font-size: 0.9rem;
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
    
    .countdown-item {
        display: inline-block;
        background: #f8f9fa;
        padding: 0.5rem;
        margin: 0.25rem;
        border-radius: 8px;
        text-align: center;
        min-width: 60px;
    }
    
    .countdown-number {
        font-size: 1.5rem;
        font-weight: bold;
        color: #2563eb;
    }
    
    .countdown-label {
        font-size: 0.8rem;
        color: #6c757d;
    }
</style>
@endsection

@section('scripts')
@if($campaign->end_date)
<script>
    // Countdown timer
    function updateCountdown() {
        const endDate = new Date('{{ $campaign->end_date->format('Y-m-d H:i:s') }}').getTime();
        const now = new Date().getTime();
        const distance = endDate - now;
        
        if (distance < 0) {
            document.getElementById('countdown').innerHTML = '<span class="text-danger">Kampanya sona erdi</span>';
            return;
        }
        
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        let countdownHtml = '';
        
        if (days > 0) {
            countdownHtml += `<div class="countdown-item">
                <div class="countdown-number">${days}</div>
                <div class="countdown-label">Gün</div>
            </div>`;
        }
        
        countdownHtml += `
            <div class="countdown-item">
                <div class="countdown-number">${hours}</div>
                <div class="countdown-label">Saat</div>
            </div>
            <div class="countdown-item">
                <div class="countdown-number">${minutes}</div>
                <div class="countdown-label">Dakika</div>
            </div>
            <div class="countdown-item">
                <div class="countdown-number">${seconds}</div>
                <div class="countdown-label">Saniye</div>
            </div>
        `;
        
        document.getElementById('countdown').innerHTML = countdownHtml;
    }
    
    // Update countdown every second
    updateCountdown();
    setInterval(updateCountdown, 1000);
</script>
@endif
@endsection
