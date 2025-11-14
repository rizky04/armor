@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Laporan Penjualan Barang</h4>
        <h6>Omzet, Pendapatan, dan Profit</h6>
    </div>
</div>

<form method="GET" action="">
    <div class="row mb-4">
        <div class="col-md-3">
            <label>Dari Tanggal</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
        </div>
        <div class="col-md-3">
            <label>Sampai Tanggal</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
        </div>
        <div class="col-md-3 align-self-end">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </div>
</form>

{{-- 3 Kartu Ringkasan --}}
<div class="row">
    <div class="col-md-4">
        <div class="card p-3 shadow-sm h-100">
            <h5>Omzet</h5>
            <h3 class="text-success">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 shadow-sm h-100 d-flex flex-column justify-content-between">
            <div>
                <h5>Pendapatan (Cash In)</h5>
                <h3 class="text-primary mb-0">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
            </div>
            <div class="mt-3 small border-top pt-2">
                <div>💵 Cash: Rp {{ number_format($cashInCash, 0, ',', '.') }}</div>
                <div>💳 Transfer: Rp {{ number_format($cashInTransfer, 0, ',', '.') }}</div>
                <div>📱 QRIS: Rp {{ number_format($cashInQris, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 shadow-sm h-100">
            <h5>Keuntungan (Profit)</h5>
            <h3 class="text-danger">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h3>
        </div>
    </div>
</div>

{{-- Grafik Omzet / Pendapatan / Profit --}}
<div class="card mt-4 p-4 shadow-sm">
    <h5 class="mb-3">Grafik Penjualan per Hari</h5>
    <canvas id="salesChart" height="100"></canvas>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('salesChart');

new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($chartData->pluck('tanggal')) !!},
        datasets: [
            {
                label: 'Omzet',
                data: {!! json_encode($chartData->pluck('omzet')) !!},
                borderColor: 'green',
                fill: false,
            },
            {
                label: 'Pendapatan',
                data: {!! json_encode($pendapatanData->pluck('pendapatan')) !!},
                borderColor: 'blue',
                fill: false,
            },
            {
                label: 'Profit',
                data: {!! json_encode($profitData->pluck('profit')) !!},
                borderColor: 'red',
                fill: false,
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => 'Rp ' + value.toLocaleString('id-ID')
                }
            }
        }
    }
});
</script>
@endpush
