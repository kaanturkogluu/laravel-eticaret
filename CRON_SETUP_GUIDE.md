# 🕐 Server'da Cron Job Kurulum Rehberi

## 📋 Gereksinimler
- Linux/Unix sunucu (cPanel, Plesk, VPS, Dedicated Server)
- PHP CLI erişimi
- Laravel projesi yüklü

## 🔧 Adım 1: Sunucuda Cron Job Ekleme

### cPanel ile:
1. **cPanel'e giriş yapın**
2. **"Cron Jobs"** bölümüne gidin
3. **"Add New Cron Job"** tıklayın
4. Aşağıdaki ayarları yapın:

```
Minute: * (her dakika)
Hour: * (her saat)
Day: * (her gün)
Month: * (her ay)
Weekday: * (her hafta günü)
Command: /usr/bin/php /home/kullaniciadi/public_html/eticaret/artisan schedule:run
```

### SSH ile (VPS/Dedicated):
```bash
# Crontab'ı düzenle
crontab -e

# Aşağıdaki satırı ekle
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

### Plesk ile:
1. **Plesk Panel'e giriş yapın**
2. **"Scheduled Tasks"** bölümüne gidin
3. **"Create Task"** tıklayın
4. Ayarlar:
   - **Command**: `php artisan schedule:run`
   - **Working directory**: `/var/www/vhosts/domain.com/httpdocs/eticaret`
   - **Run**: `Every minute`

## 🔧 Adım 2: PHP Yolu Kontrolü

### PHP CLI yolunu bulun:
```bash
which php
# veya
whereis php
```

### Alternatif yollar (sunucuya göre değişir):
- `/usr/bin/php`
- `/usr/local/bin/php`
- `/opt/php/bin/php`
- `/usr/local/php/bin/php`

## 🔧 Adım 3: Proje Yolu Kontrolü

### Mutlak yol kullanın:
```bash
# Proje dizinine gidin
cd /home/kullaniciadi/public_html/eticaret

# Tam yolu alın
pwd
```

### Örnek cron job komutu:
```bash
* * * * * cd /home/kullaniciadi/public_html/eticaret && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

## 🔧 Adım 4: Test Etme

### 1. Manuel test:
```bash
cd /path/to/your/project
php artisan schedule:run
```

### 2. Log kontrolü:
```bash
# Laravel log'larını kontrol edin
tail -f storage/logs/laravel.log

# Cron log'larını kontrol edin
tail -f /var/log/cron
```

## 🔧 Adım 5: Mevcut Scheduled Jobs

Projenizde şu otomatik görevler tanımlı:

### 1. **Güneş Bilgisayar XML Import** (30 dakikada bir)
```php
Schedule::command('gunes:xml-import')->everyThirtyMinutes();
```

### 2. **Döviz Kurları Güncelleme** (30 dakikada bir)
```php
Schedule::command('currency:update')->everyThirtyMinutes();
```

## 🔧 Adım 6: Ek Güvenlik ve Optimizasyon

### 1. **Log dosyası oluşturun:**
```bash
* * * * * cd /path/to/project && /usr/bin/php artisan schedule:run >> /path/to/project/storage/logs/cron.log 2>&1
```

### 2. **Sadece belirli saatlerde çalıştırın:**
```bash
# Sadece 09:00-18:00 arası
* 9-18 * * * cd /path/to/project && /usr/bin/php artisan schedule:run >> /dev/null 2>&1
```

### 3. **Lock dosyası ile çakışmayı önleyin:**
```bash
* * * * * cd /path/to/project && /usr/bin/php artisan schedule:run --timeout=60 >> /dev/null 2>&1
```

## 🔧 Adım 7: Sorun Giderme

### 1. **Cron job çalışmıyor:**
```bash
# PHP CLI versiyonunu kontrol edin
php -v

# Laravel projesi çalışıyor mu?
php artisan --version

# Permission'ları kontrol edin
ls -la artisan
chmod +x artisan
```

### 2. **Permission hatası:**
```bash
# Storage ve cache klasörlerine yazma izni
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### 3. **Memory limit hatası:**
```bash
# php.ini'de memory_limit artırın
memory_limit = 256M
```

## 🔧 Adım 8: Monitoring

### 1. **Cron job durumunu kontrol edin:**
```bash
# Aktif cron job'ları listele
crontab -l

# Cron servisini kontrol edin
systemctl status cron
# veya
service cron status
```

### 2. **Laravel Scheduler durumunu kontrol edin:**
```bash
php artisan schedule:list
```

## 📝 Örnek cPanel Cron Job Ayarları

```
Minute: *
Hour: *
Day: *
Month: *
Weekday: *
Command: /usr/bin/php /home/kullaniciadi/public_html/eticaret/artisan schedule:run
```

## ⚠️ Önemli Notlar

1. **Her sunucu farklıdır** - PHP yolu ve proje yolu değişebilir
2. **Test edin** - Önce manuel olarak çalıştırıp test edin
3. **Log tutun** - Hataları takip etmek için log dosyası oluşturun
4. **Backup alın** - Önemli değişikliklerden önce backup alın

## 🚀 Hızlı Test

```bash
# 1. Proje dizinine gidin
cd /path/to/your/project

# 2. Manuel çalıştırın
php artisan schedule:run

# 3. Log'ları kontrol edin
tail -f storage/logs/laravel.log
```

Bu adımları takip ederek cron job'larınızı server'da çalıştırabilirsiniz! 🎉
