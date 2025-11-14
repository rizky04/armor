@extends('layouts.main')

@section('content')
<div class="container">
    <h4>Edit Data Ganti Oli</h4>
    <form action="{{ route('oil_services.update', $oilService->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Tanggal Service</label>
            <input type="text" class="form-control" value="{{ $oilService->service_date }}" readonly>
        </div>

        <div class="mb-3">
            <label>Kendaraan</label>
            <input type="text" class="form-control" value="{{ $oilService->service->vehicle->nopol ?? '-' }}" readonly>
        </div>

        <div class="mb-3">
            <label>Oli Digunakan</label>
            <input type="text" name="oil_name" class="form-control" value="{{ old('oil_name', $oilService->oil_name) }}" required>
        </div>

        <div class="mb-3">
            <label>KM Service</label>
            <input type="number" name="km_service" class="form-control" value="{{ old('km_service', $oilService->km_service) }}" required>
        </div>

        <div class="mb-3">
            <label>KM Service Berikutnya</label>
            <input type="number" name="km_service_next" class="form-control" value="{{ old('km_service_next', $oilService->km_service_next) }}">
        </div>

        <div class="mb-3">
            <label>Tanggal Service Berikutnya</label>
            <input type="date" name="next_service_date" class="form-control" value="{{ old('next_service_date', $oilService->next_service_date) }}">
        </div>

        <button type="submit" class="btn btn-success">Update</button>
        <a href="{{ route('oil_services.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection
