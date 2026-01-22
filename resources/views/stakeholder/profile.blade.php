@extends('layouts.stakeholderdashboard')
@section('title', 'Profile')

@section('active')
<li class="breadcrumb-item">Profile</li>
@endsection
@section('content')
@php
    $user = Auth()->guard('stakeholder')->user();
@endphp
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Edit Profile</h4>
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('stakeholders.saveprofile') }}"
                                onsubmit="return confirm('You are about to update your profile');"
                                method="POST" enctype="multipart/form-data">
                                @csrf

                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?? $user->name }}" placeholder="Enter name" required>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="phone">Phone</label>
                                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') ?? $user->phone }}" placeholder="Enter phone" required>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="email">Email</label>
                                            <input type="text" class="form-control" id="email" name="email" value="{{ old('email') ?? $user->email }}" placeholder="Enter email address" required>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="gender">Gender</label>
                                            <select class="form-control" name="gender" id="gender">
                                                <option value="">--Select--</option>
                                                <option value="Male" {{ old('gender', $user->gender ?? '') === 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ old('gender', $user->gender ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <label>Birthday Details</label> <br>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="day">Day</label>
                                            <input type="number" min="1" max="31" class="form-control" id="day" name="day" value="{{ old('day') ?? $user->day }}" placeholder="Enter day" required>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="month">Month</label>
                                            <input type="number" min="1" max="12" class="form-control" id="month" name="month" value="{{ old('month') ?? $user->month }}" placeholder="Enter month" required>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-4 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="year">Year (Optional), e.g. {{ date('Y') }}</label>
                                            <input type="text" class="form-control" id="year" pattern="^\d{4}$" name="year" value="{{ old('year') ?? $user->year}}" placeholder="Enter year">
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="password">Change Password</label>
                                            <input type="text" class="form-control" id="password" name="password" value="{{ old('password') }}" placeholder="Enter a new password or leave blank to use current">
                                        </fieldset>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="avatar">Avatar</label>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                @if(!empty($user?->avatar))
                                                    <img
                                                        src="{{ asset($user->avatar) }}"
                                                        alt="Current Avatar"
                                                        style="height: 55px; width: 55px; border-radius: 50%; object-fit: cover;">
                                                @endif
                                                <input
                                                    type="file"
                                                    class="form-control"
                                                    id="avatar"
                                                    name="avatar"
                                                    placeholder="Upload avatar"
                                                    style="flex: 1;">
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <button class="btn btn-primary" style="width:100%" type="submit">Save</button>
                                </div>
                            </div>
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
<script>
$(document).ready(function(){
    var value = $('#communion').find(':selected');
    console.log(value);
    alert(value);
});
})


    $('#communion').on('change', function(){
        alert('as');
        console.log($('#communion').val());

            if($('#communion').val()=='Yes'){
                $('.communion-details').css('display','block');

            }else if($('#communion').val()=='No'){
                $('.communion-details').css('display','none');

            }
    });


</script>

@endsection
