@php
    $layout = $isAdmin ? 'layouts.dashboard' : 'layouts.stakeholderdashboard';
@endphp

@extends($layout)

@section('title', 'Compliance Analytics')

@if($isAdmin)
    @section('item')
        <li class="breadcrumb-item">
            <a href="{{ route('reports.analytics') }}">Monthly Reports</a>
        </li>
    @endsection
@else
    @section('active')
        <li class="breadcrumb-item">Monthly Reports</li>
    @endsection
@endif

@section('content')
<div class="row mb-2">
    <div class="col-12">
        @include('admin.reports.analytics.filter')
    </div>
</div>

<div class="compliance-chart-card">
    <div class="compliance-chart-heading">
        <h2>Monthly report status</h2>
        <p id="complianceChartSummary">Loading chapter status counts…</p>
    </div>
    <div class="compliance-chart-wrap">
        <canvas id="reportGraph" aria-label="Monthly report status distribution" role="img"></canvas>
    </div>
</div>

@endsection

@section('extra_scripts')
<x-reports.graph_scripts :route="Route::currentRouteName()" :level="$level" :type="$type" />
@endsection
