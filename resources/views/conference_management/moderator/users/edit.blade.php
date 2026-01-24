@extends('layouts.dashboard')
@section('title', 'Update Participant')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('conferencemanagement.show', ['conferencemanagement'=>$transaction->id, 'edition'=>$transaction->conference_edition_id]) }}">My Participants</a></li>
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
                        <h4 class="card-title">Update: {{ $transaction->user->name }}</h4>
                        @if($transaction->user->completeReg($edition) && $transaction->hostel && $transaction->food)
                        <div style="padding:20px">
                            <a href="{{ route('participants.card', ['id'=>$transaction->id, 'edition'=>$edition->id]) }}" class="btn btn-primary glow"><i class="fa fa-print" aria-hidden="true"></i> View/Download Badge</a>
                            @if(isset($edition->material) && !empty($edition->material))
                            <a href="{{ route('materials.index', ['edition'=>$edition->id, 'payment_id'=>$transaction->id]) }}" class="btn btn-info glow"><i class="fa fa-print" aria-hidden="true"></i> View/Donload Conference Materials</a>
                            @endif
                        </div>
                        @else
                        <a href="#" onclick="return false;" data-toggle="tooltip" data-placement="top" title="You must complete registration to use this button" class="btn btn-primary glow disabled"><i class="fa fa-print" aria-hidden="true"></i>  View/Download Badge
                        </a>
                        @endif
                    </div>
                    <div class="card-content">
                    <div class="card-body">
                        <form action="{{ route('conference.participants.update', ['edition'=>$edition->id,'id'=>$transaction->id]) }}" onsubmit="return confirm('I am sure all filled details are correct and current');" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')
                        <div class="row">
                            <!-- IMAGE -->
                            <div class="col-md-2 d-flex justify-content-center align-items-start">
                                <img src="{{ asset($transaction->user->passport ?: 'frontend/passports/avatar.jpg') }}"
                                    class="img-fluid rounded-circle"
                                    style="width: 150px; height:150px; object-fit: cover;">
                            </div>

                            <!-- RIGHT SIDE CONTENT -->
                            <div class="col-md-10">

                                <div class="row">
                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="conference_id">Login ID</label>
                                            <input type="text" class="form-control" value="{{ $transaction->user->family_id }}" disabled>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="transid">Transaction ID</label>
                                            <input type="text" class="form-control" value="{{ $transaction->transid }}" disabled>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label>Payment Status</label>
                                            <input type="text" class="form-control" value="{{ $transaction->status }}" disabled>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label>Registration Status</label>
                                            <input type="text" class="form-control" value="{{ $transaction->registration_status }}" disabled>
                                        </fieldset>
                                    </div>
                                </div>

                            </div>

                        </div>

                        <div class="row mt-2">
                            <div class="col-md-6 col-sm-12">

                                    <fieldset class="form-group">
                                    <label for="uploaded_by" style="color:blue">Registered by</label>
                                    <input type="text" id="uploaded_by" name="uploaded_by" class="form-control" value="{{ ($transaction->moderator === NULL) ? 'N/A' : $transaction->moderator->name }}" disabled required>
                                </fieldset>
                                <fieldset class="form-group">
                                <label for="amount" style="color:blue">Amount Paid (&#8358;)</label>
                                <input type="number" id="amount" class="form-control @error('amount_paid')is-invalid @enderror" value="{{ $transaction->amount_paid }}" disabled required>

                            </fieldset>


                            </div>
                            <div class="col-md-6 col-sm-12">
                                <fieldset class="form-group" style="color:blue">
                                    <label for="hostel_id">Hostel</label>
                                    <input type="text" id="hostel_id" name="hostel_id" class="form-control" value="{{ ($transaction->hostel === NULL) ? 'N/A' : $transaction->hostel->name }}" disabled>
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="food_id" style="color:blue">Service Point</label>
                                    <input type="text" id="food_id" name="food_id" class="form-control" value="{{ ($transaction->food === NULL) ? 'N/A' : $transaction->food->name }}" disabled>
                                </fieldset>
                            </div>
                    </div>
                    <div class="row">
                        @include('includes.dashboard.edit_plan_fields')

                        <div class="col-md-6 col-sm-12">
                            <fieldset class="form-group @error('passport')is-invalid @enderror">
                                <label for="passport" style="color:blue">Change Passport</label>
                                <input type= "file"  accept="image/*" class="form-control" name="passport" id="passport">
                            </fieldset>

                            <fieldset class="form-group">
                                <label for="password" style="color:blue">Password</label><small class="text-muted"><i style="color:red">Leave blank except you want to change this participant's password</i></small>
                                <input type="text" class="form-control" name="password" id="password" value="{{ old('password') }}" placeholder="Enter password">
                            </fieldset>

                        </div>

                    </div>
                    <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <button class="btn btn-primary" style="width:100%" type="submit">{{$transaction->registration_status == 'Complete' ? 'Update' : 'Complete Registration'}}</button>
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
