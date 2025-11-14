@extends('layouts.main')

@section('content')
    <div class="page-header">
        <div class="page-title">
            <h4>Daftar Penjualan</h4>
            <h6>Manage Penjualan Barang</h6>
        </div>
        {{-- <div class="page-btn">
            <a class="btn btn-added" href="{{ route('sales.create') }}">+ Tambah Penjualan</a>
        </div> --}}
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive text-center">
                <div class="row mb-3">
    <div class="col-md-4 ms-auto">
        <input type="text" id="searchInput" class="form-control" placeholder="Cari Client atau Nomor Sales...">
    </div>
</div>

                <table class="table table-bordered" id="sales-table">
                    <thead class="bg-light">
                        <tr>
                            <th>#</th>
                            <th>Aksi</th>
                            <th>Detail</th>
                            <th>Tipe <br> Pembayaran</th>
                            <th>Tanggal</th>
                            <th>Nomor <br> Sales</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Terbayar</th>
                            <th>Hutang</th>
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
    @include('sales.modal.create-sales-payment')
   @push('scripts')
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    window.userPermissions = {
        editService: @json(auth()->user()->can('menu-penjualan-edit')),
        deleteService: @json(auth()->user()->can('menu-penjualan-delete')),
        bayar: @json(auth()->user()->can('bayar'))
    };

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(angka);
    }

    let currentPage = 1;
    let currentSearch = ''; // 🔍 Tambahkan variabel search

    // 🔹 Fungsi utama untuk load data dari backend
    function loadSales(page = 1, search = '') {
        $.get("{{ route('sales.data') }}", { page, search }, function(res) {
            let rows = '';
            let i = (res.current_page - 1) * res.per_page;

            res.data.forEach(s => {
                let btnEdit = '';
                let btnDelete = '';

                if (s.status_bayar != 'lunas' || window.userPermissions.bayar) {
                    if (window.userPermissions.editService) {
                        btnEdit =
                            `<a href="/sales/${s.id}/edit" class="dropdown-item">
                                <img src="{{ asset('assets/assets/img/icons/edit.svg') }}" class="me-2" alt="img">Ubah Penjualan
                            </a>`;
                    }
                    if (window.userPermissions.deleteService) {
                        btnDelete =
                            `<a type="button" class="dropdown-item confirm-text btn-delete" data-id="${s.id}">
                                <img src="{{ asset('assets/assets/img/icons/delete1.svg') }}" class="me-2" alt="img">
                                Hapus Penjualan
                            </a>`;
                    }
                }

                rows += `
                <tr class="sales-row" data-id="${s.id}">
                    <td>${++i}</td>
                    <td class="text-center">
                        <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="true">
                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a href="sales/${s.id}" class="dropdown-item">
                                    <img src="{{ asset('assets/assets/img/icons/eye1.svg') }}" class="me-2" alt="img">
                                    Penjualan Detail
                                </a>
                            </li>
                            ${btnEdit ? `<li>${btnEdit}</li>` : ''}
                            ${btnDelete ? `<li>${btnDelete}</li>` : ''}
                            <li>
                                <a type="button" class="dropdown-item btn-create-sales-payment"
                                   data-id="${s.id}" data-nomor="${s.nomor_sales}">
                                   <img src="{{ asset('assets/assets/img/icons/plus-circle.svg') }}" class="me-2" alt="img">
                                   Tambah Pembayaran
                                </a>
                            </li>
                            <li>
                                <a href="sales/${s.id}/print" class="dropdown-item">
                                    <i class="fa-solid fa-print me-2"></i> Cetak
                                </a>
                            </li>
                        </ul>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-info toggle-detail">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </td>
                    <td>${s.payment?.payment_type || '-'}</td>
                    <td>${s.sales_date || '-'}</td>
                    <td>${s.nomor_sales || '-'}</td>
                    <td>${s.client?.nama_client || '-'}</td>
                    <td>
                        <span class="badges ${
                            s.status_bayar === 'lunas' ? 'bg-lightgreen' :
                            s.status_bayar === 'cicil' ? 'bg-lightyellow' :
                            s.status_bayar === 'belum bayar' ? 'bg-lightgrey' : 'bg-lightred'
                        }">${s.status_bayar}</span>
                    </td>
                    <td>${formatRupiah(s.total)}</td>
                    <td class="text-green">${formatRupiah(s.total_paid) || '-'}</td>
                    <td class="text-red">${formatRupiah(s.due_amount) || '-'}</td>
                </tr>
                <tr class="detail-row d-none" id="detail-${s.id}">
                    <td colspan="11" class="bg-light text-start">
                        <strong>Detail Barang</strong>
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
                                ${s.items.map(i => `
                                    <tr>
                                        <td>${i.barang?.id_barang|| '-'} - ${i.barang?.nama_barang|| '-'} - ${i.barang?.kode_barang || '-'} - ${i.barang?.jenis || '-'} - ${i.barang?.merk_barang || '-'}</td>
                                        <td>${i.qty}</td>
                                        <td>${formatRupiah(i.price)}</td>
                                        <td>${formatRupiah(i.subtotal)}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total :</th>
                                    <th>${formatRupiah(s.total)}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </td>
                </tr>
                `;
            });

            $('#sales-table tbody').html(rows);

            // Pagination dinamis
            let pagination = '';
            for (let p = 1; p <= res.last_page; p++) {
                pagination += `
                    <li class="page-item ${res.current_page == p ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="loadSales(${p}, currentSearch)">${p}</a>
                    </li>`;
            }
            $('#pagination').html(pagination);
        });
    }

    // 🔹 Ketika klik icon detail
    $(document).on('click', '.toggle-detail', function() {
        const tr = $(this).closest('tr');
        const id = tr.data('id');
        $(`#detail-${id}`).toggleClass('d-none');
    });

    // 🔍 Event pencarian realtime
    $('#searchInput').on('keyup', function() {
        currentSearch = $(this).val();
        loadSales(1, currentSearch);
    });

    // Load awal
    $(document).ready(() => loadSales());
</script>

<script src="{{ asset('function/sales/pembayaran-sales.js') }}"></script>
<script src="{{ asset('function/sales/delete-sales.js') }}"></script>
@endpush

@endsection
