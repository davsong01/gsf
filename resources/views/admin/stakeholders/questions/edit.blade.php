@extends('layouts.dashboard')

@section('title', isset($question) ? 'Update Question' : 'Create Question')

@section('item')
<li class="breadcrumb-item">
    <a href="{{ route('stakeholder.questions.index') }}">All Questions</a>
</li>
@endsection

@section('active')
<li class="breadcrumb-item">{{ isset($question) ? 'Update' : 'Create' }} Question</li>
@endsection

@section('content')
<div class="content-body">
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">@include('includes.alerts')</div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ isset($question) ? route('stakeholder.questions.update', $question->id) : route('stakeholder.questions.store') }}" method="POST">
                                @csrf
                                @isset($question)
                                    @method('PATCH')
                                @endisset

                                <div class="row g-2">

                                    {{-- Label --}}
                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <label>Label</label>
                                            <input
                                                type="text"
                                                name="label"
                                                class="form-control"
                                                value="{{ old('label', $question->label ?? '') }}"
                                                required
                                            >
                                        </fieldset>
                                    </div>

                                    {{-- Slug --}}
                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <label>Slug</label>
                                            <input
                                                type="text"
                                                name="slug"
                                                class="form-control"
                                                value="{{ old('slug', $question->slug ?? '') }}"
                                            >
                                        </fieldset>
                                    </div>

                                    {{-- Section --}}
                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <label>Section</label>
                                            <select name="section_id" class="form-control">
                                                <option value="">-- Select --</option>
                                                @foreach($sections as $section)
                                                    <option value="{{ $section->id }}" {{ old('section_id', $question->section_id ?? '') == $section->id ? 'selected' : '' }}>{{ $section->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Sub Section</label>
                                        <select name="sub_section_id" class="form-control">
                                            <option value="">-- Select --</option>
                                            @foreach($subsections as $sub)
                                                <option value="{{ $sub->id }}"
                                                    {{ old('sub_section_id', $question->sub_section_id ?? '') == $sub->id ? 'selected' : '' }}>
                                                    {{ $sub->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Status</label>
                                        <select name="status" class="form-control" required>
                                            <option value="">Select...</option>
                                            <option value="1" {{ old('status', $question->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('status', $question->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>

                                    {{-- Required --}}
                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <label>Required?</label>
                                            <select name="is_required" class="form-control" required>
                                                <option value="" selected>Select...</option>
                                                <option value="0" {{ old('is_required', $question->is_required ?? 0) == 0 ? 'selected' : '' }}>No</option>
                                                <option value="1" {{ old('is_required', $question->is_required ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                                            </select>
                                        </fieldset>
                                    </div>

                                    {{-- Width --}}
                                    <div class="col-md-3">
                                        <fieldset class="form-group">
                                            <label>Width Class</label>
                                            <select name="width_class" class="form-control" required>
                                                <option value="" selected>Select...</option>
                                                @foreach(['col-md-2','col-md-3','col-md-4','col-md-6','col-md-8','col-md-12'] as $width)
                                                    <option value="{{ $width }}"
                                                        {{ old('width_class', $question->width_class ?? 'col-md-6') === $width ? 'selected' : '' }}>
                                                        {{ strtoupper(str_replace('-', ' ', $width)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    {{-- Type --}}
                                    <div class="col-md-3">
                                        <fieldset class="form-group">
                                            <label>Type</label>
                                            <select name="type" class="form-control" required>
                                                <option value="" selected>Select...</option>
                                                @foreach(['text','number','textarea','select','radio','checkbox','date','month','year','rating','dynamic_table'] as $type)
                                                    <option value="{{ $type }}"
                                                        {{ old('type', $question->type ?? '') === $type ? 'selected' : '' }}>
                                                        {{ ucfirst(str_replace('_', ' ', $type)) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    {{-- Quantifiable --}}
                                    <div class="col-md-3">
                                        <label>Quantifiable?</label>
                                        <select name="is_quantifiable" class="form-control">
                                            <option value="" selected>Select...</option>
                                            <option value="0" {{ old('is_quantifiable', $question->is_quantifiable ?? 0) == 0 ? 'selected' : '' }}>No</option>
                                            <option value="1" {{ old('is_quantifiable', $question->is_quantifiable ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                                        </select>
                                    </div>
                                    {{-- Order --}}
                                    <div class="col-md-3">
                                        <fieldset class="form-group">
                                            <label>Order</label>
                                            <input type="number" name="order" class="form-control" value="{{ old('order', $question->order ?? 0) }}"
                                            >
                                        </fieldset>
                                    </div>

                                    <div class="col-md-12">
                                        <label>Options</label>
                                        <div id="options-wrapper">
                                            @php
                                                $options = old('options', $question->options ?? []);
                                            @endphp

                                            @if(is_array($options) && count($options) > 0)
                                                @foreach($options as $key => $value)
                                                    <div class="option-row mb-2 d-flex gap-2">
                                                        <input type="text" name="options_keys[]" class="form-control" placeholder="Option label" value="{{ $key }}">
                                                        <input type="text" name="options_values[]" class="form-control" placeholder="Option value" value="{{ $value }}">
                                                        <button type="button" class="btn btn-danger remove-option">Remove</button>
                                                    </div>
                                                @endforeach
                                            @else
                                                <div class="option-row mb-2 d-flex gap-2">
                                                    <input type="text" name="options_keys[]" class="form-control" placeholder="Option label">
                                                    <input type="text" name="options_values[]" class="form-control" placeholder="Option value">
                                                    <button type="button" class="btn btn-danger remove-option">Remove</button>
                                                </div>
                                            @endif
                                        </div>

                                        <button type="button" id="add-option" class="btn btn-primary btn-sm">Add Option</button>
                                        <small class="text-muted d-block mt-1">Used for select, radio, checkbox</small>
                                    </div>

                                    {{-- Permissions --}}
                                    <div class="col-md-12">
                                        <fieldset class="form-group">
                                            <label class="mb-1">Access Permissions</label>
                                            @php
                                                $selectedPermissions = old(
                                                    'access_permissions',
                                                    $question->permissions->pluck('id')->toArray() ?? []
                                                );
                                                
                                            @endphp

                                            <div class="row">
                                                @forelse($permissions as $permission)
                                                    <div class="col-md-4">
                                                        <div class="form-check">
                                                            <input
                                                                class="form-check-input"
                                                                type="checkbox"
                                                                name="access_permissions[]"
                                                                value="{{ $permission->id }}"
                                                                id="perm_{{ $permission->id }}"
                                                                {{ in_array($permission->id, $selectedPermissions) ? 'checked' : '' }}
                                                            >
                                                            <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                                {{ $permission->name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @empty
                                                    <div class="col-md-12">
                                                        <em>No permissions available</em>
                                                    </div>
                                                @endforelse
                                            </div>

                                            <small class="text-muted d-block mt-1">
                                                Leave unchecked to allow all
                                            </small>
                                        </fieldset>
                                    </div>

                                    {{-- Submit --}}
                                    <div class="col-md-12 mt-3">
                                        <button type="submit" class="btn btn-primary w-100">
                                            {{ isset($question) ? 'Update Question' : 'Save Question' }}
                                        </button>
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
<script>
    $(document).ready(function() {
        $('#add-option').click(function() {
            $('#options-wrapper').append(`
                <div class="option-row mb-2 d-flex gap-2">
                    <input type="text" name="options_keys[]" class="form-control" placeholder="Option label">
                    <input type="text" name="options_values[]" class="form-control" placeholder="Option value">
                    <button type="button" class="btn btn-danger remove-option">Remove</button>
                </div>
            `);
        });

        $(document).on('click', '.remove-option', function() {
            $(this).closest('.option-row').remove();
        });
    });
</script>
@endsection
