@extends('layouts.main')

@section('content')
    <div class="page-header">
        <div class="page-title">
            <h4>Permission List</h4>
            <h6>Jangan di otak atik</h6>
        </div>
        <div class="page-btn">
            <button class="btn btn-primary mb-2 btnCreateModal"
        data-bs-toggle="modal"
        data-bs-target="#createModal123">
    Tambah Permission
</button>

        </div>
    </div>

    <div class="card">
        <div class="card-body">

            <!-- 🔍 Input Search -->
            <div class="mb-3">
                <input type="text" id="searchPermission" class="form-control" placeholder="Cari permission...">
            </div>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Guard</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="permissionTable">
                        <!-- data akan diisi via jQuery -->
                    </tbody>
                </table>
                <div id="paginationLinks" class="mt-3"></div>
            </div>
        </div>
    </div>

    {{-- Modal Create --}}
    <div class="modal fade" id="createModal123">
        <div class="modal-dialog">
            <form id="createForm">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Tambah Permission</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control">
                        <small class="text-danger error-name"></small>

                        <label>Guard</label>
                        <input type="text" name="guard_name" class="form-control" value="web">
                        <small class="text-danger error-guard"></small>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="modal fade" id="editModal">
        <div class="modal-dialog">
            <form id="editForm">
                @csrf
                <input type="hidden" id="edit_id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5>Edit Permission</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <label>Nama</label>
                        <input type="text" id="edit_name" name="name" class="form-control">
                        <small class="text-danger error-edit-name"></small>

                        <label>Guard</label>
                        <input type="text" id="edit_guard" name="guard_name" class="form-control">
                        <small class="text-danger error-edit-guard"></small>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection


@push('scripts')
<script>
    function loadPermissions(page = 1, search = '') {
        $.get("{{ route('permissions.list') }}", { page: page, search: search }, function(data) {
            let rows = '';
            data.data.forEach(item => {
                rows += `
                    <tr>
                        <td>${item.id}</td>
                        <td>${item.name}</td>
                        <td>${item.guard_name}</td>
                        <td>
                            <button class="btn btn-sm btn-info editBtn"
                                data-id="${item.id}"
                                data-name="${item.name}"
                                data-guard="${item.guard_name}">Edit</button>
                            <button class="btn btn-sm btn-danger deleteBtn" data-id="${item.id}">Hapus</button>
                        </td>
                    </tr>`;
            });
            $('#permissionTable').html(rows);

            // render pagination
            let pagination = '';
            if (data.links.length > 0) {
                pagination += `<ul class="pagination">`;
                data.links.forEach(link => {
                    let active = link.active ? 'active' : '';
                    let disabled = link.url == null ? 'disabled' : '';
                    pagination += `
                        <li class="page-item ${active} ${disabled}">
                            <a class="page-link" href="#" data-page="${link.url ? link.url.split('page=')[1] : ''}">
                                ${link.label}
                            </a>
                        </li>`;
                });
                pagination += `</ul>`;
            }
            $('#paginationLinks').html(pagination);
        });
    }

    // event delegation untuk pagination
    $(document).on('click', '#paginationLinks .page-link', function(e) {
        e.preventDefault();
        let page = $(this).data('page');
        let search = $('#searchPermission').val();
        if (page) loadPermissions(page, search);
    });

    $(function() {
        loadPermissions();

        // 🔍 Search realtime (debounce biar gak spam request)
        let searchTimeout = null;
        $('#searchPermission').on('keyup', function() {
            clearTimeout(searchTimeout);
            let search = $(this).val();
            searchTimeout = setTimeout(() => {
                loadPermissions(1, search);
            }, 400);
        });

        // ===== CREATE =====
        $('#createForm').on('submit', function(e) {
            e.preventDefault();
            $('.error-name, .error-guard').text('');

            $.post("{{ route('permissions.store') }}", $(this).serialize())
                .done(res => {
                    $('#createModal123').modal('hide');
                    $('#createForm')[0].reset();
                    loadPermissions();
                    Swal.fire('Berhasil', 'Permission ditambahkan', 'success');
                })
                .fail(err => {
                    if (err.status === 422) {
                        let errors = err.responseJSON.errors;
                        if (errors.name) $('.error-name').text(errors.name[0]);
                        if (errors.guard_name) $('.error-guard').text(errors.guard_name[0]);
                    }
                });
        });

        // ===== EDIT =====
        $(document).on('click', '.editBtn', function() {
            $('#edit_id').val($(this).data('id'));
            $('#edit_name').val($(this).data('name'));
            $('#edit_guard').val($(this).data('guard'));
            $('#editModal').modal('show');
        });

        $('#editForm').on('submit', function(e) {
            e.preventDefault();
            let id = $('#edit_id').val();

            $.ajax({
                url: `/permissions/${id}`,
                method: 'PUT',
                data: $(this).serialize(),
                success: res => {
                    $('#editModal').modal('hide');
                    loadPermissions();
                    Swal.fire('Berhasil', 'Permission diupdate', 'success');
                },
                error: err => {
                    if (err.status === 422) {
                        let errors = err.responseJSON.errors;
                        if (errors.name) $('.error-edit-name').text(errors.name[0]);
                        if (errors.guard_name) $('.error-edit-guard').text(errors.guard_name[0]);
                    }
                }
            });
        });

        // ===== DELETE =====
        $(document).on('click', '.deleteBtn', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Yakin hapus?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/permissions/${id}`,
                        method: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" },
                        success: res => {
                            loadPermissions();
                            Swal.fire('Dihapus!', 'Permission berhasil dihapus', 'success');
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
