@extends('layouts.conference')

@section('title', isset($hostel) ? 'Update Hostel' : 'Create Hostel')

@section('item')
<li class="breadcrumb-item">
    <a href="{{ route('hostels.index',['edition'=>$edition->id]) }}">Hostels</a>
</li>
@endsection

@section('active')
<li class="breadcrumb-item">
    {{ isset($hostel) ? 'Update Hostel' : 'Create Hostel' }}
</li>
@endsection

@section('content2')
<div class="content-body">
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            {{ isset($hostel) ? 'Update: '.$hostel->name : 'Create Hostel' }}
                        </h4>

                        @if(isset($hostel))
                            <a href="{{ route('hostelusers.export',['id'=>$hostel->id, 'edition'=>$edition->id]) }}"
                            class="btn btn-primary mt-1">
                            Export Hostel Participants
                            </a>
                        @endif
                        @include('includes.alerts')
                    </div>

                    <div class="card-body">
                        <form action="{{ isset($hostel)
                                ? route('hostels.update', [$hostel->id, 'edition_id'=>$edition->id])
                                : route('hostels.store', ['edition_id'=>$edition->id]) }}"
                            method="POST">

                            @csrf
                            @if(isset($hostel))
                                @method('PATCH')
                            @endif

                            <div class="row">
                                <div class="col-md-12">

                                    <fieldset class="form-group">
                                        <label>Name</label>
                                        <input type="text" class="form-control" name="name"
                                            value="{{ old('name', $hostel->name ?? '') }}" required>
                                    </fieldset>

                                    <fieldset class="form-group">
                                        <label>Type</label>
                                        <select class="form-control" name="type" required>
                                            <option value="Male" {{ old('type', $hostel->type ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ old('type', $hostel->type ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                    </fieldset>
                                    <input type="hidden" name="conference_edition_id" value="{{$edition->id}}">
                                    <fieldset class="form-group">
                                        <label>Conference Plan</label>
                                        <select class="form-control" name="level" required>
                                            @foreach($conferenceplans as $plan)
                                                <option value="{{ $plan->level }}"
                                                    {{ old('level', $hostel->level ?? '') == $plan->level ? 'selected' : '' }}>
                                                    {{ $plan->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </fieldset>

                                    <fieldset class="form-group">
                                        <label>Fields (Optional)</label>
                                        <select class="form-control" name="field_ids[]" multiple>
                                            @foreach($fields as $field)
                                                <option value="{{ $field->id }}"
                                                    {{ isset($hostel) && in_array($field->id, $hostel->fields->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                    {{ $field->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </fieldset>

                                    <fieldset class="form-group">
                                        <label>Chapters (Optional)</label>
                                        <select class="form-control" name="chapter_ids[]" multiple>
                                            @foreach($chapters as $chapter)
                                                <option value="{{ $chapter->id }}"
                                                    {{ isset($hostel) && in_array($chapter->id, $hostel->chapters->pluck('id')->toArray()) ? 'selected' : '' }}>
                                                    {{ $chapter->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </fieldset>

                                    <fieldset class="form-group">
                                        <label>Capacity</label>
                                        <input type="number" name="capacity" class="form-control"
                                            value="{{ old('capacity', $hostel->capacity ?? '') }}" required>
                                    </fieldset>

                                    @if(isset($hostel))
                                    <fieldset class="form-group">
                                        <label>Allocation</label>
                                        <input type="number" class="form-control" disabled
                                            value="{{ $hostel->allocation }}">
                                    </fieldset>
                                    @endif

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-primary w-100" type="submit">
                                        {{ isset($hostel) ? 'Update Hostel' : 'Create Hostel' }}
                                    </button>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>
@endsection
