@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Stok Opname Barang</h4>
        <h6>Periksa dan perbarui stok fisik barang</h6>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="search" class="form-control" placeholder="Cari barang...">
            </div>
            {{-- <div class="col-md-3">
                <select id="jenis" class="form-control">
                    <option value="">Semua Jenis</option>
                    <option value="Sparepart">Sparepart</option>
                    <option value="Alat">Alat</option>
                    <option value="ATK">ATK</option>
                    <option value="Elektronik">Elektronik</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div> --}}
        </div>

        <div class="table-responsive">
            <table class="table table-striped" id="barang-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Aksi</th>
                        <th>Stok Sistem</th>
                        <th>Kode Sistem</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Merk</th>
                        <th>Jenis</th>
                        <th>keterangan</th>
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

<!-- Modal Update Stok -->
<div class="modal fade" id="stokModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="stokForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Update Stok Barang</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="id_barang">
            <div class="mb-2">
                <label>Nama Barang</label>
                <input type="text" id="nama_barang" class="form-control" readonly>
            </div>
            <div class="mb-2">
                <label>Stok Sistem</label>
                <input type="number" id="stok_sistem" class="form-control" readonly>
            </div>
            <div class="mb-2">
                <label>Stok Fisik</label>
                <input type="number" id="stok_fisik" class="form-control" required>
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
let jenisFilter = '';

function loadBarang(page = 1, search = '', jenis = '') {
    $.get("{{ route('stok-opname.data') }}", { page: page, search: search, jenis: jenis }, function(res) {
        let rows = '';
        let i = (res.current_page - 1) * res.per_page;
        res.data.forEach(b => {
            rows += `
                <tr>
                    <td>${++i}</td>
                    <td>
                        <button class="btn btn-sm btn-warning btn-edit" data-id="${b.id_barang}" data-nama="${b.nama_barang}" data-stok="${b.stok_barang}">Opname</button>
                    </td>
                    <td>${b.stok_barang}</td>
                    <td>${b.id_barang}</td>
                    <td>${b.kode_barang}</td>
                    <td>${b.nama_barang}</td>
                    <td>${b.merk_barang ?? '-'}</td>
                    <td>${b.jenis ?? '-'}</td>
                    <td>${b.keterangan ?? '-'}</td>
                </tr>`;
        });
        $('#barang-table tbody').html(rows);

        // pagination
        let pagination = '';
        const totalPages = res.last_page;
        const current = res.current_page;
        const delta = 2;
        if (res.prev_page_url) {
            pagination += `<li class="page-item"><a class="page-link" href="#" onclick="loadBarang(${current - 1}, searchQuery, jenisFilter)">Prev</a></li>`;
        }
        for (let i = Math.max(1, current - delta); i <= Math.min(totalPages, current + delta); i++) {
            pagination += `<li class="page-item ${current === i ? 'active' : ''}">
                              <a class="page-link" href="#" onclick="loadBarang(${i}, searchQuery, jenisFilter)">${i}</a>
                           </li>`;
        }
        if (res.next_page_url) {
            pagination += `<li class="page-item"><a class="page-link" href="#" onclick="loadBarang(${current + 1}, searchQuery, jenisFilter)">Next</a></li>`;
        }
        $('#pagination').html(pagination);
    });
}

$(document).ready(function() {
    loadBarang();

    $('#search').on('keyup', function() {
        searchQuery = $(this).val();
        loadBarang(1, searchQuery, jenisFilter);
    });

    $('#jenis').on('change', function() {
        jenisFilter = $(this).val();
        loadBarang(1, searchQuery, jenisFilter);
    });

    $(document).on('click', '.btn-edit', function() {
        $('#id_barang').val($(this).data('id'));
        $('#nama_barang').val($(this).data('nama'));
        $('#stok_sistem').val($(this).data('stok'));
        $('#stok_fisik').val('');
        $('#stokModal').modal('show');
    });

    $('#stokForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('stok-opname.update') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id_barang: $('#id_barang').val(),
                stok_fisik: $('#stok_fisik').val()
            },
            success: function(res) {
                $('#stokModal').modal('hide');
                Swal.fire('Berhasil!', res.message, 'success');
                loadBarang(currentPage, searchQuery, jenisFilter);
            }
        });
    });
});
</script>
@endpush
