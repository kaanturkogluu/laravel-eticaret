@extends('layouts.admin')

@section('title', 'Kupon Kullanım Geçmişi - Basital.com')
@section('page-title', 'Kupon Kullanım Geçmişi')

@section('content')

<!-- Kupon Bilgileri -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-ticket-alt me-2"></i>Kupon Bilgileri
                    <a href="{{ route('admin.coupons.edit', $coupon) }}" class="btn btn-sm btn-outline-primary ms-2">
                        <i class="fas fa-edit me-1"></i>Düzenle
                    </a>
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <strong>Kupon Kodu:</strong><br>
                        <code class="bg-light px-2 py-1 rounded">{{ $coupon->code }}</code>
                    </div>
                    <div class="col-md-3">
                        <strong>Ad:</strong><br>
                        <span>{{ $coupon->name }}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>İndirim:</strong><br>
                        <span class="badge {{ $coupon->type === 'percentage' ? 'bg-info' : 'bg-warning' }}">
                            {{ $coupon->formatted_value }}
                        </span>
                    </div>
                    <div class="col-md-3">
                        <strong>Durum:</strong><br>
                        <span class="badge bg-{{ $coupon->status_color }}">{{ $coupon->status_label }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kullanım İstatistikleri -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card primary">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">{{ $coupon->used_count }}</div>
                    <div class="stats-label">Toplam Kullanım</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">{{ $coupon->remaining_usage ?? '∞' }}</div>
                    <div class="stats-label">Kalan Kullanım</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">{{ number_format($usages->sum('discount_amount'), 2) }} TL</div>
                    <div class="stats-label">Toplam İndirim</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stats-number">{{ number_format($usages->avg('order_total'), 2) }} TL</div>
                    <div class="stats-label">Ortalama Sipariş</div>
                </div>
                <div class="stats-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kullanım Geçmişi -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Kullanım Geçmişi
                    <span class="badge bg-primary ms-2">{{ $usages->total() }}</span>
                </h5>
            </div>
            <div class="card-body">
                @if($usages->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Tarih</th>
                                    <th>Kullanıcı</th>
                                    <th>Sipariş</th>
                                    <th>Sipariş Tutarı</th>
                                    <th>İndirim Tutarı</th>
                                    <th>IP Adresi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($usages as $usage)
                                <tr>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span>{{ $usage->created_at->format('d.m.Y') }}</span>
                                            <small class="text-muted">{{ $usage->created_at->format('H:i:s') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        @if($usage->user)
                                            <div class="d-flex flex-column">
                                                <span>{{ $usage->user->name }}</span>
                                                <small class="text-muted">{{ $usage->user->email }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">Misafir Kullanıcı</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($usage->order)
                                            <a href="{{ route('orders.show', $usage->order) }}" class="text-decoration-none">
                                                <code>{{ $usage->order->order_number }}</code>
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $usage->formatted_order_total }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-success fw-bold">{{ $usage->formatted_discount_amount }}</span>
                                    </td>
                                    <td>
                                        <code class="bg-light px-2 py-1 rounded">{{ $usage->ip_address }}</code>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $usages->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Henüz kullanım kaydı bulunmuyor</h5>
                        <p class="text-muted">Bu kupon henüz hiç kullanılmamış.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Geri Dön Butonu -->
<div class="row mt-4">
    <div class="col-12">
        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kupon Listesine Dön
        </a>
    </div>
</div>

@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Tablo satırlarına hover efekti
    $('table tbody tr').hover(
        function() {
            $(this).addClass('table-active');
        },
        function() {
            $(this).removeClass('table-active');
        }
    );
});
</script>
@endsection
