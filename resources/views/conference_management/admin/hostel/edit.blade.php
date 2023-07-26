@extends('layouts.conference')
@section('title', 'Update hostel')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('hostels.index',['edition'=>$edition->id]) }}">Hostels</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Update hostel</li>
@endsection
@section('content2')
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Update: {{ $hostel->name }}</h4>
                        <a href="{{ route('hostelusers.export',['id'=>$hostel->id, 'edition'=>$edition->id]) }}" class="btn btn-primary mt-1">Export Hostel Participants</a>                        
                        
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('hostels.update', [$hostel->id, 'edition_id'=>$edition->id]) }}" method="POST">
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
                                            <option value="Male" {{ $hostel->type == 'Male' ? 'selected' : ''}}>Male</option>
                                            <option value="Female" {{ $hostel->type == 'Female' ? 'selected' : ''}}>Female</option>
                                        </select>
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <label for="level">Level</label>
                                        <select class="form-control" name="level" id="level" required>
                                            <option value="Alumni" {{ $hostel->level == 'Admin' ? 'selected' : ''}}>Alumni</option>
                                            <option value="Participant" {{ $hostel->level == 'Participant' ? 'selected' : ''}}>Participant</option>
                                            <option value="Choir" {{ $hostel->level == 'Choir' ? 'selected' : ''}}>Choir</option>
                                            <option value="Official" {{ $hostel->level == 'Official' ? 'selected' : ''}}>Official</option>
                                            <option value="Medical" {{ $hostel->level == 'Medical' ? 'selected' : ''}}>Medical</option>
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