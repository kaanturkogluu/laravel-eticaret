@extends('layouts.admin')

@section('title', 'Ödeme Sağlayıcıları - Admin Panel')
@section('page-title', 'Ödeme Sağlayıcıları')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-credit-card me-2"></i>Ödeme Sağlayıcıları
                </h5>
                <a href="{{ route('admin.payment-providers.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Yeni Sağlayıcı Ekle
                </a>
            </div>
            <div class="card-body">
                @if($providers->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Logo</th>
                                    <th>Ad</th>
                                    <th>Kod</th>
                                    <th>Para Birimleri</th>
                                    <th>Ödeme Yöntemleri</th>
                                    <th>Komisyon</th>
                                    <th>Durum</th>
                                    <th>Test Modu</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($providers as $provider)
                                <tr>
                                    <td>
                                        <img src="{{ $provider->logo_url }}" alt="{{ $provider->name }}" 
                                             style="width: 40px; height: 40px; object-fit: contain;"
                                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px; display: none;">
                                            <i class="fas fa-credit-card text-muted"></i>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $provider->name }}</strong>
                                        @if($provider->description)
                                            <br><small class="text-muted">{{ $provider->description }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <code>{{ $provider->code }}</code>
                                    </td>
                                    <td>
                                        @if($provider->supported_currencies)
                                            @foreach($provider->supported_currencies as $currency)
                                                <span class="badge bg-info me-1">{{ $currency }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($provider->supported_payment_methods)
                                            @foreach($provider->supported_payment_methods as $method)
                                                <span class="badge bg-secondary me-1">
                                                    @switch($method)
                                                        @case('credit_card') Kredi Kartı @break
                                                        @case('bank_transfer') Banka Havalesi @break
                                                        @case('wallet') Cüzdan @break
                                                        @case('cash_on_delivery') Kapıda Ödeme @break
                                                        @default {{ $method }} @break
                                                    @endswitch
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($provider->commission_rate > 0 || $provider->commission_fixed > 0)
                                            @if($provider->commission_rate > 0)
                                                <span class="badge bg-warning">{{ $provider->commission_rate }}%</span>
                                            @endif
                                            @if($provider->commission_fixed > 0)
                                                <span class="badge bg-warning">{{ number_format($provider->commission_fixed, 2) }} ₺</span>
                                            @endif
                                        @else
                                            <span class="badge bg-success">Komisyonsuz</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $provider->status_color }}">
                                            {{ $provider->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($provider->test_mode)
                                            <span class="badge bg-warning">Test</span>
                                        @else
                                            <span class="badge bg-success">Canlı</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.payment-providers.edit', $provider) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Düzenle">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <a href="{{ route('admin.payment-providers.config', $provider) }}" 
                                               class="btn btn-sm btn-outline-info" title="Konfigürasyon">
                                                <i class="fas fa-cog"></i>
                                            </a>
                                            
                                            <form action="{{ route('admin.payment-providers.toggle-status', $provider) }}" 
                                                  method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-{{ $provider->is_active ? 'warning' : 'success' }}" 
                                                        title="{{ $provider->is_active ? 'Pasif Yap' : 'Aktif Yap' }}"
                                                        onclick="return confirm('{{ $provider->is_active ? 'Bu sağlayıcıyı pasif yapmak istediğinizden emin misiniz?' : 'Bu sağlayıcıyı aktif yapmak istediğinizden emin misiniz?' }}')">
                                                    <i class="fas fa-{{ $provider->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('admin.payment-providers.destroy', $provider) }}" 
                                                  method="POST" style="display: inline;"
                                                  onsubmit="return confirm('Bu ödeme sağlayıcısını silmek istediğinizden emin misiniz? Bu işlem geri alınamaz.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Sil">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Henüz ödeme sağlayıcısı eklenmemiş</h5>
                        <p class="text-muted">İlk ödeme sağlayıcınızı eklemek için aşağıdaki butona tıklayın.</p>
                        <a href="{{ route('admin.payment-providers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>İlk Sağlayıcıyı Ekle
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- İstatistikler -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="stats-card primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">{{ $providers->count() }}</div>
                    <div class="stats-label">Toplam Sağlayıcı</div>
                </div>
                <i class="fas fa-credit-card stats-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">{{ $providers->where('is_active', true)->count() }}</div>
                    <div class="stats-label">Aktif Sağlayıcı</div>
                </div>
                <i class="fas fa-check-circle stats-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">{{ $providers->where('test_mode', true)->count() }}</div>
                    <div class="stats-label">Test Modu</div>
                </div>
                <i class="fas fa-flask stats-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">{{ $providers->where('test_mode', false)->count() }}</div>
                    <div class="stats-label">Canlı Mod</div>
                </div>
                <i class="fas fa-globe stats-icon"></i>
            </div>
        </div>
    </div>
</div>
@endsection
