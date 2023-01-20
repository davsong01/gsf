@extends('layouts.dashboard')
@section('title', 'Send Email')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('email.index') }}">Emails</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Send Emails</li>
@endsection
@section('content')
<div class="content-body">
    <script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('useremails.store') }}" method="POST"  onsubmit="return confirm('Are you sure you want to send emails?');">
                            @csrf
                            @include('includes.alerts')
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="name">Recipients</label>
                                        <select class="form-control" name="recipient" id="recipient" required>
                                            <option value="All">All</option>
                                            <option value="1">Alumni</option>
                                            <option value="0">Student</option>
                                        </select>
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <label for="chapter_id">Campus</label>
                                        <select class="form-control" name="chapter_id" id="chapter_id" required>
                                            <option value="All">All</option>
                                            @foreach($chapters as $chapter)
                                            <option value="{{ $chapter->id }}">{{ $chapter->name }}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <label for="phone">Subject</label>
                                        <input type="text" class="form-control" id="subject" name="subject" value="{{ old('subject') }}" placeholder="Enter subject" required>
                                    </fieldset>
                                   
                                    <fieldset class="form-group">
                                        <label for="content">Content</label>
                                        <textarea class="form-control" id="content" rows="3" name="content" rows="10" cols="200">{{ old('content') }}</textarea required>
                                        <div class="form-control-position">
                                           &#9745;
                                        </div>
                                    </fieldset>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <button class="btn btn-primary" style="width:100%" type="submit">Send</button>
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
CKEDITOR.replace('content' );
</script>
@endsection