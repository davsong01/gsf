@extends('layouts.dashboard')
@section('title', 'Update hostel')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('hostels.index') }}">Hostels</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Update hostel</li>
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
                            <form action="{{ route('hostels.update', $hostel->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                         
                       
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?? $hostel->name }}" placeholder="Enter name">
                                    </fieldset>

                                    <fieldset class="form-group">
                                        <label for="type">Type</label>
                                        <select class="form-control" name="type" id="type" required>
                                        
                                         <option value="Male" {{ $hostel->level == 'Male' ? 'selected' : ''}}>Male</option>
                                        <option value="Female" {{ $hostel->level == 'Female' ? 'selected' : ''}}>Female</option>
                                        </select>
                                    </fieldset>
                                    
                                    

                                    <fieldset class="form-group">
                                        <label for="level">Level</label>
                                        <select class="form-control" name="level" id="level" required>
                                            <option value="Alumni" {{ $hostel->level == 'Admin' ? 'selected' : ''}}>Alumni</option>
                                            <option value="Participant" {{ $hostel->level == 'Participant' ? 'selected' : ''}}>Participant</option>
                                            <option value="Nec" {{ $hostel->level == 'Nec' ? 'selected' : ''}}>Nec</option>
                                        </select>
                                    </fieldset>

                                    <fieldset class="form-group">
                                        <label for="capacity">Capacity</label>
                                        <input type="number" id="capacity" name="capacity" class="form-control" value="{{ old('capacity') ?? $hostel->capacity }}" required>
                                    </fieldset>

                                <fieldset class="form-group">
                                        <label for="allocation">Allocation</label>
                                        <input type="numer" id="allocation" name="allocation" class="form-control" disabled value="{{ old('allocation') ?? $hostel->allocation }}" required>
                                    </fieldset>
                                
                                
                                </div>

                            
                                
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <button class="btn btn-primary" style="width:100%" type="submit">Update</button>
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