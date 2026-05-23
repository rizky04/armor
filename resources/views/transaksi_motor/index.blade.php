@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Transaksi Motor</h4>
        <h6>Daftar Transaksi Barang & Jasa</h6>
    </div>
    <div class="page-btn">
        <a href="{{ route('transaksi-motor.create') }}" class="btn btn-added">
            <i class="fa-solid fa-plus me-1"></i> Transaksi Baru
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <!-- Filter -->
        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" id="filterDari" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" id="filterSampai" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select id="filterStatus" class="form-select">
                    <option value="">Semua</option>
                    <option value="selesai">Selesai</option>
                    <option value="draft">Draft</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Cari</label>
                <input type="text" id="filterSearch" class="form-control" placeholder="No. transaksi / nama / plat">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button class="btn btn-primary w-100" id="btnFilter">Cari</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="tabelTransaksi">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>No. Transaksi</th>
                        <th>Nama Customer</th>
                        <th>Plat / Motor</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Pembayaran</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="bodyTransaksi">
                    <tr><td colspan="9" class="text-center">Memuat data...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let allData = [];

function loadData() {
    const params = {
        tanggal_dari:   $('#filterDari').val(),
        tanggal_sampai: $('#filterSampai').val(),
        status:         $('#filterStatus').val(),
        search:         $('#filterSearch').val(),
    };

    $.get("{{ route('transaksi-motor.data') }}", params, function(res) {
        allData = res.data;
        renderTable(allData);
    });
}

function renderTable(data) {
    const tbody = $('#bodyTransaksi');
    tbody.empty();

    if (!data.length) {
        tbody.append('<tr><td colspan="9" class="text-center text-muted">Tidak ada data.</td></tr>');
        return;
    }

    data.forEach(function(item, i) {
        const badge = item.status === 'selesai'
            ? '<span class="badge bg-success">Selesai</span>'
            : '<span class="badge bg-warning text-dark">Draft</span>';

        const metodeBadge = {
            cash:     '<span class="badge bg-success"><i class="fa-solid fa-money-bill-wave me-1"></i>Cash</span>',
            transfer: '<span class="badge bg-primary"><i class="fa-solid fa-building-columns me-1"></i>Transfer</span>',
            qris:     '<span class="badge bg-warning text-dark"><i class="fa-solid fa-qrcode me-1"></i>QRIS</span>',
            debit:    '<span class="badge bg-info"><i class="fa-solid fa-credit-card me-1"></i>Debit</span>',
        }[item.metode_pembayaran] ?? '<span class="badge bg-secondary">-</span>';

        tbody.append(`
            <tr>
                <td>${i + 1}</td>
                <td>${item.nomor_transaksi}</td>
                <td>${item.nama_customer}</td>
                <td>${item.plat_nomor}<br><small class="text-muted">${item.nama_motor}</small></td>
                <td>${item.tanggal}</td>
                <td>${item.total_fmt}</td>
                <td>${metodeBadge}</td>
                <td>${badge}</td>
                <td>
                    <a href="/transaksi-motor/${item.id}" class="btn btn-sm btn-info">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                    ${item.status === 'draft' ? `<a href="/transaksi-motor/${item.id}/edit" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen"></i></a>` : ''}
                    <a href="/transaksi-motor/${item.id}/print" target="_blank" class="btn btn-sm btn-secondary">
                        <i class="fa-solid fa-print"></i>
                    </a>
                    <button class="btn btn-sm btn-danger btn-hapus" data-id="${item.id}">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            </tr>
        `);
    });
}

$(document).ready(function() {
    loadData();

    $('#btnFilter').on('click', loadData);
    $('#filterSearch').on('keypress', function(e) {
        if (e.which === 13) loadData();
    });

    $(document).on('click', '.btn-hapus', function() {
        const id = $(this).data('id');
        Swal.fire({
            title: 'Hapus transaksi ini?',
            text: 'Stok barang akan dikembalikan jika status Selesai.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
        }).then(result => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/transaksi-motor/${id}`,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: () => {
                        Swal.fire('Dihapus!', 'Transaksi berhasil dihapus.', 'success');
                        loadData();
                    },
                    error: err => Swal.fire('Gagal!', err.responseJSON?.message ?? 'Terjadi kesalahan.', 'error'),
                });
            }
        });
    });
});
</script>
@endpush
