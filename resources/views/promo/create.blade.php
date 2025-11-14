@extends('layouts.main')

@section('content')


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

    <div class="page-header">
        <div class="pull-left">
                <a class="btn btn-primary btn-sm" href="{{ route('category.index') }}">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>
        <div class="page-title">
            <h4>category Add</h4>
            <h6>Create new category</h6>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('category.store') }}" method="POST" enctype="multipart/form-data">
                @method('POST')
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>category Name</label>
                            <input type="text" name="name">
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label> category Image</label>
                            <div class="image-upload">
                                <input type="file" name="image">
                                <div class="image-uploads">
                                    <img src="{{asset('assets/assets/img/icons/upload.svg')}}" alt="img">
                                    <h4>Drag and drop a file to upload</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <button type="submit" class="btn btn-submit me-2">Submit</button>
                        <a href="{{route('category.index')}}" class="btn btn-cancel">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
