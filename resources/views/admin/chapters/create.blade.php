@extends('layouts.dashboard')
@section('title', 'New chapter')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('chapters.index') }}">chapters</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Create chapter</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{-- <h4 class="card-title">Update: {{ $chapter->name }}</h4> --}}
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('chapters.store') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Enter name" required>
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <label for="address">Address(Optional)</label>
                                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address') }}" placeholder="Enter address">
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <label for="email">Email(Optional)</label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Enter address">
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <label for="phone">Phone(Optional)</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Enter address">
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <label for="facebook">Facebook Link(Optional)</label>
                                        <input type="text" class="form-control" id="facebook" name="facebook" value="{{ old('facebook') }}" placeholder="Enter address">
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <label for="twitter">Twitter Link(Optional)</label>
                                        <input type="text" class="form-control" id="twitter" name="twitter" value="{{ old('twitter') }}" placeholder="Enter address">
                                    </fieldset>
                                
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <button class="btn btn-primary" style="width:100%" type="submit">Create</button>
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