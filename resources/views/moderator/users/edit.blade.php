@extends('layouts.dashboard')
@section('title', 'Update Participant')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('users.index') }}">Participants</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Update Participant</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Update: {{ $user->name }}</h4>
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                    <div class="card-body">
                        <form action="{{ route('users.update', $user->id) }}" onsubmit="return confirm('I am sure all filled details are correct and current');" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <div class="row">
                             <div class="col-md-3">
                                <div class="media-left pr-0"><img style="width: 150px !important; border-radius: 50%;" class="mr-1" src="{{ asset($user->passport ? $user->passport : 'frontend/passports/avatar.jpg') }}" alt="avatar" height="20%">
                                </div>
                            </div>
                            <div class="col-md-9">
                                <fieldset class="form-group">
                                    <label for="conference_id">Conference ID</label>
                                    <input type="text" class="form-control" name="conference_id" id="conference_id" value="{{ $user->conference_number }}" disabled required>
                                </fieldset>
                            <fieldset class="form-group">
                                <label for="registration_status">Registration Status</label>
                                <input type="text" class="form-control" name="registration_status" id="registration_status" value="{{ $user->registration_status }}" disabled required>
                                </fieldset>
                            </div>
                        </div>
                        <div class="row"> 
                            <div class="col-md-6 col-sm-12">
                                
                                    <fieldset class="form-group">
                                    <label for="uploaded_by">Registered by</label>
                                    <input type="text" id="uploaded_by" name="uploaded_by" class="form-control" value="{{ ($user->moderator === NULL) ? 'N/A' : $user->moderator->name }}" disabled required>
                                </fieldset>
                                <fieldset class="form-group">
                                <label for="amount">Amount Paid (&#8358;)</label>
                                <input type="number" id="amount" class="form-control @error('amount_paid')is-invalid @enderror" value="{{ $user->amount_paid }}" disabled required>
                            
                            </fieldset>
                            <fieldset class="form-group">
                                <label for="email">Email</label>
                                    <input type="email" id="email" class="form-control @error('email')is-invalid @enderror" value="{{ $user->email }}" required disabled>						
                            </fieldset>
                            </div>
                            <div class="col-md-6 col-sm-12">
                                <fieldset class="form-group">
                                    <label for="hostel_id">Hostel</label>
                                    <input type="text" id="hostel_id" name="hostel_id" class="form-control" value="{{ ($user->hostel === NULL) ? 'N/A' : $user->hostel->name }}" disabled required>
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="food_id">Food Stand</label>
                                    <input type="text" id="food_id" name="food_id" class="form-control" value="{{ ($user->food === NULL) ? 'N/A' : $user->food->name }}" disabled required>
                                </fieldset>
                                <fieldset class="form-group">
                                <label for="payment_type">Payment Type</label>
                                <input type="text" id="payment_type"  class="form-control @error('payment_type')is-invalid @enderror" value="{{ $user->payment_type }}" disabled required>
                            
                            </fieldset>
                                <fieldset class="form-group">
                                <label for="transid">Transaction ID</label>
                                <input type="text" id="transid" name="transid" class="form-control" value="{{ old('transid') ?? $user->transid }}" disabled required>
                            </fieldset>
                            
                            
                            </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <fieldset class="form-group">
                                <label for="name">Name</label>
                                    <input type="text" class="@error('name')is-invalid @enderror form-control" id="name" name="name" value="{{ old('name') ?? $user->name }}" placeholder="Enter name">
                                
                            </fieldset>

                            <fieldset class="form-group">
                                <label for="phone">Phone</label>
                                <input type="phone" id="phone" name="phone" class="form-control @error('phone')is-invalid @enderror" value="{{ old('phone') ?? $user->phone }}" required>
                            
                            </fieldset>

                            <fieldset class="form-group">
                                <label for="sex">Gender</label>
                                <select class="form-control @error('sex')is-invalid @enderror" name="sex" id="sex" required>
                                    <option value="">--Select Option--</option>
                                    <option value="Male" {{ ($user->sex == 'Male' || old('sex') == 'Male') ? 'selected' : ''}}>Male</option>
                                    <option value="Female" {{ ($user->sex == 'Female' || old('sex') == 'Female') ? 'selected' : ''}}>Female</option>
                                    </select>
                                
                            </fieldset>
                            
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <fieldset class="form-group">
                                <label for="chapter">Campus</label>
                                <input type="text" disabled class="form-control" value="{{ isset($user->campus->name) ? $user->campus->name : 'N/A' }}">
                            </fieldset>
                            
                            <fieldset class="form-group @error('passport')is-invalid @enderror">
                                <label for="passport">Change Passport</label>
                                <input type= "file"  accept="image/*" class="form-control" name="passport" id="passport">	
                            </fieldset>

                            <fieldset class="form-group">
                                <label for="password">Password</label><small class="text-muted"><i style="color:red">Leave blank except you want to change this participant's password</i></small>
                                <input type="text" class="form-control" name="password" id="password" value="{{ old('password') }}" placeholder="Enter password">
                            </fieldset>

                        </div>
                        
                    </div>
                    <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <button class="btn btn-primary" style="width:100%" type="submit">Complete Registration</button>
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
