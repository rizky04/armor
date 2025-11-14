@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Daftar Penjualan</h4>
        <h6>Manage Penjualan Barang</h6>
    </div>
    <div class="page-btn">
        <a class="btn btn-added" href="{{ route('sales.create') }}">+ Tambah Penjualan</a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="search" class="form-control" placeholder="Cari nama client / nomor sales...">
            </div>
        </div>

        <div class="table-responsive text-center">
            <table class="table" id="sales-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Nomor Sales</th>
                        <th>Client</th>
                        <th>Total</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <nav class="mt-3">
            <ul class="pagination" id="pagination"></ul>
        </nav>
    </div>
</div>

@push('scripts')
<script>
$.ajaxSetup({
    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

function formatRupiah(angka) {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
}

let currentPage = 1;
let searchQuery = '';

function loadSales(page = 1, search = '') {
    currentPage = page;
    $.get("{{ route('sales.data') }}", { page, search }, function(res) {
        let rows = '';
        let i = (res.current_page - 1) * res.per_page;

        res.data.forEach(s => {
            rows += `
                <tr>
                    <td>${++i}</td>
                    <td>${s.sales_date || '-'}</td>
                    <td>${s.nomor_sales || '-'}</td>
                    <td>${s.client?.nama_client || '-'}</td>
                    <td>${formatRupiah(s.total) || '-'}</td>
                    <td>${s.note || '-'}</td>
                    <td class="text-center">
                        <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="{{ url('sales') }}/${s.id}" class="dropdown-item">
                                    <img src="{{ asset('assets/assets/img/icons/eye1.svg') }}" class="me-2" alt="img">Detail
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('sales') }}/${s.id}/edit" class="dropdown-item">
                                    <img src="{{ asset('assets/assets/img/icons/edit.svg') }}" class="me-2" alt="img">Edit
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" data-url="{{ url('sales') }}/${s.id}" class="dropdown-item btn-delete">
                                    <img src="{{ asset('assets/assets/img/icons/delete1.svg') }}" class="me-2" alt="img">Hapus
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('sales') }}/${s.id}/print" class="dropdown-item">
                                    <i class="fa-solid fa-print me-2"></i> Cetak
                                </a>
                            </li>
                        </ul>
                    </td>
                </tr>`;
        });

        $('#sales-table tbody').html(rows);

        // Pagination
        let pagination = '';
        for (let p = 1; p <= res.last_page; p++) {
            pagination += `
                <li class="page-item ${res.current_page == p ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="loadSales(${p}, '${searchQuery}')">${p}</a>
                </li>`;
        }
        $('#pagination').html(pagination);
    });
}

$(document).ready(function() {
    loadSales();

    $('#search').on('keyup', function() {
        searchQuery = $(this).val();
        loadSales(1, searchQuery);
    });
});

// Hapus data
$(document).on('click', '.btn-delete', function() {
    let url = $(this).data('url');
    Swal.fire({
        title: 'Yakin hapus penjualan?',
        text: 'Data penjualan akan dihapus permanen!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (result.isConfirmed) {
            $.ajax({
                url,
                type: 'DELETE',
                success: function(res) {
                    Swal.fire({
                        title: 'Terhapus!',
                        text: res.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    loadSales(currentPage, searchQuery);
                },
                error: function(xhr) {
                    Swal.fire('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
                }
            });
        }
    });
});
</script>
@endpush
@endsection
