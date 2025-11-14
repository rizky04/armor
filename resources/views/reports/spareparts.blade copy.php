@extends('layouts.main')

@section('content')
    <div class="page-header">
        <div class="page-title">
            <h4>Laporan Sparepart</h4>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Sparepart</th>
                        <th>Qty</th>
                        <th>Kendaraan</th>
                        <th>Mekanik</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($spareparts as $s)
                        @foreach ($s->spareparts as $sp)
                            <tr>
                                <td>{{ $s->service_date }}</td>
                                <td>{{ $sp->barang->nama_barang }}</td>
                                <td>{{ $sp->qty }}</td>
                                <td>{{ $s->vehicle->license_plate }}</td>
                                <td>{{ $s->mechanics->pluck('name')->join(', ') }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
