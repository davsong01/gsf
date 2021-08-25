@extends('layouts.dashboard')
@section('title', 'New stakeholder')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('staff.index') }}">All Stakeholders</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Create stakeholder</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('staff.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Enter name" required>
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <label for="phone">Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Enter phone" required>
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <label for="email">Email</label>
                                        <input type="text" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Enter email address" required>
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <label for="signature">Upload Signature</label>
                                        <input type="file" class="form-control" id="signature" name="signature" value="{{ old('signature') }}" placeholder="Upload signature" required>
                                    </fieldset>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="field_id">Field</label>
                                        <select class="form-control" name="field_id" id="field_id" required>
                                            @foreach($fields as $field)
                                            <option value="{{ $field->id }}" {{ old('field_id') == $field->id ? 'selected' : ''}}>{{ $field->name }}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <label for="zone_id">Zone</label>
                                        <select class="form-control" name="zone_id" id="zone_id" required>
                                            @foreach($zones as $zone)
                                            <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : ''}}>{{ $zone->name }}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <label for="chapter_id">Chapter</label>
                                        <select class="form-control" name="chapter_id" id="chapter_id">
                                            @foreach($chapters as $chapter)
                                            <option value="{{ $chapter->id }}" {{ old('chapter_id') == $chapter->id ? 'selected' : ''}}>{{ $chapter->name }}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <label for="role">Role</label>
                                        <select class="form-control" name="role" id="role" required>
                                            <option value="President" {{ old('role') == 'President' ? 'selected' : ''}}>President</option>
                                            <option value="Zonal Pastor" {{ old('role') == 'Zonal Pastor' ? 'selected' : ''}}>Zonal Pastor</option>
                                            <option value="Field Pastor" {{ old('role') == 'Field Pastor' ? 'selected' : ''}}>Field Pastor</option>
                                            <option value="Secretariat" {{ old('role') == 'Secretariat' ? 'selected' : ''}}>Secretariat</option>
                                        </select>
                                    </fieldset>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="password">Password(Default: 12345@GSF2021)</label>
                                        <input type="password" class="form-control" id="password" name="password" value="{{ old('password') }}" placeholder="Enter password or leave blank to use default">
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <label>Birthday Details</label> <br>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="day">Day</label>
                                        <input type="number" min="1" max="31" class="form-control" id="day" name="day" value="{{ old('day') }}" placeholder="Enter day" required>
                                    </fieldset>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="month">Month</label>
                                        <input type="number" min="1" max="12" class="form-control" id="month" name="month" value="{{ old('month') }}" placeholder="Enter month" required>
                                    </fieldset>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="year">Year (Optional), e.g. {{ date('Y') }}</label>
                                        <input type="text" class="form-control" id="year" pattern="^\d{4}$" name="year" value="{{ old('year') }}" placeholder="Enter year">
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