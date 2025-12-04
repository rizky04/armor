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
                            <th>Pemilik <br></th>
                            <th>Plat nomor</th>
                            <th>Referece</th>
                            <th>Status</th>
                            <th>Bayar</th>
                            <th>total</th>
                            <th>paid</th>
                            <th>due</th>
                            <th>Action</th>
                            <th>Detail</th>
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

 window.userPermissions = {
                editService: @json(auth()->user()->can('menu-service-edit')),
                deleteService: @json(auth()->user()->can('menu-service-delete'))
            };
   function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(angka);
            }
            let currentPage = 1;
            let searchQuery = '';


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
                                                <li>
                                                <a href="services/${s.id}/print" class="dropdown-item">
                                                            <i class="fa-solid fa-print me-2"></i> Cetak
                                                    </a>

                                                </li>
                                            </ul>
                                            </td>
                                            <td>
                                           <button class="btn btn-sm btn-info btn-toggle-detail" data-id="${s.id}"><i class="fa-solid fa-eye"></i></button>
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



            // Klik tombol show

        </script>
        <script src="{{asset('function/service/delete-service.js')}}"></script>
        <script src="{{asset('function/service/status-service.js')}}"></script>
        <script src="{{asset('function/service/status-bayar.js')}}"></script>
        <script src="{{asset('function/service/pembayaran-service.js')}}"></script>
        <script src="{{asset('function/service/detail-service.js')}}"></script>
    @endpush
@endsection
