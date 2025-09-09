<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'phone',
        'address',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Admin kontrolü
     */
    public function isAdmin(): bool
    {
        return $this->is_admin;
    }

    /**
     * Kullanıcının siparişleri
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Kullanıcının sepeti
     */
    public function cartItems()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Kullanıcının favori ürünleri
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Kullanıcının favori ürünleri (ürün detayları ile)
     */
    public function favoriteProducts()
    {
        return $this->belongsToMany(Product::class, 'favorites', 'user_id', 'product_kod', 'id', 'kod')
            ->withTimestamps()
            ->orderBy('favorites.created_at', 'desc');
    }

    /**
     * Kullanıcının API token'ları
     */
    public function apiTokens()
    {
        return $this->hasMany(ApiToken::class);
    }

    /**
     * Aktif API token'ları
     */
    public function activeApiTokens()
    {
        return $this->apiTokens()->active();
    }
}
