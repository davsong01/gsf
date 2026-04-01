@extends('layouts.conference')
@section('title', isset($food) ? 'Update Service Point' : 'Create Service Point')
@section('item')
<li class="breadcrumb-item">
    <a href="{{ route('foods.index',['edition'=>$edition->id]) }}">Service Points</a>
</li>
@endsection
@section('active')
<li class="breadcrumb-item">{{ isset($food) ? 'Update Service Point' : 'Create Service Point' }}</li>
@endsection
@section('content2')
<div class="content-body">
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">{{ isset($food) ? 'Update: '.$food->name : 'Create Service Point' }}</h4>
                        @if(isset($food))
                        <a href="{{ route('foodusers.export',['id'=>$food->id, 'edition'=>$edition->id]) }}" class="btn btn-primary mt-1">Export Service Point Participants</a>
                        @endif
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ isset($food) ? route('foods.update',['food'=>$food->id, 'edition'=>$edition->id]) : route('foods.store',['edition'=>$edition->id]) }}" method="POST">
                                @csrf
                                @if(isset($food))
                                    @method('PATCH')
                                @endif

                                <div class="row">
                                    <div class="col-md-12 col-sm-12">

                                        <fieldset class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') ?? ($food->name ?? '') }}" placeholder="Enter name" required>
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label>Conference Plan</label>
                                            <select class="form-control" name="conference_plan_id" required>
                                                <option value="">Select Conference Plan</option>
                                                @foreach($conferenceplans as $plan)
                                                    <option value="{{ $plan->id }}"
                                                        {{ old('conference_plan_id', $food->conference_plan_id ?? '') == $plan->id ? 'selected' : '' }}>
                                                        {{ $plan->title }} ({{ $plan->level }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="field_ids">Fields (Optional)</label>
                                            <select class="form-control" name="field_ids[]" id="field_ids" multiple>
                                                @foreach($fields as $field)
                                                    <option value="{{ $field->id }}" {{ isset($food) && in_array($field->id, $food->fields->pluck('id')->toArray()) ? 'selected' : '' }}>{{ $field->name }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="chapter_ids">Chapters (Optional)</label>
                                            <select class="form-control" name="chapter_ids[]" id="chapter_ids" multiple>
                                                @foreach($chapters as $chapter)
                                                    <option value="{{ $chapter->id }}" {{ isset($food) && in_array($chapter->id, $food->chapters->pluck('id')->toArray()) ? 'selected' : '' }}>{{ $chapter->name }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="capacity">Capacity</label>
                                            <input type="number" id="capacity" name="capacity" class="form-control" value="{{ old('capacity') ?? ($food->capacity ?? '') }}" required>
                                        </fieldset>

                                        @if(isset($food))
                                        <fieldset class="form-group">
                                            <label for="allocation">Allocation</label>
                                            <input type="number" id="allocation" name="allocation" class="form-control" value="{{ old('allocation') ?? $food->allocation }}" disabled>
                                        </fieldset>
                                        @endif

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <button class="btn btn-primary" style="width:100%" type="submit">{{ isset($food) ? 'Update' : 'Create' }}</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
