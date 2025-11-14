@extends('layouts.main')

@section('content')
    <div class="page-header">
        <div class="page-title">
            <h4>Laporan Penjualan Barang</h4>
            <h6>Filter Harian / Bulanan / Rentang Tanggal</h6>
        </div>
    </div>

    <form method="GET" action="{{ route('reports.sale') }}" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <label>Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label>Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-2 align-self-end">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </div>
    </form>

    {{-- === 3 Kartu Ringkasan === --}}
    <div class="row">
        <div class="col-md-4">
            <div class="card p-3 shadow-sm h-100">
                <h5>Omzet</h5>
                <h3 class="text-success mb-0">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3 shadow-sm h-100 d-flex flex-column justify-content-between">
                <div>
                    <h5>Pendapatan (Cash In)</h5>
                    <h3 class="text-primary mb-0">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                </div>

                <div class="mt-3 small border-top pt-2">
                    <div>💵 <strong>Cash:</strong> Rp {{ number_format($cashInCash, 0, ',', '.') }}</div>
                    <div>💳 <strong>Transfer:</strong> Rp {{ number_format($cashInTransfer, 0, ',', '.') }}</div>
                    <div>📱 <strong>QRIS:</strong> Rp {{ number_format($cashInQris, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        @can('profit')
        <div class="col-md-4">
            <div class="card p-3 shadow-sm h-100">
                <h5>Keuntungan (Profit)</h5>
                <h3 class="text-danger mb-0">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h3>
            </div>
        </div>
        @endcan
    </div>

    {{-- === Grafik Batang Total === --}}
    <div class="card mt-4 p-3 shadow-sm">
        <h5 class="mb-3">Grafik Total Penjualan</h5>
        <canvas id="salesBarChart" height="120"></canvas>
    </div>

    {{-- === Grafik Garis Per Hari === --}}
    <div class="card mt-4 p-3 shadow-sm">
        <h5 class="mb-3">Grafik Per Hari</h5>
        <canvas id="salesDailyChart" height="120"></canvas>
    </div>

    {{-- === Tabel Rincian === --}}
    <div class="card">
        <div class="card-body">
            <table class="table table-bordered mt-4">
                <thead class="table-light">
                    <tr>
                        <th>No Sales</th>
                        <th>Tanggal</th>
                        <th>Omzet</th>
                        <th>Pendapatan</th>
                        @can('profit')
                        <th>Profit</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sales as $sale)
                        <tr>
                            <td>{{ $sale->nomor_sales }}</td>
                            <td>{{ $sale->sales_date }}</td>
                            <td>Rp {{ number_format($sale->omzet, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($sale->total_paid, 0, ',', '.') }}</td>
                            @can('profit')
                            <td>Rp {{ number_format($sale->profit, 0, ',', '.') }}</td>
                            @endcan
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

{{-- Grafik Total (Bar) --}}
<script>
const ctxBar = document.getElementById('salesBarChart').getContext('2d');
new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: ['Omzet', 'Pendapatan (Cash In)', 'Profit'],
        datasets: [{
            label: 'Total (Rp)',
            data: [{{ $totalOmzet }}, {{ $totalPendapatan }}, {{ $totalProfit }}],
            backgroundColor: [
                'rgba(54, 162, 235, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(255, 99, 132, 0.7)'
            ],
            borderColor: [
                'rgba(54, 162, 235, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(255, 99, 132, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: value => 'Rp ' + new Intl.NumberFormat('id-ID').format(value)
                }
            }
        },
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw)
                }
            }
        }
    }
});
</script>

{{-- Grafik Garis Per Hari --}}
<script>
const daily = @json($daily);
const labels = daily.map(d => d.date);
const omzet = daily.map(d => d.omzet);
const pendapatan = daily.map(d => d.pendapatan);
const profit = daily.map(d => d.profit);

new Chart(document.getElementById('salesDailyChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: labels,
        datasets: [
            { label: 'Omzet', data: omzet, borderColor: 'rgba(54, 162, 235, 1)', backgroundColor: 'rgba(54, 162, 235, 0.2)', tension: 0.3 },
            { label: 'Pendapatan (Cash In)', data: pendapatan, borderColor: 'rgba(75, 192, 192, 1)', backgroundColor: 'rgba(75, 192, 192, 0.2)', tension: 0.3 },
            { label: 'Profit', data: profit, borderColor: 'rgba(255, 99, 132, 1)', backgroundColor: 'rgba(255, 99, 132, 0.2)', tension: 0.3 },
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(ctx.parsed.y)
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { callback: value => 'Rp ' + new Intl.NumberFormat('id-ID').format(value) }
            }
        }
    }
});
</script>
@endpush
