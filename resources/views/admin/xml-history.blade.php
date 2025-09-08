@extends('layouts.admin')

@section('title', 'Güneş Bilgisayar XML Geçmişi - Admin Panel')
@section('page-title', 'XML Geçmişi')

@section('content')
<div class="row">
    <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>Güneş Bilgisayar XML Geçmişi
                        </h5>
                        <div>
                            <button type="button" class="btn btn-warning me-2" onclick="showErrorAnalysis()">
                                <i class="fas fa-exclamation-triangle me-2"></i>Hata Analizi
                            </button>
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#manualImportModal">
                                <i class="fas fa-play me-2"></i>Manuel Import
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- İstatistikler -->
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $stats['total'] }}</h5>
                                    <p class="card-text">Toplam İşlem</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $stats['completed'] }}</h5>
                                    <p class="card-text">Başarılı</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $stats['failed'] }}</h5>
                                    <p class="card-text">Başarısız</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $stats['today'] }}</h5>
                                    <p class="card-text">Bugün</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-secondary text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">{{ $stats['last_7_days'] }}</h5>
                                    <p class="card-text">Son 7 Gün</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card bg-dark text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">30dk</h5>
                                    <p class="card-text">Otomatik</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hata Analizi -->
                    @php
                        $failedHistories = $histories->where('status', 'failed');
                        $totalErrors = $histories->sum('error_count');
                        $totalProcessed = $histories->sum('total_processed');
                        $errorRate = $totalProcessed > 0 ? round(($totalErrors / $totalProcessed) * 100, 1) : 0;
                    @endphp
                    
                    @if($failedHistories->count() > 0 || $totalErrors > 0)
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Hata Analizi</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-danger">{{ $failedHistories->count() }}</h4>
                                                <p class="text-muted">Başarısız İşlem</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-danger">{{ $totalErrors }}</h4>
                                                <p class="text-muted">Toplam Hata</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-warning">%{{ $errorRate }}</h4>
                                                <p class="text-muted">Hata Oranı</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-info">{{ $totalProcessed }}</h4>
                                                <p class="text-muted">Toplam İşlenen</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($failedHistories->count() > 0)
                                    <div class="mt-3">
                                        <h6>Son Başarısız İşlemler:</h6>
                                        <div class="list-group">
                                            @foreach($failedHistories->take(3) as $failed)
                                            <div class="list-group-item">
                                                <div class="d-flex w-100 justify-content-between">
                                                    <h6 class="mb-1">{{ $failed->filename }}</h6>
                                                    <small>{{ $failed->created_at->format('d.m.Y H:i') }}</small>
                                                </div>
                                                <p class="mb-1 text-danger">
                                                    <i class="fas fa-exclamation-circle me-1"></i>
                                                    {{ Str::limit($failed->error_message, 100) }}
                                                </p>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Filtreler -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <form method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <select name="status" class="form-control">
                                        <option value="">Tüm Durumlar</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Tamamlandı</option>
                                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Başarısız</option>
                                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>İşleniyor</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Bekliyor</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="Başlangıç Tarihi">
                                </div>
                                <div class="col-md-3">
                                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="Bitiş Tarihi">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-filter me-2"></i>Filtrele
                                    </button>
                                    <a href="{{ route('admin.xml-history') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Temizle
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tablo -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Dosya Adı</th>
                                    <th>Durum</th>
                                    <th>İşlem Sonuçları</th>
                                    <th>Dosya Boyutu</th>
                                    <th>İşlem Süresi</th>
                                    <th>Başlangıç</th>
                                    <th>Bitiş</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($histories as $history)
                                <tr>
                                    <td>{{ $history->id }}</td>
                                    <td>
                                        <small class="text-muted">{{ $history->filename }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ $history->status_badge_class }}">
                                            {{ $history->status_text }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>
                                            <strong>Yeni:</strong> {{ $history->imported_count }}<br>
                                            <strong>Güncellenen:</strong> {{ $history->updated_count }}<br>
                                            <strong>Atlanan:</strong> {{ $history->skipped_count }}<br>
                                            <strong>Hata:</strong> 
                                            @if($history->error_count > 0)
                                                <span class="text-danger">{{ $history->error_count }}</span>
                                            @else
                                                {{ $history->error_count }}
                                            @endif
                                            <br>
                                            <strong>Toplam:</strong> {{ $history->total_processed }}
                                            @if($history->error_message)
                                                <br><span class="text-danger" title="{{ $history->error_message }}">
                                                    <i class="fas fa-exclamation-triangle"></i> Hata Detayı
                                                </span>
                                            @endif
                                        </small>
                                    </td>
                                    <td>{{ $history->formatted_file_size }}</td>
                                    <td>{{ $history->formatted_duration }}</td>
                                    <td>
                                        <small>{{ $history->started_at ? $history->started_at->format('d.m.Y H:i') : '-' }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $history->completed_at ? $history->completed_at->format('d.m.Y H:i') : '-' }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.xml-history.show', $history) }}" class="btn btn-info btn-sm" title="Detay">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($history->file_path)
                                            <a href="{{ route('admin.xml-history.download', $history) }}" class="btn btn-success btn-sm" title="İndir">
                                                <i class="fas fa-download"></i>
                                            </a>
                                            @endif
                                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteHistory({{ $history->id }})" title="Sil">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <div class="py-4">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Henüz XML import geçmişi bulunmuyor.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $histories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Manuel Import Modal -->
<div class="modal fade" id="manualImportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-play me-2"></i>Manuel XML Import</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.xml-history.manual-import') }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Güneş Bilgisayar API'sinden XML dosyası indirilip işlenecektir.
                    </div>
                    <p>Bu işlem biraz zaman alabilir. Devam etmek istiyor musunuz?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-play me-2"></i>Import Başlat
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Silme Onay Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash me-2"></i>XML Geçmişi Sil</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bu XML import geçmişini silmek istediğinizden emin misiniz?</p>
                <p class="text-muted">Bu işlem geri alınamaz ve ilgili XML dosyası da silinecektir.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Sil
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Hata Analizi Modal -->
<div class="modal fade" id="errorAnalysisModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Hata Analizi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="errorAnalysisContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Yükleniyor...</span>
                        </div>
                        <p class="mt-2">Hata analizi yükleniyor...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function deleteHistory(historyId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/admin/xml-history/${historyId}`;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function showErrorAnalysis() {
    const modal = new bootstrap.Modal(document.getElementById('errorAnalysisModal'));
    modal.show();
    
    // Hata analizi verilerini yükle
    fetch('{{ route("admin.xml-history.error-analysis") }}')
        .then(response => response.json())
        .then(data => {
            displayErrorAnalysis(data);
        })
        .catch(error => {
            console.error('Hata analizi yüklenirken hata:', error);
            document.getElementById('errorAnalysisContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    Hata analizi yüklenirken bir hata oluştu.
                </div>
            `;
        });
}

