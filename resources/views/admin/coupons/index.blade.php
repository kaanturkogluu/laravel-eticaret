@extends('layouts.admin')

@section('title', 'Kupon Yönetimi - Basital.com')
@section('page-title', 'Kupon Yönetimi')

@section('content')

<!-- Filtreler ve Arama -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtreler</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.coupons.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Arama</label>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                               placeholder="Kupon kodu, adı veya açıklaması...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Durum</label>
                        <select class="form-control" name="status">
                            <option value="">Tümü</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Pasif</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Süresi Dolmuş</option>
                            <option value="limit_reached" {{ request('status') == 'limit_reached' ? 'selected' : '' }}>Limit Dolmuş</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i>Filtrele
                        </button>
                        <a href="{{ route('admin.coupons.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>Temizle
                        </a>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <a href="{{ route('admin.coupons.create') }}" class="btn btn-success w-100">
                            <i class="fas fa-plus me-1"></i>Yeni Kupon
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Kupon Listesi -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-ticket-alt me-2"></i>Kuponlar
                    <span class="badge bg-primary ms-2">{{ $coupons->total() }}</span>
                </h5>
            </div>
            <div class="card-body">
                @if($coupons->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Kupon Kodu</th>
                                    <th>Ad</th>
                                    <th>Tip</th>
                                    <th>Değer</th>
                                    <th>Kullanım</th>
                                    <th>Durum</th>
                                    <th>Geçerlilik</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($coupons as $coupon)
                                <tr>
                                    <td>
                                        <code class="bg-light px-2 py-1 rounded">{{ $coupon->code }}</code>
                                    </td>
                                    <td>
                                        <strong>{{ $coupon->name }}</strong>
                                        @if($coupon->description)
                                            <br><small class="text-muted">{{ Str::limit($coupon->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $coupon->type === 'percentage' ? 'bg-info' : 'bg-warning' }}">
                                            {{ $coupon->type_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $coupon->formatted_value }}</strong>
                                        @if($coupon->minimum_amount)
                                            <br><small class="text-muted">Min: {{ number_format($coupon->minimum_amount, 2) }} TL</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span>{{ $coupon->used_count }} / {{ $coupon->usage_limit ?: '∞' }}</span>
                                            @if($coupon->usage_limit_per_user)
                                                <small class="text-muted">Kullanıcı başına: {{ $coupon->usage_limit_per_user }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $coupon->status_color }}">
                                            {{ $coupon->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            @if($coupon->starts_at)
                                                <small>Başlangıç: {{ $coupon->starts_at->format('d.m.Y') }}</small>
                                            @endif
                                            @if($coupon->expires_at)
                                                <small>Bitiş: {{ $coupon->expires_at->format('d.m.Y') }}</small>
                                            @endif
                                            @if(!$coupon->starts_at && !$coupon->expires_at)
                                                <small class="text-muted">Süresiz</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.coupons.edit', $coupon) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Düzenle">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.coupons.usage-history', $coupon) }}" 
                                               class="btn btn-sm btn-outline-info" title="Kullanım Geçmişi">
                                                <i class="fas fa-history"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.coupons.toggle-status', $coupon) }}" 
                                                  class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-{{ $coupon->is_active ? 'warning' : 'success' }}" 
                                                        title="{{ $coupon->is_active ? 'Pasifleştir' : 'Aktifleştir' }}"
                                                        onclick="return confirm('Kupon durumunu değiştirmek istediğinizden emin misiniz?')">
                                                    <i class="fas fa-{{ $coupon->is_active ? 'pause' : 'play' }}"></i>
                                                </button>
                                            </form>
                                            @if($coupon->used_count == 0)
                                                <form method="POST" action="{{ route('admin.coupons.destroy', $coupon) }}" 
                                                      class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                            title="Sil"
                                                            onclick="return confirm('Bu kuponu silmek istediğinizden emin misiniz?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $coupons->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Henüz kupon bulunmuyor</h5>
                        <p class="text-muted">İlk kuponunuzu oluşturmak için yukarıdaki "Yeni Kupon" butonunu kullanın.</p>
                        <a href="{{ route('admin.coupons.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Yeni Kupon Oluştur
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
$(document).ready(function() {
    // Durum değiştirme işlemi
    $('form[action*="toggle-status"]').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $button = $form.find('button[type="submit"]');
        var originalText = $button.html();
        
        $button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            success: function(response) {
                showAlert('success', 'Kupon durumu başarıyla güncellendi.');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            },
            error: function() {
                showAlert('danger', 'Kupon durumu güncellenirken bir hata oluştu.');
                $button.prop('disabled', false).html(originalText);
            }
        });
    });
});
</script>
@endsection
