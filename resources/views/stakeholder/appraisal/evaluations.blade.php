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

    .eval-action-btn {
        padding: 0.28rem 0.65rem;
        font-size: 0.78rem;
        line-height: 1.1;
        border-radius: 999px;
        font-weight: 600;
        letter-spacing: 0.01em;
    }

    .eval-action-btn i {
        font-size: 0.78rem;
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
                This list shows everyone you are expected to evaluate, whether or not they have published their self appraisal yet.
            </p>
        </div>

        <div class="card border-0 shadow-sm mb-4 eval-note">
            <div class="card-body">
                <h5 class="mb-2">Evaluation Guidance</h5>
                <p class="mb-0">
                    Open a stakeholder to review their submission status and complete your evaluation when available.
                </p>
            </div>
        </div>

        @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle zero-configuration">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Self Status</th>
                                <th>Evaluation Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($targets as $target)
                                @php
                                    $publishedAppraisal = $target->published_appraisal ?? null;
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $target->name }}</div>
                                        <small class="text-muted">{{ $target->email }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $target->designation?->name ?? 'Officer being evaluated' }}</div>
                                        <small class="text-muted">{{ $target->role?->name ?? 'N/A' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge {{ ($publishedAppraisal?->self_status ?? 'draft') === 'published' ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $publishedAppraisal?->self_status ?? 'draft' }}
                                        </span>
                                        <div class="small text-muted mt-1">
                                            {{ $publishedAppraisal?->self_published_at ? 'Published ' . $publishedAppraisal->self_published_at->format('d M Y, h:i A') : 'Not published yet' }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($publishedAppraisal)
                                            <span class="badge {{ ($publishedAppraisal?->evaluation_status ?? 'draft') === 'published' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $publishedAppraisal?->evaluation_status ?? 'draft' }}
                                            </span>
                                            <div class="small text-muted mt-1">
                                                {{ $publishedAppraisal?->evaluation_published_at ? 'Published ' . $publishedAppraisal->evaluation_published_at->format('d M Y, h:i A') : 'Not published yet' }}
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                            <div class="small text-muted mt-1">No published self appraisal yet</div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('stakeholders.appraisal.evaluations.show', $target) }}" class="btn btn-outline-primary eval-action-btn">
                                            <i class="fa fa-eye me-1"></i>
                                            Open
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No stakeholders found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
