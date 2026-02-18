@extends('layouts.dashboard')
@section('title', 'Compliance Analytics')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('reports.analytics') }}">Monthly Reports</a></li>
@endsection
@section('content')
<div class="row mb-2">
    <div class="col-12">
        @include('admin.reports.analytics.filter')
    </div>
</div>

<div class="row">
    <div class="col-md-9">
        <canvas id="reportGraph" height="1000"></canvas>
    </div>

    <div class="col-md-3">
        <input type="text" id="legendSearch" class="form-control mb-1" placeholder="Search chapter...">

        <div class="d-flex gap-2 mb-1 align-items-center">
            <input type="checkbox" id="legendSelectAllCheckbox" />
            <label for="legendSelectAllCheckbox" class="mb-0 pl-1"> Select All</label>
        </div>
        {{-- <div id="customLegend" class="overflow-auto" style="max-height:400px;"></div> --}}

        <div id="customLegend" style="
            max-height: 380px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 11px;
        "></div>
    </div>
    <div class="col-md-9">
        <div class="col-md-12">
            <div class="text-center mt-1">
                <button class="btn btn-primary btn-sm" id="compareBtn">Group <span>Current Group</span></button>
            </div>
        </div>
    </div>
</div>

<!-- Compare Modal -->
<div class="modal fade" id="compareModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-3">
      <h5>Compare By</h5>
      <div class="form-check">
        <input class="form-check-input compare-radio" type="radio" name="compareGroup" id="compareChapter" value="chapter">
        <label class="form-check-label" for="compareChapter">Chapter</label>
      </div>
      <div class="form-check">
        <input class="form-check-input compare-radio" type="radio" name="compareGroup" id="compareField" value="field">
        <label class="form-check-label" for="compareField">Field</label>
      </div>
      <div class="form-check">
        <input class="form-check-input compare-radio" type="radio" name="compareGroup" id="compareZone" value="zone">
        <label class="form-check-label" for="compareZone">Zone</label>
      </div>
    </div>
  </div>
</div>

@endsection

@section('extra_scripts')
<x-reports.graph_scripts :route="Route::currentRouteName()" :level="$level" :type="$type" />
@endsection
