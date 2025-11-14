@extends('layouts.main')

@section('content')
    <div class="page-header">
        <div class="page-title">
            <h4>Product List</h4>
            <h6>Manage your products</h6>
        </div>
        @can('category-create')
            <div class="page-btn">
                <a href="{{ route('category.create') }}" class="btn btn-added"><img
                        src="{{ asset('assets/assets/img/icons/plus.svg') }}" alt="img" class="me-1">Add
                    New Category</a>
            </div>
        @endcan
    </div>
    @if ($message = Session::get('success'))
        <div class="alert alert-success" role="alert">
            {{ $message }}
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-top">
                <div class="search-set">
                    <div class="search-path">
                        <a class="btn btn-filter" id="filter_search">
                            <img src="{{ asset('assets/assets/img/icons/filter.svg') }}" alt="img">
                            <span><img src="{{ asset('assets/assets/img/icons/closes.svg') }}"
                                    alt="img"></span>
                        </a>
                    </div>
                    <div class="search-input">
                        <a class="btn btn-searchset"><img
                                src="{{ asset('assets/assets/img/icons/search-white.svg') }}"
                                alt="img"></a>
                    </div>
                </div>
                <div class="wordset">
                    <ul>
                        <li>
                            <a data-bs-toggle="tooltip" data-bs-placement="top" title="pdf"><img
                                    src="{{ asset('assets/assets/img/icons/pdf.svg') }}" alt="img"></a>
                        </li>
                        <li>
                            <a data-bs-toggle="tooltip" data-bs-placement="top" title="excel"><img
                                    src="{{ asset('assets/assets/img/icons/excel.svg') }}" alt="img"></a>
                        </li>
                        <li>
                            <a data-bs-toggle="tooltip" data-bs-placement="top" title="print"><img
                                    src="{{ asset('assets/assets/img/icons/printer.svg') }}" alt="img"></a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card mb-0" id="filter_inputs">

                    </div>

            <div class="table-responsive">
                <table class="table  datanew">
                    <thead>
                        <tr>
                            <th>no</th>
                            <th>image</th>
                            <th>category Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($category as $category)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="productimgname">
                                    <a href="javascript:void(0);" class="product-img">
                                        <img src="{{ asset('uploads/category/' . $category->image) }}" alt="product">
                                    </a>
                                </td>
                                <td>{{ $category->name }}</td>
                                <td>
                                    {{-- <form action="{{ route('products.destroy', $product->id) }}" method="POST"> --}}
                                    {{-- <a class="me-3" href="{{ route('products.show', $product->id) }}">
                                        <img src="{{ asset('assets/assets/img/icons/eye.svg') }}"
                                            alt="img">
                                    </a> --}}
                                    @can('category-edit')
                                        <a class="me-3" href="{{ route('category.edit', $category->id) }}">
                                            <img src="{{ asset('assets/assets/img/icons/edit.svg') }}"
                                                alt="img">
                                        </a>
                                    @endcan
                                    @csrf
                                    @method('DELETE')

                                    @can('category-delete')
                                        {{-- <a type="submit" class="confirm-text" href="javascript:void(0);">
                                                        <img src="{{ asset('assets/assets/img/icons/delete.svg') }}"
                                                            alt="img">
                                                    </a> --}}
                                        <form action="{{ route('category.destroy', $category->id) }}" method="POST"
                                            class="d-inline delete-form">
                                            <a href="javascript:void(0);" class="btn-delete">
                                                <img src="{{ asset('assets/assets/img/icons/delete.svg') }}"
                                                    alt="delete">
                                            </a>
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endcan
                                    {{-- </form> --}}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {{-- {!! $products->links() !!} --}}
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Cari semua tombol delete
                document.querySelectorAll('.btn-delete').forEach(function(button) {
                    button.addEventListener('click', function(e) {
                        e.preventDefault(); // cegah default

                        let form = this.closest('form'); // ambil form terdekat

                        Swal.fire({
                            title: 'Apakah Anda yakin?',
                            text: "Data produk akan dihapus permanen!",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Ya, hapus!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit(); // jalankan delete
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
@endsection
