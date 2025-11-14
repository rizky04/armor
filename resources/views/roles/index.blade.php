@extends('layouts.main')

@section('content')
    <div class="page-header">
        <div class="page-title">
            <h4>Role List</h4>
            <h6>Manage your Role</h6>
        </div>
        <div class="page-btn">
            <a class="btn btn-success btn-sm" href="{{ route('roles.create') }}">
                <i class="fa fa-plus"></i> Create New Role
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="card">
        <div class="card-body">
            <div class="table-top">
                <div class="search-set">
                    <div class="search-input">
                        <a class="btn btn-searchset"><img src="{{ asset('assets/assets/img/icons/search-white.svg') }}"
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
            <div class="table-responsive">
                <table class="table datanew">
                    <thead>
                        <tr>
                            <th width="100px">No</th>
                            <th>Name</th>
                            <th width="280px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $key => $role)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $role->name }}</td>
                                <td>
                                    <a class="btn btn-sm" href="{{ route('roles.show', $role->id) }}">
                                       <img src="{{ asset('assets/assets/img/icons/eye.svg') }}"
                                            alt="img">
                                    </a>
                                    @can('role-edit')
                                        <a class="btn btn-sm" href="{{ route('roles.edit', $role->id) }}">
                                                                    <img src="{{ asset('assets/assets/img/icons/edit.svg') }}" alt="img">
                                        </a>
                                    @endcan
                                    @can('role-delete')
                                        <form method="POST" action="{{ route('roles.destroy', $role->id) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-delete">
                                                                            <img src="{{ asset('assets/assets/img/icons/delete.svg') }}" alt="delete">
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    {!! $roles->links('pagination::bootstrap-5') !!}

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
                            text: "Data Rolers akan dihapus permanen!",
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
