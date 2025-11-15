@extends('layouts.dashboard')
@section('title', 'Add new participant')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('conferencemanagement.show', ['conferencemanagement'=>$payment->id, 'edition'=>$payment->conference_edition_id]) }}">My Participants</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Add New Participant</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Add New Participant</h4>
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form
                                action="{{ route('conference.participants.store',['edition'=>$edition->id]) }}"
                                onsubmit="return confirm('I am sure all inputed details are correct and current');"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                              
                                <div class="row">
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="@error('name')is-invalid @enderror form-control"
                                                id="name" name="name"
                                                value="{{ old('name') }}"
                                                placeholder="Enter name">
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" id="email" name="email"
                                                class="form-control @error('email')is-invalid @enderror"
                                                value="{{ old('email') }}"  placeholder="Enter email"
                                                required>
                                            </fieldset>

                                        <fieldset class="form-group">
                                            <label for="phone">Phone</label>
                                            <input type="phone" id="phone" name="phone"
                                                class="form-control @error('phone')is-invalid @enderror"
                                                value="{{ old('phone') }}"  placeholder="Enter phone"
                                                required>
                                          
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="gender">Gender</label>
                                            <select class="form-control @error('gender')is-invalid @enderror" name="gender"
                                                id="gender" required>
                                                <option value="">--Select Option--</option>
                                                <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>
                                                    Male</option>
                                                <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>
                                                    Female</option>
                                            </select>
                                           
                                        </fieldset>
                                       
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                         <fieldset class="form-group @error('passport')is-invalid @enderror">
                                            <label for="gender">Upload Passport (Only jpeg and png files of less than 200kb allowed)</label>
                                            <input type="file" accept="image/*" class="form-control" name="passport"
                                                id="passport">
                                        </fieldset>
                                        

                                      <fieldset class="form-group">
                                            <label for="password">Password (Default password is this participant's phone number)</label><small class="text-muted"><i
                                                    style="color:red">Leave blank except you want to change this participant's password
                                                    password</i></small>
                                            <input type="text" class="form-control" name="password" id="password"
                                                value="{{ old('password') }}"
                                                placeholder="Enter password">
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
        </div>
</div>
</section>
<!-- Basic Inputs end -->
</div>
@endsection