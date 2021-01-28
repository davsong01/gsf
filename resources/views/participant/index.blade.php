@extends('layouts.dashboard')
@section('title', 'My Account')
@section('active')
<li class="breadcrumb-item">Dashboard</li>
@endsection
@section('content')
<div class="content-body">
    @include('includes.alerts')
    <!-- Dashboard Ecommerce Starts -->
    <section id="dashboard-ecommerce">
        <div class="row">
            <!-- Greetings Content Starts -->
            <div class="col-md-12 col-12 dashboard-greetings">
                <div class="card">
                    <div class="card-header">
                        <h3 class="greeting-text">Welcome {{ auth()->user()->name }}!</h3>
                        <p class="mb-0">Here, you can fill in the form below, click save to complete your registration. When your registration is complete, the buttons to download your ID and meal ticket will be enabled.</p>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-end">
                                <div class="dashboard-content-left">
                                    <h1 class="text-primary font-large-2 text-bold-500"></h1>
                                    
                                  @if(auth()->user()->registration_status == 'Pending')  
                                
                                     <a href="#" onclick="return false;" data-toggle="tooltip" data-placement="top" title="You must complete registration to use this button" class="btn btn-primary glow disabled"><i class="fa fa-download" aria-hidden="true"></i> Download Conference I.D. Card
                                       
                                    </a> 
                                     <a href="#" onclick="return false;" data-toggle="tooltip" data-placement="top" title="You must complete registration to use this button" class="btn btn-primary glow disabled"><i class="fa fa-download" aria-hidden="true"></i> Download Conference Meal ticket
                                        
                                    </a> 
                                    @endif

                                    @if(auth()->user()->registration_status == 'Complete')  
                                                                   
                                    <a href="{{ route('user/card', $user->id) }}" onclick="return false;"  class="btn btn-primary glow"> <i class="fa fa-download" aria-hidden="true"></i> Download Conference I.D. card
                                    </a>
                                     <a href="{{ route('meal.ticket', $user->id) }}"  >
                                        <button type="button" onclick="return confirm('You are about to download your conference meal ticket?');" class="btn btn-primary glow">Download Conference Meal ticket
                                        </button>
                                    </a> 

                                    @endif
                                   
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>               
        </div>
    </section>
</div>
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="card">
            <div class="card-header">
                <h4 class="greeting-text">Your Registration Details</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <form action="{{ route('participants.update', auth()->user()->id) }}" onsubmit="return confirm('I am sure all my detailss are correct and current');" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <fieldset class="form-group">
                                <label for="conference_id">Conference ID</label>
                                <input type="text" class="form-control" name="conference_id" id="conference_id" value="{{ auth()->user()->conference_number }}" disabled required>
                            </fieldset>

                            <fieldset class="form-group">
                            <label for="registration_status">Registration Status</label>
                            <input type="text" class="form-control" name="registration_status" id="registration_status" value="{{ auth()->user()->registration_status }}" disabled required>
                            </fieldset>

                                <fieldset class="form-group">
                                <label for="uploaded_by">Registered by</label>
                                <input type="text" id="uploaded_by" name="uploaded_by" class="form-control" value="{{ (auth()->user()->moderator === NULL) ? 'N/A' : auth()->user()->moderator->name }}" disabled required>
                            </fieldset>
                            
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <fieldset class="form-group">
                                <label for="hostel_id">Hostel</label>
                                <input type="text" id="hostel_id" name="hostel_id" class="form-control" value="{{ (auth()->user()->hostel === NULL) ? 'N/A' : auth()->user()->hostel->name }}" disabled required>
                            </fieldset>

                            <fieldset class="form-group">
                                <label for="food_id">Food Stand</label>
                                <input type="text" id="food_id" name="food_id" class="form-control" value="{{ (auth()->user()->food === NULL) ? 'N/A' : auth()->user()->food->name }}" disabled required>
                            </fieldset>
                            <fieldset class="form-group">
                            <label for="transid">Transaction ID</label>
                            <input type="text" id="transid" name="transid" class="form-control" value="{{ old('transid') ?? auth()->user()->transid }}" disabled required>
                        </fieldset>
                        </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <fieldset class="form-group">
                            <label for="name">Name</label>
														<input type="text" class="@error('name')is-invalid @enderror form-control" id="name" name="name" value="{{ old('name') ?? auth()->user()->name }}" placeholder="Enter name">
														@error('name')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
														@enderror
                        </fieldset>

                        <fieldset class="form-group">
                            <label for="email">Email</label>
														<input type="email" id="email" name="email" class="form-control @error('email')is-invalid @enderror" value="{{ old('email') ?? auth()->user()->email }}" required>
														@error('email')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
														@enderror
                        </fieldset>

                        <fieldset class="form-group">
                            <label for="phone">Phone</label>
														<input type="phone" id="phone" name="phone" class="form-control @error('phone')is-invalid @enderror" value="{{ old('phone') ?? auth()->user()->phone }}" required>
														@error('phone')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
														@enderror
                        </fieldset>

                        <fieldset class="form-group">
                            <label for="sex">Sex</label>
                            <select class="form-control @error('sex')is-invalid @enderror" name="sex" id="sex" required>
                                <option value="">--Select Option--</option>
                                <option value="Male" {{ (auth()->user()->sex == 'Male' || old('sex') == 'Male') ? 'selected' : ''}}>Male</option>
                                <option value="Female" {{ (auth()->user()->sex == 'Female' || old('sex') == 'Female') ? 'selected' : ''}}>Female</option>
														</select>
														@error('sex')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
														@enderror
                        </fieldset>
                         <fieldset class="form-group @error('passport')is-invalid @enderror">
                            <label for="sex">Change Passport</label>
                            <input type= "file"  accept="image/*" class="form-control" name="passport" id="passport" required>
														@error('passport')
														<span class="invalid-feedback" role="alert">
															<strong>{{ $message }}</strong>
														</span>
														@enderror
                        </fieldset>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <fieldset class="form-group">
                            <label for="chapter">Campus</label>
                            <select class="form-control @error('chapter')is-invalid @enderror" name="chapter" id="chapter" required>
                                {{-- //include chapter --}}
                                <option value="">--Select Campus--</option>
                                @foreach($chapters as $chapter)
                                <option value="{{ $chapter->id }}" {{ auth()->user()->chapter == $chapter->id ? 'selected' : ''}}>{{ $chapter->name }}</option>
                                @endforeach
                                </select>
                                @error('chapter')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                        </fieldset>

                        <fieldset class="form-group">
                            <label for="amount">Amount Paid</label>
                            <input type="number" id="amount" name="amount_paid" class="form-control @error('amount_paid')is-invalid @enderror" value="{{ old('amount_paid') ?? auth()->user()->amount_paid }}" required>
                            @error('amount_paid')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </fieldset>
                        <fieldset class="form-group">
                            <label for="payment_type">Payment Type</label>
                            <input type="text" id="payment_type" name="payment_type" class="form-control @error('payment_type')is-invalid @enderror" value="{{ old('payment_type') ?? auth()->user()->payment_type }}" required>
                            @error('payment_type')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </fieldset>
                        
                        <fieldset class="form-group">
                            <label for="password">Password</label><small class="text-muted"><i style="color:red">Leave blank except you want to change your password</i></small>
                            <input type="text" class="form-control" name="password" id="password" value="{{ old('password') }}" placeholder="Enter password">
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
    </section>
    <!-- Basic Inputs end -->          
</div>
@endsection