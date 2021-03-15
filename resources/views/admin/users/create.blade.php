@extends('layouts.dashboard')
@section('title', 'Add Participant')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('users.index') }}">Participants</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Add Participant</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Register new participant</h4>
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                                                  
                        <div class="row">
                            <div class="col-md-6 col-sm-12">
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
                                        <option value="Male" {{ old('sex') == 'Male' ? 'selected' : ''}}>Male</option>
                                        <option value="Female" {{ old('sex') == 'Female' ? 'selected' : ''}}>Female</option>
                                    </select>
                                </fieldset>
                            
                                <fieldset class="form-group">
                            <label for="chapter">Campus</label>
                            <select class="form-control" name="chapter" id="chapter" required>
                                {{-- //include chapter --}}
                                <option value="">--Select Campus--</option>
                                @foreach($chapters as $chapter)
                                <option value="{{ $chapter->id }}" {{ old('old') == $chapter->id ? 'selected' : ''}}>{{ $chapter->name }}</option>
                                @endforeach
                            </select>
                            </fieldset>

                            <fieldset class="form-group">
                                    <label for="level">Level</label>
                                    <select class="form-control" name="level" id="level" required>
                                         <option value="">--Select Level--</option>
                                        <option value="Participant" {{ old('level') == 'Participant' ? 'selected' : ''}}>Participant</option>
                                        <option value="Moderator" {{ old('level') == 'Moderator' ? 'selected' : ''}}>Moderator</option>
                                         <option value="Alumni" {{ old('level') == 'Alumni' ? 'selected' : ''}}>Alumni</option>
                                         <option value="Choir" {{ old('level') == 'Choir' ? 'selected' : ''}}>Choir</option>
                                          <option value="Medical" {{ old('level') == 'Medical' ? 'selected' : ''}}>Medical</option>
                                        <option value="Official" {{ old('level') == 'Official' ? 'selected' : ''}}>Official</option>
                                        <option value="Nec" {{ old('level') == 'Nec' ? 'selected' : ''}}>Nec</option>
                                        
                                    </select>
                                </fieldset>

                            <fieldset class="form-group @error('passport')is-invalid @enderror">
                                <label for="passport">Upload Passport</label>
                                <input type= "file"  accept="image/*" class="form-control" name="passport" id="passport">	
                            </fieldset>           
                                
                            </div>
                            <div class="col-md-6 col-sm-12">                               
                                <fieldset class="form-group">
                                    <label for="payment_type">Payment Type</label>
                                    <input type="text" id="payment_type" placeholder="Online or Bank" name="payment_type" class="form-control" value="{{ old('payment_type') }}" required>
                                </fieldset> 
                                <fieldset class="form-group">
                                    <label for="transid">Transaction ID</label>
                                    <input type="text" id="transid" name="transid" placeholder="Enter Transaction Id or Bank name " class="form-control" value="{{ old('transid') }}" required>
                                </fieldset>
                                <fieldset class="form-group">
                                    <label for="hostel_id">Hostel</label>
                                    <select class="form-control" name="hostel_id" id="hostel_id" required>
                                        <option value="">--Select Hostel--</option>
                                        @foreach($hostels as $hostel)
                                        @if($hostel->capacity > $hostel->allocation)  
                                        <option value="{{ old('hostel_id') ?? $hostel->id}}" {{ old('hostel_id') == $hostel->id ? 'selected' : '' }}>{{ $hostel->name. ' ('.($hostel->capacity - $hostel->allocation). ' participant(s) left) | '.$hostel->type. ' | '.$hostel->level}}</option>
                                        @endif
                                        @endforeach
                                    </select>
                                </fieldset>
                                <fieldset class="form-group">
                                    <label for="uploaded_by">Food Stand</label>
                                    <select class="form-control" name="food_id" id="food_id" required>
                                         <option value="">--Select Foodstand--</option>
                                        @foreach($foods as $food)
                                            @if($food->capacity > $food->allocation)
                                            <option value="{{ old('food_id') ?? $food->id }}" {{ old('food_id') == $food->id ? 'selected' : '' }}>{{ $food->name. ' ('.($food->capacity - $food->allocation). ' participant(s) left) | '.$food->type. ' | '.$food->level}}</option>
                                             @endif
                                            @endforeach
                                        </select>
                                    </select>
                                </fieldset>
                               
                                <fieldset class="form-group">
                                    <label for="uploaded_by">Uploaded by (Optional)</label>
                                    <select class="form-control" name="uploaded_by" id="uploaded_by">
                                          <option value="">--Select Option--</option>
                                        @foreach($moderators as $moderator)
                                          
                                            <option value="{{ old('uploaded_by') ?? $moderator->id }}" {{ old('uploaded_by') == $moderator->id ? 'selected' : '' }}>{{ $moderator->name }}</option>
                                            @endforeach
                                        </select>
                                    </select>
                                </fieldset>
                                
                                <fieldset class="form-group">
                                    <label for="password">Password</label><small class="text-muted"><i style="color:red">Leave blank to use the participant's phone number as password</i></small>
                                    <input type="text" class="form-control" name="password" id="password" value="{{ old('password') }}" placeholder="Enter password">
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="amount_paid">Amount Paid</label>
                                    <input type="number" name="amount_paid" id="amount_paid" placeholder="Enter amount paid" class="form-control" value="{{ old('amount_paid') }}" required>
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

