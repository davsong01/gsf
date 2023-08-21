@extends('layouts.dashboard')
@section('title', 'Update chapter')
@if(auth()->user()->isAdmin()) 
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('chapters.index') }}">Chapters</a></li>
@endsection
@endif
@section('active')
@if(auth()->user()->isAdmin()) 
<li class="breadcrumb-item">Update chapter</li>
@else
<li class="breadcrumb-item">Update {{ $chapter->name }}</li>
@endif
@endsection
@section('content')
<script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{-- <h4 class="card-title">Update: {{ $chapter->name }}</h4> --}}
                        {{-- @include('includes.alerts') --}}
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('chapters.update', $chapter->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="name">Name</label>
                                        <input @if(!auth()->user()->isAdmin())readonly @endif type="text" class="form-control" id="name" name="name" value="{{ old('name') ?? $chapter->name }}" placeholder="Enter name">
                                    </fieldset> 
                                    <fieldset class="form-group">
                                        <label for="field">Field</label>
                                        <input disabled class="form-control" type="text" value="{{ $chapter->zone->field->name ?? ''}}">
                                    </fieldset> 
                                    @if(auth()->user()->isAdmin()) 
                                    <fieldset class="form-group">
                                        <label for="zone_id">Zone</label>
                                        <select class="form-control" name="zone_id" id="zone_id" required>
                                            <option value="">--Select--</option>
                                            @foreach($zones as $zone)
                                            <option value="{{ $zone->id }}" {{ $chapter->zone_id == $zone->id ? 'selected' : ''}}>{{ $zone->name }}</option>
                                            @endforeach
                                        </select>
                                    </fieldset>
                                    @else
                                    <fieldset class="form-group">
                                        <label for="zone_id">Zone</label>
                                        <input class="form-control" type="text" readonly name="zone_id" value="{{ $chapter->zone->name }}">
                                    </fieldset>
                                    @endif
                                    <fieldset class="form-group">
                                        <label for="address">Address(Optional)</label>
                                        <input type="text" class="form-control" id="address" name="address" value="{{ old('address') ?? $chapter->address}}" placeholder="Enter address">
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <label for="email">Email(Optional)</label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') ?? $chapter->email }}" placeholder="Enter address">
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <label for="phone">Phone(Optional)</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') ?? $chapter->phone }}" placeholder="Enter address">
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <label for="facebook">Facebook Link(Optional)</label>
                                        <input type="text" class="form-control" id="facebook" name="facebook" value="{{ old('facebook') ?? $chapter->facebook}}" placeholder="Enter address">
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <label for="twitter">Twitter Link(Optional)</label>
                                        <input type="text" class="form-control" id="twitter" name="twitter" value="{{ old('twitter') ?? $chapter->twitter}}" placeholder="Enter address">
                                    </fieldset>    
                                    <label for="about">About</label><small> <br>Write brief biography of chapter, you can include fellowship periods</small>
                                    <fieldset class="form-group position-relative has-icon-left">
                                        <textarea class="form-control" id="about" rows="3" name="about" rows="10" cols="200">{!! old('about') ?? $chapter->about !!}</textarea>
                                       
                                    </fieldset>
                                    <fieldset class="form-group">
                                        <label for="chapter_banner">Change banner</label> <br>
                                        <img style="width:100px" src="{{ asset($chapter->banner) }}" alt="{{ $chapter->name . 'banner' }}" class="mb10 responsive-image"> <br><br>
                                        <input type="file" accept="image/*" class="form-control" name="chapter_banner" id="chapter_banner">	
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
<script>
    CKEDITOR.replace( 'about' );
</script>
@endsection