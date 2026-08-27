@extends('layouts.stakeholderdashboard')

@section('title', $pageTitle ?? 'Evaluations')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
    .eval-hero {
        background: linear-gradient(135deg, #102542 0%, #1f6aa5 58%, #6a8fc7 100%);
        border-radius: 1.25rem;
        color: #fff;
        box-shadow: 0 18px 40px rgba(16, 37, 66, 0.18);
    }

    .candidate-card {
        border: 0;
        border-radius: 1rem;
        box-shadow: 0 12px 30px rgba(16, 37, 66, 0.08);
        overflow: hidden;
        height: 100%;
    }

    .eval-note {
        border-left: 4px solid #1f6aa5;
        background: #f7f9fc;
        color: #102542;
        border-radius: 0.75rem;
    }
</style>

<div class="content-body">
    <div class="container-fluid">
        <div class="eval-hero p-4 p-md-5 mb-4">
            <small class="text-uppercase opacity-75 d-block mb-2">Evaluations</small>
            <h2 class="font-weight-bold mb-2">{{ $pageTitle ?? 'Evaluations' }}</h2>
            <p class="mb-0" style="max-width: 760px;">
                Only published self appraisals show here. Open a profile to review the officer's completed form and add your evaluation beneath it.
            </p>
        </div>

        <div class="card border-0 shadow-sm mb-4 eval-note">
            <div class="card-body">
                <h5 class="mb-2">Evaluation Guidance</h5>
                <p class="mb-0">
                    Review only published self appraisals, then open an officer's record to complete your evaluation.
                    Use the same rating scale and comment approach shown in the appraisal instructions.
                </p>
            </div>
        </div>

        @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        <div class="row">
            @forelse($targets as $target)
                @php $publishedAppraisal = $target->published_appraisal ?? null; @endphp
                <div class="col-md-6 col-xl-4 mb-3">
                    <div class="card candidate-card">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between mb-3">
                                <div>
                                    <h4 class="mb-1">{{ $target->name }}</h4>
                                    <div class="text-muted">{{ $target->designation?->name ?? 'Officer being evaluated' }}</div>
                                </div>
                                <span class="badge badge-success">Published</span>
                            </div>

                            <div class="text-muted small mb-3">
                                @if($target->field?->name)
                                    <div><i class="fa fa-map-marker-alt mr-1"></i>{{ $target->field->name }}</div>
                                @endif
                                @if($target->zone?->name)
                                    <div><i class="fa fa-globe mr-1"></i>{{ $target->zone->name }}</div>
                                @endif
                                @if($publishedAppraisal?->self_published_at)
                                    <div><i class="fa fa-calendar-check mr-1"></i>Published {{ optional($publishedAppraisal->self_published_at)->format('d M, Y') }}</div>
                                @endif
                            </div>

                            <a href="{{ route('stakeholders.appraisal.evaluations.show', $target) }}" class="btn btn-primary btn-block">
                                Evaluate
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <h5 class="mb-2">No published appraisals are available yet.</h5>
                            <p class="mb-0 text-muted">Once an officer publishes their self appraisal, it will appear here for review.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
