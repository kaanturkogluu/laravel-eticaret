@extends('layouts.admin')

@section('title', 'XML Import Detayı - Admin Panel')
@section('page-title', 'XML Import Detayı')

@section('content')
<div class="row">
    <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-info-circle me-2"></i>XML Import Detayı
                        </h5>
                        <div>
                            <a href="{{ route('admin.xml-history') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Geri Dön
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Genel Bilgiler -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-file-alt me-2"></i>Dosya Bilgileri</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Dosya Adı:</strong></td>
                                            <td>{{ $xmlHistory->filename }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Dosya Boyutu:</strong></td>
                                            <td>{{ $xmlHistory->formatted_file_size }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Durum:</strong></td>
                                            <td>
                                                <span class="badge {{ $xmlHistory->status_badge_class }}">
                                                    {{ $xmlHistory->status_text }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Başlangıç:</strong></td>
                                            <td>{{ $xmlHistory->started_at ? $xmlHistory->started_at->format('d.m.Y H:i:s') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Bitiş:</strong></td>
                                            <td>{{ $xmlHistory->completed_at ? $xmlHistory->completed_at->format('d.m.Y H:i:s') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>İşlem Süresi:</strong></td>
                                            <td>{{ $xmlHistory->formatted_duration }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>İşlem Sonuçları</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-6 mb-3">
                                            <div class="card bg-success text-white">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ $xmlHistory->imported_count }}</h5>
                                                    <p class="card-text">Yeni Ürün</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="card bg-info text-white">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ $xmlHistory->updated_count }}</h5>
                                                    <p class="card-text">Güncellenen</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="card bg-warning text-dark">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ $xmlHistory->skipped_count }}</h5>
                                                    <p class="card-text">Atlanan</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <div class="card bg-danger text-white">
                                                <div class="card-body">
                                                    <h5 class="card-title">{{ $xmlHistory->error_count }}</h5>
                                                    <p class="card-text">Hata</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <h6>Toplam İşlenen: {{ $xmlHistory->total_processed }}</h6>
                                        <h6>Başarı Oranı: %{{ $xmlHistory->success_rate }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hata Detayları -->
                    @if($xmlHistory->error_message)
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Hata Detayları</h6>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-danger">
                                        <h6><strong>Hata Mesajı:</strong></h6>
                                        <p class="mb-0">{{ $xmlHistory->error_message }}</p>
                                    </div>
                                    
                                    @if($xmlHistory->error_count > 0)
                                    <div class="alert alert-warning">
                                        <h6><strong>Hata Analizi:</strong></h6>
                                        <ul class="mb-0">
                                            <li><strong>Toplam Hata Sayısı:</strong> {{ $xmlHistory->error_count }}</li>
                                            <li><strong>Hata Oranı:</strong> %{{ round(($xmlHistory->error_count / $xmlHistory->total_processed) * 100, 1) }}</li>
                                            <li><strong>Başarılı İşlem:</strong> {{ $xmlHistory->imported_count + $xmlHistory->updated_count }}</li>
                                        </ul>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- İşlem Geçmişi -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>İşlem Geçmişi</h6>
                                </div>
                                <div class="card-body">
                                    <div class="timeline">
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-primary"></div>
                                            <div class="timeline-content">
                                                <h6>İşlem Başlatıldı</h6>
                                                <p class="text-muted mb-0">
                                                    {{ $xmlHistory->started_at ? $xmlHistory->started_at->format('d.m.Y H:i:s') : 'Bilinmiyor' }}
                                                </p>
                                            </div>
                                        </div>
                                        
                                        @if($xmlHistory->status === 'completed')
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-success"></div>
                                            <div class="timeline-content">
                                                <h6>İşlem Tamamlandı</h6>
                                                <p class="text-muted mb-0">
                                                    {{ $xmlHistory->completed_at ? $xmlHistory->completed_at->format('d.m.Y H:i:s') : 'Bilinmiyor' }}
                                                </p>
                                            </div>
                                        </div>
                                        @elseif($xmlHistory->status === 'failed')
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-danger"></div>
                                            <div class="timeline-content">
                                                <h6>İşlem Başarısız</h6>
                                                <p class="text-muted mb-0">
                                                    {{ $xmlHistory->completed_at ? $xmlHistory->completed_at->format('d.m.Y H:i:s') : 'Bilinmiyor' }}
                                                </p>
                                            </div>
                                        </div>
                                        @elseif($xmlHistory->status === 'processing')
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-warning"></div>
                                            <div class="timeline-content">
                                                <h6>İşlem Devam Ediyor</h6>
                                                <p class="text-muted mb-0">İşlem henüz tamamlanmadı</p>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- İşlemler -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>İşlemler</h6>
                                </div>
                                <div class="card-body">
                                    <div class="btn-group">
                                        @if($xmlHistory->file_path)
                                        <a href="{{ route('admin.xml-history.download', $xmlHistory) }}" class="btn btn-success">
                                            <i class="fas fa-download me-2"></i>XML Dosyasını İndir
                                        </a>
                                        @endif
                                        
                                        <button type="button" class="btn btn-danger" onclick="deleteHistory({{ $xmlHistory->id }})">
                                            <i class="fas fa-trash me-2"></i>Geçmişi Sil
                                        </button>
                                        
                                        <a href="{{ route('admin.xml-history') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>Listeye Dön
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
@endsection

@section('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #dee2e6;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 3px solid #007bff;
}

.timeline-content h6 {
    margin-bottom: 5px;
    color: #495057;
}
</style>
@endsection

@section('scripts')
<script>
function deleteHistory(historyId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/admin/xml-history/${historyId}`;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endsection

