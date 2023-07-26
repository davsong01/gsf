@extends('layouts.dashboard')
@section('title', 'Edit zone')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('zones.index') }}">Zones</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Update zone</li>
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
                            <form action="{{ route('zones.update', $zone->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?? $zone->name }}" placeholder="Enter name" required>
                                    </fieldset>
                                    
                                </div>
                                <div class="col-md-12 col-sm-12">
                                    <fieldset class="form-group">
                                        <label for="field_id">Parent Field</label>
                                        <select class="form-control" name="field_id" id="field_id" required>
                                            @foreach($fields as $field)
                                            <option value="{{ $field->id }}" {{ $zone->field_id == $field->id ? 'selected' : ''}}>{{ $field->name }}</option>
                                            @endforeach
                                        </select>
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