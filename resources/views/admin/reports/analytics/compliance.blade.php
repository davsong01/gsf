@extends('layouts.dashboard')
@section('title', 'Compliance Analytics')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <form method="GET" class="row g-2 align-items-end graph-submit">
            <div class="col-md-4">
                <label>Fields (Start Typing)</label>
                <select name="fields[]" class="form-select" multiple>
                    @foreach($fields ?? [] as $field)
                        <option value="{{ $field->id }}">{{ $field->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label>Zones (Start Typing)</label>
                <select name="zones[]" class="form-select" multiple>
                    @foreach($zones ?? [] as $zone)
                        <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Submission Status</label>
                <select name="submission_status" class="form-select">
                    <option value="">All</option>
                    <option value="0" {{ request('submission_status') === '0' ? 'selected' : '' }}>Not Submitted</option>
                    <option value="1" {{ request('submission_status') === '1' ? 'selected' : '' }}>Currently Editing</option>
                    <option value="2" {{ request('submission_status') === '2' ? 'selected' : '' }}>Submitted</option>
                    <option value="3" {{ request('submission_status') === '3' ? 'selected' : '' }}>Zone Rejected</option>
                    <option value="4" {{ request('submission_status') === '4' ? 'selected' : '' }}>Zone Approved</option>
                    <option value="5" {{ request('submission_status') === '5' ? 'selected' : '' }}>Field Rejected</option>
                    <option value="6" {{ request('submission_status') === '6' ? 'selected' : '' }}>Field Approved</option>
                    <option value="7" {{ request('submission_status') === '7' ? 'selected' : '' }}>National Rejected</option>
                    <option value="8" {{ request('submission_status') === '8' ? 'selected' : '' }}>National Approved</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>From</label>
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
            </div>

            <div class="col-md-2">
                <label>To</label>
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
            </div>

            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Apply</button>
                <button type="submit" name="filter_type" value="download" class="btn btn-success"
                        formmethod="post" formaction="{{ route(Route::currentRouteName(), $type) }}">
                    Download
                </button>

                <a href="{{ url()->current() }}" class="btn btn-outline-danger w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-9">
        <canvas id="reportGraph" height="450"></canvas>
    </div>

    <div class="col-md-3">
        <input type="text" id="legendSearch" class="form-control mb-2" placeholder="Search chapter...">

        <div id="customLegend" style="
            max-height: 450px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 13px;
        "></div>
    </div>

</div>


@endsection

@section('extra_scripts')
<x-reports.graph_scripts :route="Route::currentRouteName()" :level="$level" :type="$type" />
@endsection
