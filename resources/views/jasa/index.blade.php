@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Daftar Jasa</h4>
        <h6>Manajemen Data Jasa</h6>
    </div>
    <div class="page-btn">
        <button class="btn btn-added" id="btn-add">+ Tambah Jasa</button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="search" class="form-control mb-3" placeholder="Cari jasa...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table" id="jasa-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Jasa</th>
                        <th>Harga Jasa</th>
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
<div class="modal fade" id="jasaModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="jasaForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Jasa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="id_jasa">
            <div class="mb-2">
                <label>Nama Jasa</label>
                <input type="text" id="nama_jasa" class="form-control" required>
            </div>
            <div class="mb-2">
                <label>Harga Jasa</label>
                <input type="number" id="harga_jasa" class="form-control" required>
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

function loadJasa(page = 1, search = '') {
    $.get("{{ route('jasa.data') }}", { page: page, search: search }, function(res) {
        let rows = '';
        let i = (res.current_page - 1) * res.per_page;
        res.data.forEach(j => {
            rows += `
                <tr>
                    <td>${++i}</td>
                    <td>${j.nama_jasa}</td>
                    <td>${formatRupiah(j.harga_jasa)}</td>
                    <td>
                        <button class="btn btn-sm btn-warning btn-edit" data-id="${j.id_jasa}">Edit</button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${j.id_jasa}">Delete</button>
                    </td>
                </tr>`;
        });
        $('#jasa-table tbody').html(rows);

        // pagination
        let pagination = '';
        const totalPages = res.last_page;
        const current = res.current_page;
        const delta = 2;
        if (res.prev_page_url) {
            pagination += `<li class="page-item"><a class="page-link" href="#" onclick="loadJasa(${current - 1}, searchQuery)">Prev</a></li>`;
        }
        for (let i = Math.max(1, current - delta); i <= Math.min(totalPages, current + delta); i++) {
            pagination += `<li class="page-item ${current === i ? 'active' : ''}">
                              <a class="page-link" href="#" onclick="loadJasa(${i}, searchQuery)">${i}</a>
                           </li>`;
        }
        if (res.next_page_url) {
            pagination += `<li class="page-item"><a class="page-link" href="#" onclick="loadJasa(${current + 1}, searchQuery)">Next</a></li>`;
        }
        $('#pagination').html(pagination);
    });
}

$(document).ready(function() {
    loadJasa();

    $('#search').on('keyup', function() {
        searchQuery = $(this).val();
        loadJasa(1, searchQuery);
    });

    $('#btn-add').on('click', function() {
        $('#jasaForm')[0].reset();
        $('#id_jasa').val('');
        $('#jasaModal .modal-title').text('Tambah Jasa');
        $('#jasaModal').modal('show');
    });

    $('#jasaForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#id_jasa').val();
        let url = id ? `/jasa/${id}` : "{{ route('jasa.store') }}";
        let method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: {
                nama_jasa: $('#nama_jasa').val(),
                harga_jasa: $('#harga_jasa').val(),
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                $('#jasaModal').modal('hide');
                loadJasa(currentPage, searchQuery);
                Swal.fire('Success', res.message, 'success');
            }
        });
    });

    $(document).on('click', '.btn-edit', function() {
        let id = $(this).data('id');
        $.get(`/jasa/${id}`, function(j) {
            $('#id_jasa').val(j.id_jasa);
            $('#nama_jasa').val(j.nama_jasa);
            $('#harga_jasa').val(j.harga_jasa);
            $('#jasaModal .modal-title').text('Edit Jasa');
            $('#jasaModal').modal('show');
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
                    url: `/jasa/${id}`,
                    method: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        loadJasa(currentPage, searchQuery);
                        Swal.fire('Deleted!', res.message, 'success');
                    }
                });
            }
        });
    });
});
</script>
@endpush
