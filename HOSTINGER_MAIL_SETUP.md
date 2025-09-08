# Hostinger Mail Kurulumu

Bu dokümantasyon, Laravel uygulamanızda Hostinger e-posta hizmetini kullanarak e-posta bildirimleri göndermek için gerekli adımları açıklar.

## 1. Hostinger E-posta Hesabı Oluşturma

1. Hostinger kontrol panelinize (hPanel) giriş yapın
2. **E-postalar** sekmesine gidin
3. **E-posta Hesapları** bölümünden yeni bir e-posta hesabı oluşturun
4. Oluşturduğunuz e-posta adresi ve şifresini not edin

## 2. .env Dosyası Ayarları

Projenizin kök dizinindeki `.env` dosyasına aşağıdaki ayarları ekleyin:

```env
# Hostinger Mail Ayarları
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=noreply@yourdomain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Basital.com"
```

### Ayarlar Açıklaması:

- `MAIL_MAILER`: SMTP kullanacağımızı belirtir
- `MAIL_HOST`: Hostinger'ın SMTP sunucu adresi
- `MAIL_PORT`: SSL için 465, TLS için 587 kullanılabilir
- `MAIL_USERNAME`: Oluşturduğunuz e-posta adresi
- `MAIL_PASSWORD`: E-posta hesabınızın şifresi
- `MAIL_ENCRYPTION`: SSL şifreleme
- `MAIL_FROM_ADDRESS`: Gönderen e-posta adresi
- `MAIL_FROM_NAME`: Gönderen adı

## 3. Alternatif Port Ayarları

Hostinger'da farklı port seçenekleri mevcuttur:

### SSL ile (Önerilen):
```env
MAIL_PORT=465
MAIL_ENCRYPTION=ssl
```

### TLS ile:
```env
MAIL_PORT=587
MAIL_ENCRYPTION=tls
```

## 4. Mail Test Etme

Mail ayarlarını test etmek için aşağıdaki komutu kullanabilirsiniz:

```bash
php artisan mail:test your-email@example.com
```

## 5. Mevcut Mail Sınıfları

Uygulamada aşağıdaki mail sınıfları mevcuttur:

- `OrderConfirmationMail`: Sipariş onayı
- `PaymentSuccessMail`: Ödeme başarılı
- `OrderStatusUpdateMail`: Sipariş durumu güncelleme
- `WelcomeMail`: Hoş geldin maili

## 6. Sorun Giderme

### Mail gönderilmiyor:
1. E-posta adresi ve şifrenin doğru olduğundan emin olun
2. Port ve şifreleme ayarlarını kontrol edin
3. Hostinger'da e-posta hesabının aktif olduğundan emin olun

### SSL/TLS Hataları:
1. Port 465 ve SSL kullanmayı deneyin
2. Port 587 ve TLS kullanmayı deneyin
3. Firewall ayarlarını kontrol edin

### Günlük Kontrolü:
```bash
tail -f storage/logs/laravel.log
```

## 7. Güvenlik Notları

- E-posta şifrenizi güvenli tutun
- `.env` dosyasını versiyon kontrolüne eklemeyin
- Üretim ortamında `APP_DEBUG=false` yapın

## 8. Hostinger Destek

Sorun yaşarsanız Hostinger destek ekibiyle iletişime geçin:
- https://support.hostinger.com/
- Live Chat desteği mevcuttur
