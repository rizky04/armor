@extends('layouts.main')

@section('content')

    <div class="page-header">
        <div class="page-title">
            <h4>Show User</h4>
            <h6>Manage your Users</h6>
        </div>
        <div class="page-btn">
            <a class="btn btn-primary btn-sm" href="{{ route('users.index') }}">Back</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12 mb-3">
                    <strong>Name:</strong>
                    <p>{{ $user->name }}</p>
                </div>
                <div class="col-12 mb-3">
                    <strong>Email:</strong>
                    <p>{{ $user->email }}</p>
                </div>
                <div class="col-12">
                    <strong>Roles:</strong><br>
                    @if ($user->getRoleNames()->isNotEmpty())
                        @foreach ($user->getRoleNames() as $role)
                            <span class="badge badge-success">{{ $role }}</span>
                        @endforeach
                    @else
                        <p>No Roles Assigned</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
