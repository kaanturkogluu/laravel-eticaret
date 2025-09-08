@extends('layouts.admin')

@section('title', 'Ödeme Sağlayıcısı Konfigürasyonu - Admin Panel')
@section('page-title', 'API Konfigürasyonu')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-cog me-2"></i>{{ $paymentProvider->name }} API Konfigürasyonu
                </h5>
                <div>
                    <span class="badge bg-{{ $paymentProvider->test_mode ? 'warning' : 'success' }} me-2">
                        {{ $paymentProvider->test_mode ? 'Test Modu' : 'Canlı Mod' }}
                    </span>
                    <span class="badge bg-{{ $paymentProvider->is_active ? 'success' : 'danger' }}">
                        {{ $paymentProvider->is_active ? 'Aktif' : 'Pasif' }}
                    </span>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.payment-providers.update-config', $paymentProvider) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <!-- API Bilgileri -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-key me-2"></i>API Bilgileri
                            </h6>
                        </div>
                    </div>
                    
                    <div class="row">
                        @foreach($configFields as $field => $fieldConfig)
                        <div class="col-md-6 mb-3">
                            <label for="config_{{ $field }}" class="form-label">
                                {{ $fieldConfig['label'] }}
                                @if($fieldConfig['required'] ?? false)
                                    <span class="text-danger">*</span>
                                @endif
                            </label>
                            
                            @if($fieldConfig['type'] === 'password')
                                <div class="input-group">
                                    <input type="password" 
                                           class="form-control @error('config.' . $field) is-invalid @enderror" 
                                           id="config_{{ $field }}" 
                                           name="config[{{ $field }}]" 
                                           value="{{ old('config.' . $field, $paymentProvider->getConfig($field)) }}"
                                           {{ ($fieldConfig['required'] ?? false) ? 'required' : '' }}>
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePassword('config_{{ $field }}')">
                                        <i class="fas fa-eye" id="icon_config_{{ $field }}"></i>
                                    </button>
                                </div>
                            @elseif($fieldConfig['type'] === 'url')
                                <input type="url" 
                                       class="form-control @error('config.' . $field) is-invalid @enderror" 
                                       id="config_{{ $field }}" 
                                       name="config[{{ $field }}]" 
                                       value="{{ old('config.' . $field, $paymentProvider->getConfig($field)) }}"
                                       {{ ($fieldConfig['required'] ?? false) ? 'required' : '' }}>
                            @else
                                <input type="{{ $fieldConfig['type'] }}" 
                                       class="form-control @error('config.' . $field) is-invalid @enderror" 
                                       id="config_{{ $field }}" 
                                       name="config[{{ $field }}]" 
                                       value="{{ old('config.' . $field, $paymentProvider->getConfig($field)) }}"
                                       {{ ($fieldConfig['required'] ?? false) ? 'required' : '' }}>
                            @endif
                            
                            @error('config.' . $field)
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            @if($field === 'test_api_url' || $field === 'live_api_url')
                                <small class="form-text text-muted">
                                    @if($field === 'test_api_url')
                                        Test ortamı için API URL'i
                                    @else
                                        Canlı ortam için API URL'i
                                    @endif
                                </small>
                            @elseif($field === 'merchant_id' || $field === 'api_key')
                                <small class="form-text text-muted">
                                    {{ $paymentProvider->name }} tarafından sağlanan kimlik bilgisi
                                </small>
                            @elseif($field === 'secret_key')
                                <small class="form-text text-muted">
                                    Güvenlik için gizli tutulması gereken anahtar
                                </small>
                            @elseif(str_contains($field, 'url'))
                                <small class="form-text text-muted">
                                    Callback/webhook URL'leri otomatik olarak oluşturulur
                                </small>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    
                    <hr>
                    
                    <!-- URL Bilgileri -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-link me-2"></i>URL Bilgileri
                            </h6>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Aşağıdaki URL'ler otomatik olarak oluşturulur. Bu URL'leri {{ $paymentProvider->name }} panelinde yapılandırmanız gerekebilir.
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Callback URL</label>
                            <div class="input-group">
                                <input type="text" class="form-control" 
                                       value="{{ url('/payment/callback/' . $paymentProvider->code) }}" readonly>
                                <button class="btn btn-outline-secondary" type="button" 
                                        onclick="copyToClipboard('{{ url('/payment/callback/' . $paymentProvider->code) }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Webhook URL</label>
                            <div class="input-group">
                                <input type="text" class="form-control" 
                                       value="{{ url('/payment/webhook/' . $paymentProvider->code) }}" readonly>
                                <button class="btn btn-outline-secondary" type="button" 
                                        onclick="copyToClipboard('{{ url('/payment/webhook/' . $paymentProvider->code) }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Success URL</label>
                            <div class="input-group">
                                <input type="text" class="form-control" 
                                       value="{{ url('/payment/success') }}" readonly>
                                <button class="btn btn-outline-secondary" type="button" 
                                        onclick="copyToClipboard('{{ url('/payment/success') }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Failure URL</label>
                            <div class="input-group">
                                <input type="text" class="form-control" 
                                       value="{{ url('/payment/failure') }}" readonly>
                                <button class="btn btn-outline-secondary" type="button" 
                                        onclick="copyToClipboard('{{ url('/payment/failure') }}')">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Test Bağlantısı -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-primary mb-3">
                                <i class="fas fa-flask me-2"></i>Bağlantı Testi
                            </h6>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                API bilgilerini kaydettikten sonra bağlantı testini yapabilirsiniz.
                            </div>
                            <button type="button" class="btn btn-warning" id="testConnectionBtn" disabled>
                                <i class="fas fa-plug me-2"></i>Bağlantıyı Test Et
                            </button>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('admin.payment-providers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Geri Dön
                        </a>
                        <div>
                            <button type="button" class="btn btn-outline-primary me-2" onclick="saveAndTest()">
                                <i class="fas fa-save me-2"></i>Kaydet ve Test Et
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Konfigürasyonu Kaydet
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Test Sonucu Modal -->
<div class="modal fade" id="testResultModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bağlantı Test Sonucu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="testResultContent">
                <!-- Test sonucu buraya gelecek -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById('icon_' + inputId);
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Başarılı kopyalama bildirimi
        const btn = event.target.closest('button');
        const originalIcon = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check text-success"></i>';
        
        setTimeout(() => {
            btn.innerHTML = originalIcon;
        }, 2000);
    }).catch(function(err) {
        console.error('Kopyalama hatası: ', err);
        alert('URL kopyalanamadı. Lütfen manuel olarak kopyalayın.');
    });
}

