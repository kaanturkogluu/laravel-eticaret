# E-Ticaret Sitesi - XML Import Sistemi
* Deploy Test Denemesi

Bu proje, XML verilerinden otomatik olarak ürün kataloğu oluşturan ve yöneten bir e-ticaret sitesidir.

## Özellikler

- ✅ XML dosyalarından otomatik ürün import
- ✅ Stok takibi (2'den az stok olan ürünler otomatik gizlenir)
- ✅ Responsive ve mobil uyumlu tasarım
- ✅ Admin paneli ile XML import yönetimi
- ✅ Otomatik güncelleme sistemi (30 dakikada bir)
- ✅ Ürün arama ve filtreleme
- ✅ Marka ve kategori filtreleri
- ✅ Ürün detay sayfaları
- ✅ Teknik özellikler ve resim galerisi

## Kurulum

1. **Gereksinimler:**
   - PHP 8.1+
   - MySQL 5.7+
   - Composer
   - Node.js (opsiyonel)

2. **Projeyi klonlayın:**
   ```bash
   git clone <repository-url>
   cd eticaret
   ```

3. **Bağımlılıkları yükleyin:**
   ```bash
   composer install
   ```

4. **Ortam değişkenlerini ayarlayın:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Veritabanını yapılandırın:**
   `.env` dosyasında veritabanı ayarlarını yapın:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=eticaret
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. **Veritabanını oluşturun:**
   ```bash
   php artisan migrate
   ```

7. **XML dosyasını import edin:**
   ```bash
   php artisan xml:import
   ```

8. **Sunucuyu başlatın:**
   ```bash
   php artisan serve
   ```

## Kullanım

### Web Arayüzü

- **Ana Sayfa:** `http://localhost:8000` - Ürün listesi
- **Admin Panel:** `http://localhost:8000/admin/xml-import` - XML import yönetimi

### Komut Satırı

```bash
# XML import
php artisan xml:import

# Belirli bir dosyayı import et
php artisan xml:import --file=/path/to/your/file.xml
```

### Otomatik Güncelleme

Sistem 30 dakikada bir otomatik olarak `liste.xml` dosyasını kontrol eder ve günceller. Bu özelliği aktif etmek için:

```bash
# Cron job ekleyin (Linux/Mac)
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1

# Windows için Task Scheduler kullanın
```

## XML Format

XML dosyası aşağıdaki formatta olmalıdır:

```xml
<ArrayOfXMLUrunView>
    <XMLUrunView>
        <Kod>12345</Kod>
        <Ad>Ürün Adı</Ad>
        <Miktar>10</Miktar>
        <Fiyat_SK>100.00</Fiyat_SK>
        <Fiyat_Bayi>80.00</Fiyat_Bayi>
        <Fiyat_Ozel>70.00</Fiyat_Ozel>
        <Doviz>USD</Doviz>
        <Marka>Marka Adı</Marka>
        <Kategori>Kategori Adı</Kategori>
        <AnaResim>https://example.com/image.jpg</AnaResim>
        <Barkod>123456789</Barkod>
        <Aciklama>Ürün açıklaması</Aciklama>
        <Detay>Detaylı açıklama</Detay>
        <Desi>1.5</Desi>
        <Kdv>20</Kdv>
        <urunResimleri>
            <UrunResimler>
                <UrunKodu>12345</UrunKodu>
                <Resim>https://example.com/image1.jpg</Resim>
            </UrunResimler>
        </urunResimleri>
        <TeknikOzellikler>
            <UrunTeknikOzellikler>
                <UrunKodu>12345</UrunKodu>
                <Ozellik>Özellik Adı</Ozellik>
                <Deger>Özellik Değeri</Deger>
            </UrunTeknikOzellikler>
        </TeknikOzellikler>
    </XMLUrunView>
</ArrayOfXMLUrunView>
```

## Veritabanı Yapısı

### Products Tablosu
- `kod` - Ürün kodu (unique)
- `ad` - Ürün adı
- `miktar` - Stok adeti
- `fiyat_sk`, `fiyat_bayi`, `fiyat_ozel` - Fiyat bilgileri
- `marka`, `kategori` - Marka ve kategori
- `ana_resim` - Ana resim URL'i
- `is_active` - Aktif/pasif durumu
- `last_updated` - Son güncelleme zamanı

### Product Images Tablosu
- Ürün resimlerini saklar
- `product_id` - Ürün ID'si
- `resim_url` - Resim URL'i
- `sort_order` - Sıralama

### Product Specifications Tablosu
- Ürün teknik özelliklerini saklar
- `product_id` - Ürün ID'si
- `ozellik` - Özellik adı
- `deger` - Özellik değeri

## Stok Kontrolü

- Stok adedi 2'den az olan ürünler otomatik olarak pasif yapılır
- Stok adedi 2 ve üzeri olan ürünler aktif yapılır
- Bu kontrol her XML import işleminde otomatik yapılır

## API Endpoints

- `GET /api/products` - Ürün listesi (JSON)
- `GET /product/{kod}` - Ürün detayı
- `POST /admin/xml-import` - XML dosya upload
- `POST /admin/xml-import/liste` - Liste.xml import
- `POST /admin/stock-control` - Stok kontrolü

## Geliştirme

### Yeni Özellik Ekleme

1. Migration oluşturun: `php artisan make:migration`
2. Model güncelleyin: `app/Models/`
3. Controller oluşturun: `php artisan make:controller`
4. View oluşturun: `resources/views/`
5. Route tanımlayın: `routes/web.php`

### Test

```bash
# Test çalıştır
php artisan test

# XML import test
php artisan xml:import --file=tests/fixtures/test.xml
```

## Sorun Giderme

### XML Import Hatası
- XML dosyasının formatını kontrol edin
- Dosya boyutunun 10MB'dan küçük olduğundan emin olun
- Log dosyalarını kontrol edin: `storage/logs/laravel.log`

### Veritabanı Hatası
- Veritabanı bağlantısını kontrol edin
- Migration'ların çalıştığından emin olun: `php artisan migrate:status`

### Stok Kontrolü
- Stok kontrolünü manuel çalıştırın: `php artisan xml:import`
- Admin panelinden stok kontrolü yapın

## Lisans

Bu proje MIT lisansı altında lisanslanmıştır.

## Katkıda Bulunma

1. Fork yapın
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Commit yapın (`git commit -m 'Add amazing feature'`)
4. Push yapın (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## İletişim

Proje hakkında sorularınız için issue açabilirsiniz.