function displayErrorAnalysis(data) {
    let html = `
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h4>${data.total_failed}</h4>
                        <p class="mb-0">Başarısız İşlem</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <h4>${data.total_errors}</h4>
                        <p class="mb-0">Toplam Hata</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h4>%${data.error_rate}</h4>
                        <p class="mb-0">Hata Oranı</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-secondary text-white">
                    <div class="card-body text-center">
                        <h4>${Object.keys(data.error_types).length}</h4>
                        <p class="mb-0">Hata Türü</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Hata türleri
    if (Object.keys(data.error_types).length > 0) {
        html += `
            <div class="row mb-4">
                <div class="col-12">
                    <h6><i class="fas fa-chart-pie me-2"></i>Hata Türleri</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Hata Türü</th>
                                    <th>Sayı</th>
                                    <th>Yüzde</th>
                                </tr>
                            </thead>
                            <tbody>
        `;
        
        const totalErrors = Object.values(data.error_types).reduce((a, b) => a + b, 0);
        Object.entries(data.error_types).forEach(([type, count]) => {
            const percentage = totalErrors > 0 ? ((count / totalErrors) * 100).toFixed(1) : 0;
            html += `
                <tr>
                    <td>${type}</td>
                    <td>${count}</td>
                    <td>%${percentage}</td>
                </tr>
            `;
        });
        
        html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Son başarısız işlemler
    if (data.recent_failures.length > 0) {
        html += `
            <div class="row mb-4">
                <div class="col-12">
                    <h6><i class="fas fa-history me-2"></i>Son Başarısız İşlemler</h6>
                    <div class="list-group">
        `;
        
        data.recent_failures.forEach(failure => {
            html += `
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">${failure.filename}</h6>
                        <small>${failure.created_at}</small>
                    </div>
                    <p class="mb-1 text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        ${failure.error_message}
                    </p>
                    <small class="text-muted">Hata sayısı: ${failure.error_count}</small>
                </div>
            `;
        });
        
        html += `
                    </div>
                </div>
            </div>
        `;
    }
    
    // Günlük hata grafiği
    if (data.daily_errors.length > 0) {
        html += `
            <div class="row">
                <div class="col-12">
                    <h6><i class="fas fa-chart-line me-2"></i>Son 7 Günün Hata Trendi</h6>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Tarih</th>
                                    <th>Hata Sayısı</th>
                                    <th>Başarısız Import</th>
                                </tr>
                            </thead>
                            <tbody>
        `;
        
        data.daily_errors.forEach(day => {
            html += `
                <tr>
                    <td>${day.date}</td>
                    <td>${day.errors}</td>
                    <td>${day.failed_imports}</td>
                </tr>
            `;
        });
        
        html += `
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        `;
    }
    
    document.getElementById('errorAnalysisContent').innerHTML = html;
}

// Sayfa yüklendiğinde otomatik yenileme (5 dakikada bir)
setInterval(function() {
    // Sadece işleniyor durumunda olan kayıtlar varsa sayfayı yenile
    const processingRows = document.querySelectorAll('tr:has(.bg-warning)');
    if (processingRows.length > 0) {
        location.reload();
    }
}, 300000); // 5 dakika
</script>
@endsection
