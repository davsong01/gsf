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
                                                @foreach(['text','number','textarea','select','radio','checkbox','date','month','year','rating','dynamic_table','income_table'] as $type)
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

                                    @php
                                        $questionType = old('type', $question->type ?? '');
                                        $simpleTypes = ['select', 'radio', 'checkbox','rating'];
                                        $isSimpleOptionType = in_array($questionType, $simpleTypes);
                                    @endphp

                                    <div class="col-md-12" id="simple-options" style="display:none">
                                        <label>Options</label>

                                        <div id="simple-options-wrapper">
                                            @if(in_array($questionType, ['select','radio','checkbox','rating']))
                                                @php $options = $question->options ?? []; @endphp
                                                @foreach($options as $key => $opt)
                                                    @if(is_array($opt) && isset($opt['label'], $opt['value']))
                                                        <div class="option-row mb-2 d-flex gap-2">
                                                            <input type="text" name="options[{{ $key }}][label]" class="form-control" placeholder="Label" value="{{ $opt['label'] }}">
                                                            <input type="text" name="options[{{ $key }}][value]" class="form-control" placeholder="Value" value="{{ $opt['value'] }}">
                                                            <button type="button" class="btn btn-danger remove-option">Remove</button>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>

                                        <button type="button" id="add-simple-option" class="btn btn-primary btn-sm">
                                            Add Option
                                        </button>
                                    </div>

                                    {{-- complex dynamic options --}}
                                    <div class="col-md-12" id="complex-options" style="display:none">
                                        <label>Options</label>
                                        
                                        <div id="complex-options-wrapper">
                                            @if($questionType === 'dynamic_table')
                                                @php $options = $question->options ?? []; @endphp
                                                @foreach($options as $index => $option)
                                                    @if(is_array($option))
                                                        <div class="option-row border rounded p-1 mb-1">
                                                            <div class="row g-2">
                                                                <div class="col-md-3">
                                                                    <input type="text"
                                                                        name="options[{{ $index }}][label]"
                                                                        class="form-control"
                                                                        value="{{ $option['label'] ?? '' }}"
                                                                        required>
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <select name="options[{{ $index }}][type]" class="form-control">
                                                                        @foreach(['text','date','number','textarea'] as $t)
                                                                            <option value="{{ $t }}" @selected(($option['type'] ?? '') === $t)>
                                                                                {{ ucfirst($t) }}
                                                                            </option>
                                                                        @endforeach
                                                                    </select>
                                                                </div>

                                                                <div class="col-md-2">
                                                                    <input type="checkbox"
                                                                        name="options[{{ $index }}][required]"
                                                                        value="1"
                                                                        @checked($option['required'] ?? false)>
                                                                    Required
                                                                </div>

                                                                <div class="col-md-3">
                                                                    <input type="checkbox"
                                                                        name="options[{{ $index }}][is_quantifiable]"
                                                                        value="1"
                                                                        @checked($option['is_quantifiable'] ?? false)>
                                                                    Quantifiable
                                                                </div>

                                                                <div class="col-md-2 text-end">
                                                                    <button type="button" class="btn btn-danger remove-option">Remove</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>

                                        <button type="button" id="add-complex-option" class="btn btn-primary btn-sm">
                                            Add Field
                                        </button>
                                    </div>
                                    
                                    {{-- income table options --}}
                                    <div class="col-md-12" id="income-options" style="display:none">
                                        <label>Income Table Setup</label>

                                        @php
                                            $income = old('options', $question->options ?? ['columns'=>[], 'rows'=>[]]);
                                        @endphp

                                        <div class="row g-3">

                                            {{-- COLUMNS --}}
                                            <div class="col-md-6">
                                                <h6>Columns</h6>
                                                <div id="income-columns-wrapper">
                                                    @if($questionType === 'income_table' && isset($question->options['columns']))
                                                        @foreach($income['columns'] ?? [] as $col)
                                                            <div class="d-flex gap-2 mb-2">
                                                                <input type="text"
                                                                    name="options[columns][]"
                                                                    class="form-control"
                                                                    value="{{ $col }}"
                                                                    placeholder="Column name">
                                                                <button type="button" class="btn btn-danger remove-option">Remove</button>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>

                                                <button type="button" id="add-income-column" class="btn btn-sm btn-primary">
                                                    Add Column
                                                </button>
                                            </div>

                                            {{-- ROWS --}}
                                            <div class="col-md-6">
                                                <h6>Rows</h6>
                                                <div id="income-rows-wrapper">
                                                    @foreach($income['rows'] ?? [] as $row)
                                                        <div class="d-flex gap-2 mb-2">
                                                            <input type="text"
                                                                name="options[rows][]"
                                                                class="form-control"
                                                                value="{{ $row }}"
                                                                placeholder="Row label (e.g Week 1)">
                                                            <button type="button" class="btn btn-danger remove-option">Remove</button>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <button type="button" id="add-income-row" class="btn btn-sm btn-primary">
                                                    Add Row
                                                </button>
                                            </div>

                                        </div>

                                        <small class="text-muted d-block mt-2">
                                            Columns become table headers, rows become weeks
                                        </small>
                                    </div>

                                    {{-- Permissions --}}
                                    <div class="col-md-12">
                                        <fieldset class="form-group">
                                            <label class="mb-1">Access Permissions</label>
                                            @php
                                                $selectedPermissions = old('access_permissions', $question->permissions->pluck('id')->toArray() ?? []);
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
    $(function () {
        const simpleTypes  = ['select','radio','checkbox','rating'];
        const complexTypes = ['dynamic_table'];
        const incomeType   = 'income_table';

        function toggleOptionEditors(type) {
            $('#simple-options').hide();
            $('#complex-options').hide();
            $('#income-options').hide();

            if (simpleTypes.includes(type)) {
                $('#simple-options').show();
            } else if (complexTypes.includes(type)) {
                $('#complex-options').show();
            } else if (type === incomeType) {
                $('#income-options').show();
            }
        }

        // Init
        toggleOptionEditors($('select[name="type"]').val());

        // On change
        $('select[name="type"]').on('change', function () {
            toggleOptionEditors(this.value);
        });

        // REMOVE handler (single source)
        $(document).on('click', '.remove-option', function () {
            $(this).closest('.option-row, .d-flex').remove();
        });

        // SIMPLE OPTIONS (label + value)
        let simpleIndex = {{ count($question->options ?? []) }};
        function renderSimpleOption(label = '', value = '') {
            return `
                <div class="option-row mb-2 d-flex gap-2">
                    <input type="text" name="options[${simpleIndex}][label]" class="form-control" placeholder="Label" value="${label}" required>
                    <input type="text" name="options[${simpleIndex}][value]" class="form-control" placeholder="Value" value="${value}" required>
                    <button type="button" class="btn btn-danger remove-option">Remove</button>
                </div>
            `;
        }

        $('#add-simple-option').on('click', function () {
            $('#simple-options-wrapper').append(renderSimpleOption());
            simpleIndex++;
        });

        // COMPLEX OPTIONS
        let complexIndex = {{ count($question->options ?? []) }};
        $('#add-complex-option').on('click', function () {
            $('#complex-options-wrapper').append(`
                <div class="option-row border rounded p-1 mb-1">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <input type="text" name="options[${complexIndex}][label]" class="form-control" placeholder="Label" required>
                        </div>
                        <div class="col-md-2">
                            <select name="options[${complexIndex}][type]" class="form-control">
                                <option value="text">Text</option>
                                <option value="date">Date</option>
                                <option value="number">Number</option>
                                <option value="textarea">Textarea</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-center">
                            <label class="me-2">Required</label>
                            <input type="checkbox" name="options[${complexIndex}][required]" value="1">
                        </div>
                        <div class="col-md-3 d-flex align-items-center">
                            <label class="me-2">Quantifiable</label>
                            <input type="checkbox" name="options[${complexIndex}][is_quantifiable]" value="1">
                        </div>
                        <div class="col-md-2 text-end">
                            <button type="button" class="btn btn-danger remove-option">Remove</button>
                        </div>
                    </div>
                </div>
            `);
            complexIndex++;
        });

        // INCOME TABLE
        $('#add-income-column').on('click', function () {
            $('#income-columns-wrapper').append(`
                <div class="d-flex gap-2 mb-2">
                    <input type="text" name="options[columns][]" class="form-control">
                    <button type="button" class="btn btn-danger remove-option">Remove</button>
                </div>
            `);
        });

        $('#add-income-row').on('click', function () {
            $('#income-rows-wrapper').append(`
                <div class="d-flex gap-2 mb-2">
                    <input type="text" name="options[rows][]" class="form-control">
                    <button type="button" class="btn btn-danger remove-option">Remove</button>
                </div>
            `);
        });

        $('form').on('submit', function () {
            $('#simple-options:hidden input, #complex-options:hidden input, #income-options:hidden input').prop('required', false);
        });
    });
</script>


@endsection
