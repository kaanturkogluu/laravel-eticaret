@extends('layouts.app')

@section('title', 'Kargo Takip')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Kargo Takip</h3>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('cargo-tracking.track') }}" class="mb-4">
                        <div class="input-group">
                            <input type="text" name="tracking_number" class="form-control form-control-lg" 
                                   placeholder="Takip numaranızı giriniz..." value="{{ $trackingNumber ?? '' }}" required>
                            <button class="btn btn-primary btn-lg" type="submit">
                                <i class="fas fa-search"></i> Sorgula
                            </button>
                        </div>
                    </form>

                    @if($trackingNumber && $cargoTracking)
                        <div class="alert alert-info">
                            <h5><i class="fas fa-truck"></i> Kargo Bilgileri</h5>
                            <p class="mb-1"><strong>Sipariş No:</strong> {{ $cargoTracking->order->order_number }}</p>
                            <p class="mb-1"><strong>Kargo Şirketi:</strong> {{ $cargoTracking->cargoCompany->name }}</p>
                            <p class="mb-1"><strong>Takip No:</strong> {{ $cargoTracking->tracking_number }}</p>
                            <p class="mb-0"><strong>Son Durum:</strong> 
                                <span class="badge bg-{{ $cargoTracking->status_color }}">
                                    {{ $cargoTracking->status_label }}
                                </span>
                            </p>
                        </div>

                        @if($trackingHistory->count() > 0)
                            <h5>Takip Geçmişi</h5>
                            <div class="timeline">
                                @foreach($trackingHistory as $event)
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-{{ $event->status_color }}"></div>
                                        <div class="timeline-content">
                                            <h6 class="timeline-title">{{ $event->status_label }}</h6>
                                            <p class="timeline-text">
                                                @if($event->description)
                                                    {{ $event->description }}
                                                @endif
                                                @if($event->location)
                                                    <br><small class="text-muted"><i class="fas fa-map-marker-alt"></i> {{ $event->location }}</small>
                                                @endif
                                            </p>
                                            <small class="text-muted">{{ $event->formatted_event_date }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if($cargoTracking->cargoCompany->tracking_url)
                            <div class="mt-3">
                                <a href="{{ $cargoTracking->cargoCompany->getTrackingUrl($cargoTracking->tracking_number) }}" 
                                   target="_blank" class="btn btn-outline-primary">
                                    <i class="fas fa-external-link-alt"></i> Kargo Şirketi Sitesinde Görüntüle
                                </a>
                            </div>
                        @endif

                    @elseif($trackingNumber && !$cargoTracking)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            "{{ $trackingNumber }}" takip numarası bulunamadı. Lütfen takip numaranızı kontrol ediniz.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}

.timeline-title {
    margin-bottom: 5px;
    font-weight: 600;
}

.timeline-text {
    margin-bottom: 5px;
}
</style>
@endsection
