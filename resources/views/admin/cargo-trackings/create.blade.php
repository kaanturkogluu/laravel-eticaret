@extends('layouts.admin')

@section('title', 'Yeni Kargo Takip Kaydı')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Yeni Kargo Takip Kaydı Ekle</h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.cargo-trackings.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="order_id" class="form-label">Sipariş <span class="text-danger">*</span></label>
                                    <select class="form-select @error('order_id') is-invalid @enderror" id="order_id" name="order_id" required>
                                        <option value="">Sipariş seçiniz...</option>
                                        @foreach($orders as $order)
                                            <option value="{{ $order->id }}" {{ old('order_id') == $order->id ? 'selected' : '' }}>
                                                {{ $order->order_number }} - {{ $order->customer_name }} ({{ $order->user->name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('order_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="cargo_company_id" class="form-label">Kargo Şirketi <span class="text-danger">*</span></label>
                                    <select class="form-select @error('cargo_company_id') is-invalid @enderror" id="cargo_company_id" name="cargo_company_id" required>
                                        <option value="">Kargo şirketi seçiniz...</option>
                                        @foreach($cargoCompanies as $company)
                                            <option value="{{ $company->id }}" {{ old('cargo_company_id') == $company->id ? 'selected' : '' }}>
                                                {{ $company->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('cargo_company_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="tracking_number" class="form-label">Takip Numarası <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('tracking_number') is-invalid @enderror" 
                                           id="tracking_number" name="tracking_number" value="{{ old('tracking_number') }}" required>
                                    @error('tracking_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="status" class="form-label">Durum <span class="text-danger">*</span></label>
                                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="">Durum seçiniz...</option>
                                        <option value="created" {{ old('status') == 'created' ? 'selected' : '' }}>Kargo Oluşturuldu</option>
                                        <option value="picked_up" {{ old('status') == 'picked_up' ? 'selected' : '' }}>Kargo Alındı</option>
                                        <option value="in_transit" {{ old('status') == 'in_transit' ? 'selected' : '' }}>Yolda</option>
                                        <option value="out_for_delivery" {{ old('status') == 'out_for_delivery' ? 'selected' : '' }}>Dağıtımda</option>
                                        <option value="delivered" {{ old('status') == 'delivered' ? 'selected' : '' }}>Teslim Edildi</option>
                                        <option value="exception" {{ old('status') == 'exception' ? 'selected' : '' }}>Sorun Var</option>
                                        <option value="returned" {{ old('status') == 'returned' ? 'selected' : '' }}>İade Edildi</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="location" class="form-label">Konum</label>
                                    <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                           id="location" name="location" value="{{ old('location') }}">
                                    @error('location')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="event_date" class="form-label">Olay Tarihi <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('event_date') is-invalid @enderror" 
                                           id="event_date" name="event_date" value="{{ old('event_date', now()->format('Y-m-d\TH:i')) }}" required>
                                    @error('event_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Açıklama</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.cargo-trackings.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Geri
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
