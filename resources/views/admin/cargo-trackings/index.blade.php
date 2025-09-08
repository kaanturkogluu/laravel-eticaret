@extends('layouts.admin')

@section('title', 'Kargo Takip')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Kargo Takip Kayıtları</h3>
                    <div class="d-flex gap-2">
                        <form action="{{ route('admin.cargo-trackings.search') }}" method="GET" class="d-flex">
                            <input type="text" name="tracking_number" class="form-control me-2" 
                                   placeholder="Takip numarası ara..." value="{{ $trackingNumber ?? '' }}">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                        <a href="{{ route('admin.cargo-trackings.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Yeni Takip Kaydı
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Sipariş No</th>
                                    <th>Kargo Şirketi</th>
                                    <th>Takip No</th>
                                    <th>Durum</th>
                                    <th>Konum</th>
                                    <th>Tarih</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cargoTrackings as $tracking)
                                    <tr>
                                        <td>{{ $tracking->id }}</td>
                                        <td>
                                            <a href="{{ route('admin.cargo-trackings.order', $tracking->order_id) }}" class="text-decoration-none">
                                                {{ $tracking->order->order_number }}
                                            </a>
                                        </td>
                                        <td>{{ $tracking->cargoCompany->name }}</td>
                                        <td>
                                            <code>{{ $tracking->tracking_number }}</code>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $tracking->status_color }}">
                                                {{ $tracking->status_label }}
                                            </span>
                                        </td>
                                        <td>{{ $tracking->location ?? '-' }}</td>
                                        <td>{{ $tracking->formatted_event_date }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.cargo-trackings.show', $tracking) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.cargo-trackings.edit', $tracking) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.cargo-trackings.destroy', $tracking) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu takip kaydını silmek istediğinizden emin misiniz?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            @if(isset($trackingNumber))
                                                "{{ $trackingNumber }}" takip numarası bulunamadı.
                                            @else
                                                Henüz kargo takip kaydı bulunmuyor.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($cargoTrackings->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $cargoTrackings->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
