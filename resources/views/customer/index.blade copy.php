@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Customer List</h4>
        <h6>Manage your customers</h6>
    </div>
    <div class="page-btn">
        <button class="btn btn-added" id="add-customer-btn">Tambah Customer</button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="customer-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>No. Telepon</th>
                        <th>Alamat</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody id="customer-body">
                    <tr>
                        <td colspan="5" class="text-center">Memuat data...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Customer -->
<div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerModalLabel">Tambah Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <form id="customer-form">
                    <input type="hidden" id="customer-id" name="id">

                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" id="customer-name" name="name" class="form-control">
                        <small id="error-name" class="text-danger error-text"></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No. Telp</label>
                        <input type="text" id="customer-phone" name="no_telp" class="form-control">
                        <small id="error-phone" class="text-danger error-text"></small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <textarea id="customer-address" name="address" class="form-control"></textarea>
                        <small id="error-address" class="text-danger error-text"></small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="customer-form" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function () {
    const customerUrl = "/customers";
   let currentPage = 1;
    let searchQuery = '';

    function loadCustomers(page = 1, search = '') {
        $.get("{{ route('customers.data') }}", { page: page, search: search }, function(res) {
            let rows = '';
            let i = (res.current_page - 1) * res.per_page;

            if (res.data.length > 0) {
                res.data.forEach(cust => {
                    rows += `
                        <tr>
                            <td>${++i}</td>
                            <td>${cust.name}</td>
                            <td>${cust.no_telp}</td>
                            <td>${cust.address ?? '-'}</td>
                            <td class="text-end">
                                <button class="btn btn-sm edit-customer"
                                    data-id="${cust.id}"
                                    data-name="${cust.name}"
                                    data-phone="${cust.no_telp}"
                                    data-address="${cust.address}">
                                    <img src="{{ asset('assets/assets/img/icons/edit.svg') }}" alt="Edit">
                                </button>
                                <button class="btn btn-sm delete-customer" data-id="${cust.id}">
                                    <img src="{{ asset('assets/assets/img/icons/delete.svg') }}" alt="Hapus">
                                </button>
                            </td>
                        </tr>`;
                });
            } else {
                rows = `<tr><td colspan="5" class="text-center">Belum ada customer</td></tr>`;
            }
            $('#customer-body').html(rows);

            // build pagination
            let pagination = '';
            if (res.prev_page_url) {
                pagination += `<li class="page-item">
                    <a class="page-link" href="#" onclick="loadCustomers(${res.current_page - 1}, searchQuery)">Prev</a>
                </li>`;
            }
            for (let i = 1; i <= res.last_page; i++) {
                pagination += `<li class="page-item ${res.current_page == i ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="loadCustomers(${i}, searchQuery)">${i}</a>
                </li>`;
            }
            if (res.next_page_url) {
                pagination += `<li class="page-item">
                    <a class="page-link" href="#" onclick="loadCustomers(${res.current_page + 1}, searchQuery)">Next</a>
                </li>`;
            }
            $('#pagination').html(pagination);
        });
    }

    // panggil pertama kali
    loadCustomers();

    // search
    $('#search-input').on('keyup', function () {
        searchQuery = $(this).val();
        loadCustomers(1, searchQuery);
    });

    // Tambah Customer
    $('#add-customer-btn').click(function () {
        $('#customer-form')[0].reset();
        $('#customer-id').val('');
        $('.error-text').text('');
        $('#customerModalLabel').text('Tambah Customer');
        $('#customerModal').modal('show');
    });

    // Edit Customer
    $(document).on('click', '.edit-customer', function () {
        $('#customer-id').val($(this).data('id'));
        $('#customer-name').val($(this).data('name'));
        $('#customer-phone').val($(this).data('phone'));
        $('#customer-address').val($(this).data('address'));

        $('#customerModalLabel').text('Edit Customer');
        $('#customerModal').modal('show');
    });

    // Submit form
    $('#customer-form').on('submit', function (e) {
        e.preventDefault();

        let id = $('#customer-id').val();
        let url = id ? `${customerUrl}/${id}` : customerUrl + "/store";
        let formData = $(this).serialize();
        if (id) formData += '&_method=PUT';

        $.ajax({
            url: url,
            method: "POST",
            data: formData,
            beforeSend: function () {
                Swal.fire({
                    title: 'Sedang menyimpan...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
            },
            success: function (res) {
                Swal.close();
                $('#customerModal').modal('hide');
                $('#customer-form')[0].reset();
                Swal.fire('Sukses', res.message || 'Data berhasil disimpan', 'success');
                loadCustomers();
            },
            error: function (xhr) {
                Swal.close();
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    if (errors.name) $('#error-name').text(errors.name[0]);
                    if (errors.no_telp) $('#error-phone').text(errors.no_telp[0]);
                    if (errors.address) $('#error-address').text(errors.address[0]);
                } else {
                    Swal.fire('Error', xhr.responseJSON?.error || 'Terjadi kesalahan server.', 'error');
                }
            }
        });
    });

    // Hapus Customer
    $(document).on('click', '.delete-customer', function () {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus customer?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `${customerUrl}/${id}`,
                    method: 'DELETE',
                    success: function (res) {
                        Swal.fire('Sukses', res.message, 'success');
                        loadCustomers();
                    }
                });
            }
        });
    });
});
</script>
@endpush
@endsection
