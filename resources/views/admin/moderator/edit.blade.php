@extends('layouts.dashboard')
@section('title', 'Update moderator')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('moderators.index') }}">Moderators</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Update Moderator</li>
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
                            <form action="{{ route('moderators.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                          <div class="row">
                           <div class="col-md-4 col-sm-12">
                                <fieldset class="form-group">
                                <label for="created_at">Date Paid</label>
                                <input type="text" class="form-control" name="created_at" id="created_at" value="{{ $user->created_at }}" disabled required>
                                </fieldset>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                 <fieldset class="form-group">
                                <label for="created_at">Registration Slots Used</label>
                                <input type="number" class="form-control" name="slots_filled" id="slot_filled" value="{{ $user->slot_filled }}" disabled required>
                                </fieldset>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                 <fieldset class="form-group">
                                <label for="created_at">Registration Slots Remaining</label>
                                <input type="number" class="form-control" name="slots_filled" id="slot_filled" value="{{ $user->slot - $user->slot_filled }}" disabled required>
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
                                    <label for="gender">Gender</label>
                                    <select class="form-control" name="gender" id="gender" required>
                                        <option value="Male" {{ $user->gender == 'Male' ? 'selected' : ''}}>Male</option>
                                        <option value="Female" {{ $user->gender == 'Female' ? 'selected' : ''}}>Female</option>
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
                            </div>

                            <div class="col-md-6 col-sm-12">
                                 <fieldset class="form-group">
                                    <label for="amount">Amount Paid (&#8358;)</label>
                                    <input type="number" id="amount" name="amount_paid" class="form-control" value="{{ old('amount_paid') ?? $user->amount_paid }}" required>
                                </fieldset>
                                <fieldset class="form-group">
                                    <label for="slot">Slot</label>
                                    <input type="number" id="slot" name="slot" class="form-control" value="{{ old('slot') ?? $user->slot }}" required>
                                </fieldset>
                                <fieldset class="form-group">
                                    <label for="payment_type">Payment Type</label>
                                    <input type="text" id="payment_type" name="payment_type" class="form-control" value="{{ old('payment_type') ?? $user->payment_type }}" required>
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="transid">Transaction ID</label>
                                    <input type="text" id="transid" name="transid" class="form-control" value="{{ old('transid') ?? $user->transid }}" required>
                                </fieldset>
                                
                                <fieldset class="form-group">
                                    <label for="password">Password</label><small class="text-muted"><i style="color:red">Leave blank except you want to reset participant's password</i></small>
                                    <input type="text" class="form-control" name="password" id="password" value="{{ old('password') }}" placeholder="Enter password">
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