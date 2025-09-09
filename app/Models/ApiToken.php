<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ApiToken extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'token',
        'abilities',
        'last_used_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'abilities' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'token',
    ];

    /**
     * Token'ın sahibi kullanıcı
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Token oluştur
     */
    public static function createToken(User $user, string $name, array $abilities = ['*'], int $expiresInDays = null): self
    {
        $token = Str::random(64);
        
        $apiToken = self::create([
            'user_id' => $user->id,
            'name' => $name,
            'token' => hash('sha256', $token),
            'abilities' => $abilities,
            'expires_at' => $expiresInDays ? now()->addDays($expiresInDays) : null,
        ]);
        
        // Token'ı response'da döndürmek için geçici olarak sakla
        $apiToken->plainTextToken = $token;
        
        return $apiToken;
    }

    /**
     * Token'ın geçerli olup olmadığını kontrol et
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Token'ı kullan (last_used_at güncelle)
     */
    public function use(): void
    {
        $this->update(['last_used_at' => now()]);
    }

    /**
     * Token'ı devre dışı bırak
     */
    public function revoke(): void
    {
        $this->update(['is_active' => false]);
    }

    /**
     * Token'ı yenile
     */
    public function refresh(): string
    {
        $newToken = Str::random(64);
        $this->update([
            'token' => hash('sha256', $newToken),
            'last_used_at' => null,
        ]);
        
        return $newToken;
    }

    /**
     * Scope: Aktif token'lar
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    });
    }

    /**
     * Scope: Belirli yetkileri olan token'lar
     */
    public function scopeWithAbility($query, string $ability)
    {
        return $query->where(function ($q) use ($ability) {
            $q->whereJsonContains('abilities', '*')
              ->orWhereJsonContains('abilities', $ability);
        });
    }
}
