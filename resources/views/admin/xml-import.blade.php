@extends('layouts.admin')

@section('title', 'XML Import - Admin Panel')
@section('page-title', 'XML İçe Aktar')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-upload me-2"></i>XML Import Yönetimi
                </h5>
            </div>
            <div class="card-body">
                <!-- Liste.xml Import -->
                <div class="mb-4">
                    <h5>
                        <i class="fas fa-file-code me-2"></i>Liste.xml Import
                    </h5>
                    <p class="text-muted">
                        Proje kök dizinindeki liste.xml dosyasını import eder.
                    </p>
                    
                    <form method="POST" action="{{ route('admin.xml-import.liste') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-primary" 
                                onclick="return confirm('Liste.xml dosyası import edilecek. Devam etmek istiyor musunuz?')">
                            <i class="fas fa-play me-2"></i>Liste.xml Import Et
                        </button>
                    </form>
                </div>

                <hr>

                <!-- XML Dosya Upload -->
                <div class="mb-4">
                    <h5>
                        <i class="fas fa-cloud-upload-alt me-2"></i>XML Dosya Upload
                    </h5>
                    <p class="text-muted">
                        Yeni bir XML dosyası yükleyerek import edebilirsiniz.
                    </p>
                    
                    <form method="POST" action="{{ route('admin.xml-import.upload') }}" 
                          enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="xml_file" class="form-label">XML Dosyası Seçin</label>
                            <input type="file" class="form-control" id="xml_file" name="xml_file" 
                                   accept=".xml" required>
                            <div class="form-text">
                                Maksimum dosya boyutu: 10MB. Sadece XML dosyaları kabul edilir.
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-upload me-2"></i>XML Dosyasını Import Et
                        </button>
                    </form>
                </div>

                <hr>

                <!-- Stok Kontrolü -->
                <div class="mb-4">
                    <h5>
                        <i class="fas fa-boxes me-2"></i>Stok Kontrolü
                    </h5>
                    <p class="text-muted">
                        Stok adedi 2'den az olan ürünleri pasif yapar, 2 ve üzeri olanları aktif yapar.
                    </p>
                    
                    <form method="POST" action="{{ route('admin.stock-control') }}" class="d-inline">
                        @csrf
                        <div class="mb-3">
                            <label for="min_stock" class="form-label">Minimum Stok Miktarı</label>
                            <input type="number" id="min_stock" name="min_stock" value="2" min="0" 
                                   class="form-control" placeholder="Min. Stok" required>
                            <div class="form-text">Bu miktarın altındaki ürünler stokta az olarak işaretlenir.</div>
                        </div>
                        <button type="submit" class="btn btn-warning" 
                                onclick="return confirm('Stok kontrolü yapılacak. Devam etmek istiyor musunuz?')">
                            <i class="fas fa-check-circle me-2"></i>Stok Kontrolü Yap
                        </button>
                    </form>
                </div>

                <hr>

                <!-- İstatistikler -->
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h3 class="text-primary">{{ \App\Models\Product::count() }}</h3>
                                <p class="mb-0">Toplam Ürün</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h3 class="text-success">{{ \App\Models\Product::active()->inStock()->count() }}</h3>
                                <p class="mb-0">Aktif Ürün</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h3 class="text-warning">{{ \App\Models\Product::where('miktar', '<', 2)->count() }}</h3>
                                <p class="mb-0">Düşük Stok</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Son Güncelleme -->
                <div class="mt-4">
                    <h6>Son Güncelleme Bilgileri</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Ürün Kodu</th>
                                    <th>Ürün Adı</th>
                                    <th>Stok</th>
                                    <th>Son Güncelleme</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\Product::orderBy('last_updated', 'desc')->limit(5)->get() as $product)
                                <tr>
                                    <td>{{ $product->kod }}</td>
                                    <td>{{ Str::limit($product->ad, 30) }}</td>
                                    <td>
                                        <span class="badge {{ $product->miktar >= 2 ? 'bg-success' : 'bg-warning' }}">
                                            {{ $product->miktar }}
                                        </span>
                                    </td>
                                    <td>{{ $product->last_updated ? $product->last_updated->format('d.m.Y H:i') : '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- XML Format Bilgisi -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>XML Format Bilgisi
                </h5>
            </div>
            <div class="card-body">
                <p>XML dosyası aşağıdaki formatta olmalıdır:</p>
                <pre class="bg-light p-3 rounded"><code>&lt;ArrayOfXMLUrunView&gt;
    &lt;XMLUrunView&gt;
        &lt;Kod&gt;12345&lt;/Kod&gt;
        &lt;Ad&gt;Ürün Adı&lt;/Ad&gt;
        &lt;Miktar&gt;10&lt;/Miktar&gt;
        &lt;Fiyat_SK&gt;100.00&lt;/Fiyat_SK&gt;
        &lt;Fiyat_Bayi&gt;80.00&lt;/Fiyat_Bayi&gt;
        &lt;Fiyat_Ozel&gt;70.00&lt;/Fiyat_Ozel&gt;
        &lt;Doviz&gt;USD&lt;/Doviz&gt;
        &lt;Marka&gt;Marka Adı&lt;/Marka&gt;
        &lt;Kategori&gt;Kategori Adı&lt;/Kategori&gt;
        &lt;AnaResim&gt;https://example.com/image.jpg&lt;/AnaResim&gt;
        &lt;Barkod&gt;123456789&lt;/Barkod&gt;
        &lt;Aciklama&gt;Ürün açıklaması&lt;/Aciklama&gt;
        &lt;Detay&gt;Detaylı açıklama&lt;/Detay&gt;
        &lt;Desi&gt;1.5&lt;/Desi&gt;
        &lt;Kdv&gt;20&lt;/Kdv&gt;
        &lt;urunResimleri&gt;
            &lt;UrunResimler&gt;
                &lt;UrunKodu&gt;12345&lt;/UrunKodu&gt;
                &lt;Resim&gt;https://example.com/image1.jpg&lt;/Resim&gt;
            &lt;/UrunResimler&gt;
        &lt;/urunResimleri&gt;
        &lt;TeknikOzellikler&gt;
            &lt;UrunTeknikOzellikler&gt;
                &lt;UrunKodu&gt;12345&lt;/UrunKodu&gt;
                &lt;Ozellik&gt;Özellik Adı&lt;/Ozellik&gt;
                &lt;Deger&gt;Özellik Değeri&lt;/Deger&gt;
            &lt;/UrunTeknikOzellikler&gt;
        &lt;/TeknikOzellikler&gt;
    &lt;/XMLUrunView&gt;
&lt;/ArrayOfXMLUrunView&gt;</code></pre>
            </div>
        </div>
    </div>
</div>
@endsection
