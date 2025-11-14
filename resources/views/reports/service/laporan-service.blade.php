@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Service Payment Report</h4>
        <h6>Laporan Pembayaran Service</h6>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-3">
                <input type="date" id="start_date" class="form-control">
            </div>
            <div class="col-md-3">
                <input type="date" id="end_date" class="form-control">
            </div>
            <div class="col-md-3">
                <input type="text" id="search" class="form-control" placeholder="Cari invoice / customer...">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary" id="filterBtn">Filter</button>
            </div>
        </div>

        <table class="table table-bordered" id="report-table">
            <thead class="table-light">
                <tr>
                    <th>Invoice Number</th>
                    <th>Customer Name</th>
                    <th>Service Date</th>
                    <th>Total</th>
                    <th>Dibayar</th>
                    <th>Sisa</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <ul id="pagination" class="pagination"></ul>
    </div>
</div>
@push('scripts')

<script>
function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(angka);
}

function loadReport(page = 1) {
    $.get("{{ route('laporan.service.data') }}", {
        page: page,
        search: $('#search').val(),
        start_date: $('#start_date').val(),
        end_date: $('#end_date').val()
    }, function(res) {
        let rows = '';
        res.data.forEach(r => {
            const status = r.due_amount <= 0
                ? `<span class="badge bg-success">Lunas</span>`
                : `<span class="badge bg-danger">Hutang</span>`;

            rows += `
                <tr>
                    <td>${r.nomor_service}</td>
                    <td>${r.vehicle?.client?.nama_client || '-'}</td>
                    <td>${r.service_date}</td>
                    <td>${formatRupiah(r.total_cost)}</td>
                    <td class="text-green">${formatRupiah(r.total_paid)}</td>
                    <td class="text-red">${formatRupiah(r.due_amount || 0)}</td>
                    <td>${status}</td>
                </tr>
            `;
        });
        $('#report-table tbody').html(rows);

        // Pagination
        let pagination = '';
        for (let p = 1; p <= res.last_page; p++) {
            pagination += `<li class="page-item ${res.current_page==p?'active':''}">
                <a class="page-link" href="#" onclick="loadReport(${p})">${p}</a>
            </li>`;
        }
        $('#pagination').html(pagination);
    });
}

$(document).ready(function() {
    loadReport();
    $('#filterBtn').on('click', function() {
        loadReport();
    });
    $('#search').on('keyup', function(e) {
        if (e.key === 'Enter') loadReport();
    });
});
</script>
@endpush
@endsection
