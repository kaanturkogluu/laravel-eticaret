# Fiyat Düşüşü Bildirim Sistemi

Bu sistem, müşterilerin favorilerine eklediği ürünlerde fiyat düşüşü olduğunda otomatik olarak e-posta bildirimi gönderir.

## Özellikler

- ✅ Favori ürünlerde fiyat düşüşü takibi
- ✅ Otomatik e-posta bildirimleri
- ✅ İndirim yüzdesi ve tutarı hesaplama
- ✅ Fiyat geçmişi kaydetme
- ✅ Toplu bildirim gönderme
- ✅ Test modu

## Kurulum

### 1. Migration Çalıştırma
```bash
php artisan migrate
```

### 2. Mail Konfigürasyonu
`.env` dosyasında mail ayarlarınızı yapılandırın:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

## Kullanım

### Otomatik Bildirimler
Sistem, aşağıdaki durumlarda otomatik olarak çalışır:

1. **Admin panelinden ürün güncelleme**
2. **XML import işlemleri**
3. **Programatik ürün güncellemeleri**

### Manuel Bildirim Gönderme
```bash
# Tüm bekleyen bildirimleri gönder
php artisan price-drop:send-notifications

# Test modunda çalıştır
php artisan price-drop:send-notifications --test
```

### Cron Job Kurulumu
`crontab -e` ile aşağıdaki satırı ekleyin:
```bash
# Her gün saat 09:00'da fiyat düşüşü bildirimleri gönder
0 9 * * * cd /path/to/your/project && php artisan price-drop:send-notifications
```

## Dosya Yapısı

### Yeni Dosyalar
- `app/Models/ProductPriceHistory.php` - Fiyat geçmişi modeli
- `app/Mail/PriceDropNotificationMail.php` - E-posta sınıfı
- `app/Services/PriceDropNotificationService.php` - Bildirim servisi
- `app/Console/Commands/SendPriceDropNotifications.php` - Cron komutu
- `resources/views/emails/price-drop-notification.blade.php` - E-posta template
- `database/migrations/2025_01_15_150000_create_product_price_history_table.php` - Veritabanı tablosu

### Güncellenen Dosyalar
- `app/Models/Product.php` - Fiyat kontrolü metodu eklendi
- `app/Http/Controllers/Admin/ProductController.php` - Fiyat kontrolü entegrasyonu
- `app/Services/GunesXmlService.php` - XML import'ta fiyat kontrolü
- `app/Http/Controllers/Admin/XmlImportController.php` - XML import'ta fiyat kontrolü

## Veritabanı Tablosu

### product_price_history
```sql
- id (bigint, primary key)
- product_kod (string) - Ürün kodu
- old_price_sk (decimal) - Eski SK fiyatı
- old_price_bayi (decimal) - Eski bayii fiyatı
- old_price_ozel (decimal) - Eski özel fiyat
- new_price_sk (decimal) - Yeni SK fiyatı
- new_price_bayi (decimal) - Yeni bayii fiyatı
- new_price_ozel (decimal) - Yeni özel fiyat
- currency (string) - Para birimi
- price_difference (decimal) - Fiyat farkı
- discount_percentage (decimal) - İndirim yüzdesi
- is_discount (boolean) - İndirim var mı?
- changed_at (timestamp) - Değişiklik tarihi
- created_at, updated_at (timestamps)
```

## E-posta Template

E-posta template'i şu bilgileri içerir:
- Ürün bilgileri (ad, kod, marka, kategori)
- Eski ve yeni fiyatlar
- İndirim tutarı ve yüzdesi
- Ürün açıklaması
- Satın alma butonu
- Önemli bilgiler

## Loglama

Sistem tüm işlemleri loglar:
- Fiyat değişiklikleri
- E-posta gönderim durumları
- Hata mesajları
- İstatistikler

Log dosyaları: `storage/logs/laravel.log`

## Test Etme

### 1. Test Komutu
```bash
php artisan price-drop:send-notifications --test
```

### 2. Manuel Test
1. Bir ürünü favorilere ekleyin
2. Admin panelinden ürün fiyatını düşürün
3. E-posta bildiriminin geldiğini kontrol edin

### 3. Log Kontrolü
```bash
tail -f storage/logs/laravel.log | grep "Fiyat"
```

## Sorun Giderme

### E-posta Gönderilmiyor
1. Mail konfigürasyonunu kontrol edin
2. SMTP ayarlarını doğrulayın
3. Log dosyalarını inceleyin

### Fiyat Değişikliği Algılanmıyor
1. `updateWithPriceCheck()` metodunun kullanıldığından emin olun
2. Fiyat alanlarının doğru güncellendiğini kontrol edin
3. Log dosyalarını inceleyin

### Performans Sorunları
1. Toplu işlemlerde queue kullanın
2. E-posta gönderimini asenkron yapın
3. Veritabanı indekslerini kontrol edin

## Gelişmiş Özellikler

### Queue Kullanımı
E-posta gönderimini queue ile yapmak için:
```php
// PriceDropNotificationMail sınıfında zaten ShouldQueue implement edilmiş
```

### Özelleştirme
- E-posta template'ini düzenleyin
- İndirim eşik değerlerini ayarlayın
- Bildirim sıklığını değiştirin
- Filtreleme kriterleri ekleyin

## Güvenlik

- E-posta adreslerini doğrulayın
- Rate limiting uygulayın
- Spam koruması ekleyin
- Gizlilik politikasına uygun hareket edin

## Destek

Herhangi bir sorun yaşarsanız:
1. Log dosyalarını kontrol edin
2. Test komutunu çalıştırın
3. Veritabanı durumunu kontrol edin
4. Mail konfigürasyonunu doğrulayın
