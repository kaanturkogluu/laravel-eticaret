@extends('layouts.admin')

@section('title', 'Kampanya Yönetimi')
@section('page-title', 'Kampanya Yönetimi')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-bullhorn me-2"></i>Kampanyalar
                </h5>
                <a href="{{ route('admin.campaigns.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Yeni Kampanya
                </a>
            </div>
            <div class="card-body">
                @if($campaigns->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Resim</th>
                                    <th>Başlık</th>
                                    <th>Tip</th>
                                    <th>Durum</th>
                                    <th>Başlangıç</th>
                                    <th>Bitiş</th>
                                    <th>Sıra</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($campaigns as $campaign)
                                    <tr>
                                        <td>
                                            @if($campaign->image_url)
                                                <img src="{{ $campaign->image_url }}" alt="{{ $campaign->title }}" 
                                                     class="img-thumbnail" style="width: 60px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 60px; height: 40px;">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $campaign->title }}</strong>
                                                @if($campaign->description)
                                                    <br><small class="text-muted">{{ Str::limit($campaign->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge 
                                                @if($campaign->type === 'banner') bg-info
                                                @elseif($campaign->type === 'campaign') bg-primary
                                                @else bg-warning
                                                @endif">
                                                @if($campaign->type === 'banner') Banner
                                                @elseif($campaign->type === 'campaign') Kampanya
                                                @else Promosyon
                                                @endif
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $campaign->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $campaign->is_active ? 'Aktif' : 'Pasif' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($campaign->start_date)
                                                {{ $campaign->start_date->format('d.m.Y H:i') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($campaign->end_date)
                                                {{ $campaign->end_date->format('d.m.Y H:i') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $campaign->sort_order }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.campaigns.edit', $campaign) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Düzenle">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <form action="{{ route('admin.campaigns.toggle-status', $campaign) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-{{ $campaign->is_active ? 'warning' : 'success' }}" 
                                                            title="{{ $campaign->is_active ? 'Pasif Yap' : 'Aktif Yap' }}">
                                                        <i class="fas fa-{{ $campaign->is_active ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                </form>
                                                
                                                <form action="{{ route('admin.campaigns.destroy', $campaign) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Bu kampanyayı silmek istediğinizden emin misiniz?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Sil">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="d-flex justify-content-center">
                        {{ $campaigns->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Henüz kampanya bulunmuyor</h5>
                        <p class="text-muted">İlk kampanyanızı oluşturmak için yukarıdaki butona tıklayın.</p>
                        <a href="{{ route('admin.campaigns.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Kampanya Oluştur
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Kampanya durumu değiştirme
    $('form[action*="toggle-status"]').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var button = form.find('button[type="submit"]');
        var originalHtml = button.html();
        
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                location.reload();
            },
            error: function() {
                button.prop('disabled', false).html(originalHtml);
                alert('İşlem sırasında hata oluştu.');
            }
        });
    });
</script>
@endsection
