@extends('layouts.dashboard')
@section('title', 'Create event')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('events.index') }}">All events</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Add Event</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Add Event (Only 5 events can be uploaded per chapter)</h4>
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                        
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="title">Title</label>
                                        <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}">
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <label for="venue">Venue</label>
                                        <input type="text" class="form-control" id="venue" name="venue" value="{{ old('venue') }}">
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <label for="date">Date</label>
                                        <input type="date" class="form-control" id="date" name="date" value="{{ old('date') }}">
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <label for="time">Time</label>
                                        <input type="time" class="form-control" id="time" name="time" value="{{ old('time') }}">
                                    </fieldset>
                                
                                    <fieldset class="form-group">
                                        <label for="banners">Upload Banner (Only jpeg,png,jpg formats accepted | maximum of 2mb)</label>
                                        <input type="file"  accept="image/*" class="form-control" name="banners" id="banner">	
                                    </fieldset>  
                                    @if(auth()->user()->role == 1)
                                    <fieldset class="form-group">
                                        <label for="chapter_id">Campus</label>
                                        <select class="form-control" name="chapter_id" id="chapter_id" required>
                                            {{-- //include chapter --}}
                                            <option value="">--Select Campus--</option>
                                            <option value="0">General</option>
                                            @foreach($chapters as $chapter)
                                            <option value="{{ $chapter->id ?? old('chapter_id')}}" {{ old('chapter_id') == $chapter->id ? 'selected' : ''}}>{{ $chapter->name }}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                    @endif  
                                    <div class="form-check form-group">
                                        <input name="sendemail" class="form-check-input" type="checkbox" value="1" id="sendemail">
                                        <label class="form-check-label" for="sendemail">
                                          Send Emails to alumni and students
                                        </label>
                                    </div> 
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
        </div>
    </section>
    <!-- Basic Inputs end -->     
</div>
@endsection
