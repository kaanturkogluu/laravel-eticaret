@extends('layouts.app')

@section('title', 'Çok Fazla İstek - Basital.com')

@section('content')
<div class="container-fluid px-0">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-header bg-warning text-dark text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>Çok Fazla İstek
                        </h4>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-4">
                            <i class="fas fa-clock text-warning" style="font-size: 4rem;"></i>
                        </div>

                        <h5 class="mb-3">Rate Limit Aşıldı</h5>
                        
                        <p class="text-muted mb-4">
                            Çok fazla istek gönderdiniz. Güvenlik nedeniyle geçici olarak engellendiniz.
                        </p>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Bekleme Süresi:</strong> 
                            <span id="countdown">{{ $retryAfter ?? 60 }}</span> saniye
                        </div>

                        <p class="text-muted mb-4">
                            Lütfen belirtilen süre kadar bekleyin ve tekrar deneyin.
                        </p>

                        <div class="mt-4">
                            <a href="{{ route('home') }}" class="btn btn-primary">
                                <i class="fas fa-home me-2"></i>
                                Ana Sayfaya Dön
                            </a>
                            
                            <button onclick="location.reload()" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-refresh me-2"></i>
                                Sayfayı Yenile
                            </button>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <i class="fas fa-shield-alt text-primary mb-2" style="font-size: 2rem;"></i>
                                    <h6>Güvenlik</h6>
                                    <small class="text-muted">Sisteminizi koruyoruz</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <i class="fas fa-clock text-warning mb-2" style="font-size: 2rem;"></i>
                                    <h6>Geçici</h6>
                                    <small class="text-muted">Kısa süreli kısıtlama</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <i class="fas fa-check-circle text-success mb-2" style="font-size: 2rem;"></i>
                                    <h6>Otomatik</h6>
                                    <small class="text-muted">Otomatik olarak açılır</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Geri sayım sayacı
let countdown = {{ $retryAfter ?? 60 }};
const countdownElement = document.getElementById('countdown');

const timer = setInterval(() => {
    countdown--;
    countdownElement.textContent = countdown;
    
    if (countdown <= 0) {
        clearInterval(timer);
        countdownElement.textContent = '0';
        // Sayfayı otomatik yenile
        setTimeout(() => {
            location.reload();
        }, 1000);
    }
}, 1000);
</script>
@endsection
