@extends('layouts.dashboard')
@section('title', 'Create User')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('users.index') }}">Users</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Create User</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Add New User</h4>
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('users.store') }}" method="POST">
                            @csrf
                            <div class="col-md-12 col-sm-12">
                                <fieldset class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Enter name">
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" name="username" id="username" value="{{ old('username') }}" placeholder="Enter Username" required>
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="Enter Email" value="{{ old('email') }}" required>
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="verify">Verify?</label>
                                    <select class="form-control" name="verify" id="verify" required>
                                        <option value="{{ now() }}" {{ old('verify') == now() ? 'selected' : '' }}>Yes</option>
                                        <option value="NULL" {{ old('verify') == NULL ? 'selected' : '' }} >No</option>
                                    </select>
                                </fieldset>
                           
                                <fieldset class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control" name="phone" id="username" value="{{ old('phone') }}" placeholder="Enter phone">
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="password">Password</label>
                                    <input type="text" class="form-control" name="password" id="username" value="{{ old('password') }}" placeholder="Enter password" required>
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="verify">Permission</label>
                                    <select class="form-control" name="permission" id="permission" required>
                                        <option value="1" selected>User</option>
                                        <option value="2">Admin</option>
                                    </select>
                                </fieldset>
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
