@extends('layouts.main')

@section('content')
    <div class="page-header">
        <div class="pull-left">
            <a class="btn btn-primary btn-sm" href="{{ route('users.index') }}">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
        <div class="page-title">
            <h4>User Edit</h4>
            <h6>Edit User</h6>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mt-2">
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
            <form method="POST" action="{{ route('users.update', $user->id) }}">
                @csrf
                @method('PUT')

                <div class="row mt-3">
                    <div class="col-12 mb-3">
                        <label><strong>Name:</strong></label>
                        <input type="text" name="name" class="form-control" placeholder="Name"
                            value="{{ old('name', $user->name) }}">
                    </div>

                    <div class="col-12 mb-3">
                        <label><strong>User Bengkel:</strong></label>
                        <select name="id_pengguna" id="id_pengguna" class="form-control">
                            @foreach ($pengguna as $p)
                                <option value="{{ $p->id_pengguna }}" {{ $user->id_pengguna == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 mb-3">
                        <label><strong>Email:</strong></label>
                        <input type="email" name="email" class="form-control" placeholder="Email"
                            value="{{ old('email', $user->email) }}">
                    </div>
                    <div class="col-12 mb-3">
                        <label><strong>Password:</strong></label>
                        <input type="password" name="password" class="form-control" placeholder="Password">
                    </div>
                    <div class="col-12 mb-3">
                        <label><strong>Confirm Password:</strong></label>
                        <input type="password" name="confirm-password" class="form-control" placeholder="Confirm Password">
                    </div>
                    <div class="col-12 mb-3">
                        <label><strong>Role:</strong></label>
                        <select name="roles[]" class="form-control" multiple>
                            @foreach ($roles as $value => $label)
                                <option value="{{ $value }}" {{ isset($userRole[$value]) ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
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
