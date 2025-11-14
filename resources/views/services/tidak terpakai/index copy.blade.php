@extends('layouts.main')

@section('content')
    <div class="page-header">
        <div class="page-title">
            <h4>Daftar Service Kendaraan</h4>
            <h6>Manage Service Kendaraan</h6>
        </div>
        <div class="page-btn">
            <a class="btn btn-added" href="{{ route('services.create') }}">+ Tambah Service</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" id="search" class="form-control"
                        placeholder="Cari nama pemilik / plat nomor...">
                </div>
            </div>

            <div class="table-responsive text-center">
                <table class="table" id="service-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Waktu Datang</th>
                            <th>Pemilik</th>
                            <th>Plat Nomor</th>
                            <th>Kategori</th>
                            <th>Status</th>
                            <th>Waktu Progres</th>
                            <th>Action</th>
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

    <!-- Modal Detail Service -->
    <div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg"> <!-- bisa diganti modal-xl kalau mau lebih lebar -->
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="serviceModalLabel">Detail Service</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" id="service-detail">
                    <!-- konten detail service dari AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>



    <!-- Modal Edit Service -->
    <div class="modal fade" id="editServiceModal" tabindex="-1" aria-labelledby="editServiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="editServiceModalLabel">Edit Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="form-edit-service">
                    <div class="modal-body">
                        <input type="hidden" id="edit_service_id" name="id">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kendaraan</label>
                                <select id="edit_vehicle_id" name="vehicle_id" class="form-control select2-vehicle"
                                    required></select>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Tanggal Service</label>
                                <input type="date" id="edit_service_date" name="service_date" class="form-control"
                                    required>
                                <input type="text" id="edit_status" name="status" class="form-control" hidden>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label class="form-label">Kategori</label>
                                <select id="edit_category" name="category" class="form-control" required>
                                    <option value="fast service">Fast Service</option>
                                    <option value="inap">Inap</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keluhan</label>
                            <textarea id="edit_complaint" name="complaint" class="form-control"></textarea>
                        </div>

                        <!-- Mekanik -->
                        <div class="mb-3">
                            <label class="form-label">Mekanik</label>
                            <select id="edit_mechanics" name="mechanics[]" class="form-control select2-mechanics" multiple
                                required></select>
                        </div>

                        <!-- Jobs -->
                        <div class="mb-3">
                            <label class="form-label d-flex justify-content-between align-items-center">
                                <span>Pekerjaan</span>
                                <button type="button" class="btn btn-success btn-sm" id="btn-add-job"><i
                                        class="bi bi-plus-lg"></i> Tambah</button>
                            </label>
                            <table class="table table-sm table-bordered" id="edit-jobs-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Pekerjaan</th>
                                        <th class="text-center" style="width:60px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <!-- Spareparts -->
                        <div class="mb-3">
                            <label class="form-label d-flex justify-content-between align-items-center">
                                <span>Sparepart</span>
                                <button type="button" class="btn btn-success btn-sm" id="btn-add-sparepart"><i
                                        class="bi bi-plus-lg"></i> Tambah</button>
                            </label>
                            <table class="table table-sm table-bordered" id="edit-sparepart-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Sparepart</th>
                                        <th style="width:80px;">Qty</th>
                                        <th class="text-center" style="width:60px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>


                        {{-- <!-- Jobs -->
                        <div class="mb-3">
                            <label class="form-label">Pekerjaan</label>
                            <table class="table table-sm" id="edit-jobs-table">
                                <thead>
                                    <tr>
                                        <th>Pekerjaan <span class="mr-10"> <button type="button" class="btn btn-success btn-sm"
                                                id="btn-add-job">+</button></span></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <!-- Spareparts -->
                        <div class="mb-3">
                            <label class="form-label">Sparepart</label>
                            <table class="table table-sm" id="edit-sparepart-table">
                                <thead>
                                    <tr>
                                        <th>Nama Sparepart</th>
                                        <th>Qty<span> <button type="button" class="btn btn-success btn-sm"
                                                id="btn-add-sparepart">+</button></span></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Ubah Status -->
    <div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Ubah Status Service</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form id="form-status-service">
                    <div class="modal-body">
                        <input type="hidden" id="status_service_id" name="id">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select id="new_status" name="status" class="form-control" required>
                                <option value="menunggu">Menunggu</option>
                                <option value="proses">Proses</option>
                                <option value="selesai">Selesai</option>
                                <option value="diambil">Diambil</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    @push('scripts')
        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            let currentPage = 1;
            let searchQuery = '';

            window.userPermissions = {
                editService: @json(auth()->user()->can('edit-service')),
                deleteService: @json(auth()->user()->can('delete-service'))
            };

            function loadServices(page = 1, search = '') {
                currentPage = page;
                $.get("{{ route('services.data') }}", {
                    page: page,
                    search: search
                }, function(res) {
                    let rows = '';
                    let i = (res.current_page - 1) * res.per_page;
                    res.data.forEach(s => {
                        let btnEdit = '';
                        let btnDelete = '';

                        if (window.userPermissions.editService) {
                            btnEdit =
                                `<a href="{{ url('services') }}/${s.id}/edit" class="btn btn-sm btn-warning">
                                    <i class="fa fa-pencil"></i>
                                </a>`;
                        }
                        if (window.userPermissions.deleteService) {
                            btnDelete =
                                `<button class="btn btn-sm btn-danger btn-delete" data-url="{{ url('services') }}/${s.id}"><i class="fa fa-trash"></i></button>`;
                        }
                        rows += `<tr>
                <td>${++i}</td>
                <td>${s.service_date}</td>
                <td>${s.vehicle.client?.nama_client || '-'}</td>
                <td>${s.vehicle.license_plate || '-'}</td>
                <td>${s.category}</td>
                <td>
                <span class="badge ${s.status === 'selesai' ? 'bg-success' : 'bg-warning'}">
                        ${s.status}
                    </span>
                </td>
                 <td>
                    ${s.service_progress || '-'}
                </td>
                <td>
                      <button class="btn btn-sm btn-info btn-show" data-id="${s.id}"><i class="fa fa-eye"></i></button>
                    ${btnEdit}
                    ${btnDelete}
                    <button class="btn btn-sm btn-primary btn-change-status" data-id="${s.id}" data-status="${s.status}">Ubah Status</button>
                </td>
            </tr>`;
                    });
                    $('#service-table tbody').html(rows);

                    // pagination
                    let pagination = '';
                    for (let p = 1; p <= res.last_page; p++) {
                        pagination += `<li class="page-item ${res.current_page==p?'active':''}">
                <a class="page-link" href="#" onclick="loadServices(${p}, '${searchQuery}')">${p}</a>
            </li>`;
                    }
                    $('#pagination').html(pagination);
                });
            }

            $(document).ready(function() {
                loadServices();

                $('#search').on('keyup', function() {
                    searchQuery = $(this).val();
                    loadServices(1, searchQuery);
                });
            });

            $(document).on('click', '.btn-delete', function() {
                let url = $(this).data('url');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data service akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: url,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: res.message,
                                    icon: 'success',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                // sekarang aman, fungsi global
                                loadServices(currentPage, searchQuery);
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    title: 'Gagal!',
                                    text: 'Terjadi kesalahan saat menghapus data.',
                                    icon: 'error'
                                });
                            }
                        });
                    }
                });
            });
            // Klik tombol show
            $(document).on('click', '.btn-show', function() {
                let id = $(this).data('id');

                $('#service-detail').html('<div class="text-center py-3">Loading...</div>');
                $('#serviceModal').modal('show');

                $.get("{{ url('services') }}/" + id, function(res) {
                    let html = `
            <h5 class="mb-3">Informasi Kendaraan</h5>
            <table class="table table-bordered">
                <tr><th width="30%">Pemilik</th><td>${res.vehicle.client.nama_client || '-'}</td></tr>
                <tr><th>Plat Nomor</th><td><span class="badge bg-primary">${res.vehicle?.license_plate || '-'}</span></td></tr>
                <tr><th>Kategori</th><td>${res.category}</td></tr>
            </table>

            <h5 class="mt-4 mb-3">Detail Service</h5>
            <table class="table table-bordered">
                <tr><th width="30%">Waktu Datang</th><td>${res.service_date}</td></tr>
                <tr><th>Status</th>
                    <td><span class="badge ${res.status === 'selesai' ? 'bg-success' : 'bg-warning'}">${res.status}</span></td>
                </tr>
                <tr><th>Keluhan</th><td>${res.complaint || '-'}</td></tr>
                <tr><th>Total Biaya</th><td>Rp ${Number(res.total_cost || 0).toLocaleString('id-ID')}</td></tr>
                <tr><th>Dibuat Oleh</th><td>${res.creator?.name || '-'}</td></tr>
                <tr><th>Diperbarui Oleh</th><td>${res.updater?.name || '-'}</td></tr>
            </table>

            <h5 class="mt-4 mb-3">Pekerjaan</h5>
            <table class="table table-striped table-sm">
                <thead><tr><th>Nama Jasa</th><th>qty</th><th>Harga</th><th>subtotal</th></tr></thead>
                <tbody>
                    ${res.jobs.length
                        ? res.jobs.map(j => `
                                            <tr>
                                                <td>${j.jasa?.nama_jasa || '-'}</td>
                                                 <td>${j.qty}</td>
                                                <td>Rp ${Number(j.price || 0).toLocaleString('id-ID')}</td>
                                                <td>Rp ${Number(j.subtotal || 0).toLocaleString('id-ID')}</td>
                                            </tr>
                                        `).join('')
                        : '<tr><td colspan="2" class="text-center text-muted">Tidak ada pekerjaan</td></tr>'
                    }
                </tbody>
            </table>

            <h5 class="mt-4 mb-3">Sparepart</h5>
            <table class="table table-striped table-sm">
                <thead><tr><th>Nama Sparepart</th><th>Qty</th><th>Harga</th><th>subtotal</th></tr></thead>
                <tbody>
                    ${res.spareparts.length
                        ? res.spareparts.map(sp => `
                                            <tr>
                                                <td>${sp.barang?.nama_barang || '-'}</td>
                                                <td>${sp.qty}</td>
                                                <td>Rp ${Number(sp.price || 0).toLocaleString('id-ID')}</td>
                                                <td>Rp ${Number(sp.subtotal || 0).toLocaleString('id-ID')}</td>
                                            </tr>
                                        `).join('')
                        : '<tr><td colspan="3" class="text-center text-muted">Tidak ada sparepart</td></tr>'
                    }
                </tbody>
            </table>

            <h5 class="mt-4 mb-3">Mekanik</h5>
            <ul class="list-group mb-3">
                ${res.mechanics.length
                    ? res.mechanics.map(m => `<li class="list-group-item">${m.name}</li>`).join('')
                    : '<li class="list-group-item text-muted">Tidak ada mekanik</li>'
                }
            </ul>
        `;

                    $('#service-detail').html(html);
                }).fail(function() {
                    $('#service-detail').html('<p class="text-danger">Gagal memuat data.</p>');
                });
            });


            // Klik tombol edit
            $(document).on('click', '.btn-edit', function() {
                let id = $(this).data('id');
                $('#form-edit-service')[0].reset();
                $('#edit-jobs-table tbody').html('');
                $('#edit-sparepart-table tbody').html('');
                $('#editServiceModal').modal('show');
                $('#edit_service_id').val(id);

                $.get("{{ url('services') }}/" + id, function(res) {
                    // isi field utama
                    console.log("data edit", res);
                    $('#edit_vehicle_id').append(new Option(res.vehicle.license_plate + " - " + res.vehicle
                        .customer.name, res.vehicle.id, true, true)).trigger('change');
                    $('#edit_service_date').val(res.service_date);
                    $('#edit_category').val(res.category);
                    $('#edit_complaint').val(res.complaint);
                    $('#edit_status').val(res.status);


                    if (res.mechanics) {
                        // kosongkan dulu biar tidak dobel
                        $('#edit_mechanics').empty();

                        res.mechanics.forEach(m => {
                            $('#edit_mechanics').append(
                                new Option(m.name + ' (' + m.specialty + ')', m.id, true, true)
                            );
                        });

                        $('#edit_mechanics').trigger('change');
                    }



                    // jobs
                    if (res.jobs && res.jobs.length > 0) {
                        res.jobs.forEach((job, i) => {
                            let row = `
                    <tr>
                        <td><input type="text" name="jobs[${i}][job_name]" value="${job.job_name}" class="form-control"></td>
                        <td><button type="button" class="btn btn-danger btn-sm btn-remove-row">x</button></td>
                    </tr>
                `;
                            $('#edit-jobs-table tbody').append(row);
                        });
                    }

                    // spareparts
                    if (res.spareparts && res.spareparts.length > 0) {
                        $('#edit-sparepart-table tbody').empty(); // biar bersih
                        res.spareparts.forEach((sp, i) => {
                            let row = `
                                <tr>
                                    <td>
                                        <select name="spareparts[${i}][sparepart_id]"
                                                class="form-control select2-sparepart" required>
                                            <option value="${sp.sparepart.id}" selected>
                                                ${sp.sparepart.product_name}
                                            </option>
                                        </select>
                                    </td>
                                    <td><input type="number" name="spareparts[${i}][quantity]" value="${sp.qty}" class="form-control"></td>
                                    <td><button type="button" class="btn btn-danger btn-sm btn-remove-row">x</button></td>
                                </tr>`;
                            $('#edit-sparepart-table tbody').append(row);
                        });

                        // 🚀 panggil ulang Select2 agar bisa search
                        initSelect2Spareparts();
                    }

                }).fail(function() {
                    Swal.fire('Error', 'Gagal memuat data service', 'error');
                });
            });

            // tambah row job
            $(document).on('click', '#btn-add-job', function() {
                let i = $('#edit-jobs-table tbody tr').length;
                let row = `
        <tr>
            <td><input type="text" name="jobs[${i}][job_name]" class="form-control"></td>
            <td><button type="button" class="btn btn-danger btn-sm btn-remove-row">x</button></td>
        </tr>
    `;
                $('#edit-jobs-table tbody').append(row);
            });

            // tambah row sparepart
            $(document).on('click', '#btn-add-sparepart', function() {
                let i = $('#edit-sparepart-table tbody tr').length;
                let row = `
        <tr>
            <td><select name="spareparts[${i}][sparepart_id]" class="form-control select2-sparepart" required></select></td>
            <td><input type="number" name="spareparts[${i}][quantity]" value="1" class="form-control"></td>
            <td><button type="button" class="btn btn-danger btn-sm btn-remove-row">x</button></td>
        </tr>
    `;
                $('#edit-sparepart-table tbody').append(row);
                initSelect2Spareparts();
            });

            // hapus row
            $(document).on('click', '.btn-remove-row', function() {
                $(this).closest('tr').remove();
            });

            // submit edit
            $(document).on('submit', '#form-edit-service', function(e) {
                e.preventDefault();
                let id = $('#edit_service_id').val();
                let formData = new FormData(this);

                $.ajax({
                    url: "{{ url('services') }}/" + id,
                    type: "POST",
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-HTTP-Method-Override': 'PUT'
                    },
                    success: function(res) {
                        $('#editServiceModal').modal('hide');
                        Swal.fire({
                            title: 'Berhasil',
                            text: 'Data service berhasil diperbarui',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadServices(currentPage, searchQuery);
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON.message || 'Terjadi kesalahan', 'error');
                    }
                });
            });

            // hapus row sparepart baru
            $(document).on('click', '.btn-remove-row', function() {
                $(this).closest('tr').remove();
            });

            //sparpart
            function initSelect2Spareparts() {
                $('.select2-sparepart').select2({
                    placeholder: "Pilih Sparepart",
                    dropdownParent: $('#editServiceModal'),
                    allowClear: true,
                    width: '100%',
                    ajax: {
                        url: "{{ url('/select2/products') }}",
                        dataType: 'json',
                        delay: 250,
                        processResults: function(data) {
                            return {
                                results: data
                            };
                        }
                    },
                });
            }


            $('.select2-vehicle').select2({
                placeholder: "Pilih Kendaraan",
                ajax: {
                    url: "{{ url('/select2/vehicles') }}",
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                }
            });


            $('.select2-mechanics').select2({
                placeholder: "Pilih Mekanik",
                ajax: {
                    url: "{{ url('/select2/mechanics') }}",
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                }
            });

            $(document).on('click', '.btn-change-status', function() {
                let id = $(this).data('id');
                let status = $(this).data('status');

                $('#status_service_id').val(id);
                $('#new_status').val(status);
                $('#statusModal').modal('show');
            });
            $(document).on('submit', '#form-status-service', function(e) {
                e.preventDefault();
                let id = $('#status_service_id').val();
                let status = $('#new_status').val();

                $.ajax({
                    url: "{{ url('services') }}/" + id + "/status",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        status: status
                    },
                    success: function(res) {
                        $('#statusModal').modal('hide');
                        Swal.fire({
                            title: 'Berhasil',
                            text: 'Status service berhasil diperbarui',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        loadServices(currentPage, searchQuery);
                    },
                    error: function(xhr) {
                        Swal.fire('Error', xhr.responseJSON.message || 'Terjadi kesalahan', 'error');
                    }
                });
            });
        </script>
    @endpush
@endsection
