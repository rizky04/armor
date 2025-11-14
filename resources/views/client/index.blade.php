@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Daftar Client</h4>
        <h6>Manajemen Data Client</h6>
    </div>
    <div class="page-btn">
        <button class="btn btn-added" id="btn-add">+ Tambah Client</button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="search" class="form-control mb-3" placeholder="Cari client...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table" id="client-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Client</th>
                        <th>No. Telp</th>
                        <th>No. KTP</th>
                        <th>Alamat</th>
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
<div class="modal fade" id="clientModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="clientForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Client</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="id_client" >
            <div class="mb-2">
                <label>Nama Client</label>
                <input type="text" id="nama_client" class="form-control" required>
            </div>
            <div class="mb-2">
                <label>No. Telp</label>
                <input type="text" id="no_telp" class="form-control" required>
            </div>
            <div class="mb-2">
                <label>No. KTP</label>
                <input type="text" id="no_ktp" class="form-control" required>
            </div>
            <div class="mb-2">
                <label>Alamat</label>
                <textarea id="alamat" class="form-control" required></textarea>
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

function loadClient(page = 1, search = '') {
    $.get("{{ route('client.data') }}", { page: page, search: search }, function(res) {
        let rows = '';
        let i = (res.current_page - 1) * res.per_page;
        res.data.forEach(c => {
            console.log(c);
            rows += `
                <tr>
                    <td>${++i}</td>
                    <td>${c.nama_client}</td>
                    <td>${c.no_telp}</td>
                    <td>${c.no_ktp}</td>
                    <td>${c.alamat}</td>
                    <td>
                        <button class="btn btn-sm btn-warning btn-edit" data-id="${c.id_client}">Edit</button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${c.id_client}">Delete</button>
                    </td>
                </tr>`;
        });
        $('#client-table tbody').html(rows);

        // pagination
        let pagination = '';
        const totalPages = res.last_page;
        const current = res.current_page;
        const delta = 2;
        if (res.prev_page_url) {
            pagination += `<li class="page-item"><a class="page-link" href="#" onclick="loadClient(${current - 1}, searchQuery)">Prev</a></li>`;
        }
        for (let i = Math.max(1, current - delta); i <= Math.min(totalPages, current + delta); i++) {
            pagination += `<li class="page-item ${current === i ? 'active' : ''}">
                              <a class="page-link" href="#" onclick="loadClient(${i}, searchQuery)">${i}</a>
                           </li>`;
        }
        if (res.next_page_url) {
            pagination += `<li class="page-item"><a class="page-link" href="#" onclick="loadClient(${current + 1}, searchQuery)">Next</a></li>`;
        }
        $('#pagination').html(pagination);
    });
}

$(document).ready(function() {
    loadClient();

    $('#search').on('keyup', function() {
        searchQuery = $(this).val();
        loadClient(1, searchQuery);
    });

    $('#btn-add').on('click', function() {
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
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                $('#clientModal').modal('hide');
                loadClient(currentPage, searchQuery);
                Swal.fire('Success', res.message, 'success');
            }
        });
    });

    $(document).on('click', '.btn-edit', function() {
        let id = $(this).data('id');
        $.get(`/client/${id}`, function(c) {
            $('#id_client').val(c.id_client);
            $('#nama_client').val(c.nama_client);
            $('#no_telp').val(c.no_telp);
            $('#no_ktp').val(c.no_ktp);
            $('#alamat').val(c.alamat);
            $('#clientModal .modal-title').text('Edit Client');
            $('#clientModal').modal('show');
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
                    url: `/client/${id}`,
                    method: 'DELETE',
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        loadClient(currentPage, searchQuery);
                        Swal.fire('Deleted!', res.message, 'success');
                    }
                });
            }
        });
    });
});
</script>
@endpush
