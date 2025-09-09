# API Dokümantasyonu

Bu dokümantasyon, e-ticaret sisteminin API endpoint'lerini ve kullanımını açıklar.

## 🔐 Authentication

API, token-based authentication kullanır. Tüm korumalı endpoint'ler için geçerli bir API token gerekir.

### Token Alma

```http
POST /api/auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "password123",
    "device_name": "Mobile App",
    "abilities": ["read", "write"],
    "expires_in_days": 30
}
```

**Response:**
```json
{
    "success": true,
    "message": "Giriş başarılı",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@example.com",
            "is_admin": false
        },
        "token": "your-api-token-here",
        "token_name": "Mobile App",
        "abilities": ["read", "write"],
        "expires_at": "2024-10-09T21:17:00.000000Z"
    }
}
```

### Token Kullanımı

Token'ı aşağıdaki yöntemlerden biriyle gönderin:

1. **Authorization Header:**
```http
Authorization: Bearer your-api-token-here
```

2. **X-API-TOKEN Header:**
```http
X-API-TOKEN: your-api-token-here
```

3. **Query Parameter:**
```http
GET /api/products?api_token=your-api-token-here
```

## 📋 API Endpoints

### Authentication Endpoints

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| POST | `/api/auth/login` | Token al | ❌ |
| GET | `/api/auth/me` | Kullanıcı bilgileri | ✅ |
| GET | `/api/auth/tokens` | Token listesi | ✅ |
| POST | `/api/auth/refresh-token` | Token yenile | ✅ |
| POST | `/api/auth/revoke-token` | Token devre dışı bırak | ✅ |
| POST | `/api/auth/revoke-all-tokens` | Tüm token'ları devre dışı bırak | ✅ |
| POST | `/api/auth/logout` | Çıkış yap | ✅ |

### Product Endpoints

| Method | Endpoint | Description | Auth Required | Permission |
|--------|----------|-------------|---------------|------------|
| GET | `/api/products` | Ürün listesi | ✅ | read |
| GET | `/api/products/{kod}` | Ürün detayı | ✅ | read |

### User Endpoints

| Method | Endpoint | Description | Auth Required | Permission |
|--------|----------|-------------|---------------|------------|
| GET | `/api/user/profile` | Profil bilgileri | ✅ | read |
| PUT | `/api/user/profile` | Profil güncelle | ✅ | write |

### Cart Endpoints

| Method | Endpoint | Description | Auth Required | Permission |
|--------|----------|-------------|---------------|------------|
| GET | `/api/cart` | Sepet içeriği | ✅ | read |
| GET | `/api/cart/count` | Sepet ürün sayısı | ✅ | read |
| POST | `/api/cart/add` | Sepete ürün ekle | ✅ | write |
| POST | `/api/cart/remove` | Sepetten ürün çıkar | ✅ | write |
| POST | `/api/cart/clear` | Sepeti temizle | ✅ | write |

### Favorites Endpoints

| Method | Endpoint | Description | Auth Required | Permission |
|--------|----------|-------------|---------------|------------|
| GET | `/api/favorites` | Favori ürünler | ✅ | read |
| POST | `/api/favorites/add` | Favorilere ekle | ✅ | write |
| POST | `/api/favorites/remove` | Favorilerden çıkar | ✅ | write |

### Order Endpoints

| Method | Endpoint | Description | Auth Required | Permission |
|--------|----------|-------------|---------------|------------|
| GET | `/api/orders` | Sipariş listesi | ✅ | admin |
| GET | `/api/orders/{id}` | Sipariş detayı | ✅ | admin |

## 🔑 Token Yetkileri (Abilities)

| Yetki | Açıklama |
|-------|----------|
| `read` | Okuma yetkisi (GET istekleri) |
| `write` | Yazma yetkisi (POST, PUT, DELETE istekleri) |
| `delete` | Silme yetkisi |
| `admin` | Admin yetkisi |
| `*` | Tüm yetkiler |

## 📝 Request/Response Formatları

### Başarılı Response
```json
{
    "success": true,
    "message": "İşlem başarılı",
    "data": {
        // Response data
    }
}
```

### Hata Response
```json
{
    "success": false,
    "message": "Hata mesajı",
    "error_code": "ERROR_CODE",
    "errors": {
        // Validation errors
    }
}
```

### HTTP Status Kodları

| Kod | Açıklama |
|-----|----------|
| 200 | Başarılı |
| 201 | Oluşturuldu |
| 400 | Hatalı İstek |
| 401 | Yetkisiz |
| 403 | Yasak |
| 404 | Bulunamadı |
| 422 | Validation Hatası |
| 429 | Rate Limit Aşıldı |
| 500 | Sunucu Hatası |

## 🧪 Test Örnekleri

### 1. Token Alma

```bash
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password123",
    "device_name": "Test App",
    "abilities": ["read", "write"],
    "expires_in_days": 30
  }'
```

### 2. Ürün Listesi Alma

```bash
curl -X GET http://127.0.0.1:8000/api/products \
  -H "Authorization: Bearer your-api-token-here"
```

### 3. Sepete Ürün Ekleme

```bash
curl -X POST http://127.0.0.1:8000/api/cart/add \
  -H "Authorization: Bearer your-api-token-here" \
  -H "Content-Type: application/json" \
  -d '{
    "product_kod": "PROD001",
    "quantity": 2
  }'
```

### 4. Profil Güncelleme

```bash
curl -X PUT http://127.0.0.1:8000/api/user/profile \
  -H "Authorization: Bearer your-api-token-here" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Yeni İsim",
    "phone": "05551234567",
    "address": "Yeni Adres"
  }'
```

## 🔒 Güvenlik Özellikleri

### 1. Token Güvenliği
- Token'lar SHA-256 ile hash'lenir
- Token'lar veritabanında hash'li olarak saklanır
- Token'lar süreli olabilir (expires_at)
- Token'lar iptal edilebilir

### 2. Rate Limiting
- API endpoint'leri rate limiting ile korunur
- IP tabanlı sınırlama
- Endpoint bazında farklı limitler

### 3. Permission System
- Token bazlı yetki kontrolü
- Endpoint bazında yetki gereksinimleri
- Granular permission kontrolü

### 4. Validation
- Tüm input'lar validate edilir
- SQL injection koruması
- XSS koruması

## 📊 Monitoring ve Logging

### 1. Token Kullanımı
- Her token kullanımında `last_used_at` güncellenir
- Token kullanım istatistikleri
- Şüpheli aktivite tespiti

### 2. Error Logging
- Tüm API hataları loglanır
- Rate limiting olayları loglanır
- Güvenlik ihlalleri loglanır

## 🚀 Gelişmiş Özellikler

### 1. Token Yönetimi
- Token yenileme
- Token iptal etme
- Token listesi görüntüleme
- Toplu token iptal etme

### 2. API Versiyonlama
- API versiyonlama desteği
- Backward compatibility
- Deprecation warnings

### 3. Webhook Desteği
- Event-based notifications
- Real-time updates
- Custom webhook endpoints

## 📞 Destek

API ile ilgili sorunlar için:

1. **Dokümantasyon**: Bu dosyayı kontrol edin
2. **Test**: Postman collection'ları kullanın
3. **Logs**: `storage/logs/laravel.log` dosyasını kontrol edin
4. **Rate Limiting**: Rate limiting kurallarını kontrol edin

---

**Son Güncelleme**: {{ date('Y-m-d H:i:s') }}
**API Versiyonu**: 1.0.0
**Base URL**: `http://127.0.0.1:8000/api`
