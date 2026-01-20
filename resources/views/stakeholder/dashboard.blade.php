@extends('layouts.stakeholderdashboard')
<style>
    .monitor-card{
        margin-bottom:20px !important
    }
</style>
@section('title', 'Reports Dashboard')

@section('active')
<li class="breadcrumb-item">Reports</li>
@endsection

@section('content')
<div class="content-body">

    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold">Reports Dashboard</h4>
            <p class="text-muted mb-0">
                Manage submissions, approvals, and performance insights for stakeholder reports.
            </p>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="row g-3 mb-4">

        {{-- My Reports --}}
        <div class="col-md-3 col-sm-6 monitor-card">
            <a href="{{ route('stakeholders.reports.index') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3 text-primary">
                                <i class="fa fa-file-alt fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">All Reports</h6>
                                <small class="text-muted">View submitted reports</small>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Submit Report --}}
        @if(canAddReport(Auth::guard('stakeholder')->user()))
        <div class="col-md-3 col-sm-6 monitor-card">
            <a href="{{ route('stakeholders.reports.create') }}" class="text-decoration-none">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3 text-success">
                                <i class="fa fa-plus-circle fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Submit Report</h6>
                                <small class="text-muted">Create a new monthly report</small>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        @endif
        {{-- Pending Approvals --}}
        <div class="col-md-3 col-sm-6 monitor-card">
            <a href="{{ route('stakeholders.reports.index', ['status_filter' => 'pending']) }}" class="text-decoration-none">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3 text-warning">
                                <i class="fa fa-clock fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Pending Zonal Reviews</h6>
                                <small class="text-muted">Reports awaiting action from zonal Pastor</small>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3 col-sm-6 monitor-card">
            <a href="{{ route('stakeholders.reports.index', ['status_filter' => 'pending']) }}" class="text-decoration-none">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3 text-warning">
                                <i class="fa fa-clock fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Pending Field Reviews</h6>
                                <small class="text-muted">Reports awaiting action from field pastor</small>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3 col-sm-6 monitor-card">
            <a href="{{ route('stakeholders.reports.index', ['status_filter' => 'pending']) }}" class="text-decoration-none">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3 text-warning">
                                <i class="fa fa-clock fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Pending National Reviews</h6>
                                <small class="text-muted">Reports awaiting action from the national</small>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Approved Reports --}}
        <div class="col-md-3 col-sm-6 monitor-card">
            <a href="{{ route('stakeholders.reports.index', ['status_filter' => 'approved']) }}" class="text-decoration-none">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="me-3 text-info">
                                <i class="fa fa-check-circle fa-2x"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Approved Reports</h6>
                                <small class="text-muted">Completed approvals</small>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Insights / Analytics --}}
    <div class="row g-3">

        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Report Insights</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2">
                        Access summarized insights and trends from submitted reports.
                    </p>
                    <a href="{{ route('stakeholders.reports.analysis') }}"
                       class="btn btn-outline-primary btn-sm">
                        View Analysis
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Download Reports</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-2">
                        Download approved reports for offline review or record keeping.
                    </p>
                    <a href="{{ route('stakeholders.reports.index', ['download' => 1]) }}" class="btn btn-outline-success btn-sm">
                        Browse Downloads
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
