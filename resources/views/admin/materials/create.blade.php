@extends('layouts.dashboard')
@section('title', 'Upload new material')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('materials.index') }}">Materials</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Upload Material</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Upload new material</h4>
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('materials.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="col-md-12 col-sm-12">
                                <fieldset class="form-group">
                                    <label for="file">Upload Materials (Multiple files can be uploaded)</label>
                                    <input type="file" class="form-control" id="file" name="file[]" value="{{ old('file') }}" multiple>
                                </fieldset>

                                <button class="btn btn-primary" style="width:100%" type="submit">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Inputs end -->          
</div>
@endsection
