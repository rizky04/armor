@extends('layouts.main')

@section('content')
    <div class="page-header">
        <div class="pull-left">
            <a class="btn btn-primary btn-sm" href="{{ route('users.index') }}">
                <i class="fa fa-arrow-left"></i> Back
            </a>
        </div>
        <div class="page-title">
            <h4>User Creater</h4>
            <h6>Create new User</h6>
        </div>
    </div>

    @if (count($errors) > 0)
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
            <form method="POST" action="{{ route('users.store') }}">
                @csrf
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Name:</strong>
                            <input type="text" name="name" placeholder="Name" class="form-control">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Email:</strong>
                            <input type="email" name="email" placeholder="Email" class="form-control">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Pengguna Bengkel :</strong>
                            <select name="id_pengguna" id="id_pengguna" class="form-control">
                                @foreach ($pengguna as $p)
                                    <option value="{{ $p->id_pengguna }}">
                                        {{ $p->nama }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Password:</strong>
                            <input type="password" name="password" placeholder="Password" class="form-control">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Confirm Password:</strong>
                            <input type="password" name="confirm-password" placeholder="Confirm Password"
                                class="form-control">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <strong>Role:</strong>
                            <select name="roles[]" class="form-control" multiple="multiple">
                                @foreach ($roles as $value => $label)
                                    <option value="{{ $value }}">
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                        <button type="submit" class="btn btn-primary btn-sm mt-2 mb-3">
                            <i class="fa-solid fa-floppy-disk"></i> Submit
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
