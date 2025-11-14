@extends('layouts.main')

@section('content')
<div class="container">
    <h4>Pencatatan Ganti Oli untuk {{ $service->vehicle->nopol ?? 'Tanpa Nopol' }}</h4>
    <form action="{{ route('oil_services.store') }}" method="POST">
        @csrf
        <input type="hidden" name="service_id" value="{{ $service->id }}">

        <div class="mb-3">
            <label>Oli yang digunakan</label>
            <select name="oil_name" class="form-control">
                @foreach($oilParts as $oil)
                    <option value="{{ $oil->barang->nama_barang }}">{{ $oil->barang->nama_barang }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>KM Service</label>
            <input type="number" name="km_service" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>KM Service Berikutnya</label>
            <input type="number" name="km_service_next" class="form-control">
        </div>

        <div class="mb-3">
            <label>Tanggal Service Berikutnya</label>
            <input type="date" name="next_service_date" class="form-control">
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
    </form>
</div>
@endsection
