@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Laporan Gabungan</h4>
        <h6>Ringkasan Service & Penjualan Barang</h6>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.Gabungan') }}" class="row mb-4">
            <div class="col-md-4">
                <label>Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control"
                    value="{{ $startDate }}">
            </div>
            <div class="col-md-4">
                <label>Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control"
                    value="{{ $endDate }}">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-primary w-100">🔍 Tampilkan</button>
            </div>
        </form>

        <div class="row text-center mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm p-3">
                    <h5>💰 Total Omzet</h5>
                    <h3 class="text-success">{{ number_format($totalOmzet, 0, ',', '.') }}</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm p-3">
                    <h5>💵 Total Pendapatan (Cash In)</h5>
                    <h3 class="text-primary">{{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                </div>
            </div>
            @can('profit')
            <div class="col-md-4">
                <div class="card shadow-sm p-3">
                    <h5>📈 Total Profit</h5>
                    <h3 class="text-danger">{{ number_format($totalProfit, 0, ',', '.') }}</h3>
                </div>
            </div>
            @endcan
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <div class="alert alert-success">
                    💵 Cash : <strong>{{ number_format($cashInCash, 0, ',', '.') }}</strong>
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-info">
                    💳 Transfer : <strong>{{ number_format($cashInTransfer, 0, ',', '.') }}</strong>
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-warning">
                    📱 QRIS : <strong>{{ number_format($cashInQris, 0, ',', '.') }}</strong>
                </div>
            </div>
        </div>

        <h5 class="mt-4 mb-3">📊 Grafik Harian (Omzet, Pendapatan, Profit)</h5>
        <canvas id="chartGabungan" height="120"></canvas>

        <h5 class="mt-5 mb-3">📅 Rata-rata Harian</h5>
        <table class="table table-bordered">
            <thead>
                <tr class="table-light text-center">
                    <th>Rata-rata Omzet</th>
                    <th>Rata-rata Pendapatan</th>
                    @can('profit')
                    <th>Rata-rata Profit</th>
                    @endcan
                </tr>
            </thead>
            <tbody class="text-center">
                <tr>
                    <td>{{ number_format($average['omzet'], 0, ',', '.') }}</td>
                    <td>{{ number_format($average['pendapatan'], 0, ',', '.') }}</td>
                    @can('profit')
                    <td>{{ number_format($average['profit'], 0, ',', '.') }}</td>
                    @endcan
                </tr>
            </tbody>
        </table>

        <h5 class="mt-5 mb-3">📋 Detail Harian</h5>
        <table class="table table-striped">
            <thead>
                <tr class="table-secondary text-center">
                    <th>Tanggal</th>
                    <th>Omzet</th>
                    <th>Pendapatan</th>
                    @can('profit')
                    <th>Profit</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @foreach ($daily as $d)
                    <tr class="text-center">
                        <td>{{ $d['date'] }}</td>
                        <td>{{ number_format($d['omzet'], 0, ',', '.') }}</td>
                        <td>{{ number_format($d['pendapatan'], 0, ',', '.') }}</td>
                        @can('profit')
                        <td>{{ number_format($d['profit'], 0, ',', '.') }}</td>
                        @endcan
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('chartGabungan');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($daily->pluck('date')) !!},
        datasets: [
            {
                label: 'Omzet',
                data: {!! json_encode($daily->pluck('omzet')) !!},
                borderColor: 'green',
                tension: 0.3,
                fill: false
            },
            {
                label: 'Pendapatan',
                data: {!! json_encode($daily->pluck('pendapatan')) !!},
                borderColor: 'blue',
                tension: 0.3,
                fill: false
            },
            {
                label: 'Profit',
                data: {!! json_encode($daily->pluck('profit')) !!},
                borderColor: 'red',
                tension: 0.3,
                fill: false
            },
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            title: { display: true, text: 'Grafik Laporan Gabungan' }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
@endpush


@endsection
