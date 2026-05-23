@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Detail Transaksi</h4>
        <h6>{{ $transaksi->nomor_transaksi }}</h6>
    </div>
    <div class="page-btn d-flex gap-2">
        <a href="/transaksi-motor/{{ $transaksi->id }}/print" target="_blank" class="btn btn-info">
            <i class="fa-solid fa-print me-1"></i> Print Nota
        </a>
        <a href="{{ route('transaksi-motor.index') }}" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <small class="text-muted">No. Transaksi</small>
                <div class="fw-bold">{{ $transaksi->nomor_transaksi }}</div>
            </div>
            <div class="col-md-3">
                <small class="text-muted">Tanggal</small>
                <div>{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('d M Y') }}</div>
            </div>
            <div class="col-md-2">
                <small class="text-muted">Status</small>
                <div>
                    @if($transaksi->status === 'selesai')
                        <span class="badge bg-success">Selesai</span>
                    @else
                        <span class="badge bg-warning text-dark">Draft</span>
                    @endif
                </div>
            </div>
            <div class="col-md-2">
                <small class="text-muted">Metode Pembayaran</small>
                <div>
                    @php
                        $metodeMap = [
                            'cash'     => ['label'=>'Cash',     'icon'=>'fa-money-bill-wave',    'color'=>'success'],
                            'transfer' => ['label'=>'Transfer',  'icon'=>'fa-building-columns',   'color'=>'primary'],
                            'qris'     => ['label'=>'QRIS',      'icon'=>'fa-qrcode',             'color'=>'warning'],
                            'debit'    => ['label'=>'Debit',     'icon'=>'fa-credit-card',        'color'=>'info'],
                        ];
                        $m = $metodeMap[$transaksi->metode_pembayaran] ?? ['label'=>$transaksi->metode_pembayaran,'icon'=>'fa-circle','color'=>'secondary'];
                    @endphp
                    <span class="badge bg-{{ $m['color'] }} {{ $m['color']==='warning' ? 'text-dark' : '' }}">
                        <i class="fa-solid {{ $m['icon'] }} me-1"></i>{{ $m['label'] }}
                    </span>
                </div>
            </div>
            <div class="col-md-3">
                <small class="text-muted">Kasir</small>
                <div>{{ $transaksi->creator->name ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <small class="text-muted">Nama Customer</small>
                <div class="fw-bold">{{ $transaksi->nama_customer }}</div>
            </div>
            <div class="col-md-2">
                <small class="text-muted">No. HP</small>
                <div>{{ $transaksi->no_hp ?? '-' }}</div>
            </div>
            <div class="col-md-2">
                <small class="text-muted">Plat Nomor</small>
                <div>{{ $transaksi->plat_nomor ?? '-' }}</div>
            </div>
            <div class="col-md-3">
                <small class="text-muted">Nama Motor</small>
                <div>{{ $transaksi->nama_motor ?? '-' }}</div>
            </div>
            @if($transaksi->catatan)
            <div class="col-md-12">
                <small class="text-muted">Catatan</small>
                <div>{{ $transaksi->catatan }}</div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- BARANG -->
<div class="card mb-3">
    <div class="card-header fw-bold"><i class="fa-solid fa-box me-1"></i> Barang / Sparepart</div>
    <div class="card-body p-0">
        <table class="table table-bordered mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Kode</th>
                    <th>Nama Barang</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Harga</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksi->barangs as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->kode_barang ?? '-' }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td class="text-end">{{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">Tidak ada barang.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="table-light">
                    <td colspan="5" class="text-end fw-bold">Total Barang</td>
                    <td class="text-end fw-bold">Rp {{ number_format($transaksi->total_barang, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- JASA -->
<div class="card mb-3">
    <div class="card-header fw-bold"><i class="fa-solid fa-wrench me-1"></i> Jasa / Pekerjaan</div>
    <div class="card-body p-0">
        <table class="table table-bordered mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Kode</th>
                    <th>Nama Jasa</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Harga</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transaksi->jasas as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->kode_jasa ?? '-' }}</td>
                    <td>{{ $item->nama_jasa }}</td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td class="text-end">{{ number_format($item->harga, 0, ',', '.') }}</td>
                    <td class="text-end">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">Tidak ada jasa.</td></tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="table-light">
                    <td colspan="5" class="text-end fw-bold">Total Jasa</td>
                    <td class="text-end fw-bold">Rp {{ number_format($transaksi->total_jasa, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- GRAND TOTAL -->
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-end">
            <table style="min-width:280px;">
                <tr>
                    <td class="pe-4">Total Barang</td>
                    <td class="text-end fw-bold">Rp {{ number_format($transaksi->total_barang, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="pe-4">Total Jasa</td>
                    <td class="text-end fw-bold">Rp {{ number_format($transaksi->total_jasa, 0, ',', '.') }}</td>
                </tr>
                <tr class="border-top">
                    <td class="pe-4 fs-5 text-primary fw-bold">Grand Total</td>
                    <td class="text-end fs-5 text-primary fw-bold">Rp {{ number_format($transaksi->total, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="pe-4 text-muted pt-2">Metode Pembayaran</td>
                    <td class="text-end pt-2">
                        <span class="badge bg-{{ $m['color'] }} {{ $m['color']==='warning' ? 'text-dark' : '' }}">
                            <i class="fa-solid {{ $m['icon'] }} me-1"></i>{{ $m['label'] }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>
@endsection
