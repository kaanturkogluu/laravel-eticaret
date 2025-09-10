# Image Optimization Documentation

Bu dokümantasyon, e-ticaret uygulamasında resim optimizasyonu özelliklerinin nasıl kullanılacağını açıklar.

## Özellikler

### 1. Otomatik Resim Optimizasyonu
- Yüklenen resimler otomatik olarak optimize edilir
- WebP formatı ile %30-50 daha küçük dosya boyutları
- JPEG fallback desteği (eski tarayıcılar için)
- Çoklu boyut desteği (thumbnail, medium, large)

### 2. Responsive Images
- Tarayıcı desteğine göre otomatik format seçimi
- Farklı ekran boyutları için optimize edilmiş boyutlar
- Lazy loading desteği

### 3. Performans İyileştirmeleri
- Dosya boyutunda %30-50 azalma
- Sayfa yükleme hızında artış
- Bant genişliği tasarrufu

## Kullanım

### Blade Template'lerde

```blade
{{-- Basit kullanım --}}
{!! product_image($product->images->first()->resim_url, $product->ad) !!}

{{-- Responsive image --}}
{!! responsive_image($image->resim_url, $alt, ['class' => 'custom-class']) !!}

{{-- Optimized URL --}}
<img src="{{ optimized_image_url($image->resim_url, 'medium') }}" alt="{{ $alt }}">

{{-- Lazy loading --}}
{!! lazy_image($image->resim_url, $alt, ['class' => 'product-thumb']) !!}
```

### Model'lerde

```php
// ProductImage model'inde
$image = ProductImage::find(1);

// Farklı boyutlarda URL'ler
$thumbnailUrl = $image->thumbnail_url;
$mediumUrl = $image->medium_url;
$largeUrl = $image->large_url;

// Responsive HTML
$html = $image->getResponsiveImageHtml($product->ad, ['class' => 'product-image']);
```

### Controller'larda

```php
// Yeni resim yükleme
$imageOptimizationService = app(\App\Services\ImageOptimizationService::class);
$result = $imageOptimizationService->optimizeAndStore($request->file('image'), 'products');

if ($result['success']) {
    // Optimize edilmiş resim URL'i
    $optimizedUrl = $result['original_url'];
}
```

## Konfigürasyon

`config/images.php` dosyasında ayarları değiştirebilirsiniz:

```php
return [
    'quality' => [
        'thumbnail' => 80,  // Thumbnail kalitesi
        'medium' => 85,     // Orta boyut kalitesi
        'large' => 90,      // Büyük boyut kalitesi
    ],
    
    'dimensions' => [
        'thumbnail' => ['width' => 300, 'height' => 300],
        'medium' => ['width' => 600, 'height' => 600],
        'large' => ['width' => 1200, 'height' => 1200],
    ],
    
    'formats' => [
        'primary' => 'webp',    // Ana format
        'fallback' => 'jpeg',   // Fallback format
    ],
];
```

## Komutlar

### Mevcut Resimleri Optimize Etme

```bash
# Tüm mevcut resimleri optimize et
php artisan images:optimize

# Zorla yeniden optimize et (zaten optimize edilmiş resimler dahil)
php artisan images:optimize --force
```

## Kurulum

1. Composer bağımlılıklarını yükleyin:
```bash
composer install
```

2. Intervention Image paketinin kurulduğundan emin olun:
```bash
composer require intervention/image
```

3. Storage linkini oluşturun:
```bash
php artisan storage:link
```

## Dosya Yapısı

Optimize edilmiş resimler şu yapıda saklanır:

```
storage/app/public/products/
├── large/
│   ├── image1.webp
│   ├── image1.jpg
│   └── ...
├── medium/
│   ├── image1.webp
│   └── ...
└── thumbnails/
    ├── image1.webp
    └── ...
```

## Performans Metrikleri

### Optimizasyon Öncesi
- Ortalama dosya boyutu: 2-5 MB
- Yükleme süresi: 3-8 saniye
- Bant genişliği kullanımı: Yüksek

### Optimizasyon Sonrası
- Ortalama dosya boyutu: 200-800 KB
- Yükleme süresi: 0.5-2 saniye
- Bant genişliği kullanımı: %60-70 azalma

## Tarayıcı Desteği

- **WebP**: Chrome, Firefox, Safari (iOS 14+), Edge
- **JPEG Fallback**: Tüm tarayıcılar
- **Responsive Images**: Modern tarayıcılar

## Sorun Giderme

### Resim Optimize Edilmiyor
1. Intervention Image paketinin kurulu olduğunu kontrol edin
2. Storage disk'inin yazılabilir olduğunu kontrol edin
3. PHP GD veya Imagick extension'ının aktif olduğunu kontrol edin

### WebP Desteklenmiyor
- JPEG fallback otomatik olarak kullanılır
- Tarayıcı desteği kontrol edilir

### Performans Sorunları
- Resim boyutlarını `config/images.php`'de azaltın
- Kalite ayarlarını düşürün
- Lazy loading'i aktifleştirin

## API Kullanımı

### Resim Optimizasyon Servisi

```php
use App\Services\ImageOptimizationService;

$service = app(ImageOptimizationService::class);

// Dosya yükleme
$result = $service->optimizeAndStore($uploadedFile, 'products');

// URL'den optimize etme
$result = $service->optimizeFromUrl('https://example.com/image.jpg', 'products');

// Optimize edilmiş URL alma
$url = $service->getOptimizedImageUrl($imagePath, 'medium');

// Responsive HTML oluşturma
$html = $service->generateResponsiveImageHtml($imagePath, $alt, $attributes);
```

## Güvenlik

- Sadece güvenilir kaynaklardan resim yükleyin
- Dosya türü kontrolü yapılır
- Maksimum dosya boyutu sınırları uygulanır
- XSS koruması için HTML escape yapılır
