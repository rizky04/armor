@extends('layouts.main')

@section('content')
    <div class="page-header">
        <div class="pull-left">
            <a class="btn btn-primary btn-sm" href="{{ route('users.index') }}">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
        <div class="page-title">
            <h4>Roles Creater</h4>
            <h6>Create new Roles</h6>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('roles.store') }}">
                @csrf
                <div class="row">
                    <div class="col-12 mb-3">
                        <label><strong>Name:</strong></label>
                        <input type="text" name="name" class="form-control" placeholder="Name"
                            value="{{ old('name') }}">
                    </div>
                    <div class="col-12 mb-3">
                        <label><strong>Permission:</strong></label><br>
                        @foreach ($permission as $perm)
                            <label>
                                <input type="checkbox" name="permission[{{ $perm->id }}]" value="{{ $perm->id }}"
                                    class="me-1">
                                {{ $perm->name }}
                            </label><br>
                        @endforeach
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-floppy-disk"></i> Submit
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
