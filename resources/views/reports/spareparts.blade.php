@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Laporan Pemakaian Sparepart</h4>
    </div>
</div>

<div class="card">
    <div class="card-body">

        {{-- Filter Form --}}
        <form action="{{ route('services.report.spareparts') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-3">
                <label for="date" class="form-label">Tanggal</label>
                <input type="date" name="date" id="date" class="form-control"
                       value="{{ request('date') }}">
            </div>
            <div class="col-md-3">
                <label for="month" class="form-label">Bulan</label>
                <input type="month" name="month" id="month" class="form-control"
                       value="{{ request('month') }}">
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
                <a href="{{ route('services.report.spareparts') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        {{-- Tabel Laporan --}}
        <table class="table table-bordered table-striped">
            <thead class="table-primary">
                <tr>
                    <th>No</th>
                    <th style="width: 120px;">Tanggal</th>
                    <th>Nama Sparepart</th>
                    <th style="width: 80px;">Qty</th>
                    <th>Kendaraan</th>
                    <th>Mekanik</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($spareparts as $s)
                    @foreach ($s->spareparts as $sp)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($s->service_date)->format('d/m/Y') }}</td>
                            <td>{{ $sp->barang->nama_barang ?? '-' }}</td>
                            <td>{{ $sp->qty }}</td>
                            <td>{{ $s->vehicle->license_plate ?? '-' }}</td>
                            <td>{{ $s->mechanics->pluck('name')->join(', ') ?: '-' }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>
@endsection
