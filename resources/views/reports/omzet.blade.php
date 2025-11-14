@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Laporan Omset</h4>
    </div>
</div>

{{-- Filter Form --}}
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.omzet') }}" class="row g-3">
            <div class="col-md-3">
                <label for="month" class="form-label">Bulan</label>
                <select name="month" id="month" class="form-control">
                    @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="year" class="form-label">Tahun</label>
                <select name="year" id="year" class="form-control">
                    @foreach(range(date('Y')-5, date('Y')+1) as $y)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="page-header">
    <div class="page-title">
        <h6>Bulan: {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} / {{ $year }}</h6>
    </div>
</div>

{{-- Ringkasan --}}
{{-- <div class="card">
    <div class="card-body">
        <ul>
            <li>Total Transaksi: {{ $laporan['total_transaksi'] }}</li>
            <li>Omset Kotor: Rp {{ number_format($laporan['total_omset'], 0, ',', '.') }}</li>
            <li>Total Diskon: Rp {{ number_format($laporan['total_diskon'], 0, ',', '.') }}</li>
            <li>Total Pajak: Rp {{ number_format($laporan['total_pajak'], 0, ',', '.') }}</li>
            <li>Omset Bersih: <strong>Rp {{ number_format($laporan['total_bersih'], 0, ',', '.') }}</strong></li>
        </ul>
    </div>
</div> --}}
{{-- Ringkasan --}}
<div class="row">
    <div class="col-md-3">
        <div class="card mb-3 shadow-sm">
            <div class="card-body text-center">
                <h6 class="text-muted">Total Transaksi</h6>
                <h4 class="fw-bold">{{ $laporan['total_transaksi'] }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card mb-3 shadow-sm">
            <div class="card-body text-center">
                <h6 class="text-muted">Omset Kotor</h6>
                <h4 class="fw-bold">Rp {{ number_format($laporan['total_omset'], 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card mb-3 shadow-sm">
            <div class="card-body text-center">
                <h6 class="text-muted">Total Diskon</h6>
                <h4 class="fw-bold">Rp {{ number_format($laporan['total_diskon'], 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card mb-3 shadow-sm">
            <div class="card-body text-center">
                <h6 class="text-muted">Total Pajak</h6>
                <h4 class="fw-bold">Rp {{ number_format($laporan['total_pajak'], 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card mb-3 shadow-sm">
            <div class="card-body text-center">
                <h5 class="text-muted">Omset Bersih</h5>
                <h3 class="fw-bold">Rp {{ number_format($laporan['total_bersih'], 0, ',', '.') }}</h3>
            </div>
        </div>
    </div>
</div>


{{-- Detail --}}
<div class="card mt-3">
    <div class="card-body">
        <h6>Detail Transaksi</h6>
        <table class="table table-bordered datanew">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Reference</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Diskon</th>
                    <th>Pajak</th>
                    <th>Total Akhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $i => $trx)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>{{ \Carbon\Carbon::parse($trx->date)->format('d/m/Y') }}</td>
                    <td>{{ $trx->reference }}</td>
                    <td>{{ $trx->customer->name ?? '-' }}</td>
                    <td>{{ number_format($trx->total, 0, ',', '.') }}</td>
                    <td>{{ number_format($trx->discount, 0, ',', '.') }}</td>
                    <td>{{ number_format($trx->tax, 0, ',', '.') }}</td>
                    <td>{{ number_format($trx->total_after_tax, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
