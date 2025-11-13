@extends('layouts.conference')
@section('title', isset($conferencePlan) ? 'Edit Conference Plan' : 'Create Conference Plan')

@section('content2')
<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ isset($conferencePlan) ? 'Edit Conference Plan' : 'Add Conference Plan' }}</h4>
            @include('includes.alerts')

        </div>
        <div class="card-body">
            <form action="{{ isset($conferencePlan) ? route('conference_plans.update', ['conferencePlan' => $conferencePlan->id, 'edition' => $edition]) : route('conference_plans.store',['edition' => $edition]) }}" method="POST">
                @csrf
                @if(isset($conferencePlan))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label for="title">Plan Name</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $conferencePlan->title ?? '') }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label for="price">Plan Price</label>
                            <input type="number" min="1" name="price" id="price" value="{{ old('price', $conferencePlan->price ?? '') }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label for="type">Type</label>
                            <select name="type" id="type" class="form-control" required>
                                <option value="">-- Select Type --</option>
                                <option value="single" {{ old('type', $conferencePlan->type ?? '') == 'single' ? 'selected' : '' }}>Single</option>
                                <option value="multiple" {{ old('type', $conferencePlan->type ?? '') == 'multiple' ? 'selected' : '' }}>Multiple</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label for="level">Participant Level</label>
                            <select name="level" id="level" class="form-control" required>
                                <option value="">-- Select Level --</option>
                                <option value="Participant" {{ old('level', $conferencePlan->level ?? '') == 'Participant' ? 'selected' : '' }}>Participant</option>
                                <option value="Moderator" {{ old('level', $conferencePlan->level ?? '') == 'Moderator' ? 'selected' : '' }}>Moderator</option>
                                <option value="Alumni" {{ old('level', $conferencePlan->level ?? '') == 'Alumni' ? 'selected' : '' }}>Alumni</option>
                                <option value="GSF Nec" {{ old('level', $conferencePlan->level ?? '') == 'GSF Nec' ? 'selected' : '' }}>GSF Nec</option>
                                <option value="GYF Nec" {{ old('level', $conferencePlan->level ?? '') == 'GYF Nec' ? 'selected' : '' }}>GYF Nec</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="">-- Select Status --</option>
                                <option value="1" {{ old('status', $conferencePlan->status ?? '') == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $conferencePlan->status ?? '') == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label for="registration_fields">Registration Fields</label>
                            <select name="registration_fields[]" id="registration_fields" class="form-control" required multiple>
                                @php
                                    $selectedFields = old('registration_fields', isset($conferencePlan->registration_fields) ? $conferencePlan->registration_fields : []);
                                @endphp

                                @foreach($registration_fields as $field)
                                    <option value="{{ $field->id }}" {{ in_array($field->id, $selectedFields) ? 'selected' : '' }}>
                                        {{ $field->label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label for="items">Items <small class="text-muted">(Comma or Enter separated)</small></label>
                            <textarea name="items" id="items" rows="4" class="form-control" placeholder="Enter each item on a new line or separated by commas...">{{ old('items', isset($conferencePlan->items) ? (is_array($conferencePlan->items) ? implode("\n", $conferencePlan->items) : str_replace(',', "\n", $conferencePlan->items)) : '') }}</textarea>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">
                    {{ isset($conferencePlan) ? 'Update Plan' : 'Create Plan' }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
