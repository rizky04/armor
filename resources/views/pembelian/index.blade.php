@extends('layouts.main')

@section('content')
    <div class="page-header">
        <div class="page-title">
            <h4>Data Pembelian Barang</h4>
            <h6>Catat pembelian dan pembaruan stok</h6>
        </div>
        <div class="page-btn">
            @can('master-data-pembelian-create')
                <button class="btn btn-added" id="btn-add">+ Tambah Pembelian</button>
            @endcan
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-4">
                    <input type="text" id="search" class="form-control" placeholder="Cari barang...">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table" id="pembelian-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Kode Sistem</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Merk</th>
                            <th>Keterangan</th>
                            <th>Jenis</th>
                            <th>Harga Kulak</th>
                            <th>Harga Jual</th>
                            <th>Jumlah Beli</th>
                            <th>Stok Sekarang</th>
                            <th>Stok Akhir</th>
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

    <!-- Modal Pembelian -->
    <div class="modal fade" id="pembelianModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="pembelianForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Pembelian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="id_pembelian">

                        <div class="mb-2">
                            <label>Pilih Barang</label>
                            <select id="id_barang" class="form-control"></select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Harga Kulak Lama</label>
                                    <input type="text" id="harga_kulak_lama" class="form-control" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label>Harga Jual Lama</label>
                                    <input type="text" id="harga_jual_lama" class="form-control" readonly>
                                </div>
                            </div>

                        </div>
                        <div class="mb-2">
                            <label>Stok Sekarang</label>
                            <input type="text" id="stok_sekarang" class="form-control" readonly>
                        </div>

                        <div class="mb-2">
                            <label>Jumlah Pembelian</label>
                            <input type="number" id="jumlah_pembelian" class="form-control" min="1" required>
                        </div>
                        @can('master-data-pembelian-harga-baru')
                            <div class="mb-2">
                                <label>Harga Kulak Baru</label>
                                <input type="number" id="harga_kulak" class="form-control" required>
                            </div>

                            <div class="mb-2">
                                <label>Harga Jual Baru</label>
                                <input type="number" id="harga_jual" class="form-control" required>
                            </div>
                            @else
                            <input type="hidden" id="harga_kulak" class="form-control" required>
                            <input type="hidden" id="harga_jual" class="form-control" required>
                            @endcan
                        <div class="mb-2">
                            <label>Stok Akhir (Otomatis)</label>
                            <input type="text" id="stok_akhir" class="form-control" readonly>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        let currentPage = 1;
        let searchQuery = '';

         window.userPermissions = {
                editPembelian: @json(auth()->user()->can('master-data-pembelian-edit')),
                deletePembelian: @json(auth()->user()->can('master-data-pembelian-delete')),
            };

        function loadPembelian(page = 1, search = '') {
            $.get("{{ route('pembelian.data') }}", {
                page,
                search
            }, function(res) {
                let rows = '';
                let i = (res.current_page - 1) * res.per_page;

                res.data.forEach(p => {
                    let btnEdit = '', btnDelete = '';
                    let stok_akhir = (p.barang.stok_barang ?? 0);
                    if (window.userPermissions.editPembelian)
                btnEdit = `<button class="btn btn-sm btn-warning btn-edit" data-id="${p.id_pembelian}">Edit</button>`;
            if (window.userPermissions.deletePembelian)
                btnDelete = `<button class="btn btn-sm btn-danger btn-delete" data-id="${p.id_pembelian}">Delete</button>`;
                    rows += `
        <tr>
          <td>${++i}</td>
          <td>${p.barang.id_barang}</td>
            <td>${p.barang.kode_barang}</td>
          <td>${p.barang.nama_barang}</td>
            <td>${p.barang.merk_barang}</td>
            <td>${p.barang.keterangan}</td>
            <td>${p.barang.jenis}</td>
          <td>${p.harga_kulak}</td>
          <td>${p.harga_jual}</td>
          <td>${p.jumlah_pembelian}</td>
          <td>${stok_akhir - p.jumlah_pembelian}</td>
          <td>${stok_akhir}</td>
          <td>
            ${btnEdit}
            ${btnDelete}
           </td>
        </tr>`;
                });

                $('#pembelian-table tbody').html(rows);

                // === PAGINATION ===
                let pagination = '';
                const totalPages = res.last_page;
                const current = res.current_page;
                const delta = 2;

                if (res.prev_page_url) {
                    pagination +=
                        `<li class="page-item"><a class="page-link" href="#" onclick="loadPembelian(${current - 1}, searchQuery)">Prev</a></li>`;
                }

                for (let i = Math.max(1, current - delta); i <= Math.min(totalPages, current + delta); i++) {
                    pagination += `<li class="page-item ${current === i ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="loadPembelian(${i}, searchQuery)">${i}</a>
                     </li>`;
                }

                if (res.next_page_url) {
                    pagination +=
                        `<li class="page-item"><a class="page-link" href="#" onclick="loadPembelian(${current + 1}, searchQuery)">Next</a></li>`;
                }

                $('#pagination').html(pagination);
            });
        }

        $('#search').on('keyup', function() {
            searchQuery = $(this).val();
            loadPembelian(1, searchQuery);
        });

        function initSelect2Barang() {
            $('#id_barang').select2({
                dropdownParent: $('#pembelianModal'),
                placeholder: 'Pilih Barang...',
                allowClear: true,
                ajax: {
                    url: "{{ route('select.barang') }}",
                    dataType: 'json',
                    delay: 250,
                    data: params => ({
                        q: params.term
                    }),
                    processResults: data => ({
                        results: data
                    })
                }
            });

            // isi otomatis harga & stok
            $('#id_barang').on('select2:select', function(e) {
                const data = e.params.data;
                $('#harga_kulak_lama').val(data.harga_kulak);
                $('#harga_jual_lama').val(data.harga_jual);
                $('#harga_kulak').val(data.harga_kulak);
                $('#harga_jual').val(data.harga_jual);
                $('#stok_sekarang').val(data.stok);
            });
        }

        $(document).ready(function() {
            loadPembelian();
            initSelect2Barang();

            $('#btn-add').on('click', function() {
                $('#pembelianForm')[0].reset();
                $('#id_pembelian').val('');
                $('#id_barang').val(null).trigger('change').prop('disabled', false);
                $('.modal-title').text('Tambah Pembelian');
                $('#pembelianModal').modal('show');
            });

            $('#jumlah_pembelian').on('input', function() {
                let jumlah = parseInt($(this).val()) || 0;
                let stok = parseInt($('#stok_sekarang').val()) || 0;
                $('#stok_akhir').val(stok + jumlah);
            });

            // Tombol Edit
            $(document).on('click', '.btn-edit', function() {
                let id = $(this).data('id');
                $.get(`/pembelian/${id}/edit`, function(res) {
                    $('#id_pembelian').val(res.id_pembelian);
                    $('#jumlah_pembelian').val(res.jumlah_pembelian);
                    $('#harga_kulak').val(res.harga_kulak);
                    $('#harga_jual').val(res.harga_jual);
                    $('#harga_kulak_lama').val(res.barang.harga_kulak);
                    $('#harga_jual_lama').val(res.barang.harga_jual);
                    $('#stok_sekarang').val(res.barang.stok_barang - res.jumlah_pembelian);
                    $('#stok_akhir').val(res.barang.stok_barang);

                    // tampilkan barang di Select2
                    let option = new Option(res.barang.nama_barang, res.id_barang, true, true);
                    $('#id_barang').append(option).trigger('change').prop('disabled', true);

                    $('.modal-title').text('Edit Pembelian');
                    $('#pembelianModal').modal('show');
                });
            });

            // Simpan (create/update)
            $('#pembelianForm').on('submit', function(e) {
                e.preventDefault();
                let id = $('#id_pembelian').val();
                let url = id ? `/pembelian/${id}` : `{{ route('pembelian.store') }}`;
                let method = id ? 'PUT' : 'POST';

                $.ajax({
                    url: url,
                    method: method,
                    data: {
                        _token: "{{ csrf_token() }}",
                        id_barang: $('#id_barang').val(),
                        jumlah_pembelian: $('#jumlah_pembelian').val(),
                        harga_kulak: $('#harga_kulak').val(),
                        harga_jual: $('#harga_jual').val(),
                    },
                    success: function(res) {
                        $('#pembelianModal').modal('hide');
                        loadPembelian();
                        Swal.fire('Berhasil', res.message, 'success');
                    }
                });
            });

            // Hapus
            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                Swal.fire({
                    title: 'Yakin hapus pembelian ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus'
                }).then((r) => {
                    if (r.isConfirmed) {
                        $.ajax({
                            url: `/pembelian/${id}`,
                            method: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                loadPembelian();
                                Swal.fire('Dihapus', res.message, 'success');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
