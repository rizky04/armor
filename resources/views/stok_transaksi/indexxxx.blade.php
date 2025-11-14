@extends('layouts.main')

@section('content')
<div class="container-fluid mt-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Transaksi Barang</h5>
            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#barangModal">
                <i class="bi bi-plus-circle"></i> Tambah Transaksi
            </button>
        </div>
        <div class="card-body">
            <div class="d-flex mb-3">
                <input type="text" id="search" class="form-control me-2" placeholder="Cari barang...">
                <select id="filter_tipe" class="form-select w-auto">
                    <option value="">Semua</option>
                    <option value="keluar">Barang Keluar</option>
                    <option value="rusak">Barang Rusak</option>
                    <option value="return_pembelian">Return Pembelian</option>
                    <option value="return_penjualan">Return Penjualan</option>
                </select>
            </div>

            <table id="barangTable" class="table table-bordered table-striped">
                <thead class="table-secondary">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Jenis Transaksi</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>

            <nav>
                <ul id="pagination" class="pagination justify-content-center"></ul>
            </nav>
        </div>
    </div>
</div>

<!-- Modal Tambah Transaksi -->
<div class="modal fade" id="barangModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Transaksi Barang</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="formTransaksi">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Barang</label>
                        <select id="id_barang" name="id_barang" class="form-select" required></select>
                    </div>
                    <div class="mb-3">
                        <label>Jumlah</label>
                        <input type="number" name="jumlah" id="jumlah" class="form-control" required min="1">
                    </div>
                    <div class="mb-3">
                        <label>Jenis Transaksi</label>
                        <select name="jenis_transaksi" id="jenis_transaksi" class="form-select" required>
                            <option value="keluar">Barang Keluar</option>
                            <option value="rusak">Barang Rusak</option>
                            <option value="return_pembelian">Return Pembelian</option>
                            <option value="return_penjualan">Return Penjualan</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Keterangan</label>
                        <textarea name="keterangan" id="keterangan" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary" type="submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')


<script>
$(function() {
    loadData();

    $('#search, #filter_tipe').on('input change', function() {
        loadData();
    });

    // === LOAD DATA DENGAN PAGINATION ===
    function loadData(page = 1) {
        let search = $('#search').val();
        let jenis = $('#filter_tipe').val();

        $.get(`{{ route('stok-transaksi.data') }}`, { search, jenis, page }, function(res) {
            let rows = '';
            $.each(res.data, function(i, item) {
                rows += `
                    <tr>
                        <td>${item.barang.kode_barang}</td>
                        <td>${item.barang.nama_barang}</td>
                        <td>${item.jumlah}</td>
                        <td>${item.jenis_transaksi}</td>
                        <td>${item.created_at}</td>
                        <td>
                            <button class="btn btn-danger btn-sm btn-delete" data-id="${item.id}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            });
            $('#barangTable tbody').html(rows);

            // === PAGINATION ===
            let pagination = '';
            if (res.links) {
                $.each(res.links, function(i, link) {
                    if (link.url) {
                        pagination += `
                            <li class="page-item ${link.active ? 'active' : ''}">
                                <a href="#" class="page-link" data-page="${link.url.split('page=')[1]}">${link.label}</a>
                            </li>
                        `;
                    } else {
                        pagination += `<li class="page-item disabled"><span class="page-link">${link.label}</span></li>`;
                    }
                });
            }
            $('#pagination').html(pagination);
        });
    }

    // Klik pagination
    $(document).on('click', '#pagination a', function(e) {
        e.preventDefault();
        let page = $(this).data('page');
        if (page) loadData(page);
    });

    // === SIMPAN TRANSAKSI ===
    $('#formTransaksi').on('submit', function(e) {
        e.preventDefault();

        $.ajax({
            url: `{{ route('stok-transaksi.store') }}`,
            method: 'POST',
            data: $(this).serialize(),
            success: function(res) {
                Swal.fire('Berhasil', res.message, 'success');
                $('#barangModal').modal('hide');
                $('#formTransaksi')[0].reset();
                $('#id_barang').val(null).trigger('change');
                loadData();
            },
            error: function(err) {
                Swal.fire('Gagal', 'Terjadi kesalahan!', 'error');
            }
        });
    });

    // === DELETE TRANSAKSI ===
    $(document).on('click', '.btn-delete', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('stok-transaksi') }}/${id}`,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire('Berhasil', res.message, 'success');
                        loadData();
                    }
                });
            }
        });
    });

    // === SELECT2 BARANG ===
    $('#id_barang').select2({
        dropdownParent: $('#barangModal'),
        placeholder: 'Pilih Barang...',
        ajax: {
            url: "{{ route('select.barang') }}",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return { q: params.term };
            },
            processResults: function(data) {
                return {
                    results: data.map(item => ({
                        id: item.id_barang,
                        text: `${item.kode_barang} - ${item.nama_barang} (${item.merk_barang ?? '-'})`
                    }))
                };
            }
        },
        allowClear: true
    });
});
</script>
@endpush

@endsection

