@extends('layouts.stakeholderdashboard')

@section('title', $pageTitle ?? 'Self Appraisal')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

@php
    $isPublished = ($appraisal->self_status ?? 'draft') === 'published';
    $isAdmin = $isAdmin ?? false;
    $formAction = $formAction ?? route('stakeholders.appraisal.my.store');
    $backUrl = $backUrl ?? null;
    $formModeLabel = $formModeLabel ?? null;
    $formEditable = $formEditable ?? (! $isPublished);
@endphp

<style>
    .appraisal-hero {
        background: linear-gradient(135deg, #102542 0%, #1f6aa5 58%, #6a8fc7 100%);
        border-radius: 0.9rem;
        color: #fff;
        overflow: hidden;
        position: relative;
        box-shadow: 0 12px 26px rgba(16, 37, 66, 0.14);
    }

    .appraisal-section-card {
        border: 1px solid #cfd8e3;
        border-radius: 0.75rem;
        background: #fff;
        box-shadow: 0 4px 12px rgba(16, 37, 66, 0.05);
        overflow: hidden;
    }

    .section-heading {
        background: #eef5fb;
        color: #193085;
        padding: 0.75rem 1rem;
        font-size: 1rem;
        border-bottom: 1px solid #d6e1ee;
    }

    .subsection-heading {
        background: #f7f9fc;
        color: #102542;
        border-left: 4px solid #1f6aa5;
        border-radius: 0;
        padding: 0.55rem 0.85rem;
        margin-bottom: 0;
        border-bottom: 1px solid #d6e1ee;
    }

    .subsection-heading h5 {
        margin-bottom: 0;
        font-size: 0.98rem;
    }

    .appraisal-question-label {
        color: #102542;
        font-size: 0.95rem;
        font-weight: 600;
        margin-bottom: 0.35rem;
    }

    .appraisal-field {
        margin-bottom: 0.25rem;
    }

    .appraisal-control {
        border-radius: 0.45rem;
    }

    .appraisal-control,
    .appraisal-readonly-answer {
        min-height: 40px;
        font-size: 0.95rem;
    }

    .appraisal-readonly-answer {
        border-radius: 0.45rem;
        border: 1px solid #d6e1ee;
        background: #fff;
        padding: 0.75rem 0.85rem;
        white-space: pre-wrap;
        color: #102542;
    }

    .subsection-block + .subsection-block {
        border-top: 1px solid #d6e1ee;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border-radius: 999px;
        padding: 0.35rem 0.75rem;
        font-size: 0.82rem;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.16);
    }

    .appraisal-topbar {
        margin-bottom: 1rem;
    }

    .appraisal-topbar h2 {
        font-size: 1.45rem;
        margin-bottom: 0.2rem;
    }

    .appraisal-topbar p {
        margin-bottom: 0;
        max-width: 760px;
        font-size: 0.95rem;
    }

    .action-row {
        margin-top: 1rem !important;
    }
</style>

<div class="content-body">
    <div class="container-fluid">
        <div class="appraisal-hero px-3 py-3 px-md-4 py-md-3 appraisal-topbar">
            <div class="position-relative d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <small class="text-uppercase opacity-75 d-block mb-1">
                        {{ $formModeLabel ?? 'Self Appraisal' }}
                    </small>
                    <p class="mt-1">
                        @if($isAdmin)
                            Review and update this stakeholder's self appraisal directly.
                        @else
                            Complete your self appraisal below, then save it as a draft or publish it when ready.
                        @endif
                    </p>
                </div>
                <div class="status-pill">
                    <i class="fa fa-circle-info"></i>
                    Status: <span class="text-capitalize">{{ $appraisal->self_status ?? 'draft' }}</span>
                </div>
            </div>
            @if($backUrl)
                <div class="mt-3">
                    <a href="{{ $backUrl }}" class="btn btn-sm btn-light">
                        <i class="fa fa-arrow-left me-1"></i> Back to Appraisals
                    </a>
                </div>
            @endif
        </div>

        @include('stakeholder.appraisal._instructions', [
            'instructionProfile' => $instructionProfile,
            'instructionTitle' => 'Appraisal Instructions',
        ])

        @if ($errors->any())
            <div class="alert alert-danger">
                Please fix the highlighted fields below and try again.
            </div>
        @endif

        @if($isPublished)
            <div class="alert alert-success">
                Appraisal Completed, thank you! This form is now locked.
            </div>
        @endif

        <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="status" id="my_appraisal_status" value="draft">

            @include('stakeholder.appraisal._form', [
                'sections' => $sections,
                'answers' => $answers,
                'prefillData' => $prefillData ?? [],
                'editable' => $formEditable,
                'audience' => 'fill',
                'fieldPrefix' => 'answers',
            ])

            @if($formEditable)
                <div class="d-flex flex-wrap gap-2 justify-content-end action-row">
                    <button type="submit" class="btn btn-outline-primary px-4" onclick="return window.AppraisalSubmission.prepare('draft', this.form)">
                        Save Draft
                    </button>
                    <button type="submit" class="btn btn-primary px-4" onclick="return window.AppraisalSubmission.prepare('published', this.form)">
                        Publish Appraisal
                    </button>
                </div>
            @elseif($isPublished)
                <div class="alert alert-success mt-3">
                    Appraisal Completed, thank you! This form is now locked.
                </div>
            @endif
        </form>
    </div>
</div>
@if($formEditable)
    @include('stakeholder.appraisal._submission_script')
@endif
@endsection
