@extends('layouts.admin')

@section('title', 'Ödeme İşlemleri - Admin Panel')
@section('page-title', 'Ödeme İşlemleri')

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
                <form method="GET" action="{{ route('admin.payment-transactions.index') }}">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Durum</label>
                            <select name="status" class="form-control">
                                <option value="">Tümü</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
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
                                    </option>
                                @endforeach
                            </select>
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
                        
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Başlangıç Tarihi</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Bitiş Tarihi</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        
                        <div class="col-md-2 mb-3">
                            <label class="form-label">Arama</label>
                            <input type="text" name="search" class="form-control" 
                                   placeholder="Transaction ID, Sipariş No..." value="{{ request('search') }}">
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Filtrele
                        </button>
                        <div>
                            <a href="{{ route('admin.payment-transactions.export', request()->query()) }}" 
                               class="btn btn-success me-2">
                                <i class="fas fa-download me-2"></i>Excel İndir
                            </a>
                            <a href="{{ route('admin.payment-transactions.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Temizle
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- İşlemler Listesi -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-receipt me-2"></i>Ödeme İşlemleri
                </h5>
                <a href="{{ route('admin.payment-transactions.statistics') }}" class="btn btn-info">
                    <i class="fas fa-chart-bar me-2"></i>İstatistikler
                </a>
            </div>
            <div class="card-body">
                @if($transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Sipariş</th>
                                    <th>Sağlayıcı</th>
                                    <th>Tutar</th>
                                    <th>Durum</th>
                                    <th>Komisyon</th>
                                    <th>Tarih</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td>
                                        <code>{{ $transaction->transaction_id }}</code>
                                        @if($transaction->external_transaction_id)
                                            <br><small class="text-muted">{{ $transaction->external_transaction_id }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->order)
                                            <a href="{{ route('orders.show', $transaction->order->id) }}" 
                                               class="text-decoration-none">
                                                {{ $transaction->order->order_number }}
                                            </a>
                                            <br><small class="text-muted">{{ $transaction->order->customer_email }}</small>
                                        @else
                                            <span class="text-muted">Sipariş bulunamadı</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->paymentProvider)
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $transaction->paymentProvider->logo_url }}" 
                                                     alt="{{ $transaction->paymentProvider->name }}" 
                                                     style="width: 20px; height: 20px; object-fit: contain;" class="me-2"
                                                     onerror="this.style.display='none';">
                                                <span>{{ $transaction->paymentProvider->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted">Sağlayıcı bulunamadı</span>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $transaction->formatted_amount }}</strong>
                                        @if($transaction->refund_amount > 0)
                                            <br><small class="text-danger">İade: {{ $transaction->formatted_refund_amount }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $transaction->status_color }}">
                                            {{ $transaction->status_label }}
                                        </span>
                                        @if($transaction->payment_method)
                                            <br><small class="text-muted">
                                                @switch($transaction->payment_method)
                                                    @case('credit_card') Kredi Kartı @break
                                                    @case('bank_transfer') Banka Havalesi @break
                                                    @case('wallet') Cüzdan @break
                                                    @case('cash_on_delivery') Kapıda Ödeme @break
                                                    @default {{ $transaction->payment_method }} @break
                                                @endswitch
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($transaction->commission_amount > 0)
                                            <span class="text-warning">{{ $transaction->formatted_commission_amount }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $transaction->created_at->format('d.m.Y H:i') }}
                                        @if($transaction->processed_at)
                                            <br><small class="text-success">İşlem: {{ $transaction->processed_at->format('d.m.Y H:i') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.payment-transactions.show', $transaction) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Detay">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($transaction->status === 'pending' || $transaction->status === 'processing')
                                                <form action="{{ route('admin.payment-transactions.check-status', $transaction) }}" 
                                                      method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-info" title="Durum Kontrolü">
                                                        <i class="fas fa-sync"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            
                                            @if($transaction->isRefundable())
                                                <button type="button" class="btn btn-sm btn-outline-warning" 
                                                        title="İade" onclick="showRefundModal({{ $transaction->id }}, {{ $transaction->amount - $transaction->refund_amount }})">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            @endif
                                            
                                            @if(in_array($transaction->status, ['pending', 'processing']))
                                                <form action="{{ route('admin.payment-transactions.cancel', $transaction) }}" 
                                                      method="POST" style="display: inline;"
                                                      onsubmit="return confirm('Bu işlemi iptal etmek istediğinizden emin misiniz?')">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="İptal">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $transactions->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Ödeme işlemi bulunamadı</h5>
                        <p class="text-muted">Belirtilen kriterlere uygun ödeme işlemi bulunmuyor.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- İade Modal -->
<div class="modal fade" id="refundModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">İade İşlemi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="refundForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">İade Tutarı</label>
                        <input type="number" name="amount" id="refundAmount" class="form-control" 
                               step="0.01" min="0.01" required>
                        <small class="form-text text-muted">Maksimum iade edilebilir tutar: <span id="maxRefundAmount"></span> ₺</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">İade Sebebi</label>
                        <textarea name="reason" class="form-control" rows="3" 
                                  placeholder="İade sebebini belirtin..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-undo me-2"></i>İade Et
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function showRefundModal(transactionId, maxAmount) {
    document.getElementById('refundForm').action = `/admin/payment-transactions/${transactionId}/refund`;
    document.getElementById('refundAmount').value = maxAmount;
    document.getElementById('refundAmount').max = maxAmount;
    document.getElementById('maxRefundAmount').textContent = maxAmount.toFixed(2);
    
    const modal = new bootstrap.Modal(document.getElementById('refundModal'));
    modal.show();
}
</script>
@endsection
