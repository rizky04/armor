@extends('layouts.main')

@section('content')
    <div class="page-header">
        <div class="page-title">
            <h4>Mobil Client List</h4>
            <h6>Manage Mobil Cleint</h6>
        </div>
        <div class="page-btn d-flex justify-content-between align-items-center gap-2">
            <button class="btn btn-added mr-1" id="add-client-btn">+ Add Client</button>
            <button class="btn btn-added" id="btn-add">+ Add Mobil Client</button>
        </div>

    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" id="search" class="form-control mb-3" placeholder="Cari kendaraan...">
                </div>
            </div>


            <div class="table-responsive">
                <table class="table" id="vehicle-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Photo</th>
                            <th>License Plate</th>
                            <th>Brand</th>
                            <th>Type</th>
                            <th>Client</th>
                            <th>Engine No</th>
                            <th>Chassis No</th>
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

    <!-- Modal Create/Edit -->
    <!-- Modal Create/Edit Vehicle -->
   @include('vehicles.modal.create-kendaraan')
    <!-- Modal Preview Foto -->

    @include('vehicles.modal.view-photo-mobil')
    <!-- Modal Preview Foto -->

    <!-- Modal Tambah/Edit Customer -->
    <!-- Modal Create/Edit -->
   @include('vehicles.modal.create-client')

    <!-- Modal Preview Foto -->
    @push('scripts')
        <script>
            $(document).ready(function() {
                // supaya select2 muncul di dalam modal dengan benar
                $('#id_client').select2({
                    dropdownParent: $('#vehicleModal'),
                    placeholder: 'Pilih Client...',
                    allowClear: true,
                    ajax: {
                        url: "{{ route('select2.clients') }}",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                q: params.term
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: $.map(data, function(item) {
                                    return {
                                        id: item.id_client,
                                        text: item.nama_client + ' - ' + (item.alamat ?? '')
                                    }
                                })
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 1
                });

                let currentPage = 1;
                let searchQuery = '';

                // load data kendaraan
                function loadVehicles(page = 1, search = '') {
                    currentPage = page; // update halaman aktif
                    $.get("{{ route('vehicles.data') }}", {
                        page: page,
                        search: search
                    }, function(res) {
                        let rows = '';
                        let i = (res.current_page - 1) * res.per_page;
                        res.data.forEach(v => {
                            rows += `
                        <tr>
                            <td>${++i}</td>
                            <td>
                                ${v.photo
                                    ? `<img src="/uploads/vehicle/${v.photo}"
                                                                             alt="${v.brand}"
                                                                             width="45" height="45"
                                                                             class="rounded border preview-photo"
                                                                             style="cursor: pointer; object-fit: cover;"
                                                                             data-src="/uploads/vehicle/${v.photo}">`
                                    : '<span class="badge bg-secondary">No Photo</span>'}
                            </td>
                            <td>${v.license_plate}</td>
                            <td>${v.brand}</td>
                            <td>${v.type ?? ''}</td>
                            <td>${v.client ? v.client.nama_client : ''}</td>
                            <td>${v.engine_number ?? ''}</td>
                            <td>${v.chassis_number ?? ''}</td>
                            <td>
                                <button class="btn btn-sm btn-warning btn-edit" data-id="${v.id}">Edit</button>
                                <button class="btn btn-sm btn-danger btn-delete" data-id="${v.id}">Delete</button>
                            </td>
                        </tr>`;
                        });
                        $('#vehicle-table tbody').html(rows);


                        // === pagination ===
                        let pagination = '';
                        const totalPages = res.last_page;
                        const current = res.current_page;
                        const delta = 2;

                        if (res.prev_page_url) {
                            pagination +=
                                `<li class="page-item"><a class="page-link" href="#" onclick="loadVehicles(${current - 1}, searchQuery)">Prev</a></li>`;
                        }

                        if (current > delta + 1) {
                            pagination +=
                                `<li class="page-item"><a class="page-link" href="#" onclick="loadVehicles(1, searchQuery)">1</a></li>`;
                            if (current > delta + 2) {
                                pagination +=
                                    `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                            }
                        }

                        for (let i = Math.max(1, current - delta); i <= Math.min(totalPages, current +
                                delta); i++) {
                            pagination += `<li class="page-item ${current === i ? 'active' : ''}">
                                      <a class="page-link" href="#" onclick="loadVehicles(${i}, searchQuery)">${i}</a>
                                   </li>`;
                        }

                        if (current < totalPages - delta) {
                            if (current < totalPages - delta - 1) {
                                pagination +=
                                    `<li class="page-item disabled"><span class="page-link">...</span></li>`;
                            }
                            pagination +=
                                `<li class="page-item"><a class="page-link" href="#" onclick="loadVehicles(${totalPages}, searchQuery)">${totalPages}</a></li>`;
                        }

                        if (res.next_page_url) {
                            pagination +=
                                `<li class="page-item"><a class="page-link" href="#" onclick="loadVehicles(${current + 1}, searchQuery)">Next</a></li>`;
                        }

                        $('#pagination').html(pagination);
                    });
                }

                // initial load
                loadVehicles();

                // search
                $('#search').on('keyup', function() {
                    searchQuery = $(this).val();
                    loadVehicles(1, searchQuery);
                });

                // tambah
                $('#btn-add').on('click', function() {
                    $('#vehicleForm')[0].reset();
                    $('#vehicle_id').val('');
                    $('#id_client').val(null).trigger('change'); // reset select2
                    $('#photo-preview').attr('src', '').hide(); // reset foto preview
                    $('#vehicleModal .modal-title').text('Add Vehicle');
                    $('#vehicleModal').modal('show');
                });

                // simpan
                $('#vehicleForm').on('submit', function(e) {
                    e.preventDefault();
                    let id = $('#vehicle_id').val();
                    console.log('Vehicle ID:', id); // Debug: cek nilai id
                    let url = id ? `/vehicles/${id}` : "{{ route('vehicles.store') }}";

                    let formData = new FormData(this);
                    if (id) formData.append('_method', 'PUT');
                    formData.append('_token', "{{ csrf_token() }}");

                    $.ajax({
                        url: url,
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(res) {
                            $('#vehicleModal').modal('hide');
                            loadVehicles(currentPage, searchQuery);
                            Swal.fire('Success', res.message, 'success');
                        },
                        error: function(xhr) {
                            if (xhr.responseJSON && xhr.responseJSON.errors) {
                                let errors = xhr.responseJSON.errors;
                                let msg = Object.values(errors).map(e => e[0]).join('<br>');
                                Swal.fire('Error', msg, 'error');
                            } else {
                                Swal.fire('Error', 'Terjadi kesalahan saat simpan data', 'error');
                            }
                        }
                    });
                });

                // preview foto di modal ketika klik thumbnail
                $(document).on('click', '.preview-photo', function() {
                    let src = $(this).data('src');
                    $('#modal-photo').attr('src', src);
                    $('#photoModal').modal('show');
                });


                // edit
                $(document).on('click', '.btn-edit', function() {
                    let id = $(this).data('id');
                    $.get(`/vehicles/${id}`, function(v) {
                        $('#vehicle_id').val(v.id);
                        $('#license_plate').val(v.license_plate);
                        $('#brand').val(v.brand);
                        $('#type').val(v.type);
                        $('#engine_number').val(v.engine_number);
                        $('#chassis_number').val(v.chassis_number);

                        // set customer di select2
                        if (v.client) {
                            let option = new Option(v.client.nama_client, v.id_client, true, true);
                            $('#id_client').append(option).trigger('change');
                        }

                        // tampilkan preview foto
                        if (v.photo) {
                            $('#photo-preview').attr('src', '/uploads/vehicle/' + v.photo).show();
                        } else {
                            $('#photo-preview').attr('src', '').hide();
                        }

                        $('#vehicleModal .modal-title').text('Edit Vehicle');
                        $('#vehicleModal').modal('show');
                    });
                });

                // hapus
                $(document).on('click', '.btn-delete', function() {
                    let id = $(this).data('id');
                    Swal.fire({
                        title: 'Yakin hapus?',
                        text: "Data tidak bisa dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, hapus'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/vehicles/${id}`,
                                method: 'DELETE',
                                data: {
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function(res) {
                                    loadVehicles(currentPage, searchQuery);
                                    Swal.fire('Deleted!', res.message, 'success');
                                }
                            });
                        }
                    });
                });

                // preview foto sebelum upload
                $('#photo').on('change', function() {
                    let reader = new FileReader();
                    reader.onload = (e) => {
                        $('#photo-preview').attr('src', e.target.result).show();
                    };
                    reader.readAsDataURL(this.files[0]);
                });

                $('#add-client-btn').on('click', function() {
                    $('#clientForm')[0].reset();
                    $('#id_client').val('');
                    $('#clientModal .modal-title').text('Tambah Client');
                    $('#clientModal').modal('show');
                });

                $('#clientForm').on('submit', function(e) {
                    e.preventDefault();
                    let id = $('#id_client').val();
                    let url = id ? `/client/${id}` : "{{ route('client.store') }}";
                    let method = id ? 'PUT' : 'POST';

                    $.ajax({
                        url: url,
                        method: method,
                        data: {
                            nama_client: $('#nama_client').val(),
                            no_telp: $('#no_telp').val(),
                            no_ktp: $('#no_ktp').val(),
                            alamat: $('#alamat').val(),
                            hapus: $('#hapus').val(),
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            $('#clientModal').modal('hide');
                            Swal.fire('Success', res.message, 'success');
                        }
                    });
                });

            });
        </script>
    @endpush
@endsection
