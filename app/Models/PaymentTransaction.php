<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'order_id',
        'payment_provider_id',
        'transaction_id',
        'external_transaction_id',
        'amount',
        'currency',
        'payment_method',
        'status',
        'gateway_response',
        'gateway_error',
        'callback_data',
        'webhook_data',
        'processed_at',
        'failed_at',
        'refunded_at',
        'refund_amount',
        'commission_amount',
        'net_amount',
        'ip_address',
        'user_agent',
        'metadata'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'gateway_response' => 'array',
        'gateway_error' => 'array',
        'callback_data' => 'array',
        'webhook_data' => 'array',
        'metadata' => 'array',
        'processed_at' => 'datetime',
        'failed_at' => 'datetime',
        'refunded_at' => 'datetime'
    ];

    /**
     * Sipariş ilişkisi
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Ödeme sağlayıcısı ilişkisi
     */
    public function paymentProvider(): BelongsTo
    {
        return $this->belongsTo(PaymentProvider::class);
    }

    /**
     * Transaction ID oluştur
     */
    public static function generateTransactionId(): string
    {
        do {
            $transactionId = 'TXN-' . date('YmdHis') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (static::where('transaction_id', $transactionId)->exists());

        return $transactionId;
    }

    /**
     * Durum etiketleri
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Beklemede',
            'processing' => 'İşleniyor',
            'completed' => 'Tamamlandı',
            'failed' => 'Başarısız',
            'cancelled' => 'İptal Edildi',
            'refunded' => 'İade Edildi',
            'partially_refunded' => 'Kısmi İade',
            default => 'Bilinmiyor'
        };
    }

    /**
     * Durum rengi
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'warning',
            'processing' => 'info',
            'completed' => 'success',
            'failed' => 'danger',
            'cancelled' => 'secondary',
            'refunded' => 'info',
            'partially_refunded' => 'warning',
            default => 'secondary'
        };
    }

    /**
     * Formatlanmış tutar
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount, 2) . ' ' . $this->getCurrencySymbol();
    }

    /**
     * Formatlanmış iade tutarı
     */
    public function getFormattedRefundAmountAttribute(): string
    {
        if ($this->refund_amount > 0) {
            return number_format($this->refund_amount, 2) . ' ' . $this->getCurrencySymbol();
        }
        return '-';
    }

    /**
     * Formatlanmış komisyon tutarı
     */
    public function getFormattedCommissionAmountAttribute(): string
    {
        if ($this->commission_amount > 0) {
            return number_format($this->commission_amount, 2) . ' ' . $this->getCurrencySymbol();
        }
        return '-';
    }

    /**
     * Formatlanmış net tutar
     */
    public function getFormattedNetAmountAttribute(): string
    {
        if ($this->net_amount > 0) {
            return number_format($this->net_amount, 2) . ' ' . $this->getCurrencySymbol();
        }
        return '-';
    }

    /**
     * Para birimi sembolü
     */
    public function getCurrencySymbol(): string
    {
        return match(strtoupper($this->currency)) {
            'TRY', 'TL' => '₺',
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            default => $this->currency
        };
    }

    /**
     * İşlem başarılı mı?
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * İşlem başarısız mı?
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * İşlem beklemede mi?
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * İşlem işleniyor mu?
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    /**
     * İade edilebilir mi?
     */
    public function isRefundable(): bool
    {
        return $this->status === 'completed' && $this->refund_amount < $this->amount;
    }

    /**
     * Tam iade edilmiş mi?
     */
    public function isFullyRefunded(): bool
    {
        return $this->status === 'refunded' || $this->refund_amount >= $this->amount;
    }

    /**
     * Kısmi iade edilmiş mi?
     */
    public function isPartiallyRefunded(): bool
    {
        return $this->refund_amount > 0 && $this->refund_amount < $this->amount;
    }

    /**
     * İşlemi başarılı olarak işaretle
     */
    public function markAsCompleted(array $gatewayResponse = []): bool
    {
        if ($this->status === 'processing') {
            $this->update([
                'status' => 'completed',
                'processed_at' => now(),
                'gateway_response' => $gatewayResponse
            ]);
            return true;
        }
        return false;
    }

    /**
     * İşlemi başarısız olarak işaretle
     */
    public function markAsFailed(array $gatewayError = []): bool
    {
        if (in_array($this->status, ['pending', 'processing'])) {
            $this->update([
                'status' => 'failed',
                'failed_at' => now(),
                'gateway_error' => $gatewayError
            ]);
            return true;
        }
        return false;
    }

    /**
     * İşlemi iptal et
     */
    public function markAsCancelled(): bool
    {
        if (in_array($this->status, ['pending', 'processing'])) {
            $this->update(['status' => 'cancelled']);
            return true;
        }
        return false;
    }

    /**
     * İade işlemi
     */
    public function refund(float $amount = null, string $reason = null): bool
    {
        if (!$this->isRefundable()) {
            return false;
        }

        $refundAmount = $amount ?? ($this->amount - $this->refund_amount);
        
        if ($refundAmount <= 0 || $refundAmount > ($this->amount - $this->refund_amount)) {
            return false;
        }

        $newRefundAmount = $this->refund_amount + $refundAmount;
        $newStatus = $newRefundAmount >= $this->amount ? 'refunded' : 'partially_refunded';

        $this->update([
            'refund_amount' => $newRefundAmount,
            'status' => $newStatus,
            'refunded_at' => now(),
            'metadata' => array_merge($this->metadata ?? [], [
                'refund_reason' => $reason,
                'last_refund_amount' => $refundAmount,
                'last_refunded_at' => now()->toISOString()
            ])
        ]);

        return true;
    }

    /**
     * Gateway response'dan önemli bilgileri al
     */
    public function getGatewayInfo(): array
    {
        $response = $this->gateway_response ?? [];
        
        return [
            'transaction_id' => $response['transaction_id'] ?? $response['id'] ?? null,
            'auth_code' => $response['auth_code'] ?? $response['authorization_code'] ?? null,
            'rrn' => $response['rrn'] ?? $response['retrieval_reference_number'] ?? null,
            'masked_card' => $response['masked_card'] ?? $response['card_number'] ?? null,
            'card_brand' => $response['card_brand'] ?? $response['card_type'] ?? null,
            'installment' => $response['installment'] ?? $response['installment_count'] ?? null,
            'commission_rate' => $response['commission_rate'] ?? null,
            'commission_amount' => $response['commission_amount'] ?? null,
        ];
    }
}
