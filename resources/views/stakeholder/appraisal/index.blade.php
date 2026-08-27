@extends('layouts.stakeholderdashboard')

@section('title', 'Appraisal')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
    .appraisal-shell {
        position: relative;
    }

    .appraisal-hero {
        background: linear-gradient(135deg, #102542 0%, #1f6aa5 58%, #6a8fc7 100%);
        border-radius: 1.25rem;
        color: #fff;
        overflow: hidden;
        position: relative;
        box-shadow: 0 18px 40px rgba(16, 37, 66, 0.18);
    }

    .appraisal-hero::after {
        content: "";
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top right, rgba(255, 255, 255, 0.2), transparent 34%);
        pointer-events: none;
    }

    .appraisal-card {
        border: 0;
        border-radius: 1rem;
        box-shadow: 0 12px 30px rgba(16, 37, 66, 0.08);
        overflow: hidden;
        transition: transform 0.18s ease, box-shadow 0.18s ease;
    }

    .appraisal-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 34px rgba(16, 37, 66, 0.12);
    }

    .appraisal-stat {
        background: #fff;
        border-radius: 1rem;
        border: 1px solid rgba(16, 37, 66, 0.06);
        box-shadow: 0 12px 28px rgba(16, 37, 66, 0.08);
    }
</style>

<div class="content-body">
    <div class="container-fluid appraisal-shell">
        <div class="appraisal-hero p-4 p-md-5 mb-4">
            <div class="position-relative">
                <small class="text-uppercase opacity-75 d-block mb-2">Stakeholder Appraisal</small>
                <h2 class="font-weight-bold mb-2">Appraisal Hub</h2>
                <p class="mb-0" style="max-width: 760px;">
                    Use the two paths below to handle your own appraisal or review the officers assigned to you.
                    Self appraisal and evaluations are kept separate so each workflow stays clear and focused.
                </p>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="appraisal-stat p-3 h-100">
                    <small class="text-muted d-block">My Sections</small>
                    <div class="d-flex align-items-center justify-content-between">
                        <h3 class="mb-0">{{ $summary['my_sections'] ?? 0 }}</h3>
                        <i class="fa fa-layer-group fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="appraisal-stat p-3 h-100">
                    <small class="text-muted d-block">My Items</small>
                    <div class="d-flex align-items-center justify-content-between">
                        <h3 class="mb-0">{{ $summary['my_questions'] ?? 0 }}</h3>
                        <i class="fa fa-list-check fa-2x text-success"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="appraisal-stat p-3 h-100">
                    <small class="text-muted d-block">Evaluation Sections</small>
                    <div class="d-flex align-items-center justify-content-between">
                        <h3 class="mb-0">{{ $summary['evaluation_sections'] ?? 0 }}</h3>
                        <i class="fa fa-folder-open fa-2x text-info"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="appraisal-stat p-3 h-100">
                    <small class="text-muted d-block">Evaluation Items</small>
                    <div class="d-flex align-items-center justify-content-between">
                        <h3 class="mb-0">{{ $summary['evaluation_questions'] ?? 0 }}</h3>
                        <i class="fa fa-people-group fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            @if($access['my_appraisal'] ?? false)
                <div class="col-md-6 mb-3">
                    <a href="{{ route('stakeholders.appraisal.my') }}" class="text-decoration-none">
                        <div class="card appraisal-card h-100 text-white" style="background: linear-gradient(135deg, #12355b, #1f6aa5);">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <p class="mb-0">Open your self appraisal form and save it as draft or published.</p>
                                    </div>
                                    <i class="fa fa-pen-to-square fa-3x"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif

            @if($access['evaluations'] ?? false)
                <div class="col-md-6 mb-3">
                    <a href="{{ route('stakeholders.appraisal.evaluations') }}" class="text-decoration-none">
                        <div class="card appraisal-card h-100 text-white" style="background: linear-gradient(135deg, #1b263b, #415a77);">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <h4 class="mb-1">Evaluations</h4>
                                        <p class="mb-0">Review only published appraisals for the officers assigned to you.</p>
                                    </div>
                                    <i class="fa fa-users-gear fa-3x"></i>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
