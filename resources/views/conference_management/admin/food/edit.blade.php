@extends('layouts.conference')
@section('title', 'Update foodstand')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('foods.index',['edition'=>$edition->id]) }}">Foodstands</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Update foodstand</li>
@endsection
@section('content2')
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                     <div class="card-header">
                        <h4 class="card-title">Update: {{ $food->name }}</h4>
                        <a href="{{ route('foodusers.export',['id'=>$food->id, 'edition'=>$edition->id]) }}" class="btn btn-primary mt-1">Export Foodstand Participants</a>                        
                        
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                        <form action="{{ route('foods.update',['food'=>$food->id, 'edition'=>$edition->id]) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <fieldset class="form-group">
                                    <label for="name">Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?? $food->name }}" placeholder="Enter name">
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="level">Level</label>
                                    <select class="form-control" name="level" id="level" required>
                                        <option value="Alumni" {{ $food->level == 'Admin' ? 'selected' : ''}}>Alumni</option>
                                        <option value="Participant" {{ $food->level == 'Participant' ? 'selected' : ''}}>Participant</option>
                                        <option value="Choir" {{ $food->level == 'Choir' ? 'selected' : ''}}>Choir</option>
                                        <option value="Official" {{ $food->level == 'Official' ? 'selected' : ''}}>Official</option>
                                        <option value="Medical" {{ $food->level == 'Medical' ? 'selected' : ''}}>Medical</option>
                                        <option value="Nec" {{ $food->level == 'Nec' ? 'selected' : ''}}>Nec</option>
                                    </select>
                                </fieldset>

                                <fieldset class="form-group">
                                    <label for="capacity">Capacity</label>
                                    <input type="number" id="capacity" name="capacity" class="form-control" value="{{ old('capacity') ?? $food->capacity }}" required>
                                </fieldset>

                            <fieldset class="form-group">
                                <label for="allocation">Allocation</label>
                                <input type="numer" id="allocation" name="allocation" class="form-control" disabled value="{{ old('allocation') ?? $food->allocation }}" required>
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
@endsection