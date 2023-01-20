@extends('layouts.dashboard')
@section('title', 'New field')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('fields.index') }}">Fields</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Create field</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{-- <h4 class="card-title">Update: {{ $field->name }}</h4> --}}
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('fields.store') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Enter name" required>
                                    </fieldset>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <button class="btn btn-primary" style="width:100%" type="submit">Save</button>
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