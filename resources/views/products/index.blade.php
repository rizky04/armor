@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Daftar Barang</h4>
        <h6>Manajemen Data Barang</h6>
    </div>
    <div class="page-btn">
        <button class="btn btn-added" id="btn-add">+ Tambah Barang</button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="search" class="form-control mb-3" placeholder="Cari barang...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table" id="barang-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Merk</th>
                        <th>Keterangan</th>
                        <th>Lokasi</th>
                        <th>Stok</th>
                        <th>Pagu</th>
                        <th>Harga Kulak</th>
                        <th>Harga Jual</th>
                        <th>Distributor</th>
                        <th>Jenis</th>
                        <th>Hapus?</th>
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

<!-- Modal Create/Edit -->
<div class="modal fade" id="barangModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form id="barangForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Barang</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="TEXT" id="id_barang">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label>Kode Barang</label>
                    <input type="text" id="kode_barang" class="form-control">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Nama Barang</label>
                    <input type="text" id="nama_barang" class="form-control">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Merk</label>
                    <input type="text" id="merk_barang" class="form-control">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Lokasi</label>
                    <input type="text" id="lokasi" class="form-control">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Stok</label>
                    <input type="number" id="stok_barang" class="form-control">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Pagu</label>
                    <input type="number" id="pagu" class="form-control">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Harga Kulak</label>
                    <input type="number" id="harga_kulak" class="form-control">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Harga Jual</label>
                    <input type="number" id="harga_jual" class="form-control">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Jenis</label>
                    <input type="text" id="jenis" class="form-control">
                </div>
                 <div class="col-md-6 mb-2">
                    <label>distributor</label>
                    <input type="text" id="distributor" class="form-control">
                </div>
                <div class="col-md-6 mb-2">
                    <label>Hapus?</label>
                    <select id="hapus" class="form-control">
                        <option value="0">Tidak</option>
                        <option value="1">Ya</option>
                    </select>
                </div>
                <div class="col-md-12 mb-2">
                    <label>Keterangan</label>
                    <textarea id="keterangan" class="form-control"></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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

function formatRupiah(angka) {
    if (!angka) return "Rp 0";
    return "Rp " + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function loadBarang(page = 1, search = '') {
    $.get("{{ route('products.data') }}", { page: page, search: search }, function(res) {
        let rows = '';
        let i = (res.current_page - 1) * res.per_page;
        res.data.forEach(b => {
            rows += `
                <tr>
                    <td>${++i}</td>
                    <td>${b.kode_barang}</td>
                    <td>${b.nama_barang}</td>
                    <td>${b.merk_barang ?? ''}</td>
                    <td>${b.keterangan ?? ''}</td>
                    <td>${b.lokasi ?? ''}</td>
                    <td>${b.stok_barang ?? 0}</td>
                    <td>${b.pagu ?? 0}</td>
                    <td>${formatRupiah(b.harga_kulak ?? 0)}</td>
                    <td>${formatRupiah(b.harga_jual ?? 0)}</td>
                    <td>${b.distributor ?? ''}</td>
                    <td>${b.jenis ?? ''}</td>
                    <td>${b.hapus == 1 ? 'Ya' : 'Tidak'}</td>
                    <td>
                        <button class="btn btn-sm btn-warning btn-edit" data-id="${b.id_barang}">Edit</button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${b.id_barang}">Delete</button>
                    </td>
                </tr>`;
        });
        $('#barang-table tbody').html(rows);

        // pagination (tetap sama seperti sebelumnya)
        let pagination = '';
        const totalPages = res.last_page;
        const current = res.current_page;
        const delta = 2;
        if (res.prev_page_url) {
            pagination += `<li class="page-item"><a class="page-link" href="#" onclick="loadBarang(${current - 1}, searchQuery)">Prev</a></li>`;
        }
        for (let i = Math.max(1, current - delta); i <= Math.min(totalPages, current + delta); i++) {
            pagination += `<li class="page-item ${current === i ? 'active' : ''}">
                              <a class="page-link" href="#" onclick="loadBarang(${i}, searchQuery)">${i}</a>
                           </li>`;
        }
        if (res.next_page_url) {
            pagination += `<li class="page-item"><a class="page-link" href="#" onclick="loadBarang(${current + 1}, searchQuery)">Next</a></li>`;
        }
        $('#pagination').html(pagination);
    });
}

$(document).ready(function() {
    loadBarang();

    $('#search').on('keyup', function() {
        searchQuery = $(this).val();
        loadBarang(1, searchQuery);
    });

    $('#btn-add').on('click', function() {
        $('#barangForm')[0].reset();
        $('#id_barang').val('');
        $('#barangModal .modal-title').text('Tambah Barang');
        $('#barangModal').modal('show');
    });

    $('#barangForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#id_barang').val();
        let url = id ? `/products/${id}` : "{{ route('products.store') }}";
        let method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: {
                kode_barang: $('#kode_barang').val(),
                nama_barang: $('#nama_barang').val(),
                merk_barang: $('#merk_barang').val(),
                lokasi: $('#lokasi').val(),
                stok_barang: $('#stok_barang').val(),
                pagu: $('#pagu').val(),
                harga_kulak: $('#harga_kulak').val(),
                harga_jual: $('#harga_jual').val(),
                distributor: $('#distributor').val(),
                jenis: $('#jenis').val(),
                hapus: $('#hapus').val(),
                keterangan: $('#keterangan').val(),
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                $('#barangModal').modal('hide');
                loadBarang(currentPage, searchQuery);
                Swal.fire('Success', res.message, 'success');
            }
        });
    });

    $(document).on('click', '.btn-edit', function() {
        let id = $(this).data('id');
        $.get(`/products/${id}`, function(b) {
            $('#id_barang').val(b.id_barang);
            $('#kode_barang').val(b.kode_barang);
            $('#nama_barang').val(b.nama_barang);
            $('#merk_barang').val(b.merk_barang);
            $('#lokasi').val(b.lokasi);
            $('#stok_barang').val(b.stok_barang);
            $('#pagu').val(b.pagu);
            $('#harga_kulak').val(b.harga_kulak);
            $('#harga_jual').val(b.harga_jual);
            $('#distributor').val(b.distributor);
            $('#jenis').val(b.jenis);
            $('#hapus').val(b.hapus);
            $('#keterangan').val(b.keterangan);
            $('#barangModal .modal-title').text('Edit Barang');
            $('#barangModal').modal('show');
        });
    });

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
                    url: `/products/${id}`,
                    method: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        loadBarang(currentPage, searchQuery);
                        Swal.fire('Deleted!', res.message, 'success');
                    }
                });
            }
        });
    });
});
</script>
@endpush
