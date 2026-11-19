@extends($isAdmin ? 'layouts.dashboard' : 'layouts.stakeholderdashboard')

@section('title', 'Report Analytics')
@section($isAdmin ? 'item' : 'active')
<li class="breadcrumb-item">Report Analytics</li>
@endsection

@section('extra_styles')
<link rel="stylesheet" href="{{ asset('assets/css/report-analytics.css') }}">
@endsection

@section('content')
@php($analyticsRoute = $isAdmin ? 'reports.analytics.type' : 'stakeholders.reports.analytics.type')
<div class="content-body analytics-workspace">
    <header class="analytics-hero">
        <div>
            <span class="analytics-eyebrow"><i class="fa fa-bar-chart" aria-hidden="true"></i> Monthly fellowship reports</span>
            <h1>Report analytics</h1>
            <p>See how chapter reporting is progressing and explore the answers behind each monthly report.</p>
        </div>
    </header>

    <div class="analytics-section-heading"><h2>Choose an analysis</h2><p>Open a view, set your filters, then export the results when needed.</p></div>
    <div class="analytics-destinations">
        <a class="analytics-destination" href="{{ route($analyticsRoute, 'compliance') }}">
            <span class="analytics-destination__icon"><i class="fa fa-line-chart" aria-hidden="true"></i></span>
            <span class="analytics-destination__label">Reporting activity</span>
            <h3>Compliance trends</h3>
            <p>Track monthly submission status across chapters and see how reporting changes over time.</p>
            <span class="analytics-destination__foot">Explore compliance <i class="fa fa-arrow-right" aria-hidden="true"></i></span>
        </a>
        <a class="analytics-destination analytics-destination--growth" href="{{ route($isAdmin ? 'reports.analytics.growth' : 'stakeholders.reports.analytics.growth') }}">
            <span class="analytics-destination__icon"><i class="fa fa-area-chart" aria-hidden="true"></i></span>
            <span class="analytics-destination__label">Fellowship progress</span>
            <h3>Growth analysis</h3>
            <p>Compare measurable progress across campuses, zones and fields, then see which areas are improving.</p>
            <span class="analytics-destination__foot">Explore growth <i class="fa fa-arrow-right" aria-hidden="true"></i></span>
        </a>
        @if($isAdmin)
            <a class="analytics-destination analytics-destination--teal" href="{{ route($analyticsRoute, 'section') }}">
                <span class="analytics-destination__icon"><i class="fa fa-list-alt" aria-hidden="true"></i></span>
                <span class="analytics-destination__label">Report responses</span>
                <h3>Question analysis</h3>
                <p>Compare answers by chapter and month, then narrow the view to specific sections or questions.</p>
                <span class="analytics-destination__foot">Explore responses <i class="fa fa-arrow-right" aria-hidden="true"></i></span>
            </a>
        @endif
    </div>
    <div class="analytics-help"><i class="fa fa-info-circle" aria-hidden="true"></i><span>Each analysis uses the reporting records available to your account.</span></div>
</div>
@endsection
