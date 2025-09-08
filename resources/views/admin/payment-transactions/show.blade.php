@extends('layouts.admin')

@section('title', 'Ödeme İşlemi Detayı - Admin Panel')
@section('page-title', 'Ödeme İşlemi Detayı')

@section('content')
<div class="row">
    <div class="col-md-8">
        <!-- İşlem Detayları -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-receipt me-2"></i>İşlem Detayları
                </h5>
                <span class="badge bg-{{ $transaction->status_color }} fs-6">
                    {{ $transaction->status_label }}
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Transaction ID:</strong></td>
                                <td><code>{{ $transaction->transaction_id }}</code></td>
                            </tr>
                            @if($transaction->external_transaction_id)
                            <tr>
                                <td><strong>External Transaction ID:</strong></td>
                                <td><code>{{ $transaction->external_transaction_id }}</code></td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong>Ödeme Sağlayıcısı:</strong></td>
                                <td>
                                    @if($transaction->paymentProvider)
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $transaction->paymentProvider->logo_url }}" 
                                                 alt="{{ $transaction->paymentProvider->name }}" 
                                                 style="width: 24px; height: 24px; object-fit: contain;" class="me-2"
                                                 onerror="this.style.display='none';">
                                            {{ $transaction->paymentProvider->name }}
                                        </div>
                                    @else
                                        <span class="text-muted">Sağlayıcı bulunamadı</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Ödeme Yöntemi:</strong></td>
                                <td>
                                    @switch($transaction->payment_method)
                                        @case('credit_card') Kredi Kartı @break
                                        @case('bank_transfer') Banka Havalesi @break
                                        @case('wallet') Cüzdan @break
                                        @case('cash_on_delivery') Kapıda Ödeme @break
                                        @default {{ $transaction->payment_method }} @break
                                    @endswitch
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Para Birimi:</strong></td>
                                <td>{{ $transaction->currency }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>İşlem Tutarı:</strong></td>
                                <td><strong class="text-primary">{{ $transaction->formatted_amount }}</strong></td>
                            </tr>
                            @if($transaction->commission_amount > 0)
                            <tr>
                                <td><strong>Komisyon:</strong></td>
                                <td class="text-warning">{{ $transaction->formatted_commission_amount }}</td>
                            </tr>
                            @endif
                            @if($transaction->net_amount > 0)
                            <tr>
                                <td><strong>Net Tutar:</strong></td>
                                <td class="text-success">{{ $transaction->formatted_net_amount }}</td>
                            </tr>
                            @endif
                            @if($transaction->refund_amount > 0)
                            <tr>
                                <td><strong>İade Tutarı:</strong></td>
                                <td class="text-danger">{{ $transaction->formatted_refund_amount }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td><strong>Oluşturulma Tarihi:</strong></td>
                                <td>{{ $transaction->created_at->format('d.m.Y H:i:s') }}</td>
                            </tr>
                            @if($transaction->processed_at)
                            <tr>
                                <td><strong>İşlem Tarihi:</strong></td>
                                <td>{{ $transaction->processed_at->format('d.m.Y H:i:s') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Gateway Yanıtları -->
        @if($transaction->gateway_response || $transaction->gateway_error)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-exchange-alt me-2"></i>Gateway Yanıtları
                </h6>
            </div>
            <div class="card-body">
                @if($transaction->gateway_response)
                <div class="mb-3">
                    <h6 class="text-success">Başarılı Yanıt:</h6>
                    <pre class="bg-light p-3 rounded"><code>{{ json_encode($transaction->gateway_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
                @endif
                
                @if($transaction->gateway_error)
                <div class="mb-3">
                    <h6 class="text-danger">Hata Yanıtı:</h6>
                    <pre class="bg-light p-3 rounded"><code>{{ json_encode($transaction->gateway_error, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Callback/Webhook Verileri -->
        @if($transaction->callback_data || $transaction->webhook_data)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-webhook me-2"></i>Callback/Webhook Verileri
                </h6>
            </div>
            <div class="card-body">
                @if($transaction->callback_data)
                <div class="mb-3">
                    <h6>Callback Verisi:</h6>
                    <pre class="bg-light p-3 rounded"><code>{{ json_encode($transaction->callback_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
                @endif
                
                @if($transaction->webhook_data)
                <div class="mb-3">
                    <h6>Webhook Verisi:</h6>
                    <pre class="bg-light p-3 rounded"><code>{{ json_encode($transaction->webhook_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
                @endif
            </div>
        </div>
        @endif
        
        <!-- Metadata -->
        @if($transaction->metadata)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Ek Bilgiler
                </h6>
            </div>
            <div class="card-body">
                <pre class="bg-light p-3 rounded"><code>{{ json_encode($transaction->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-md-4">
        <!-- Sipariş Bilgileri -->
        @if($transaction->order)
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-shopping-cart me-2"></i>Sipariş Bilgileri
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td><strong>Sipariş No:</strong></td>
                        <td>
                            <a href="{{ route('orders.show', $transaction->order->id) }}" 
                               class="text-decoration-none">
                                {{ $transaction->order->order_number }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>Müşteri:</strong></td>
                        <td>{{ $transaction->order->customer_email }}</td>
                    </tr>
                    <tr>
                        <td><strong>Sipariş Tutarı:</strong></td>
                        <td>{{ number_format($transaction->order->total, 2) }} ₺</td>
                    </tr>
                    <tr>
                        <td><strong>Sipariş Durumu:</strong></td>
                        <td>
                            <span class="badge bg-{{ $transaction->order->status === 'completed' ? 'success' : 'warning' }}">
                                {{ ucfirst($transaction->order->status) }}
                            </span>
                        </td>
                    </tr>
                </table>
                <a href="{{ route('orders.show', $transaction->order->id) }}" class="btn btn-outline-primary btn-sm w-100">
                    <i class="fas fa-eye me-2"></i>Siparişi Görüntüle
                </a>
            </div>
        </div>
        @endif
        
        <!-- İşlemler -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-tools me-2"></i>İşlemler
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($transaction->status === 'pending' || $transaction->status === 'processing')
                        <form action="{{ route('admin.payment-transactions.check-status', $transaction) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-info w-100">
                                <i class="fas fa-sync me-2"></i>Durum Kontrolü
                            </button>
                        </form>
                    @endif
                    
                    @if($transaction->isRefundable())
                        <button type="button" class="btn btn-warning w-100" 
                                onclick="showRefundModal({{ $transaction->id }}, {{ $transaction->amount - $transaction->refund_amount }})">
                            <i class="fas fa-undo me-2"></i>İade Et
                        </button>
                    @endif
                    
                    @if(in_array($transaction->status, ['pending', 'processing']))
                        <form action="{{ route('admin.payment-transactions.cancel', $transaction) }}" method="POST"
                              onsubmit="return confirm('Bu işlemi iptal etmek istediğinizden emin misiniz?')">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-times me-2"></i>İptal Et
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('admin.payment-transactions.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-arrow-left me-2"></i>Geri Dön
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Sistem Bilgileri -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-server me-2"></i>Sistem Bilgileri
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm">
                    @if($transaction->ip_address)
                    <tr>
                        <td><strong>IP Adresi:</strong></td>
                        <td><code>{{ $transaction->ip_address }}</code></td>
                    </tr>
                    @endif
                    @if($transaction->user_agent)
                    <tr>
                        <td><strong>User Agent:</strong></td>
                        <td><small>{{ Str::limit($transaction->user_agent, 50) }}</small></td>
                    </tr>
                    @endif
                    <tr>
                        <td><strong>Oluşturulma:</strong></td>
                        <td>{{ $transaction->created_at->format('d.m.Y H:i:s') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Güncellenme:</strong></td>
                        <td>{{ $transaction->updated_at->format('d.m.Y H:i:s') }}</td>
                    </tr>
                </table>
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
