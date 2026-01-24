@extends('layouts.stakeholderdashboard')
<style>
    .monitor-card{
        margin-bottom:20px !important
    }

    .content-header{
        display: none !important
    }
</style>
@section('content')
<div class="content-body">

    @if($user->role_id == 5)
    {{-- CHAPTER HEADER --}}
    @php
    $bgStyle = $chapter->banner
        ? "background-image: url('" . asset($chapter->banner) . "');"
        : "background: linear-gradient(135deg, #0d6efd, #0b5ed7);";

        $fieldCord = $chapter->field->fieldCord;
    @endphp

    <div class="card shadow-sm mb-1 border-0"
        style="{{ $bgStyle }} background-size:cover; background-position:center; position:relative;">

        {{-- Dark Overlay --}}
        <div style="
            position:absolute;
            inset:0;
            background: rgba(0, 0, 0, 0.71);
            border-radius: .25rem;
        "></div>

        <div class="card-body text-center py-4 position-relative text-white">
            <h2 class="fw-bold mb-1" style="color:white">
                {{ $chapter->name ?? 'My Chapter' }}
            </h2>

            <small class="opacity-75">
                Welcome back, {{ Auth::guard('stakeholder')->user()->name }}
            </small> <br>
            <span class="badge bg-light text-dark mt-2">
                Chapter Dashboard
            </span>
        </div>
    </div>


    {{-- FIELD & ZONE CARDS --}}
    <div class="row mb-1">

        {{-- FIELD CARD --}}
        <div class="col-12 col-md-6 mb-1">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <div class="row align-items-center">

                        {{-- Avatar --}}
                        <div class="col-12 col-md-5 text-center mb-1 mb-md-0">
                            <img
                                src="{{ asset(optional($chapter->field->fieldCord)->avatar ?? 'images/avatar.png') }}"
                                class="rounded-circle mb-2"
                                style="width:100px;height:100px;object-fit:cover;"
                            >
                            <h6 class="mb-0">{{ optional($chapter->field->fieldCord)->name ?? 'N/A' }}</h6>
                            <small class="text-muted d-block">Field Pastor</small>
                        </div>

                        {{-- Info --}}
                        <div class="col-12 col-md-7">
                            <h6 class="text-primary mb-2">
                                <i class="fa fa-map-marker"></i> Field Information
                            </h6>

                            <p class="mb-1"><strong>Field:</strong> {{ $chapter->field->name ?? 'N/A' }}</p>
                            <p class="mb-1"><i class="fa fa-envelope text-muted"></i> {{ optional($chapter->field->fieldCord)->email ?? 'N/A' }}</p>
                            <p class="mb-1"><i class="fa fa-phone text-muted"></i> {{ optional($chapter->field->fieldCord)->phone ?? 'N/A' }}</p>

                            @if($fieldCord && $fieldCord->day && $fieldCord->month)
                                <span class="badge badge-light-primary d-inline-block" style="background-color:#0028FF;color:white !important;">
                                    🎉 Celebrate: <strong>Every {{ \Carbon\Carbon::create(null, $fieldCord->month, $fieldCord->day)->format('F jS') }}</strong>
                                </span>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- ZONE CARD --}}
        <div class="col-12 col-md-6 mb-1">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h6 class="text-primary mb-2">
                        <i class="fa fa-globe"></i> Zone Information
                    </h6>

                    <p class="mb-2"><strong>Zone:</strong> {{ $chapter->zone->name ?? 'N/A' }}</p>

                    <div class="row">
                        @forelse($chapter->zone->zonalCords ?? collect() as $cord)
                            <div class="col-12 mb-1">
                                <div class="row align-items-center">

                                    {{-- Avatar --}}
                                    <div class="col-3 col-md-2 text-center mb-1 mb-md-0">
                                        <img
                                            src="{{ asset($cord->avatar ?? 'images/avatar.png') }}"
                                            class="rounded-circle"
                                            style="width:50px;height:50px;object-fit:cover;"
                                        >
                                    </div>

                                    {{-- Info --}}
                                    <div class="col-9 col-md-10 text-break">
                                        <strong>{{ $cord->name }}</strong>

                                        <span class="d-block text-muted small">
                                            <i class="fa fa-envelope"></i> {{ $cord->email ?? 'N/A' }}
                                        </span>

                                        <span class="d-block text-muted small">
                                            <i class="fa fa-phone"></i> {{ $cord->phone ?? 'N/A' }}
                                        </span>
                                        @if($cord && $cord->day && $cord->month)
                                            <span class="badge badge-light-primary d-inline-block" style="background-color:#3B50C4;color:white !important;">
                                                🎉 Celebrate: <strong>Every {{ \Carbon\Carbon::create(null, $cord->month, $cord->day)->format('F jS') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                </div>
                                <hr class="my-2">
                            </div>
                        @empty
                            <div class="col-12 text-muted">
                                No zonal coordinators assigned
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endif

    {{-- REPORT DASHBOARD HEADER --}}
    {{-- <div class="row mb-3">
        <div class="col-12">
            <h4 class="fw-bold">Reports Dashboard</h4>
            <p class="text-muted mb-0">
                Manage submissions, approvals, and performance insights.
            </p>
        </div>
    </div> --}}


    {{-- QUICK ACTION CARDS (your existing ones untouched) --}}
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
</div>
@endsection

