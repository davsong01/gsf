@extends('layouts.dashboard')
@section('title', 'Update Alumni')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('alumni.index') }}">Alumni</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Update Alumni</li>
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
                            <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                         <div class="row">
                             <div class="col-md-3">
                                <div class="media-left pr-0"><img style="width: 150px !important; border-radius: 50%;" class="mr-1" src="/{{ ($user->passport ? $user->passport : 'frontend/passports/avatar.jpg') }}" alt="avatar" height="20%">
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
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?? $user->name }}" placeholder="Enter name">
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') ?? $user->email }}" required>
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="phone" id="phone" name="phone" class="form-control" value="{{ old('phone') ?? $user->phone }}" required>
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="sex">Gender</label>
                                    <select class="form-control" name="sex" id="sex" required>
                                        <option value="Male" {{ $user->sex == 'Male' ? 'selected' : ''}}>Male</option>
                                        <option value="Female" {{ $user->sex == 'Female' ? 'selected' : ''}}>Female</option>
                                    </select>
                                </fieldset>
                            
                            <fieldset class="form-group">
                            <label for="chapter">Campus</label>
                            <select class="form-control" name="chapter" id="chapter" required>
                                {{-- //include chapter --}}
                                <option value="">--Select Campus--</option>
                                @foreach($chapters as $chapter)
                                <option value="{{ $chapter->id ?? old('chapter')}}" {{ $user->chapter == $chapter->id ? 'selected' : ''}}>{{ $chapter->name }}</option>
                                @endforeach
                            </select>
                            </fieldset>

                            <fieldset class="form-group @error('passport')is-invalid @enderror">
                                <label for="passport">Change Passport</label>
                                <input type= "file" accept="image/*" class="form-control" name="passport" id="passport">	
                            </fieldset>           
                                
                            </div>
                            <div class="col-md-6 col-sm-12">                               
                                <fieldset class="form-group">
                                    <label for="payment_type">Payment Type</label>
                                    <input type="text" id="payment_type" name="payment_type" class="form-control" value="{{ old('payment_type') ?? $user->payment_type }}" required>
                                </fieldset> 
                                <fieldset class="form-group">
                                    <label for="transid">Transaction ID</label>
                                    <input type="text" id="transid" name="transid" class="form-control" value="{{ old('transid') ?? $user->transid }}" required>
                                </fieldset>
                                <fieldset class="form-group">
                                    <label for="hostel_id">Hostel</label>
                                    <select class="form-control" name="hostel_id" id="hostel_id" required>
                                        @foreach($hostels as $hostel)
                                        @if($hostel->capacity > $hostel->allocation)
                                        <option value="{{ old('hostel_id') ?? $user->hostel_id }}" {{ $user->hostel_id == $hostel->id ? 'selected' : '' }}>{{ $hostel->name. ' ('.($hostel->capacity - $hostel->allocation). ' participant(s) left) | '.$hostel->type. ' | '.$hostel->level}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </fieldset>

                                {{-- <fieldset class="form-group">
                                    <label for="hostel">Food Stand</label>
                                    <select class="form-control" name="food_id" id="food_id" required>
                                        @foreach($foods as $food)
                                            <option value="{{ old('food_id') ?? $user->food_id }}" {{ $user->food_id == $food->id ? 'selected' : '' }}>{{ $food->name }}</option>
                                            @endforeach
                                        </select>
                                    </select>
                                </fieldset>
                                 --}}
                                <fieldset class="form-group">
                                    <label for="password">Password</label><small class="text-muted"><i style="color:red">Leave blank except you want to reset participant's password</i></small>
                                    <input type="text" class="form-control" name="password" id="password" value="{{ old('password') }}" placeholder="Enter password">
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="amount">Amount Paid</label>
                                    <input type="number" id="amount" disabled class="form-control" value="{{ old('amount_paid') ?? $user->amount_paid }}" required>
                                </fieldset>
                                {{-- <fieldset class="form-group">
                                    <label for="uploaded_by">Uploaded by</label>
                                    <input type="text" id="uploaded_by" name="uploaded_by" class="form-control" value="{{ old('uploaded_by') ?? $user->moderator->name }}" disabled required>
                                </fieldset> --}}
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

