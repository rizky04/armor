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
                <th class="text-center">No</th>
                <th class="text-center">Tanggal</th>
                <th class="text-center">Nomor Service</th>
                <th class="text-center">Mekanik</th>
                <th class="text-center">Kendaraan</th>
                <th class="text-center">Keluhan</th>
                <th class="text-center">Jasa Service</th>
                <th class="text-center">Harga Jasa</th>
                <th class="text-center">Subtotal</th>
                <th class="text-center">Total Jasa</th>
                <th class="text-center">Sparepart Digunakan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($services as $srv)
                @php
                    $rowspan = max($srv->jobs->count(), 1);
                @endphp
                @if($srv->jobs->count() > 0)
                    @foreach ($srv->jobs as $index => $job)
                        <tr>
                            @if($index == 0)
                                <td rowspan="{{ $rowspan }}">{{ $loop->parent->iteration }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $srv->service_date }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $srv->nomor_service }}</td>
                                <td rowspan="{{ $rowspan }}">
                                    @foreach ($srv->mechanics as $m)
                                        • {{ $m->name }} <br>
                                    @endforeach
                                </td>
                                <td rowspan="{{ $rowspan }}">{{ $srv->vehicle->license_plate ?? '-' }}</td>
                                <td rowspan="{{ $rowspan }}">{{ $srv->complaint }}</td>
                            @endif
                            <td>{{ $job->jasa->nama_jasa ?? '-' }} ({{ $job->qty }}x)</td>
                            <td class="text-end">{{ number_format($job->price ?? $job->jasa->harga_jasa ?? 0, 0, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($job->subtotal ?? ($job->qty * ($job->price ?? $job->jasa->harga_jasa ?? 0)), 0, ',', '.') }}</td>

                            @if($index == 0)
                                <td rowspan="{{ $rowspan }}" class="text-end fw-bold align-middle bg-light">
                                    {{ number_format($srv->total_jasa, 0, ',', '.') }}
                                </td>
                                <td rowspan="{{ $rowspan }}">
                                    @if ($srv->spareparts->count() > 0)
                                        @foreach ($srv->spareparts as $sp)
                                            - {{ $sp->barang->nama_barang ?? '-' }} ({{ $sp->qty }}x)<br>
                                        @endforeach
                                    @else
                                        <em>Tidak ada sparepart</em>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @else
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
                        <td colspan="3" class="text-center"><em>Tidak ada jasa</em></td>
                        <td>0</td>
                        <td>
                            @if ($srv->spareparts->count() > 0)
                                @foreach ($srv->spareparts as $sp)
                                    - {{ $sp->barang->nama_barang ?? '-' }} ({{ $sp->qty }}x)<br>
                                @endforeach
                            @else
                                <em>Tidak ada sparepart</em>
                            @endif
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="11" class="text-center text-muted">
                        Tidak ada data service untuk periode ini.
                    </td>
                </tr>
            @endforelse

            {{-- 🔹 Tambahkan Grand Total di bawah --}}
            @if ($services->count() > 0)
                <tr class="table-secondary fw-bold">
                    <td colspan="9" class="text-end">GRAND TOTAL JASA</td>
                    <td class="text-end">{{ number_format($grandTotal, 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            @endif
        </tbody>
    </table>
</div>


            </div>
        </div>
    </div>
@endsection
