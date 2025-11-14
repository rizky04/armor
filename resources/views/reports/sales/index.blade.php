@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>History Pembayaran Penjualan</h4>
        <h6>Manage Pembayaran Sales</h6>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <input type="date" id="start_date" class="form-control" placeholder="Dari Tanggal">
            </div>
            <div class="col-md-3">
                <input type="date" id="end_date" class="form-control" placeholder="Sampai Tanggal">
            </div>
            <div class="col-md-3">
                <input type="text" id="search" class="form-control" placeholder="Cari nomor sales / client">
            </div>
            <div class="col-md-3">
                <button id="btn-filter" class="btn btn-primary w-100">Filter</button>
            </div>
        </div>

        <div class="table-responsive text-center">
            <table class="table" id="payment-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Detail</th>
                        <th>Tanggal</th>
                        <th>Nomor Sales</th>
                        <th>Client</th>
                        <th>Jumlah Bayar</th>
                        <th>Metode</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <nav class="mt-3">
            <ul class="pagination" id="pagination"></ul>
        </nav>

        <div class="text-end mt-3">
            <h5>Total Semua: <span id="total-all" class="text-success">Rp 0</span></h5>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentPage = 1;
let searchQuery = '';
// 🔹 Klik tombol detail untuk expand/collapse
$(document).on('click', '.toggle-detail', function() {
    const tr = $(this).closest('tr');
    const id = tr.data('id');
    $(`#detail-${id}`).toggleClass('d-none');
});


function loadPayments(page = 1, search = '', start = '', end = '') {
    currentPage = page;
    $.get("{{ route('sales-payments.data') }}", { page, search, start_date: start, end_date: end }, function(res) {
        let rows = '';
        let i = (res.data.current_page - 1) * res.data.per_page;
        console.log("data", res.data.data)
       res.data.data.forEach(p => {
    rows += `
        <tr class="payment-row" data-id="${p.id}">
            <td>${++i}</td>
            <td>
                <button class="btn btn-sm btn-outline-info toggle-detail">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </td>
            <td>${p.payment_date}</td>
            <td>${p.sales?.nomor_sales || '-'}</td>
            <td>${p.sales?.client?.nama_client || '-'}</td>
            <td>${formatRupiah(p.amount_paid)}</td>
            <td>${p.payment_type || '-'}</td>

        </tr>
        <tr class="detail-row d-none" id="detail-${p.id}">
            <td colspan="8" class="bg-light text-start">
                <strong>Detail Barang Penjualan</strong>
                ${
                    p.sales?.items?.length
                    ? `
                        <table class="table table-sm mt-2 mb-1">
                            <thead>
                                <tr>
                                    <th>Nama Barang</th>
                                    <th>Qty</th>
                                    <th>Harga</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${p.sales.items.map(i => `
                                    <tr>
                                        <td>${i.barang?.id_barang || '-'} - ${i.barang?.nama_barang || '-'}</td>
                                        <td>${i.qty}</td>
                                        <td>${formatRupiah(i.price)}</td>
                                        <td>${formatRupiah(i.subtotal)}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total :</th>
                                    <th>${formatRupiah(p.sales?.total || 0)}</th>
                                </tr>
                            </tfoot>
                        </table>
                    `
                    : '<p class="text-muted mt-2">Tidak ada detail barang.</p>'
                }
            </td>
        </tr>
    `;
});

        $('#payment-table tbody').html(rows);
        $('#total-all').text(formatRupiah(res.total_all));

        // Pagination
        let pagination = '';
        for (let p = 1; p <= res.data.last_page; p++) {
            pagination += `
                <li class="page-item ${res.data.current_page==p?'active':''}">
                    <a class="page-link" href="#" onclick="loadPayments(${p}, '${searchQuery}', $('#start_date').val(), $('#end_date').val())">${p}</a>
                </li>`;
        }
        $('#pagination').html(pagination);
    });
}

$(document).ready(function() {
    loadPayments();

    $('#search').on('keyup', function() {
        searchQuery = $(this).val();
        loadPayments(1, searchQuery, $('#start_date').val(), $('#end_date').val());
    });

    $('#btn-filter').on('click', function() {
        loadPayments(1, searchQuery, $('#start_date').val(), $('#end_date').val());
    });
});

function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
}
</script>
@endpush
@endsection
