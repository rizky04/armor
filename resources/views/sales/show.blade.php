@extends('layouts.main')

@section('content')
<div class="container-fluid py-3">

    <!-- Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-0 text-primary">Detail Penjualan</h4>
                <p class="text-muted mb-0">Informasi lengkap transaksi penjualan</p>
            </div>
        </div>
        <div class="text-end mb-3 no-print">
            {{-- <a href="{{ route('sales.print', $sales->id) }}" class="btn btn-outline-primary">
                <i class="bi bi-printer"></i> Cetak
            </a> --}}
        </div>
    </div>

    <!-- Invoice Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body px-5 py-4" id="invoiceA5">

            <!-- Header Invoice -->
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                <div>
                    <h5 class="fw-bold text-uppercase text-dark mb-1">INVOICE PENJUALAN</h5>
                    <p class="text-muted mb-0">Nomor: <strong>{{ $sales->nomor_sales }}</strong></p>
                    <p class="text-muted mb-0">Tanggal: {{ $sales->sales_date }}</p>
                    <p class="text-muted mb-0">Jatuh Tempo: {{ $sales->due_date }}</p>
                </div>
                <div class="text-end">
                    <h6 class="fw-semibold text-muted mb-1">Status Pembayaran:</h6>
                    <span class="badge {{ $sales->status_bayar == 'lunas' ? 'bg-success' : 'bg-secondary' }} fs-6">
                        {{ ucfirst($sales->status_bayar ?? '-') }}
                    </span>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-primary fw-bold mb-2">Customer Info</h6>
                    <p class="mb-1 fw-semibold">Nama: {{ $sales->client->nama_client ?? '-' }}</p>
                    <p class="mb-1">Alamat: {{ $sales->client->alamat ?? '-' }}</p>
                    <p class="mb-0">Telp: {{ $sales->client->no_telp ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <h6 class="text-primary fw-bold mb-2">Invoice Info</h6>
                    <p class="mb-1 fw-bold text-success">Total:
                        Rp {{ number_format($sales->total, 0, ',', '.') }}
                    </p>
                    <p class="mb-1 fw-bold text-success">Telah Dibayar:
                        Rp {{ number_format($sales->payments->sum('amount_paid'), 0, ',', '.') }}
                    </p>
                    <p class="mb-1 fw-bold text-danger">Sisa Pembayaran:
                        Rp {{ number_format($sales->payments->sum('due_amount'), 0, ',', '.') }}
                    </p>
                    <p class="text-muted small mb-0">Dibuat oleh: {{ $sales->user->pengguna->nama ?? '-' }}</p>
                </div>
            </div>

            <!-- Tabel Barang -->
            <div class="mt-4">
                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Barang / Produk</h6>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>Nama Barang</th>
                                <th width="100">Qty</th>
                                <th width="150">Harga</th>
                                <th width="150">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $grandTotal = 0; @endphp
                            @forelse($sales->items as $item)
                                @php $grandTotal += $item->subtotal; @endphp
                                <tr>
                                    <td>{{ $item->barang->nama_barang ?? '-' }}</td>
                                    <td class="text-center">{{ $item->qty }}</td>
                                    <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">Tidak ada barang</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="3" class="text-end bg-light">Total</td>
                                <td class="text-end bg-light">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Footer -->
            <div class="mt-5 text-center border-top pt-3 text-muted small">
                <p class="mb-0">Terima kasih telah berbelanja.</p>
                <p class="mb-0">Bengkel & Toko Kami</p>
            </div>

            <!-- Tombol Kembali -->
            <div class="mt-4 text-end no-print">
                <a href="{{ route('sales.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Penjualan
                </a>
            </div>
        </div>
    </div>
</div>

{{-- PRINT STYLING A5 --}}
<style>
@media print {
    @page { size: A5 portrait; margin: 10mm; }
    body * { visibility: hidden; }
    #invoiceA5, #invoiceA5 * { visibility: visible; }
    #invoiceA5 { position: absolute; left: 0; top: 0; width: 100%; }
}
</style>
@endsection
