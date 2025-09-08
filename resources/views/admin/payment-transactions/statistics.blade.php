@extends('layouts.admin')

@section('title', 'Ödeme İstatistikleri - Admin Panel')
@section('page-title', 'Ödeme İstatistikleri')

@section('content')
<!-- Filtreler -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-filter me-2"></i>Filtreler
                </h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.payment-transactions.statistics') }}">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Başlangıç Tarihi</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Bitiş Tarihi</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to', now()->format('Y-m-d')) }}">
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Ödeme Sağlayıcısı</label>
                            <select name="payment_provider_id" class="form-control">
                                <option value="">Tümü</option>
                                @foreach($providers as $provider)
                                    <option value="{{ $provider->id }}" {{ request('payment_provider_id') == $provider->id ? 'selected' : '' }}>
                                        {{ $provider->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Para Birimi</label>
                            <select name="currency" class="form-control">
                                <option value="">Tümü</option>
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency }}" {{ request('currency') == $currency ? 'selected' : '' }}>
                                        {{ $currency }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Filtrele
                        </button>
                        <div>
                            <a href="{{ route('admin.payment-transactions.export', array_merge(request()->query(), ['type' => 'statistics'])) }}" 
                               class="btn btn-success me-2">
                                <i class="fas fa-download me-2"></i>Excel İndir
                            </a>
                            <a href="{{ route('admin.payment-transactions.statistics') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Temizle
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Genel İstatistikler -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">{{ $statistics['total_transactions'] }}</div>
                    <div class="stats-label">Toplam İşlem</div>
                </div>
                <i class="fas fa-receipt stats-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">{{ number_format($statistics['total_amount'], 2) }} ₺</div>
                    <div class="stats-label">Toplam Tutar</div>
                </div>
                <i class="fas fa-money-bill-wave stats-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">{{ number_format($statistics['total_commission'], 2) }} ₺</div>
                    <div class="stats-label">Toplam Komisyon</div>
                </div>
                <i class="fas fa-percentage stats-icon"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">{{ number_format($statistics['success_rate'], 1) }}%</div>
                    <div class="stats-label">Başarı Oranı</div>
                </div>
                <i class="fas fa-chart-line stats-icon"></i>
            </div>
        </div>
    </div>
</div>

<!-- Durum Bazlı İstatistikler -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-chart-pie me-2"></i>Durum Bazlı İstatistikler
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($statistics['status_breakdown'] as $status => $data)
                    <div class="col-md-2 mb-3">
                        <div class="text-center">
                            <div class="h4 mb-1">{{ $data['count'] }}</div>
                            <div class="text-muted small">
                                @switch($status)
                                    @case('pending') Beklemede @break
                                    @case('processing') İşleniyor @break
                                    @case('completed') Tamamlandı @break
                                    @case('failed') Başarısız @break
                                    @case('cancelled') İptal Edildi @break
                                    @case('refunded') İade Edildi @break
                                    @case('partially_refunded') Kısmi İade @break
                                    @default {{ $status }} @break
                                @endswitch
                            </div>
                            <div class="text-primary small">{{ number_format($data['amount'], 2) }} ₺</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sağlayıcı Bazlı İstatistikler -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-credit-card me-2"></i>Sağlayıcı Bazlı İstatistikler
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Sağlayıcı</th>
                                <th>İşlem Sayısı</th>
                                <th>Toplam Tutar</th>
                                <th>Komisyon</th>
                                <th>Başarı Oranı</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statistics['provider_breakdown'] as $provider)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $provider['logo_url'] }}" 
                                             alt="{{ $provider['name'] }}" 
                                             style="width: 24px; height: 24px; object-fit: contain;" class="me-2"
                                             onerror="this.style.display='none';">
                                        {{ $provider['name'] }}
                                    </div>
                                </td>
                                <td>{{ $provider['transaction_count'] }}</td>
                                <td>{{ number_format($provider['total_amount'], 2) }} ₺</td>
                                <td>{{ number_format($provider['total_commission'], 2) }} ₺</td>
                                <td>
                                    <span class="badge bg-{{ $provider['success_rate'] >= 90 ? 'success' : ($provider['success_rate'] >= 70 ? 'warning' : 'danger') }}">
                                        {{ number_format($provider['success_rate'], 1) }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Ödeme Yöntemi Dağılımı
                </h6>
            </div>
            <div class="card-body">
                @foreach($statistics['payment_method_breakdown'] as $method => $data)
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span>
                            @switch($method)
                                @case('credit_card') Kredi Kartı @break
                                @case('bank_transfer') Banka Havalesi @break
                                @case('wallet') Cüzdan @break
                                @case('cash_on_delivery') Kapıda Ödeme @break
                                @default {{ $method }} @break
                            @endswitch
                        </span>
                        <span class="fw-bold">{{ $data['count'] }}</span>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar" role="progressbar" 
                             style="width: {{ $data['percentage'] }}%"></div>
                    </div>
                    <small class="text-muted">{{ number_format($data['amount'], 2) }} ₺ ({{ number_format($data['percentage'], 1) }}%)</small>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Günlük İstatistikler -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>Günlük İstatistikler
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tarih</th>
                                <th>İşlem Sayısı</th>
                                <th>Toplam Tutar</th>
                                <th>Başarılı İşlem</th>
                                <th>Başarısız İşlem</th>
                                <th>Başarı Oranı</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($statistics['daily_breakdown'] as $day)
                            <tr>
                                <td>{{ $day['date']->format('d.m.Y') }}</td>
                                <td>{{ $day['total_transactions'] }}</td>
                                <td>{{ number_format($day['total_amount'], 2) }} ₺</td>
                                <td class="text-success">{{ $day['successful_transactions'] }}</td>
                                <td class="text-danger">{{ $day['failed_transactions'] }}</td>
                                <td>
                                    <span class="badge bg-{{ $day['success_rate'] >= 90 ? 'success' : ($day['success_rate'] >= 70 ? 'warning' : 'danger') }}">
                                        {{ number_format($day['success_rate'], 1) }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Komisyon Analizi -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-percentage me-2"></i>Komisyon Analizi
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="h4 text-primary">{{ number_format($statistics['average_commission_rate'], 2) }}%</div>
                        <div class="text-muted small">Ortalama Komisyon Oranı</div>
                    </div>
                    <div class="col-6">
                        <div class="h4 text-success">{{ number_format($statistics['total_commission'], 2) }} ₺</div>
                        <div class="text-muted small">Toplam Komisyon</div>
                    </div>
                </div>
                <hr>
                <div class="small text-muted">
                    <strong>Komisyon Detayları:</strong><br>
                    • Sabit komisyon: {{ number_format($statistics['fixed_commission'], 2) }} ₺<br>
                    • Oranlı komisyon: {{ number_format($statistics['rate_commission'], 2) }} ₺<br>
                    • Ortalama işlem başına komisyon: {{ number_format($statistics['average_commission_per_transaction'], 2) }} ₺
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-clock me-2"></i>İşlem Süreleri
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="h4 text-info">{{ number_format($statistics['average_processing_time'], 1) }} dk</div>
                        <div class="text-muted small">Ortalama İşlem Süresi</div>
                    </div>
                    <div class="col-6">
                        <div class="h4 text-warning">{{ number_format($statistics['max_processing_time'], 1) }} dk</div>
                        <div class="text-muted small">En Uzun İşlem Süresi</div>
                    </div>
                </div>
                <hr>
                <div class="small text-muted">
                    <strong>İşlem Süresi Dağılımı:</strong><br>
                    • 0-5 dakika: {{ $statistics['processing_time_breakdown']['0-5'] }} işlem<br>
                    • 5-15 dakika: {{ $statistics['processing_time_breakdown']['5-15'] }} işlem<br>
                    • 15+ dakika: {{ $statistics['processing_time_breakdown']['15+'] }} işlem
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
