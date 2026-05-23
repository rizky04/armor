@extends('layouts.main')

@section('content')
<div class="container-fluid py-3">

    {{-- TANGGAL HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-0">Dashboard</h5>
            <small class="text-muted">{{ now()->translatedFormat('l, d F Y') }}</small>
        </div>
        <div class="text-end">
            <span class="badge bg-light text-dark border">Bulan {{ now()->translatedFormat('F Y') }}</span>
        </div>
    </div>

    {{-- KARTU RINGKASAN HARI INI --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #0d6efd!important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small">Transaksi Motor Hari Ini</div>
                            <div class="fs-3 fw-bold text-primary">{{ $trxHariIni }}</div>
                            <div class="small text-muted">transaksi selesai</div>
                        </div>
                        <div class="text-primary opacity-50 fs-2"><i class="fa-solid fa-motorcycle"></i></div>
                    </div>
                    @if($trxDraft > 0)
                    <div class="mt-2">
                        <span class="badge bg-warning text-dark">{{ $trxDraft }} draft pending</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #198754!important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small">Penjualan Platform Hari Ini</div>
                            <div class="fs-3 fw-bold text-success">{{ $pjlHariIni }}</div>
                            <div class="small text-muted">pesanan selesai</div>
                        </div>
                        <div class="text-success opacity-50 fs-2"><i class="fa-solid fa-store"></i></div>
                    </div>
                    @if($pjlPending > 0)
                    <div class="mt-2">
                        <span class="badge bg-warning text-dark">{{ $pjlPending }} pesanan pending</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left:4px solid #dc3545!important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small">Pengeluaran Bulan Ini</div>
                            <div class="fs-5 fw-bold text-danger">Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}</div>
                            <div class="small text-muted">total pengeluaran</div>
                        </div>
                        <div class="text-danger opacity-50 fs-2"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm h-100" style="border-left:4px solid {{ $labaBersihBulan >= 0 ? '#20c997' : '#dc3545' }}!important;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small">Laba Bersih Bulan Ini</div>
                            <div class="fs-5 fw-bold {{ $labaBersihBulan >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format(abs($labaBersihBulan), 0, ',', '.') }}
                            </div>
                            <div class="small text-muted">{{ $labaBersihBulan >= 0 ? 'keuntungan' : 'rugi' }}</div>
                        </div>
                        <div class="{{ $labaBersihBulan >= 0 ? 'text-success' : 'text-danger' }} opacity-50 fs-2">
                            <i class="fa-solid fa-{{ $labaBersihBulan >= 0 ? 'chart-line' : 'arrow-trend-down' }}"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- OMZET BULAN INI --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="small opacity-75 mb-1">Omzet Transaksi Motor</div>
                    <div class="fs-5 fw-bold">Rp {{ number_format($trxBulanIni, 0, ',', '.') }}</div>
                    <div class="small opacity-75">Keuntungan: Rp {{ number_format($trxKeuntungan, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <div class="small opacity-75 mb-1">Omzet Penjualan Platform</div>
                    <div class="fs-5 fw-bold">Rp {{ number_format($pjlBulanIni, 0, ',', '.') }}</div>
                    <div class="small opacity-75">Laba: Rp {{ number_format($pjlLaba, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100" style="background:#f8f9fa;">
                <div class="card-body">
                    <div class="small text-muted mb-1">Rumus Laba Bersih</div>
                    <div class="small">
                        <span class="text-primary fw-semibold">Rp {{ number_format($trxKeuntungan, 0, ',', '.') }}</span>
                        <span class="text-muted"> + </span>
                        <span class="text-success fw-semibold">Rp {{ number_format($pjlLaba, 0, ',', '.') }}</span>
                        <span class="text-muted"> − </span>
                        <span class="text-danger fw-semibold">Rp {{ number_format($pengeluaranBulanIni, 0, ',', '.') }}</span>
                    </div>
                    <div class="fw-bold mt-1 {{ $labaBersihBulan >= 0 ? 'text-success' : 'text-danger' }}">
                        = Rp {{ number_format($labaBersihBulan, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- GRAFIK --}}
    <div class="row g-3 mb-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h6 class="mb-0 fw-bold"><i class="fa-solid fa-chart-bar me-1 text-primary"></i> Omzet & Pengeluaran 6 Bulan Terakhir</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartBulanan" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- TRANSAKSI TERBARU --}}
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="fa-solid fa-motorcycle me-1 text-primary"></i> Transaksi Motor Terbaru</h6>
                    <a href="{{ route('transaksi-motor.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>No. Transaksi</th>
                                <th>Customer</th>
                                <th class="text-end">Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTrx as $trx)
                            <tr>
                                <td><small class="text-muted">{{ $trx->nomor_transaksi }}</small></td>
                                <td>{{ $trx->nama_customer }}</td>
                                <td class="text-end">Rp {{ number_format($trx->total, 0, ',', '.') }}</td>
                                <td>
                                    @if($trx->status === 'selesai')
                                        <span class="badge bg-success">Selesai</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Draft</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada transaksi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="fa-solid fa-store me-1 text-success"></i> Penjualan Platform Terbaru</h6>
                    <a href="{{ route('penjualan-platform.index') }}" class="btn btn-sm btn-outline-success">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>No. Penjualan</th>
                                <th>Platform</th>
                                <th class="text-end">Laba</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentPjl as $pjl)
                            <tr>
                                <td><small class="text-muted">{{ $pjl->nomor_penjualan }}</small></td>
                                <td>{{ $pjl->platform }}</td>
                                <td class="text-end {{ $pjl->laba_bersih >= 0 ? 'text-success' : 'text-danger' }} fw-semibold">
                                    Rp {{ number_format($pjl->laba_bersih, 0, ',', '.') }}
                                </td>
                                <td>
                                    @if($pjl->status === 'selesai')
                                        <span class="badge bg-success">Selesai</span>
                                    @elseif($pjl->status === 'pending')
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @else
                                        <span class="badge bg-danger">Dibatalkan</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada penjualan.</td></tr>
                            @endforelse
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
const labels       = @json($chartLabels);
const dataTrx      = @json($chartTrx);
const dataPjl      = @json($chartPjl);
const dataPengeluaran = @json($chartPengeluaran);

new Chart(document.getElementById('chartBulanan'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Omzet Transaksi Motor',
                data: dataTrx,
                backgroundColor: 'rgba(13,110,253,0.7)',
                borderRadius: 4,
            },
            {
                label: 'Penghasilan Platform',
                data: dataPjl,
                backgroundColor: 'rgba(25,135,84,0.7)',
                borderRadius: 4,
            },
            {
                label: 'Pengeluaran',
                data: dataPengeluaran,
                backgroundColor: 'rgba(220,53,69,0.6)',
                borderRadius: 4,
            },
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: ctx => ' Rp ' + Number(ctx.raw).toLocaleString('id-ID'),
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: val => 'Rp ' + Number(val).toLocaleString('id-ID'),
                }
            }
        }
    }
});
</script>
@endpush
