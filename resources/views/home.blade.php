@extends('layouts.main')

@section('content')
<div class="container-fluid py-4">

    {{-- === RINGKASAN UTAMA === --}}
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card text-bg-primary shadow-sm">
                <div class="card-body">
                    <h6>Total Penjualan Barang</h6>
                    <h4>Rp {{ number_format($totalSales, 0, ',', '.') }}</h4>
                    <p class="mb-0 text-white-50">Dibayar: Rp {{ number_format($totalPaidSales, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card text-bg-success shadow-sm">
                <div class="card-body">
                    <h6>Total Service</h6>
                    <h4>Rp {{ number_format($totalService, 0, ',', '.') }}</h4>
                    <p class="mb-0 text-white-50">Dibayar: Rp {{ number_format($totalPaidService, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card text-bg-warning shadow-sm">
                <div class="card-body">
                    <h6>Total Keseluruhan</h6>
                    <h4>Rp {{ number_format($grandTotal, 0, ',', '.') }}</h4>
                    <p class="mb-0 text-white-50">Sisa: Rp {{ number_format($grandDue, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- === GRAFIK PENJUALAN DAN SERVICE === --}}
    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">Grafik Penjualan & Service Bulanan</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">Status Pembayaran</h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- === TOP ITEMS & SERVICES === --}}
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">Top 5 Barang Terlaris</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Barang</th>
                                <th>Qty</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topItems as $i => $item)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $item->barang->nama_barang ?? '-' }}</td>
                                    <td>{{ $item->total_qty }}</td>
                                    <td>Rp {{ number_format($item->total_sales, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">Belum ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">Top 5 Jasa Terlaris</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Jasa</th>
                                <th>Qty</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topServices as $i => $service)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td>{{ $service->jasa->nama_jasa ?? '-' }}</td>
                                    <td>{{ $service->total_qty }}</td>
                                    <td>Rp {{ number_format($service->total_sales, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">Belum ada data</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- === TRANSAKSI TERBARU === --}}
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">Transaksi Penjualan Terbaru</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>No. Sales</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentSales as $sale)
                                <tr>
                                    <td>{{ $sale->nomor_sales }}</td>
                                    <td>{{ $sale->sales_date }}</td>
                                    <td>Rp {{ number_format($sale->total, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $sale->status_bayar == 'lunas' ? 'success' : ($sale->status_bayar == 'cicil' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($sale->status_bayar) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0">Transaksi Service Terbaru</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>No. Service</th>
                                <th>Tanggal</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentServices as $srv)
                                <tr>
                                    <td>{{ $srv->nomor_service }}</td>
                                    <td>{{ $srv->service_date }}</td>
                                    <td>Rp {{ number_format($srv->total_cost, 0, ',', '.') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $srv->status_bayar == 'lunas' ? 'success' : ($srv->status_bayar == 'cicil' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($srv->status_bayar) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // === Data grafik dari Controller ===
    const salesPerMonth = @json(array_values($salesPerMonth));
    const servicePerMonth = @json(array_values($servicePerMonth));

    @php
        // $labels = [];
        // foreach (range(1, 12) as $m) $labels[] = date('M', mktime(0,0,0,$m,1));
           $labels = [];
    $currentMonth = date('n'); // bulan sekarang (1–12)
    for ($i = 0; $i < 12; $i++) {
        $monthNumber = (($currentMonth + $i - 1) % 12) + 1;
        $labels[] = date('M', mktime(0, 0, 0, $monthNumber, 1));
    }
    @endphp

    const months = @json($labels);
    const statusData = @json(array_values($statusCounts));
    const statusLabels = @json(array_keys($statusCounts));

    // === Grafik Gabungan Penjualan + Service ===
    const ctx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: months,
            datasets: [
                {
                    label: 'Penjualan Barang',
                    data: salesPerMonth,
                    borderColor: 'rgba(54, 162, 235, 0.9)',
                    fill: false,
                    tension: 0.3
                },
                {
                    label: 'Service',
                    data: servicePerMonth,
                    borderColor: 'rgba(75, 192, 192, 0.9)',
                    fill: false,
                    tension: 0.3
                }
            ]
        },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });

    // === Pie Chart Status Pembayaran ===
    const ctx2 = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx2, {
        type: 'pie',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusData,
                backgroundColor: [
                    'rgba(75,192,192,0.7)',
                    'rgba(255,206,86,0.7)',
                    'rgba(255,99,132,0.7)',
                    'rgba(153,102,255,0.7)',
                ]
            }]
        },
        options: { responsive: true }
    });
</script>
@endpush
