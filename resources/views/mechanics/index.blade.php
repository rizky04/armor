@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Mechanic List</h4>
        <h6>Manage your mechanics</h6>
    </div>
    <div class="page-btn">
        <button class="btn btn-added" id="btn-add">+ Add Mechanic</button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="search" class="form-control" placeholder="Search mechanic...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table" id="mechanic-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Specialty</th>
                        <th>Address</th>
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

<!-- Modal -->
<div class="modal fade" id="mechanicModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="mechanicForm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Mechanic</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="mechanic_id" name="mechanic_id">
                    <div class="mb-2">
                        <label>Name</label>
                        <input type="text" name="name" id="name" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label>Phone</label>
                        <input type="text" name="phone" id="phone" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label>Specialty</label>
                        <input type="text" name="specialty" id="specialty" class="form-control">
                    </div>
                    <div class="mb-2">
                        <label>Address</label>
                        <textarea name="address" id="address" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function () {
    let currentPage = 1;
    let searchQuery = '';

    function loadMechanics(page = 1, search = '') {
        currentPage = page;
        $.get("{{ route('mechanics.data') }}", { page: page, search: search }, function(res) {
            let rows = '';
            let i = (res.current_page - 1) * res.per_page;
            res.data.forEach(m => {
                rows += `<tr>
                    <td>${++i}</td>
                    <td>${m.name}</td>
                    <td>${m.phone || ''}</td>
                    <td>${m.specialty || ''}</td>
                    <td>${m.address || ''}</td>
                    <td>
                        <button class="btn btn-sm btn-warning btn-edit" data-id="${m.id}">Edit</button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${m.id}">Delete</button>
                    </td>
                </tr>`;
            });
            $('#mechanic-table tbody').html(rows);

            // pagination
            let pagination = '';
            for(let p=1; p<=res.last_page; p++){
                pagination += `<li class="page-item ${res.current_page==p?'active':''}">
                    <a class="page-link" href="#" onclick="loadMechanics(${p}, '${searchQuery}')">${p}</a>
                </li>`;
            }
            $('#pagination').html(pagination);
        });
    }

    loadMechanics();

    $('#search').on('keyup', function() {
        searchQuery = $(this).val();
        loadMechanics(1, searchQuery);
    });

    $('#btn-add').click(function() {
        $('#mechanicForm')[0].reset();
        $('#mechanic_id').val('');
        $('#mechanicModal .modal-title').text('Add Mechanic');
        $('#mechanicModal').modal('show');
    });

    $('#mechanicForm').submit(function(e) {
        e.preventDefault();
        let id = $('#mechanic_id').val();
        let url = id ? `/mechanics/${id}` : "{{ route('mechanics.store') }}";
        let method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: function(res) {
                $('#mechanicModal').modal('hide');
                loadMechanics(currentPage, searchQuery);
                Swal.fire('Success', res.message, 'success');
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors;
                if(errors){
                    let msg = Object.values(errors).map(e=>e[0]).join('<br>');
                    Swal.fire('Error', msg, 'error');
                } else {
                    Swal.fire('Error', 'Terjadi kesalahan', 'error');
                }
            }
        });
    });

    $(document).on('click', '.btn-edit', function() {
        let id = $(this).data('id');
        $.get(`/mechanics/${id}`, function(m){
            $('#mechanic_id').val(m.id);
            $('#name').val(m.name);
            $('#phone').val(m.phone);
            $('#specialty').val(m.specialty);
            $('#address').val(m.address);
            $('#mechanicModal .modal-title').text('Edit Mechanic');
            $('#mechanicModal').modal('show');
        });
    });

    $(document).on('click', '.btn-delete', function() {
        let id = $(this).data('id');
        Swal.fire({
            title: 'Yakin hapus?',
            text: "Data tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus'
        }).then((result)=>{
            if(result.isConfirmed){
                $.ajax({
                    url: `/mechanics/${id}`,
                    method: 'DELETE',
                    data: {_token: "{{ csrf_token() }}"},
                    success: function(res){
                        loadMechanics(currentPage, searchQuery);
                        Swal.fire('Deleted!', res.message, 'success');
                    }
                });
            }
        });
    });
});
</script>
@endpush
@endsection
