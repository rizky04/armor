@extends('layouts.main')

@section('content')
<div class="container-fluid py-3">

    <!-- Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="fw-bold mb-0 text-primary">Detail Service</h4>
                <p class="text-muted mb-0">Informasi lengkap service kendaraan</p>
            </div>
        </div>
          <div class="text-end mb-3 no-print">
           <a href="{{route('services.print', $service->id)}}" class="btn btn-outline-primary">
                <i class="bi bi-printer"></i> Cetak
         </a>
    </div>
    </div>

    <!-- Invoice Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body px-5 py-4" id="invoiceA5">

            <!-- Invoice Header -->
            <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
                <div>
                    <h5 class="fw-bold text-uppercase text-dark mb-1">INVOICE SERVICE</h5>
                    <p class="text-muted mb-0">Nomor: <strong>{{ $service->nomor_service }}</strong></p>
                    <p class="text-muted mb-0">Tanggal: {{ $service->service_date }}</p>
                </div>
                <div class="text-end">
                    <h6 class="fw-semibold text-muted mb-1">Status Service:</h6>
                    <span class="badge bg-warning text-dark fs-6">{{ ucfirst($service->status ?? 'menunggu') }}</span><br>
                    <h6 class="fw-semibold text-muted mt-2 mb-1">Status Pembayaran:</h6>
                    <span class="badge {{ $service->status_bayar == 'lunas' ? 'bg-success' : 'bg-secondary' }} fs-6">
                        {{ ucfirst($service->status_bayar ?? '-') }}
                    </span>
                </div>
            </div>

            <!-- Informasi Pelanggan & Kendaraan -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <h6 class="text-primary fw-bold mb-2">Customer Info</h6>
                    <p class="mb-1 fw-semibold">Nama: {{ $service->vehicle->client->nama_client ?? '-' }}</p>
                    <p class="mb-1">Alamat: {{ $service->vehicle->client->alamat ?? '-' }}</p>
                    <p class="mb-0">Telp: {{ $service->vehicle->client->no_telp ?? '-' }}</p>
                </div>
                <div class="col-md-4">
                    <h6 class="text-primary fw-bold mb-2">Vehicle Info</h6>
                    <p class="mb-1">Plat Nomor: <strong>{{ $service->vehicle->license_plate ?? '-' }}</strong></p>
                    <p class="mb-1">Kategori Service: {{ $service->category ?? '-' }}</p>
                    <p class="mb-0">Keluhan: {{ $service->keluhan ?? '-' }}</p>
                </div>
                <div class="col-md-4">
                    <h6 class="text-primary fw-bold mb-2">Invoice Info</h6>
                    <p class="mb-1 fw-bold text-success">Total Biaya:
                        Rp {{ number_format($service->total_cost, 0, ',', '.') }}
                    </p>
                    <p class="mb-1 fw-bold text-success">Telah Dibayar:
                        Rp {{ number_format($service->total_paid, 0, ',', '.') }}
                    </p>
                    <p class="mb-1 fw-bold text-danger">Sisa Pembayaran:
                        Rp {{ number_format($service->due_amount, 0, ',', '.') }}
                    </p>
                    <p class="text-muted small mb-0">Dibuat oleh: {{ $service->creator->pengguna->nama ?? '-' }}</p>
                </div>
            </div>

            <!-- Tabel Jasa -->
            <div class="mt-4">
                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Pekerjaan / Jasa</h6>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>Nama Jasa</th>
                                <th width="100">Qty</th>
                                <th width="150">Harga</th>
                                <th width="150">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalJasa = 0; @endphp
                            @forelse($service->jobs as $job)
                                @php $totalJasa += $job->subtotal; @endphp
                                <tr>
                                    <td>{{ $job->jasa->nama_jasa ?? '-' }}</td>
                                    <td class="text-center">{{ $job->qty }}</td>
                                    <td class="text-end">Rp {{ number_format($job->price, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($job->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">Tidak ada pekerjaan</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="3" class="text-end bg-light">Total Jasa</td>
                                <td class="text-end bg-light">Rp {{ number_format($totalJasa, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Tabel Sparepart -->
            <div class="mt-4">
                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Sparepart</h6>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr class="text-center">
                                <th>Nama Sparepart</th>
                                <th width="100">Qty</th>
                                <th width="150">Harga</th>
                                <th width="150">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalSparepart = 0; @endphp
                            @forelse($service->spareparts as $sp)
                                @php $totalSparepart += $sp->subtotal; @endphp
                                <tr>
                                    <td>{{ $sp->barang->id_barang ?? '-' }} - {{ $sp->barang->kode_barang ?? '-' }} - {{ $sp->barang->nama_barang ?? '-' }} - {{ $sp->barang->merk_barang ?? '-' }} - {{ $sp->barang->keterangan ?? '-' }}</td>
                                    <td class="text-center">{{ $sp->qty }}</td>
                                    <td class="text-end">Rp {{ number_format($sp->price, 0, ',', '.') }}</td>
                                    <td class="text-end">Rp {{ number_format($sp->subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted">Tidak ada sparepart</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="fw-bold">
                                <td colspan="3" class="text-end bg-light">Total Sparepart</td>
                                <td class="text-end bg-light">Rp {{ number_format($totalSparepart, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Total Keseluruhan -->
            <div class="mt-4 text-end">
                @php $grandTotal = $totalJasa + $totalSparepart; @endphp
                <h5 class="fw-bold text-primary mb-1">Total Keseluruhan:</h5>
                <h3 class="fw-bold text-dark border-top pt-2 d-inline-block">
                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                </h3>
            </div>

            <!-- Mekanik -->
            <div class="mt-5">
                <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Mekanik</h6>
                <ul class="list-group">
                    @forelse($service->mechanics as $m)
                        <li class="list-group-item">{{ $m->name }}</li>
                    @empty
                        <li class="list-group-item text-muted">Tidak ada mekanik</li>
                    @endforelse
                </ul>
            </div>

            <!-- Footer -->
            <div class="mt-5 text-center border-top pt-3 text-muted small">
                <p class="mb-0">Terima kasih telah menggunakan layanan kami.</p>
                <p class="mb-0">Karisma Motor - Sistem Service Kendaraan</p>
            </div>

            <!-- Tombol Kembali -->
            <div class="mt-4 text-end">
                <a href="{{ route('services.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Daftar Service
                </a>
            </div>
        </div>
    </div>
</div>
{{-- PRINT STYLING A5 --}}
<style>
@media print {
    @page {
        size: A5 portrait;
        margin: 10mm;
    }
    body * {
        visibility: hidden;
    }
    #invoiceA5, #invoiceA5 * {
        visibility: visible;
    }
    #invoiceA5 {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }
}
</style>

<script>
function printInvoice() {
    window.print();
}
</script>
@endsection
