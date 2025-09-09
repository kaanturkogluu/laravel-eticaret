@extends('layouts.app')

@section('title', 'E-posta Doğrulama - Basital.com')

@section('content')
<div class="container-fluid px-0">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h4 class="mb-0">
                            <i class="fas fa-envelope me-2"></i>E-posta Doğrulama
                        </h4>
                    </div>
                    <div class="card-body text-center">
                        @if (session('resent'))
                            <div class="alert alert-success" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                Yeni bir doğrulama bağlantısı e-posta adresinize gönderildi.
                            </div>
                        @endif

                        <div class="mb-4">
                            <i class="fas fa-envelope-open-text text-primary" style="font-size: 4rem;"></i>
                        </div>

                        <h5 class="mb-3">E-posta Adresinizi Doğrulayın</h5>
                        
                        <p class="text-muted mb-4">
                            Kayıt işleminizi tamamlamak için e-posta adresinize gönderilen doğrulama bağlantısına tıklayın.
                        </p>

                        <p class="text-muted mb-4">
                            E-posta gelmedi mi? Spam klasörünüzü kontrol edin veya aşağıdaki butona tıklayarak yeni bir doğrulama e-postası talep edin.
                        </p>

                        <form method="POST" action="{{ route('verification.send') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>
                                Doğrulama E-postası Gönder
                            </button>
                        </form>

                        <div class="mt-4">
                            <a href="{{ route('logout') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Çıkış Yap
                            </a>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <p class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        E-posta doğrulama işlemi tamamlandıktan sonra tüm özelliklerden yararlanabilirsiniz.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
