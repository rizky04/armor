@extends('layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="page-header">
            <div class="page-title">
                <h4>Laporan Mekanik perbulan</h4>
            </div>
        </div>



        {{-- Table --}}
        <div class="card">
            <div class="card-body">
                {{-- Filter Form --}}
                <form method="GET" class="mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Dari Tanggal</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                        </div>
                        <div class="col-md-6">
                            <label>Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        </div>
                        <div class="col-md-12">
                            <label>Pilih Mekanik / Tim</label>
                            <select name="mechanic_ids[]" class="form-control" multiple>
                                @foreach ($mechanics as $m)
                                    <option value="{{ $m->id }}"
                                        {{ in_array($m->id, $mechanicIds ?? []) ? 'selected' : '' }}>
                                        {{ $m->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end mt-3">
                            <button class="btn btn-primary w-100">Tampilkan</button>
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th class="text-center">no</th>
                                <th class="text-center">Tanggal</th>
                                <th class="text-center">Nomor Service</th>
                                <th class="text-center">Mekanik</th>
                                <th class="text-center">Kendaraan</th>
                                <th class="text-center">Keluhan</th>
                                <th class="text-center">Jasa Service</th>
                                <th class="text-center">Sparepart Digunakan</th>
                                {{-- <th class="text-center">Status</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($services as $srv)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $srv->service_date }}</td>
                                    <td>{{ $srv->nomor_service }}</td>
                                    <td>
                                        @foreach ($srv->mechanics as $m)
                                            • {{ $m->name }} <br>
                                        @endforeach
                                    </td>
                                    <td>{{ $srv->vehicle->license_plate ?? '-' }}</td>
                                    <td>{{ $srv->complaint }}</td>
                                    <td>
                                        @if ($srv->jobs->count() > 0)
                                            @foreach ($srv->jobs as $job)
                                                - {{ $job->jasa->nama_jasa ?? '-' }} ({{ $job->qty }}x)<br>
                                            @endforeach
                                        @else
                                            <em>Tidak ada jasa</em>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($srv->spareparts->count() > 0)
                                            @foreach ($srv->spareparts as $sp)
                                                - {{ $sp->barang->nama_barang ?? '-' }} ({{ $sp->qty }}x)<br>
                                            @endforeach
                                        @else
                                            <em>Tidak ada sparepart</em>
                                        @endif
                                    </td>
                                    {{-- <td class="text-center">{{ ucfirst($srv->status) }}</td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        Tidak ada data service untuk periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
