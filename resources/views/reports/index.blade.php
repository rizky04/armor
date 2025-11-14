@extends('layouts.main')

@section('content')
    {{-- <div class="content"> --}}
        <div class="page-header">
            <div class="page-title">
                <h4>Laporan Service & Barang Keluar</h4>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <!-- Filter Form -->
                <form method="GET" class="row mb-4">
                    <div class="col-md-3">
                        <label>Range Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="{{ $start }}">
                    </div>
                    <div class="col-md-3">
                        <label>Sampai</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $end }}">
                    </div>
                    <div class="col-md-3">
                        <label>Pilih Bulan</label>
                        <input type="month" name="month" class="form-control" value="{{ $month }}">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>

                <!-- Ringkasan -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-center shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted">Total Service</h6>
                                <h3>{{ $summary['total_service'] }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted">Total Omzet</h6>
                                <h3>Rp {{ number_format($summary['total_cost'], 0, ',', '.') }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center shadow-sm">
                            <div class="card-body">
                                <h6 class="text-muted">Total Barang Keluar</h6>
                                <h3>{{ $summary['total_barang'] }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Tabel -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5>Detail Transaksi</h5>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Nomor Service</th>
                                    <th>Client</th>
                                    <th>Total</th>
                                    <th>Barang Keluar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($services as $service)
                                    <tr>
                                        <td>{{ $service->service_date }}</td>
                                        <td>{{ $service->nomor_service }}</td>
                                        <td>{{ $service->vehicle->client->nama_client }}</td>
                                        <td>Rp {{ number_format($service->total_cost, 0, ',', '.') }}</td>
                                        <td>{{ $service->spareparts->sum('qty') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    {{-- </div> --}}
@endsection
