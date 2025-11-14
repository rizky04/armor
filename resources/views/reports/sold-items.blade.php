@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Laporan Barang Terjual</h4>
    </div>
</div>

<div class="card">
    <div class="card-body">

        {{-- Filter --}}
        <form action="{{ route('reports.sold-items') }}" method="GET" class="row g-3 mb-3">
            <div class="col-md-3">
                <label for="date" class="form-label">Tanggal</label>
                <input type="date" name="date" id="date" class="form-control"
                       value="{{ request('date') }}">
            </div>
            <div class="col-md-3">
                <label for="month" class="form-label">Bulan</label>
                <input type="month" name="month" id="month" class="form-control"
                       value="{{ request('month') }}">
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
                <a href="{{ route('reports.sold-items') }}" class="btn btn-secondary">Reset</a>
            </div>
        </form>

        {{-- Tabel --}}
        <table class="table table-bordered table-striped">
            <thead class="table-primary">
                <tr>
                    <th>No</th>
                    <th style="width: 120px;">Tanggal</th>
                    <th>Nama Barang</th>
                    <th>Merk</th>
                    <th style="width: 80px;">Qty</th>
                    <th>Harga Jual</th>
                    <th>Subtotal</th>
                    <th>Pelanggan</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp

                @forelse ($sales as $s)
                    @foreach ($s->items as $item)
                        @php $grandTotal += $item->subtotal; @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ \Carbon\Carbon::parse($s->sales_date)->format('d/m/Y') }}</td>
                            <td>{{ $item->barang->nama_barang ?? '-' }}</td>
                            <td>{{ $item->barang->merk_barang ?? '-' }}</td>
                            <td>{{ $item->qty }}</td>
                            <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            <td>{{ $s->client->nama_client ?? '-' }}</td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="table-secondary fw-bold">
                    <td colspan="6" class="text-end">Total Penjualan:</td>
                    <td colspan="2">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

    </div>
</div>
@endsection
