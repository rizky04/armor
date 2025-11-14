@extends('layouts.main')

@section('content')
<div class="container">
    <h4>Laporan Mekanik Per Bulan</h4>

    <form method="GET" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <label>Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label>Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-4">
                <label>Pilih Mekanik / Tim</label>
                <select name="mechanic_ids[]" class="form-control" multiple>
                    @foreach($mechanics as $m)
                        <option value="{{ $m->id }}" {{ in_array($m->id, $mechanicIds ?? []) ? 'selected' : '' }}>
                            {{ $m->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100">Tampilkan</button>
            </div>
        </div>
    </form>

    @if(count($laporan) > 0)
        @foreach($laporan as $data)
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-dark text-white">
                    <strong>{{ $data['mechanic'] }}</strong>
                </div>
                <div class="card-body">
                    @foreach($data['services'] as $srv)
                        <div class="border-bottom pb-2 mb-3">
                            <h6 class="mb-1">Service: {{ $srv->nomor_service }}</h6>
                            <p class="mb-1">
                                <strong>Tanggal:</strong> {{ $srv->service_date }} |
                                <strong>Kendaraan:</strong> {{ $srv->vehicle->license_plate ?? '-' }} |
                                <strong>Status:</strong> {{ ucfirst($srv->status) }}
                            </p>
                            <p class="mb-2"><strong>Keluhan:</strong> {{ $srv->complaint }}</p>

                            {{-- Daftar Jasa --}}
                            @if($srv->jobs->count() > 0)
                                <div class="mb-2">
                                    <strong>Jasa Service:</strong>
                                    <ul class="mb-1">
                                        @foreach($srv->jobs as $job)
                                            <li>
                                                {{ $job->jasa->nama_jasa ?? '-' }}
                                                ({{ $job->qty }}x)
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- Daftar Sparepart --}}
                            @if($srv->spareparts->count() > 0)
                                <div class="mb-2">
                                    <strong>Sparepart Digunakan:</strong>
                                    <ul class="mb-0">
                                        @foreach($srv->spareparts as $sp)
                                            <li>
                                                {{ $sp->barang->nama_barang ?? '-' }}
                                                ({{ $sp->qty }}x)
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-warning">
            Tidak ada data service untuk periode ini.
        </div>
    @endif
</div>
@endsection
