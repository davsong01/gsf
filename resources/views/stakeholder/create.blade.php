@extends('layouts.stakeholderdashboard')

@section('extra_styles')
<style>
    .section-card {
        background-color: #f9f9f9;
        border: 1px solid #d1d1d1;
        border-radius: 8px;
        margin-top: 25px;
        padding: 10px;
    }

    .header-section{
        background-color: #004080;
        color: #fff;
        padding: 12px 15px;
        border-radius: 5px;
        margin-bottom: 20px;
        font-size: 1.25rem;
    }

    .sub-section-card {
        border-left: 4px solid #004080;
        background-color: #ffffff;
        padding: 5px;
        margin-bottom: 20px;
        border-radius: 5px;
    }

    .sub-section{
        color: #004080;
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 15px;
    }

    label {
        margin-top: 15px !important;
        font-weight: 500;
    }

    .totals-row {
        font-weight: bold;
        background-color: #f2f2f2;
    }

    .dynamic-table input, .income-table input {
        height: calc(1.8em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
    }

    .dynamic-table th, .income-table th {
        background-color: #e9ecef;
        text-align: center;
    }

    .dynamic-table td, .income-table td {
        text-align: center;
        vertical-align: middle;
    }

    .table-responsive {
        overflow-x: auto;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('title', 'Add/Edit Report')

@section('item')
<li class="breadcrumb-item"> <a href="{{ route('stakeholders.dashboard') }}">Report</a></li>
@endsection

@section('active')
<li class="breadcrumb-item">{{ isset($report) ? 'Edit Report' : 'Add New Report' }}</li>
@endsection

@section('content')
<div class="content-body">
    <section id="basic-input">
        <form action="{{ isset($report) ? route('stakeholders.reports.update', $report->id) : route('stakeholders.reports.store') }}"
              method="POST" enctype="multipart/form-data"
              onsubmit="return confirm('You are about to submit this report, action is irreversible');">
            @csrf
            @if(isset($report))
                @method('PUT')
            @endif

            @foreach($sections as $section)
            <div class="section-card">
                <h3 class="header-section">{{ $section->name }}</h3>

                @foreach($section->subsections as $subsection)
                <div class="sub-section-card">
                    <h5 class="sub-section">{{ $subsection->name }}</h5>

                    <div class="row">
                    @foreach($subsection->questions as $question)
                        @php
                            $value = old('responses.' . $question->slug) 
                                ?? ($prefillData[$question->slug] ?? '');
                        @endphp

                        <div class="{{ $question->width_class ?? 'col-md-6' }} mb-1">
                            <label for="{{ $question->slug }}">
                                {{ $question->label }}
                                @if($question->is_required) <span class="text-danger">*</span> @endif
                            </label>

                            @switch($question->type)
                                @case('text')
                                @case('year')
                                @case('month')
                                @case('number')
                                @case('date')
                                    <input type="{{ $question->type }}" 
                                           class="form-control" 
                                           id="{{ $question->slug }}" 
                                           name="responses[{{ $question->slug }}]" 
                                           value="{{ $value }}"
                                           @if($question->is_required) required @endif>
                                    @break

                                @case('textarea')
                                    <textarea class="form-control" 
                                              id="{{ $question->slug }}" 
                                              name="responses[{{ $question->slug }}]" 
                                              rows="3"
                                              @if($question->is_required) required @endif>{{ $value }}</textarea>
                                    @break

                                @case('select')
                                    <select class="form-select" 
                                            id="{{ $question->slug }}" 
                                            name="responses[{{ $question->slug }}]"
                                            @if($question->is_required) required @endif>
                                        <option value="">Select...</option>
                                        @foreach($question->options as $optKey => $optLabel)
                                            <option value="{{ $optKey }}" {{ $value == $optKey ? 'selected' : '' }}>
                                                {{ $optLabel }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @break

                                @case('dynamic_table')
                                    <div class="table-responsive">
                                        <table class="table table-bordered dynamic-table" data-slug="{{ $question->slug }}">
                                            <thead>
                                                <tr>
                                                    @foreach($question->options as $col)
                                                        <th>{{ $col['label'] ?? $col }}</th>
                                                    @endforeach
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $rows = old('responses.' . $question->slug, $prefillData[$question->slug] ?? [[]]);
                                                @endphp
                                                {{-- @foreach($rows as $row)
                                                <tr>
                                                    @foreach($question->options as $col)
                                                        @php $colLabel = $col['label'] ?? $col; @endphp
                                                        <td>
                                                            <input type="{{ $col['type'] ?? 'text' }}" 
                                                                   name="responses[{{ $question->slug }}][][{{ $colLabel }}]" 
                                                                   class="form-control"
                                                                   value="{{ $row[$colLabel] ?? '' }}"
                                                                   @if(!empty($col['required'])) required @endif>
                                                        </td>
                                                    @endforeach
                                                    <td>
                                                        <span style="color:green" class="add-row"><i class="fa fa-plus"></i></span>
                                                        <span style="color:red" class="remove-row"><i class="fa fa-minus"></i></span>
                                                    </td>
                                                </tr>
                                                @endforeach --}}
                                                @foreach($rows as $rowIndex => $row)
                                                    <tr>
                                                        @foreach($question->options as $col)
                                                            @php $colLabel = $col['label'] ?? $col; @endphp
                                                            <td>
                                                                <input type="{{ $col['type'] ?? 'text' }}" 
                                                                    name="responses[{{ $question->slug }}][{{ $rowIndex }}][{{ $colLabel }}]" 
                                                                    class="form-control"
                                                                    value="{{ $row[$colLabel] ?? '' }}"
                                                                    @if(!empty($col['required'])) required @endif>
                                                            </td>
                                                        @endforeach
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-success add-row">+</button>
                                                            <button type="button" class="btn btn-sm btn-danger remove-row">-</button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @break

                                @case('income_table')
                                    <div class="table-responsive">
                                        <table class="table table-bordered income-table" data-slug="{{ $question->slug }}">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Week</th>
                                                    @foreach($question->options['columns'] as $col)
                                                        <th>{{ $col }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($question->options['rows'] as $week)
                                                <tr>
                                                    <td>{{ $week }}</td>
                                                    @foreach($question->options['columns'] as $col)
                                                    <td>
                                                        @if($col == 'Remarks')
                                                        <input type="text" class="form-control"
                                                               name="responses[{{ $question->slug }}][{{ $week }}][{{ $col }}]"
                                                               value="{{ $value[$week][$col] ?? '' }}">
                                                        @else
                                                        <input type="number" min="0" step="0.01" class="form-control numeric-input"
                                                               name="responses[{{ $question->slug }}][{{ $week }}][{{ $col }}]"
                                                               value="{{ $value[$week][$col] ?? '' }}">
                                                        @endif
                                                    </td>
                                                    @endforeach
                                                </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr class="totals-row">
                                                    <td>Totals</td>
                                                    @foreach($question->options['columns'] as $col)
                                                        <td class="total-cell" data-column="{{ $col }}">0</td>
                                                    @endforeach
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    @break
                            @endswitch
                        </div>
                    @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach

            <div class="card mb-4">
                <div class="card-body">
                    <div class="form-check">
                        <input type="checkbox" name="confirm_information" value="1" class="form-check-input" id="confirmInfo" required>
                        <label class="form-check-label fw-semibold" for="confirmInfo">
                            I hereby confirm that all information provided in this report is true, current, and accurate to the best of my knowledge.
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button class="btn btn-primary w-100" type="submit">{{ isset($report) ? 'Update Report' : 'Submit' }}</button>
            </div>
        </form>
    </section>
</div>

@endsection

@section('extra_scripts')
<script>
    // Dynamic table row add/remove
    $(document).on('click', '.add-row', function(){
        let table = $(this).closest('table');
        let newRow = table.find('tbody tr:first').clone();
        newRow.find('input').val('');
        table.find('tbody').append(newRow);
        recalcTotals(table);
    });

    $(document).on('click', '.remove-row', function(){
        let table = $(this).closest('table');
        if(table.find('tbody tr').length > 1){
            $(this).closest('tr').remove();
            recalcTotals(table);
        }
    });

    // Income Table Totals
    function recalcTotals(table){
        if(table.hasClass('income-table')){
            table.find('tfoot td.total-cell').each(function(){
                let col = $(this).data('column');
                let sum = 0;
                table.find('tbody tr').each(function(){
                    let val = parseFloat($(this).find('input[name*="['+col+']"]').val()) || 0;
                    sum += val;
                });
                $(this).text(sum.toFixed(2));
            });
        }
    }

    $(document).on('input', '.income-table .numeric-input', function(){
        let table = $(this).closest('table');
        recalcTotals(table);
    });

    // Initialize totals on page load
    $('.income-table').each(function(){
        recalcTotals($(this));
    });
</script>
@endsection
