<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Coupon extends Model
{
    protected $fillable = [
        'code', 'name', 'description', 'type', 'value', 'minimum_amount', 'maximum_discount',
        'usage_limit', 'usage_limit_per_user', 'used_count', 'is_active',
        'starts_at', 'expires_at', 'applicable_products', 'applicable_categories',
        'excluded_products', 'excluded_categories', 'free_shipping'
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'is_active' => 'boolean',
        'free_shipping' => 'boolean',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'applicable_products' => 'array',
        'applicable_categories' => 'array',
        'excluded_products' => 'array',
        'excluded_products' => 'array',
    ];

    /**
     * Kupon kullanımları ilişkisi
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Siparişler ilişkisi
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Aktif kuponları getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('starts_at')
                          ->orWhere('starts_at', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>=', now());
                    });
    }

    /**
     * Kupon koduna göre ara
     */
    public function scopeByCode($query, $code)
    {
        return $query->where('code', strtoupper($code));
    }

    /**
     * Kupon geçerli mi kontrol et
     */
    public function isValid(): bool
    {
        // Aktif değilse
        if (!$this->is_active) {
            return false;
        }

        // Başlangıç tarihi kontrolü
        if ($this->starts_at && $this->starts_at > now()) {
            return false;
        }

        // Bitiş tarihi kontrolü
        if ($this->expires_at && $this->expires_at < now()) {
            return false;
        }

        // Kullanım limiti kontrolü
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Kullanıcı için kupon geçerli mi kontrol et
     */
    public function isValidForUser($userId = null, $sessionId = null): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        // Kullanıcı başına kullanım limiti kontrolü
        if ($this->usage_limit_per_user) {
            $userUsageCount = $this->usages()
                ->where(function ($query) use ($userId, $sessionId) {
                    if ($userId) {
                        $query->where('user_id', $userId);
                    } elseif ($sessionId) {
                        $query->where('session_id', $sessionId);
                    }
                })
                ->count();

            if ($userUsageCount >= $this->usage_limit_per_user) {
                return false;
            }
        }

        return true;
    }

    /**
     * İndirim tutarını hesapla
     */
    public function calculateDiscount($amount): float
    {
        if ($this->type === 'percentage') {
            $discount = ($amount * $this->value) / 100;
            
            // Maksimum indirim kontrolü
            if ($this->maximum_discount && $discount > $this->maximum_discount) {
                $discount = $this->maximum_discount;
            }
        } else {
            $discount = $this->value;
        }

        // İndirim tutarı sepet tutarından fazla olamaz
        return min($discount, $amount);
    }

    /**
     * Kupon kullanılabilir mi kontrol et
     */
    public function canBeUsedFor($amount, $userId = null, $sessionId = null): array
    {
        $result = [
            'valid' => false,
            'message' => '',
            'discount' => 0
        ];

        // Temel geçerlilik kontrolü
        if (!$this->isValidForUser($userId, $sessionId)) {
            $result['message'] = 'Bu kupon geçersiz veya kullanım limiti dolmuş.';
            return $result;
        }

        // Minimum tutar kontrolü
        if ($this->minimum_amount && $amount < $this->minimum_amount) {
            $result['message'] = "Bu kuponu kullanabilmek için minimum {$this->minimum_amount} TL tutarında alışveriş yapmalısınız.";
            return $result;
        }

        // İndirim hesapla
        $discount = $this->calculateDiscount($amount);
        
        if ($discount <= 0) {
            $result['message'] = 'Bu kupon için geçerli bir indirim hesaplanamadı.';
            return $result;
        }

        $result['valid'] = true;
        $result['discount'] = $discount;
        $result['message'] = "Kupon başarıyla uygulandı. İndirim: " . number_format($discount, 2) . " TL";

        return $result;
    }

    /**
     * Kuponu kullan
     */
    public function use($userId = null, $orderId = null, $sessionId = null, $discountAmount = 0, $orderTotal = 0): CouponUsage
    {
        // Kullanım sayısını artır
        $this->increment('used_count');

        // Kullanım kaydı oluştur
        return $this->usages()->create([
            'user_id' => $userId,
            'order_id' => $orderId,
            'session_id' => $sessionId,
            'discount_amount' => $discountAmount,
            'order_total' => $orderTotal,
            'ip_address' => request()->ip()
        ]);
    }

    /**
     * Kupon durumu etiketi
     */
    public function getStatusLabelAttribute(): string
    {
        if (!$this->is_active) {
            return 'Pasif';
        }

        if ($this->starts_at && $this->starts_at > now()) {
            return 'Henüz Başlamadı';
        }

        if ($this->expires_at && $this->expires_at < now()) {
            return 'Süresi Doldu';
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return 'Limit Doldu';
        }

        return 'Aktif';
    }

    /**
     * Kupon durumu rengi
     */
    public function getStatusColorAttribute(): string
    {
        if (!$this->is_active) {
            return 'secondary';
        }

        if ($this->starts_at && $this->starts_at > now()) {
            return 'info';
        }

        if ($this->expires_at && $this->expires_at < now()) {
            return 'danger';
        }

        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return 'warning';
        }

        return 'success';
    }

    /**
     * İndirim tipi etiketi
     */
    public function getTypeLabelAttribute(): string
    {
        return $this->type === 'percentage' ? 'Yüzde' : 'Sabit Tutar';
    }

    /**
     * Formatlanmış değer
     */
    public function getFormattedValueAttribute(): string
    {
        if ($this->type === 'percentage') {
            return '%' . $this->value;
        }
        return number_format($this->value, 2) . ' TL';
    }

    /**
     * Kalan kullanım sayısı
     */
    public function getRemainingUsageAttribute(): ?int
    {
        if (!$this->usage_limit) {
            return null;
        }
        return max(0, $this->usage_limit - $this->used_count);
    }

    /**
     * Kupon kodu oluştur
     */
    public static function generateCode($length = 8): string
    {
        do {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length));
        } while (static::where('code', $code)->exists());

        return $code;
    }
}
