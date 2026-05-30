@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Detail Penjualan</h4>
        <h6>{{ $penjualan->nomor_penjualan }}</h6>
    </div>
    <div class="page-btn d-flex gap-2">
        <a href="/penjualan-platform/{{ $penjualan->id }}/print" target="_blank" class="btn btn-info">
            <i class="fa-solid fa-print me-1"></i> Print
        </a>
        <a href="{{ route('penjualan-platform.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">

        <!-- INFO -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <small class="text-muted">No. Penjualan</small>
                        <div class="fw-bold">{{ $penjualan->nomor_penjualan }}</div>
                    </div>
                    <div class="col-6 col-md-2">
                        <small class="text-muted">Platform</small>
                        <div><span class="badge bg-warning text-dark">{{ $penjualan->platform }}</span></div>
                    </div>
                    <div class="col-6 col-md-3">
                        <small class="text-muted">No. Pesanan</small>
                        <div>{{ $penjualan->nomor_pesanan ?? '-' }}</div>
                    </div>
                    <div class="col-6 col-md-2">
                        <small class="text-muted">Pembeli</small>
                        <div>{{ $penjualan->nama_pembeli ?? '-' }}</div>
                    </div>
                    <div class="col-6 col-md-2">
                        <small class="text-muted">Tanggal</small>
                        <div>{{ \Carbon\Carbon::parse($penjualan->tanggal)->format('d M Y') }}</div>
                    </div>
                    @if($penjualan->catatan)
                    <div class="col-12">
                        <small class="text-muted">Catatan</small>
                        <div>{{ $penjualan->catatan }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- TABEL BARANG -->
        <div class="card">
    <div class="card-header fw-bold"><i class="fa-solid fa-box me-1"></i> Barang</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0" style="white-space: nowrap;">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th style="min-width: 200px;">Barang</th>
                        <th class="text-end">Kulak</th>
                        <th class="text-end">Jual</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Modal</th>
                        <th class="text-end">Subtotal Jual</th>
                        <th class="text-end text-success">Laba</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($penjualan->items as $i => $item)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>
                            <div style="white-space: normal;">{{ $item->nama_barang }}</div>
                            <small class="text-muted">{{ $item->kode_barang }}</small>
                        </td>
                        <td class="text-end text-muted">{{ number_format($item->harga_kulak,0,',','.') }}</td>
                        <td class="text-end">{{ number_format($item->harga_jual,0,',','.') }}</td>
                        <td class="text-center">{{ $item->qty }}</td>
                        <td class="text-end text-warning">{{ number_format($item->subtotal_modal,0,',','.') }}</td>
                        <td class="text-end fw-semibold">{{ number_format($item->subtotal_jual,0,',','.') }}</td>
                        <td class="text-end fw-bold {{ ($item->subtotal_jual - $item->subtotal_modal) >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format($item->subtotal_jual - $item->subtotal_modal,0,',','.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
    </div>

    <!-- RINGKASAN KEUANGAN -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header fw-bold"><i class="fa-solid fa-receipt me-1"></i> Ringkasan Keuangan</div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tbody>
                        <tr>
                            <td class="ps-3">Total Harga Jual</td>
                            <td class="text-end pe-3 fw-semibold">Rp {{ number_format($penjualan->total_harga_jual,0,',','.') }}</td>
                        </tr>
                        <tr class="table-light">
                            <td class="ps-3">Biaya Admin</td>
                            <td class="text-end pe-3 text-danger">− Rp {{ number_format($penjualan->biaya_admin,0,',','.') }}</td>
                        </tr>
                        <tr>
                            <td class="ps-3">Biaya Pengiriman</td>
                            <td class="text-end pe-3 text-danger">− Rp {{ number_format($penjualan->biaya_pengiriman,0,',','.') }}</td>
                        </tr>
                        <tr class="table-light">
                            <td class="ps-3">Biaya Lainnya</td>
                            <td class="text-end pe-3 text-danger">− Rp {{ number_format($penjualan->biaya_lainnya,0,',','.') }}</td>
                        </tr>
                        <tr class="border-top">
                            <td class="ps-3 fw-bold">Penghasilan Bersih</td>
                            <td class="text-end pe-3 fw-bold text-primary">Rp {{ number_format($penjualan->penghasilan_bersih,0,',','.') }}</td>
                        </tr>
                        <tr class="table-light">
                            <td class="ps-3">Modal Barang</td>
                            <td class="text-end pe-3 text-warning">− Rp {{ number_format($penjualan->total_modal,0,',','.') }}</td>
                        </tr>
                        <tr class="border-top {{ $penjualan->laba_bersih >= 0 ? 'table-success' : 'table-danger' }}">
                            <td class="ps-3 fw-bold fs-6">LABA BERSIH</td>
                            <td class="text-end pe-3 fw-bold fs-6 {{ $penjualan->laba_bersih >= 0 ? 'text-success' : 'text-danger' }}">
                                Rp {{ number_format($penjualan->laba_bersih,0,',','.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
