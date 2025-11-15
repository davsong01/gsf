@extends('layouts.dashboard')
@section('title', 'Add Participant')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('officials.index') }}">Officials</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Update official</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Update official</h4>
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                          
                                  <form action="{{ route('officials.update', $official->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                                 @method('PATCH')                 
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <fieldset class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?? $official->name }}" placeholder="Enter name">
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email" placeholder="Enter email" class="form-control" value="{{ old('email') ?? $official->email }}" required>
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="phone" id="phone" placeholder="Enter phone number" name="phone" class="form-control" value="{{ old('phone') ?? $official->phone}}" required>
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="gender">Gender</label>
                                    <select class="form-control" name="gender" id="gender" required>
                                         <option value="">--Select Gender--</option>
                                         <option value="Male" {{ $official->gender == 'Male' ? 'selected' : ''}}>Male</option>
                                         <option value="Female" {{ $official->gender == 'Female' ? 'selected' : ''}}>Female</option>
                                    </select>
                                </fieldset>
                            
                                <fieldset class="form-group">
                                    <label for="level">Level</label>
                                    <select class="form-control" name="level" id="level" required>
                                      
                                        <option value="Admin" {{ ($official->level == 'Admin' && $official->official == NULL) ? 'selected' : ''}}>Admin</option>
                                        <option value="Official" {{ ($official->level == 'Admin' && $official->official == 'YES') ? 'selected' : ''}}>Official</option>
                                       
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

