@extends('layouts.main')

@section('content')

    <div class="page-header">
        <div class="page-title">
            <h4>Show Role</h4>
            <h6>Manage your Role</h6>
        </div>
        <div class="page-btn">
            <a class="btn btn-primary btn-sm" href="{{ route('roles.index') }}">Back</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <div class="form-group">
                        <strong>Name:</strong>
                        {{ $role->name }}
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Permissions:</strong>
                        @if (!empty($rolePermissions))
                            @foreach ($rolePermissions as $permission)
                                <span class="badge bg-success">{{ $permission->name }}</span>
                            @endforeach
                        @else
                            <span>No permissions assigned.</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
