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
                            <th>Date</th>
                            <th>Pemilik <br></th>
                            <th>Plat nomor</th>
                            <th>Referece</th>
                            <th>Status</th>
                            <th>Bayar</th>
                            <th>total</th>
                            <th>paid</th>
                            <th>due</th>
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
    @include('services.modal.detail-service')
    <!-- Modal Detail Service -->


    <!-- Modal Ubah Status -->
    @include('services.modal.edit-status-service')
    <!-- Modal Ubah Status -->


    <!-- Modal Ubah Status bayar-->
    @include('services.modal.edit-status-bayar')
    <!-- Modal Ubah Status bayar-->


    <!-- Modal Create Payment-->

    @include('services.modal.create-payment')


    <!-- Modal Create Payment-->





    @push('scripts')
        <script>
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(angka);
            }
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
                    console.log(res.data)
                    let rows = '';
                    let i = (res.current_page - 1) * res.per_page;
                    res.data.forEach(s => {
                        let btnEdit = '';
                        let btnDelete = '';

                        if (window.userPermissions.editService) {
                            btnEdit =
                                `<a href="{{ url('services') }}/${s.id}/edit" class="dropdown-item"><img
                                                            src="{{ asset('assets/assets/img/icons/edit.svg') }}" class="me-2" alt="img">Edit
                                                        Service</a>`;
                        }
                        if (window.userPermissions.deleteService) {
                            btnDelete =
                                ` <a type="button" data-url="{{ url('services') }}/${s.id}"
                                                        class="dropdown-item confirm-text btn-delete"><img
                                                            src="{{ asset('assets/assets/img/icons/delete1.svg') }}" class="me-2"
                                                            alt="img">Delete Service</a>`;
                        }
                        rows += `<tr>
                <td>${++i}</td>
                <td>${s.service_date}</td>
                <td>
                    ${s.vehicle.client?.nama_client || '-'}
                  </td>
                 <td>
                    ${s.vehicle.license_plate || '-'}
                </td>
                <td>${s.nomor_service || '-'}</td>
                <td>
                <span class="badges ${s.status === 'selesai' || s.status === 'diambil' ? 'bg-lightgreen' : 'bg-lightyellow'}">
                        ${s.status}
                    </span>
                </td>
                <td>
                <span class="badges ${s.status_bayar === 'lunas' ? 'bg-lightgreen' : s.status_bayar === 'cicil' ? 'bg-lightyellow' : s.status_bayar === 'belum bayar' ? 'bg-lightgrey' : 'bg-lightred'}">
                        ${s.status_bayar}
                    </span>
                </td>
                 <td>
                    ${formatRupiah(s.total_cost) || '-'}
                </td>
                <td class="text-green">
                    ${formatRupiah(s.total_paid) || '-'}
                </td>
                <td class="text-red">
                    ${formatRupiah(s.due_amount) || '-'}
                </td>
                <td class="text-center">
                                            <a class="action-set" href="javascript:void(0);" data-bs-toggle="dropdown"
                                                aria-expanded="true">
                                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{ url('services') }}/${s.id}" class="dropdown-item"><img
                                                            src="{{ asset('assets/assets/img/icons/eye1.svg') }}" class="me-2" alt="img">Service
                                                        Detail</a>
                                                </li>
                                                <li>
                                                        ${btnEdit}
                                                </li>
                                                <li>
                                                    <a type="button" class="dropdown-item btn-change-status" data-id="${s.id}" data-status="${s.status}"><img
                                                            src="{{ asset('assets/assets/img/icons/download.svg') }}" class="me-2"
                                                            alt="img">Ubah Status</a>
                                                </li>
                                                <li>
                                                    ${btnDelete}
                                                </li>
                                                <li>
                                                   <a type="button" class="dropdown-item btn-create-payment" data-id="${s.id}" data-nomor="${s.nomor_service}">
  <img src="{{ asset('assets/assets/img/icons/plus-circle.svg') }}" class="me-2" alt="img">
  Create Payment
</a>

                                                </li>
                                            </ul>


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
                <tr><th>Dibuat Oleh</th><td>${res.creator?.pengguna.nama  || '-'}</td></tr>
                <tr><th>Diperbarui Oleh</th><td>${res.updater?.pengguna.nama || '-'}</td></tr>
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

            // Ubah Status
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




            // Ubah Status
            $(document).on('click', '.btn-change-statusBayar', function() {
                let id = $(this).data('id');
                let status_bayar = $(this).data('status_bayar');

                $('#status_service_id').val(id);
                $('#new_status_bayar').val(status_bayar);
                $('#statusModalBayar').modal('show');
            });
            $(document).on('submit', '#form-status-service-bayar', function(e) {
                e.preventDefault();
                let id = $('#status_service_id').val();
                let status_bayar = $('#new_status_bayar').val();

                $.ajax({
                    url: "{{ url('services') }}/" + id + "/statusBayar",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        status_bayar: status_bayar
                    },
                    success: function(res) {
                        $('#statusModalBayar').modal('hide');
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


            // $(document).on('click', '.btn-create-payment', function() {

            //     let id = $(this).data('id');
            //     let nomorService = $(this).data('nomor');

            //     console.log('Nomor Service:', nomorService);

            //     // Pastikan modal ditutup dan dibuka ulang agar form siap
            //     $('#createpayment').modal('show');

            //     // Tunggu modal benar-benar muncul baru isi input
            //     $('#createpayment').on('shown.bs.modal', function() {
            //         $('#form-create-payment')[0].reset(); // reset di sini

            //         $('#payment_service_id').val(id);
            //         $('#reference').val(nomorService || '-');
            //     });
            // });

            // // Submit form payment
            // $(document).on('submit', '#form-create-payment', function(e) {
            //     e.preventDefault();

            //     let serviceId = $('#payment_service_id').val();
            //     let formData = $(this).serialize();

            //     $.ajax({
            //         url: "{{ url('service-payments') }}/" + serviceId, // Route ke controller store()
            //         type: "POST",
            //         data: formData,
            //         success: function(res) {
            //             if (res.status) {
            //                 Swal.fire({
            //                     title: 'Berhasil!',
            //                     text: res.message,
            //                     icon: 'success',
            //                     timer: 1500,
            //                     showConfirmButton: false
            //                 });
            //                 $('#createpayment').modal('hide');
            //                 loadServices(currentPage, searchQuery);
            //             } else {
            //                 Swal.fire('Gagal', res.message, 'error');
            //             }
            //         },
            //         error: function(xhr) {
            //             Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
            //         }
            //     });
            // });

            // ===================================================
// 1️⃣ BUKA MODAL PEMBAYARAN
// ===================================================
$(document).on('click', '.btn-create-payment', function () {
    let id = $(this).data('id');
    let nomorService = $(this).data('nomor');

    // Reset dan siapkan modal
    $('#form-create-payment')[0].reset();
    $('#payment_service_id').val(id);
    $('#reference').val(nomorService || '-');
    $('#payment-detail-jasa, #payment-detail-sparepart').html('');
    $('#sisa-bayar').text('Rp 0');
    $('#kembalian').val('Rp 0');
    $('#btn-print-payment').addClass('d-none');
    $('#btn-save-payment').removeClass('d-none');

    $('#createpayment').modal('show');

    // Ambil data service dan detail pembayaran
    $.ajax({
        url: `/services/${id}/payment-detail`,
        type: 'GET',
        success: function (res) {
            if (res.status) {
                const data = res.data;

                // ==== Isi Tabel Jasa ====
                let jasaHtml = '';
                data.service.jobs.forEach(job => {
                    jasaHtml += `
                        <tr>
                            <td>${job.jasa?.nama_jasa ?? '-'}</td>
                            <td class="text-center">${job.qty}</td>
                            <td class="text-end">Rp ${Number(job.price).toLocaleString('id-ID')}</td>
                            <td class="text-end">Rp ${Number(job.subtotal).toLocaleString('id-ID')}</td>
                        </tr>`;
                });
                $('#payment-detail-jasa').html(jasaHtml);

                // ==== Isi Tabel Sparepart ====
                let spHtml = '';
                data.service.spareparts.forEach(sp => {
                    spHtml += `
                        <tr>
                            <td>${sp.barang?.nama_barang ?? '-'}</td>
                            <td class="text-center">${sp.qty}</td>
                            <td class="text-end">Rp ${Number(sp.price).toLocaleString('id-ID')}</td>
                            <td class="text-end">Rp ${Number(sp.subtotal).toLocaleString('id-ID')}</td>
                        </tr>`;
                });
                $('#payment-detail-sparepart').html(spHtml);

                // ==== Total & Sisa Bayar ====
                $('#sisa-bayar').text(`Rp ${Number(data.sisa_bayar).toLocaleString('id-ID')}`);
            }
        },
        error: function () {
            Swal.fire('Error', 'Gagal memuat data service.', 'error');
        }
    });
});

// ===================================================
// 2️⃣ HITUNG OTOMATIS KEMBALIAN
// ===================================================
$(document).on('input', '#input-bayar', function () {
    const total = parseFloat($('#sisa-bayar').text().replace(/[^\d]/g, '')) || 0;
    const bayar = parseFloat($(this).val().replace(/[^\d]/g, '')) || 0;
    const kembali = bayar - total;
    $('#kembalian').val(kembali > 0 ? `Rp ${kembali.toLocaleString('id-ID')}` : 'Rp 0');
});

// ===================================================
// 3️⃣ SIMPAN PEMBAYARAN VIA AJAX
// ===================================================
$(document).on('submit', '#form-create-payment', function (e) {
    e.preventDefault();

    const serviceId = $('#payment_service_id').val();
    const formData = $(this).serialize();

    $.ajax({
        url: `/service-payments/${serviceId}`,
        type: 'POST',
        data: formData,
        success: function (res) {
            if (res.status) {
                Swal.fire({
                    title: 'Berhasil!',
                    text: res.message,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });

                // Sembunyikan tombol save dan tampilkan tombol print
                $('#btn-save-payment').addClass('d-none');
                $('#btn-print-payment').removeClass('d-none')
                    .off('click')
                    .on('click', function () {
                        window.open(`/service-payments/print/${res.payment_id}`, '_blank');
                    });

                // Jika ingin reload tabel utama (opsional)
                if (typeof loadServices === 'function') loadServices();
            } else {
                Swal.fire('Gagal', res.message, 'error');
            }
        },
        error: function (xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'Terjadi kesalahan.', 'error');
        }
    });
});

// ===================================================
// 4️⃣ RESET MODAL SAAT DITUTUP
// ===================================================
$('#createpayment').on('hidden.bs.modal', function () {
    $('#form-create-payment')[0].reset();
    $('#payment-detail-jasa, #payment-detail-sparepart').html('');
    $('#sisa-bayar, #kembalian').text('Rp 0');
    $('#btn-save-payment').show();
    $('#btn-print-payment').hide();
});



        </script>
    @endpush
@endsection
