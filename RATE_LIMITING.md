# Rate Limiting Sistemi

Bu dokümantasyon, e-ticaret sisteminde uygulanan rate limiting (hız sınırlama) sistemini açıklar.

## 🛡️ Güvenlik Özellikleri

Rate limiting sistemi, aşağıdaki güvenlik tehditlerine karşı koruma sağlar:

- **Brute Force Saldırıları**: Login ve register işlemlerinde çok fazla deneme
- **DDoS Saldırıları**: Aşırı yük oluşturan istekler
- **API Kötüye Kullanımı**: API endpoint'lerinin aşırı kullanımı
- **Spam İstekler**: Otomatik bot saldırıları

## 📊 Rate Limiting Kuralları

### Kimlik Doğrulama (Authentication)

| Endpoint | Maksimum Deneme | Süre | Açıklama |
|----------|----------------|------|----------|
| `POST /login` | 5 | 15 dakika | Login denemeleri |
| `POST /register` | 3 | 60 dakika | Kayıt denemeleri |
| `POST /email/verification-notification` | 3 | 5 dakika | E-posta doğrulama |

### API Endpoint'leri

| Endpoint | Maksimum İstek | Süre | Açıklama |
|----------|----------------|------|----------|
| `GET /api/products` | 100 | 1 dakika | Ürün API'si |

### Sepet İşlemleri

| Endpoint | Maksimum İstek | Süre | Açıklama |
|----------|----------------|------|----------|
| `POST /cart/add` | 20 | 1 dakika | Sepete ekleme |
| `POST /cart/remove` | 20 | 1 dakika | Sepetten çıkarma |
| `POST /cart/update` | 20 | 1 dakika | Sepet güncelleme |
| `POST /cart/clear` | 5 | 1 dakika | Sepet temizleme |

### Kupon İşlemleri

| Endpoint | Maksimum İstek | Süre | Açıklama |
|----------|----------------|------|----------|
| `POST /cart/coupon/apply` | 10 | 1 dakika | Kupon uygulama |
| `POST /cart/coupon/remove` | 10 | 1 dakika | Kupon kaldırma |
| `POST /cart/coupon/validate` | 20 | 1 dakika | Kupon doğrulama |

### Ödeme İşlemleri

| Endpoint | Maksimum İstek | Süre | Açıklama |
|----------|----------------|------|----------|
| `POST /payment/initiate` | 5 | 1 dakika | Ödeme başlatma |
| `POST /payment/check-status` | 10 | 1 dakika | Ödeme durumu sorgulama |

## 🔧 Teknik Detaylar

### Middleware Yapısı

```php
// Custom Rate Limiter Middleware
Route::post('/login', [LoginController::class, 'login'])
    ->middleware('custom.throttle:login,5,15');
```

### Rate Limiting Anahtarları

- **IP Tabanlı**: `login:192.168.1.1`
- **Kullanıcı Tabanlı**: `email-verification:user_id`
- **Endpoint Tabanlı**: `api:192.168.1.1`

### Cache Sistemi

Rate limiting verileri Laravel'in cache sistemi üzerinden saklanır:

```php
// Varsayılan cache driver
'cache_driver' => env('RATE_LIMITING_CACHE_DRIVER', 'file')
```

## 🚨 Rate Limit Aşıldığında

### HTTP Durum Kodu
- **429 Too Many Requests**

### Yanıt Formatı
```json
{
    "message": "Çok fazla istek gönderildi. Lütfen 60 saniye sonra tekrar deneyin.",
    "retry_after": 60
}
```

### Kullanıcı Arayüzü
- Özel 429 hata sayfası (`resources/views/errors/429.blade.php`)
- Geri sayım sayacı
- Otomatik sayfa yenileme

## 🧪 Test Etme

### Komut Satırı Testi
```bash
# Login endpoint'ini test et
php artisan rate-limit:test --endpoint=login --attempts=6

# API endpoint'ini test et
php artisan rate-limit:test --endpoint=api/products --attempts=101
```

### Manuel Test
1. Belirtilen sayıda istek gönderin
2. Rate limit aşıldığında 429 hatası almalısınız
3. Bekleme süresi sonunda tekrar deneyebilirsiniz

## ⚙️ Yapılandırma

### Yeni Rate Limiting Kuralı Ekleme

1. `config/rate_limiting.php` dosyasına kural ekleyin:
```php
'new-endpoint' => [
    'max_attempts' => 10,
    'decay_minutes' => 1,
    'description' => 'Yeni endpoint - 10 istek, 1 dakika'
],
```

2. Route'a middleware ekleyin:
```php
Route::post('/new-endpoint', [Controller::class, 'method'])
    ->middleware('custom.throttle:new-endpoint,10,1');
```

### IP Whitelist

Admin IP'lerini muaf tutmak için:
```php
'whitelist' => [
    '127.0.0.1',
    '::1',
    '192.168.1.100', // Admin IP
],
```

## 📈 Monitoring

### Log Takibi
Rate limiting olayları Laravel log dosyalarında kaydedilir:
- `storage/logs/laravel.log`

### Cache Temizleme
```bash
# Rate limiting cache'ini temizle
php artisan cache:clear

# Belirli bir anahtarı temizle
php artisan tinker
>>> \Illuminate\Support\Facades\Cache::forget('rate-limiter:login:192.168.1.1')
```

## 🔒 Güvenlik Notları

1. **IP Spoofing**: Rate limiting IP tabanlı olduğu için IP spoofing saldırılarına karşı dikkatli olun
2. **Proxy Kullanımı**: Çoklu proxy kullanımı rate limiting'i bypass edebilir
3. **Distributed Attacks**: Dağıtık saldırılarda IP tabanlı rate limiting yeterli olmayabilir

## 🚀 Gelişmiş Özellikler

### Gelecek Geliştirmeler
- [ ] Kullanıcı tabanlı rate limiting
- [ ] Cooldown period'ları
- [ ] IP reputation sistemi
- [ ] Machine learning tabanlı anomali tespiti
- [ ] Real-time monitoring dashboard

### Entegrasyonlar
- [ ] Redis cache driver
- [ ] CloudFlare rate limiting
- [ ] AWS WAF entegrasyonu

## 📞 Destek

Rate limiting sistemi ile ilgili sorunlar için:
1. Log dosyalarını kontrol edin
2. Cache durumunu kontrol edin
3. Test komutunu çalıştırın
4. Yapılandırma dosyalarını kontrol edin

---

**Son Güncelleme**: {{ date('Y-m-d H:i:s') }}
**Versiyon**: 1.0.0
