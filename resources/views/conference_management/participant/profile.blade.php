@extends('layouts.dashboard')
@section('title', 'Update User')
@section('active')
<li class="breadcrumb-item">Update Profile</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Update Profile</h4>
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('profile.save') }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="col-md-12 col-sm-12">
								<fieldset class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" name="username" id="username" value="{{ $user->username }}" readonly required>
                                </fieldset>
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
                                    <input type="text" class="form-control" name="phone" id="username" value="{{ old('phone') ?? $user->phone }}">
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="bank">Bank Name</label>
                                    <input type="text" class="form-control" name="bank" id="bank" value="{{ old('bank') ?? $user->bank }}">
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="account_name">Account Name</label>
                                    <input type="text" class="form-control" name="account_name" id="account_name" value="{{ old('account_name') ?? $user->account_name }}">
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="account_number">Account Number</label>
                                    <input type="text" class="form-control" name="account_number" id="account_number" value="{{ old('account_number') ?? $user->account_number }}">
                                </fieldset>
           
                                <fieldset class="form-group">
                                    <label data-toggle="tooltip" title="View/Edit Post" style="color:blue" for="password"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                                    Change Password</label><small class="text-muted"><i style="color:red"> (Leave blank except you want to reset your password)</i></small>
                                    <input type="text" class="form-control" name="password" id="username" value="{{ old('password') }}" placeholder="Enter password">
                                </fieldset>

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