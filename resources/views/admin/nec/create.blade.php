@extends('layouts.dashboard')
@section('title', 'Add NEC Member')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('nec.index') }}">NEC</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Add NEC Member</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                   
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('nec.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                                                  
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
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
                                    <label for="office">Office</label>
                                    <input type="text" id="office" placeholder="Enter office" name="office" class="form-control" value="{{ old('office') }}" required>
                                </fieldset>
                                <fieldset class="form-group">
                                    <label for="order">Order</label>
                                    <input type="number" id="order" placeholder="Enter order" name="order" class="form-control" value="{{ old('order') }}" required>
                                </fieldset>
                                <fieldset class="form-group">
                                    <label for="bday">Birth Day/Month</label>
                                    <input type="text" id="bday" placeholder="Enter bday" name="bday" class="form-control" value="{{ old('bday') }}" required>
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="gender">Gender</label>
                                    <select class="form-control" name="gender" id="gender" required>
                                         <option value="">--Select Gender--</option>
                                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : ''}}>Male</option>
                                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : ''}}>Female</option>
                                    </select>
                                </fieldset>
                                <fieldset class="form-group @error('passport')is-invalid @enderror">
                                    <label for="passport">Upload Passport</label>
                                    <input type= "file"  accept="image/*" class="form-control" name="passport" id="passport">	
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

