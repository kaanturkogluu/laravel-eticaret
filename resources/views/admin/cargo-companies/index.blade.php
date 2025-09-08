@extends('layouts.admin')

@section('title', 'Kargo Şirketleri')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Kargo Şirketleri</h3>
                    <a href="{{ route('admin.cargo-companies.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Yeni Kargo Şirketi
                    </a>
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
                                    <th>Ad</th>
                                    <th>Kod</th>
                                    <th>Sıralama</th>
                                    <th>Durum</th>
                                    <th>Sipariş Sayısı</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cargoCompanies as $company)
                                    <tr>
                                        <td>{{ $company->id }}</td>
                                        <td>
                                            <strong>{{ $company->name }}</strong>
                                            @if($company->description)
                                                <br><small class="text-muted">{{ $company->description }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <code>{{ $company->code }}</code>
                                        </td>
                                        <td>{{ $company->sort_order }}</td>
                                        <td>
                                            <form action="{{ route('admin.cargo-companies.toggle-status', $company) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm {{ $company->is_active ? 'btn-success' : 'btn-secondary' }}">
                                                    {{ $company->is_active ? 'Aktif' : 'Pasif' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $company->orders()->count() }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.cargo-companies.show', $company) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.cargo-companies.edit', $company) }}" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.cargo-companies.destroy', $company) }}" method="POST" class="d-inline" onsubmit="return confirm('Bu kargo şirketini silmek istediğinizden emin misiniz?')">
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
                                        <td colspan="7" class="text-center">Henüz kargo şirketi eklenmemiş.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
