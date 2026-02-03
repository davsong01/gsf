@extends('layouts.dashboard')
@section('title', 'Reports Analytics')

@section('item')
<li class="breadcrumb-item">
    <a href="{{ route('stakeholderreports.index') }}">Monthly Reports</a>
</li>
@endsection

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

{{-- Header --}}
<div class="row mb-2">
    <div class="col-12">
        <p class="text-muted small">Track all chapter submissions per month</p>
    </div>
</div>

{{-- Filters --}}
<div class="row mb-4">
    <div class="col-12">
        <form method="GET" class="row g-2 align-items-end graph-submit">

            <div class="col-md-4">
                <label class="form-label">Question</label>
                <select name="question" class="form-select"></select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Customer Email</label>
                <input type="email" name="email" class="form-control" value="{{ request('email') }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ request('phone') }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">From</label>
                <input type="month" name="from_date" class="form-control" value="{{ request('from_date') }}">
            </div>

            <div class="col-md-2">
                <label class="form-label">To</label>
                <input type="month" name="to_date" class="form-control" value="{{ request('to_date') }}">
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Apply</button>
                <a href="{{ url()->current() }}" class="btn btn-outline-danger w-100">Reset</a>
            </div>

        </form>
    </div>
</div>

{{-- Chart + Legends --}}
<div class="row g-3">

    {{-- Chart --}}
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <strong>Chapter Submission Trend</strong>
                <span class="badge bg-secondary ms-2">{{ ucfirst(request()->level ?? $level ?? 'Chapter') }}</span>
            </div>
            <div class="card-body position-relative">

                {{-- Loader --}}
                <div id="graph-loader" class="position-absolute top-50 start-50 translate-middle d-none">
                    <div class="spinner-border text-primary"></div>
                </div>

                <canvas id="reportGraph" height="300"></canvas>
            </div>
        </div>
    </div>

    {{-- Legends --}}
    {{-- Legends --}}
<div class="col-md-6">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <strong>Chapters</strong>
        <div>
            <input type="checkbox" id="checkAllProducts" checked>
            <label for="checkAllProducts" class="small">All</label>
        </div>
    </div>

    <div class="row">
        @foreach($legends as $legend)
            <div class="col-md-6">
                <label class="d-flex align-items-center w-100" style="cursor:pointer;">
                    {{-- Color Dot --}}
                    <span class="legend-color me-2 flex-shrink-0"
                          data-id="{{ $legend->id }}"
                          style="width:12px;height:12px;border-radius:50%;background:#ccc;"></span>

                    {{-- Checkbox --}}
                    <input type="checkbox"
                           class="form-check-input me-2 product-checkbox"
                           value="{{ $legend->id }}"
                           checked>

                    {{-- Label --}}
                    <span class="small mb-0">{{ $legend->name }}</span>
                </label>
            </div>
        @endforeach
    </div>
</div>


</div>

{{-- Bottom Actions --}}
<div class="row mt-4">
    <div class="col-12 text-center">
        <button class="btn btn-primary me-2" id="openLevelModal">Customize</button>
    </div>
</div>

{{-- Customize Modal --}}
<div class="modal fade" id="periodModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-bs-dismiss="modal">&times;</button>
                <h4 class="modal-title">Select Administrative Level</h4>
            </div>
            <div class="modal-body">
                @php $selectedPeriod = request()->filter_period ?? 'chapter'; @endphp

                <div class="form-check mb-2">
                    <input id="chapter" class="form-check-input level-option" type="radio"
                           name="insight_level" value="chapter"
                           {{ $selectedPeriod === 'chapter' ? 'checked' : '' }}>
                    <label class="form-check-label" for="chapter">Chapters</label>
                </div>

                <div class="form-check mb-2">
                    <input id="zone" class="form-check-input level-option" type="radio"
                           name="insight_level" value="zone"
                           {{ $selectedPeriod === 'zone' ? 'checked' : '' }}>
                    <label class="form-check-label" for="zone">Zones</label>
                </div>

                <div class="form-check mb-2">
                    <input id="field" class="form-check-input level-option" type="radio"
                           name="insight_level" value="field"
                           {{ $selectedPeriod === 'field' ? 'checked' : '' }}>
                    <label class="form-check-label" for="field">Fields</label>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_scripts')
<x-reports.graph_scripts
    :route="Route::currentRouteName()"
    :level="$level"
    :type="$type"
    :allowProductCollapse="$allowProductCollapse"
/>
@endsection
