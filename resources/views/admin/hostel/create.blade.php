@extends('layouts.dashboard')
@section('title', 'New hostel')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('hostels.index') }}">Hostels</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Create hostel</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{-- <h4 class="card-title">Update: {{ $hostel->name }}</h4> --}}
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('hostels.store') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Enter name">
                                    </fieldset>

                                    <fieldset class="form-group">
                                        <label for="type">Type</label>
                                        <select class="form-control" name="type" id="type" required>
                                        <option value="">-- Select option --</option>
                                        <option value="Male" {{ old('type') == 'Male' ? 'selected' : ''}}>Male</option>
                                        <option value="Female" {{ old('type')  == 'Female' ? 'selected' : ''}}>Female</option>
                                        </select>
                                    </fieldset>

                                    <fieldset class="form-group">
                                        <label for="level">Level</label>
                                        <select class="form-control" name="level" id="level" required>
                                            <option value="">-- Select option --</option>
                                            <option value="Alumni" {{ old('level') == 'Alumni' ? 'selected' : ''}}>Alumni</option>
                                            <option value="Participant" {{ old('level')  == 'Participant' ? 'selected' : ''}}>Participant</option>
                                            <option value="Nec" {{ old('level')  == 'Nec' ? 'selected' : ''}}>Nec</option>
                                        </select>
                                    </fieldset>

                                    <fieldset class="form-group">
                                        <label for="capacity">Capacity</label>
                                        <input type="number" id="capacity" min="1" name="capacity" class="form-control" value="{{ old('capacity') }}" required>
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