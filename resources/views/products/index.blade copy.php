@extends('layouts.main')

@section('content')
<div class="page-header">
    <div class="page-title">
        <h4>Product List</h4>
        <h6>Manage your products</h6>
    </div>
    <div class="page-btn">
        <button class="btn btn-added" id="btn-add">+ Add Product</button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
        <input type="text" id="search" class="form-control mb-3" placeholder="Search product...">
    </div>
</div>

        <div class="table-responsive">
            <table class="table" id="product-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Merk</th>
                        <th>Location</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- pagination -->
        <nav class="mt-3">
            <ul class="pagination" id="pagination"></ul>
        </nav>
    </div>
</div>

<!-- Modal Create/Edit -->
<div class="modal fade" id="productModal" tabindex="-1">
  <div class="modal-dialog">
    <form id="productForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="product_id">
            <div class="mb-2">
                <label>Code</label>
                <input type="text" id="product_code" class="form-control">
            </div>
            <div class="mb-2">
                <label>Name</label>
                <input type="text" id="product_name" class="form-control">
            </div>
            <div class="mb-2">
                <label>Merk</label>
                <input type="text" id="product_merk" class="form-control">
            </div>
            <div class="mb-2">
                <label>Location</label>
                <input type="text" id="product_location" class="form-control">
            </div>
            <div class="mb-2">
                <label>Description</label>
                <textarea id="product_description" class="form-control"></textarea>
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

@endsection

@push('scripts')
<script>
let currentPage = 1;
let searchQuery = '';

function loadProducts(page = 1, search = '') {
    $.get("{{ route('products.data') }}", { page: page, search: search }, function(res) {
        let rows = '';
        let i = (res.current_page - 1) * res.per_page;
        res.data.forEach(p => {
            rows += `
                <tr>
                    <td>${++i}</td>
                    <td>${p.product_code}</td>
                    <td>${p.product_name}</td>
                    <td>${p.product_merk ?? ''}</td>
                    <td>${p.product_location ?? ''}</td>
                    <td>${p.product_description ?? ''}</td>
                    <td>
                        <button class="btn btn-sm btn-warning btn-edit" data-id="${p.id}">Edit</button>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="${p.id}">Delete</button>
                    </td>
                </tr>`;
        });
        $('#product-table tbody').html(rows);


        // Pagination dengan limit halaman
        let pagination = '';
        const totalPages = res.last_page;
        const current = res.current_page;
        const delta = 2; // jumlah halaman kiri & kanan yg ditampilkan

        // Tombol Prev
        if (res.prev_page_url) {
            pagination += `<li class="page-item">
                              <a class="page-link" href="#" onclick="loadProducts(${current - 1}, searchQuery)">Prev</a>
                           </li>`;
        }

        // First page
        if (current > delta + 1) {
            pagination += `<li class="page-item">
                              <a class="page-link" href="#" onclick="loadProducts(1, searchQuery)">1</a>
                           </li>`;
            if (current > delta + 2) {
                pagination += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
        }

        // Halaman sekitar current
        for (let i = Math.max(1, current - delta); i <= Math.min(totalPages, current + delta); i++) {
            pagination += `<li class="page-item ${current === i ? 'active' : ''}">
                              <a class="page-link" href="#" onclick="loadProducts(${i}, searchQuery)">${i}</a>
                           </li>`;
        }

        // Last page
        if (current < totalPages - delta) {
            if (current < totalPages - delta - 1) {
                pagination += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
            }
            pagination += `<li class="page-item">
                              <a class="page-link" href="#" onclick="loadProducts(${totalPages}, searchQuery)">${totalPages}</a>
                           </li>`;
        }

        // Tombol Next
        if (res.next_page_url) {
            pagination += `<li class="page-item">
                              <a class="page-link" href="#" onclick="loadProducts(${current + 1}, searchQuery)">Next</a>
                           </li>`;
        }

        $('#pagination').html(pagination);
    });
}


$(document).ready(function() {
    loadProducts();

    $('#search').on('keyup', function() {
        searchQuery = $(this).val();
        loadProducts(1, searchQuery);
    });

    // Add
    $('#btn-add').on('click', function() {
        $('#productForm')[0].reset();
        $('#product_id').val('');
        $('#productModal .modal-title').text('Add Product');
        $('#productModal').modal('show');
    });

    // Save
    $('#productForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#product_id').val();
        let url = id ? `/products/${id}` : "{{ route('products.store') }}";
        let method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: {
                product_code: $('#product_code').val(),
                product_name: $('#product_name').val(),
                product_merk: $('#product_merk').val(),
                product_location: $('#product_location').val(),
                product_description: $('#product_description').val(),
                _token: "{{ csrf_token() }}"
            },
            success: function(res) {
                $('#productModal').modal('hide');
                loadProducts(currentPage, searchQuery);
                Swal.fire('Success', res.message, 'success');
            }
        });
    });

    // Edit
    $(document).on('click', '.btn-edit', function() {
        let id = $(this).data('id');
        $.get(`/products/${id}`, function(p) {
            $('#product_id').val(p.id);
            $('#product_code').val(p.product_code);
            $('#product_name').val(p.product_name);
            $('#product_merk').val(p.product_merk);
            $('#product_location').val(p.product_location);
            $('#product_description').val(p.product_description);
            $('#productModal .modal-title').text('Edit Product');
            $('#productModal').modal('show');
        });
    });

    // Delete
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
                        loadProducts(currentPage, searchQuery);
                        Swal.fire('Deleted!', res.message, 'success');
                    }
                });
            }
        });
    });
});
</script>
@endpush
