@extends('layouts.dashboard')
@section('title', 'Add Participant')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('officials.index') }}">Participants</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Add an official</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Register new official</h4>
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('officials.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                                                  
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <fieldset class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Enter name">
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email" placeholder="Enter email" class="form-control" value="{{ old('email') }}" required>
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="phone" id="phone" placeholder="Enter phone number" name="phone" class="form-control" value="{{ old('phone') }}" required>
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="sex">Gender</label>
                                    <select class="form-control" name="sex" id="sex" required>
                                         <option value="">--Select Gender--</option>
                                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : ''}}>Male</option>
                                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : ''}}>Female</option>
                                    </select>
                                </fieldset>
                            
                                <fieldset class="form-group">
                                    <label for="level">Level</label>
                                    <select class="form-control" name="level" id="level" required>
                                        <option value="">--Select Level--</option>

                                        <option value="Official" {{ old('level') == 'Official' ? 'selected' : ''}}>Official</option>
                                        <option value="Nec" {{ old('Admin') == 'Admin' ? 'selected' : ''}}>Admin</option>
                                        
                                    </select>
                                </fieldset>

                                <fieldset class="form-group @error('passport')is-invalid @enderror">
                                    <label for="passport">Upload Passport</label>
                                    <input type= "file"  accept="image/*" class="form-control" name="passport" id="passport">	
                                </fieldset>           
                                    
                                <fieldset class="form-group">
                                    <label for="password">Password</label><small class="text-muted"><i style="color:red">Leave blank to use the participant's phone number as password</i></small>
                                    <input type="text" class="form-control" name="password" id="password" value="{{ old('password') }}" placeholder="Enter password">
                                </fieldset>
                               
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
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

