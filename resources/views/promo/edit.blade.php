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
            <h4>category Edit</h4>
            <h6>Edit category</h6>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('category.update', $category->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label>category Name</label>
                            <input type="text" value="{{ $category->name }}" name="name">
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
                            <div class="image-uploads text-center">
                                @if($category->image)
                                    <img src="{{ asset('uploads/category/' . $category->image) }}"
                                         alt="category Image"
                                         class="img-fluid mb-2"
                                         style="max-height: 150px; border-radius: 8px;">
                                @else
                                    <img src="{{ asset('assets/assets/img/icons/upload.svg') }}"
                                         alt="Upload Icon"
                                         class="img-fluid mb-2"
                                         style="max-height: 100px;">
                                    <h4>Drag and drop a file to upload</h4>
                                @endif
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
