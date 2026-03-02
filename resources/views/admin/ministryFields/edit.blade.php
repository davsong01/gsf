@extends('layouts.dashboard')
@section('title', isset($field) ? 'Edit Field' : 'Create Field')
@section('content')
<div class="content-body">
    @include('includes.alerts')

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">
                {{ isset($field) ? 'Edit Field: ' . $field->label : 'Create New Field' }}
            </h4>
        </div>

        <div class="card-body">
            <form
                action="{{ isset($field)
                    ? route('ministries.fields.update', [$ministry->id, $field->id])
                    : route('ministries.fields.store', $ministry->id)
                }}"
                method="POST"
            >
                @csrf
                @if(isset($field))
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label for="name">Field Key</label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control"
                        value="{{ old('name', $field->name ?? '') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="label">Field Label</label>
                    <input
                        type="text"
                        name="label"
                        id="label"
                        class="form-control"
                        value="{{ old('label', $field->label ?? '') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="type">Field Type</label>
                    <select name="type" id="type" class="form-control" required>
                        @foreach(['text','number','email','select','textarea','checkbox','radio'] as $type)
                            <option value="{{ $type }}"
                                {{ old('type', $field->type ?? '') === $type ? 'selected' : '' }}>
                                {{ ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="onchange">Display Order</label>
                    <input
                        type="number"
                        name="display_order"
                        id="display_order"
                        class="form-control"
                        value="{{ old('display_order', $field->display_order ?? '') }}"
                    >
                </div>
                <div class="form-group">
                    <label for="field_usage">Field Usage</label>
                    <select name="field_usage" id="field_usage" class="form-control" required>
                        @foreach(['registration','allocation','both'] as $usage)
                            <option value="{{ $usage }}"
                                {{ old('field_usage', $field->field_usage ?? '') === $usage ? 'selected' : '' }}>
                                {{ ucfirst($usage) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="registration_types">Registration Types</label>
                    <input type="hidden" name="registration_types" value="">

                    @php
                        $selectedTypes = old('registration_types', $field->registration_types ?? []);
                        $selectedTypes = is_array($selectedTypes) ? $selectedTypes : [];
                    @endphp


                    <small class="text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple.</small>
                </div>

                @php
                    $optionsValue = is_array($field->options ?? null)
                        ? json_encode($field->options, JSON_PRETTY_PRINT)
                        : ($field->options ?? '');
                @endphp

                <div class="form-group">
                    <label for="options">Options (JSON Key–Value)</label>
                    <textarea
                        name="options"
                        id="options"
                        class="form-control"
                        rows="5"
                        placeholder='Example: {"male":"Male","female":"Female"}'
                    >{{ old('options', $optionsValue) }}</textarea>

                    <small class="text-muted d-block mt-1">
                        Enter valid JSON key–value pairs.
                        Example: <code>{"option_key": "Option Label", "another_key": "Another Label"}</code>
                    </small>
                </div>


                <div class="form-group">
                    <label for="required">Required</label>
                    <select name="required" id="required" class="form-control">
                        <option value="1" {{ old('required', $field->required ?? '') == 1 ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('required', $field->required ?? '') == 0 ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="1" {{ old('status', $field->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ old('status', $field->status ?? 0) == 0 ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="has_other_option">Has 'Other' Option</label>
                    <select name="has_other_option" id="has_other_option" class="form-control">
                        <option value="1" {{ old('has_other_option', $field->has_other_option ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ old('has_other_option', $field->has_other_option ?? 0) == 0 ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="onchange">OnChange JS (Optional)</label>
                    <input
                        type="text"
                        name="onchange"
                        id="onchange"
                        class="form-control"
                        value="{{ old('onchange', $field->onchange ?? '') }}"
                    >
                </div>

                <div class="form-group">
                    <label for="depends_on">Depends On (Optional JSON)</label>
                    <input
                        type="text"
                        name="depends_on"
                        id="depends_on"
                        class="form-control"
                        value="{{ old('depends_on', is_array($field->depends_on ?? null) ? json_encode($field->depends_on) : ($field->depends_on ?? '')) }}"
                        placeholder='Example: {"chapter_id":1}'
                    >
                </div>

                <button type="submit" class="btn btn-success mt-2">
                    {{ isset($field) ? 'Update Field' : 'Create Field' }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
