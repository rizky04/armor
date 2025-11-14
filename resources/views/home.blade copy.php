@extends('layouts.main')

@section('content')
    {{-- <div class="content"> --}}
    <h4 class="mb-4">📊 Dashboard Bengkel</h4>

    <!-- Omzet Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <h5>Omzet Hari Ini</h5>
                    <h3>Rp {{ number_format($summary['omzet_today'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-body">
                    <h5>Omzet Bulan Ini</h5>
                    <h3>Rp {{ number_format($summary['omzet_month'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

  <!-- Dashboard Summary -->
<h4 class="mb-3">📊 Ringkasan Service & Barang Keluar</h4>
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Service Hari Ini</h6>
                <h3>{{ $summary['services_today'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Service Bulan Ini</h6>
                <h3>{{ $summary['services_month'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Barang Keluar Hari Ini</h6>
                <h3>{{ $summary['spare_today'] }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center shadow-sm">
            <div class="card-body">
                <h6 class="text-muted">Barang Keluar Bulan Ini</h6>
                <h3>{{ $summary['spare_month'] }}</h3>
            </div>
        </div>
    </div>
</div>


    <!-- Grafik Omzet -->
    <div class="card mb-4">
        <div class="card-header">Omzet 30 Hari Terakhir</div>
        <div class="card-body">
            <canvas id="omzetChart" height="100"></canvas>
        </div>
    </div>

    <!-- Top Mekanik -->
    <div class="card">
        <div class="card-header">Top Mekanik Bulan Ini</div>
        <div class="card-body">
            <ul class="list-group">
                @foreach ($topMechanics as $m)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $m->name }}</span>
                        <span><b>{{ $m->total }} service</b></span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    {{-- </div> --}}
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const omzetData = @json($omzetChart);
        const ctx = document.getElementById('omzetChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: omzetData.map(d => d.date),
                datasets: [{
                    label: 'Omzet',
                    data: omzetData.map(d => d.omzet),
                    borderColor: 'blue',
                    fill: false,
                    tension: 0.3
                }]
            }
        });
    </script>
@endpush
