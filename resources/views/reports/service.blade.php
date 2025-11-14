@extends('layouts.main')

@section('content')
    <div class="page-header">
        <div class="page-title">
            <h4>Laporan Keuangan Service</h4>
            <h6>Filter Harian / Bulanan / Rentang Tanggal</h6>
        </div>
    </div>

    <form method="GET" action="{{ route('reports.service') }}" class="mb-4">
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
    {{--
<div class="row">
    <div class="col-md-4">
        <div class="card p-3 shadow-sm">
            <h5>Omzet</h5>
            <h3 class="text-success">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 shadow-sm">
            <h5>Pendapatan (Cash In)</h5>
            <h3 class="text-primary">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 shadow-sm">
            <h5>Keuntungan (Profit)</h5>
            <h3 class="text-danger">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h3>
        </div>
    </div>
</div> --}}

    {{-- <div class="row">
    <div class="col-md-4">
        <div class="card p-3 shadow-sm">
            <h5>Omzet</h5>
            <h3 class="text-success">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 shadow-sm">
            <h5>Pendapatan (Cash In)</h5>
            <h3 class="text-primary">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>

            <div class="mt-2 small">
                <div>💵 Cash: Rp {{ number_format($cashInCash, 0, ',', '.') }}</div>
                <div>💳 Transfer: Rp {{ number_format($cashInTransfer, 0, ',', '.') }}</div>
                <div>📱 QRIS: Rp {{ number_format($cashInQris, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 shadow-sm">
            <h5>Keuntungan (Profit)</h5>
            <h3 class="text-danger">Rp {{ number_format($totalProfit, 0, ',', '.') }}</h3>
        </div>
    </div>
</div> --}}
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

    <div class="card mt-4 p-3 shadow-sm">
        <h5 class="mb-3">Grafik Laporan Service</h5>
        <canvas id="reportChart" height="120"></canvas>
    </div>
    <div class="card mt-4 p-3 shadow-sm">
        <h5 class="mb-3">Grafik Per Hari</h5>
        <canvas id="dailyReportChart" height="120"></canvas>
    </div>


    <div class="card">
       <div class="card-body">
         <table class="table table-bordered mt-4">
            <thead class="table-light">
                <tr>
                    <th>No Service</th>
                    <th>Tanggal</th>
                    <th>Omzet</th>
                    <th>Pendapatan</th>
                    @can('profit')
                    <th>Profit</th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @foreach ($services as $service)
                    <tr>
                        <td>{{ $service->nomor_service }}</td>
                        <td>{{ $service->service_date }}</td>
                        <td>Rp {{ number_format($service->omzet, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($service->total_paid, 0, ',', '.') }}</td>
                        @can('profit')
                        <td>Rp {{ number_format($service->profit, 0, ',', '.') }}</td>
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
            const ctx = document.getElementById('reportChart').getContext('2d');

            new Chart(ctx, {
                type: 'bar', // bisa ganti ke 'doughnut' kalau mau lingkaran
                data: {
                    labels: ['Omzet', 'Pendapatan (Cash In)', 'Profit'],
                    datasets: [{
                        label: 'Total (Rp)',
                        data: [
                            {{ $totalOmzet }},
                            {{ $totalPendapatan }},
                            {{ $totalProfit }}
                        ],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.7)', // Biru: Omzet
                            'rgba(75, 192, 192, 0.7)', // Hijau: Pendapatan
                            'rgba(255, 99, 132, 0.7)' // Merah: Profit
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
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const val = context.raw || 0;
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(val);
                                }
                            }
                        }
                    }
                }
            });
        </script>

        <script>
            // === Grafik Garis: Per Hari (Omzet, Pendapatan, Profit) ===
            const dailyData = @json($daily);
            const average = @json($average);

            const labels = dailyData.map(d => d.date);
            const omzet = dailyData.map(d => d.omzet);
            const pendapatan = dailyData.map(d => d.pendapatan);
            const profit = dailyData.map(d => d.profit);

            const ctxLine = document.getElementById('dailyReportChart').getContext('2d');

            new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                            label: 'Omzet',
                            data: omzet,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderWidth: 2,
                            tension: 0.3,
                        },
                        {
                            label: 'Pendapatan (Cash In)',
                            data: pendapatan,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            borderWidth: 2,
                            tension: 0.3,
                        },
                        {
                            label: 'Profit',
                            data: profit,
                            borderColor: 'rgba(255, 99, 132, 1)',
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderWidth: 2,
                            tension: 0.3,
                        },

                        // === Garis rata-rata (Average) ===
                        {
                            label: 'Rata-rata Omzet',
                            data: Array(labels.length).fill(average.omzet),
                            borderColor: 'rgba(54, 162, 235, 0.4)',
                            borderDash: [5, 5],
                            pointRadius: 0,
                            fill: false,
                        },
                        {
                            label: 'Rata-rata Pendapatan',
                            data: Array(labels.length).fill(average.pendapatan),
                            borderColor: 'rgba(75, 192, 192, 0.4)',
                            borderDash: [5, 5],
                            pointRadius: 0,
                            fill: false,
                        },
                        {
                            label: 'Rata-rata Profit',
                            data: Array(labels.length).fill(average.profit),
                            borderColor: 'rgba(255, 99, 132, 0.4)',
                            borderDash: [5, 5],
                            pointRadius: 0,
                            fill: false,
                        },
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(ctx) {
                                    return ctx.dataset.label + ': Rp ' +
                                        new Intl.NumberFormat('id-ID').format(ctx.parsed.y);
                                }
                            }
                        },
                        legend: {
                            position: 'bottom'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    }
                }
            });
        </script>

        {{-- <script>
    const dailyData = @json($daily);

    const labels = dailyData.map(d => d.date);
    const omzet = dailyData.map(d => d.omzet);
    const pendapatan = dailyData.map(d => d.pendapatan);
    const profit = dailyData.map(d => d.profit);

    new Chart(document.getElementById('dailyReportChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Omzet',
                    data: omzet,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderWidth: 2,
                    tension: 0.3,
                },
                {
                    label: 'Pendapatan (Cash In)',
                    data: pendapatan,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2,
                    tension: 0.3,
                },
                {
                    label: 'Profit',
                    data: profit,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderWidth: 2,
                    tension: 0.3,
                },
            ]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(ctx) {
                            return ctx.dataset.label + ': Rp ' +
                                new Intl.NumberFormat('id-ID').format(ctx.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            }
        }
    });
</script> --}}
    @endpush
@endsection