function saveAndTest() {
    // Önce formu kaydet
    const form = document.querySelector('form');
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Kaydetme başarılı, şimdi test et
            testConnection();
        } else {
            alert('Konfigürasyon kaydedilemedi: ' + (data.message || 'Bilinmeyen hata'));
        }
    })
    .catch(error => {
        console.error('Kaydetme hatası:', error);
        alert('Konfigürasyon kaydedilirken hata oluştu.');
    });
}

function testConnection() {
    const testBtn = document.getElementById('testConnectionBtn');
    const originalText = testBtn.innerHTML;
    
    testBtn.disabled = true;
    testBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Test Ediliyor...';
    
    fetch(`/admin/payment-providers/{{ $paymentProvider->id }}/test-connection`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        showTestResult(data);
    })
    .catch(error => {
        console.error('Test hatası:', error);
        showTestResult({
            success: false,
            message: 'Bağlantı testi sırasında hata oluştu.',
            details: error.message
        });
    })
    .finally(() => {
        testBtn.disabled = false;
        testBtn.innerHTML = originalText;
    });
}

function showTestResult(result) {
    const modal = new bootstrap.Modal(document.getElementById('testResultModal'));
    const content = document.getElementById('testResultContent');
    
    let alertClass = result.success ? 'alert-success' : 'alert-danger';
    let icon = result.success ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    content.innerHTML = `
        <div class="alert ${alertClass}">
            <i class="fas ${icon} me-2"></i>
            <strong>${result.success ? 'Başarılı!' : 'Başarısız!'}</strong>
            <p class="mb-0 mt-2">${result.message}</p>
        </div>
        ${result.details ? `
            <div class="mt-3">
                <h6>Detaylar:</h6>
                <pre class="bg-light p-3 rounded">${JSON.stringify(result.details, null, 2)}</pre>
            </div>
        ` : ''}
    `;
    
    modal.show();
}

// Form değişikliklerini takip et
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const testBtn = document.getElementById('testConnectionBtn');
    
    // Form alanları değiştiğinde test butonunu aktif et
    form.addEventListener('input', function() {
        testBtn.disabled = false;
    });
    
    // Test butonuna tıklandığında
    testBtn.addEventListener('click', function() {
        testConnection();
    });
});
</script>
@endsection
