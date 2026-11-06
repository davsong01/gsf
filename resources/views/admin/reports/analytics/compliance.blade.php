@extends($isAdmin ? 'layouts.dashboard' : 'layouts.stakeholderdashboard')

@section('title', 'Compliance Trends')
@section('item')
<li class="breadcrumb-item"><a href="{{ route($isAdmin ? 'reports.analytics' : 'stakeholders.reports.analytics') }}">Report Analytics</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Compliance Trends</li>
@endsection

@section('extra_styles')
<link rel="stylesheet" href="{{ asset('assets/css/report-analytics.css') }}">
@endsection

@section('content')
<div class="content-body analytics-workspace">
    <header class="analytics-hero">
        <div><span class="analytics-eyebrow"><i class="fa fa-line-chart" aria-hidden="true"></i> Reporting activity</span><h1>Compliance trends</h1><p>See how many chapters submitted reports each month and how their review status changed over time.</p></div>
        <a class="analytics-hero__link" href="{{ route($isAdmin ? 'reports.analytics' : 'stakeholders.reports.analytics') }}"><i class="fa fa-arrow-left" aria-hidden="true"></i> All analytics</a>
    </header>

    @include('admin.reports.analytics.filter')

    <div class="analytics-metrics" aria-label="Chart summary">
        <div class="analytics-metric"><span>Chapters in view</span><strong id="analyticsChapterCount">—</strong></div>
        <div class="analytics-metric"><span>Months shown</span><strong id="analyticsMonthCount">—</strong></div>
        <div class="analytics-metric"><span>Latest month</span><strong id="analyticsLatestMonth">—</strong></div>
    </div>

    <section class="compliance-chart-card" aria-labelledby="compliance-chart-title">
        <div class="compliance-chart-heading"><h2 id="compliance-chart-title">Monthly report status</h2><p id="complianceChartSummary">Loading chapter status counts…</p></div>
        <div class="analytics-chart-loader" id="graph-loader" role="status">Loading chart…</div>
        <div class="compliance-chart-wrap"><canvas id="reportGraph" aria-label="Monthly report status distribution" role="img"></canvas></div>
    </section>
</div>
@endsection

@section('extra_scripts')
<x-reports.graph_scripts :route="Route::currentRouteName()" :level="$level" :type="$type" />
@endsection
