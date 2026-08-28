@extends('layouts.stakeholderdashboard')

@section('title', $pageTitle ?? 'Evaluate')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
    .appraisal-hero {
        background: linear-gradient(135deg, #f8fbff 0%, #eef5fb 100%);
        border: 1px solid #d6e1ee;
        border-radius: 0.85rem;
        color: #102542;
        overflow: hidden;
        position: relative;
        box-shadow: 0 8px 24px rgba(16, 37, 66, 0.06);
    }

    .panel-card {
        border: 0;
        border-radius: 1rem;
        box-shadow: 0 12px 30px rgba(16, 37, 66, 0.08);
        overflow: hidden;
        background: #fff;
    }

    .section-heading {
        background: #eef5fb;
        color: #193085;
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #d6e1ee;
    }

    .subsection-heading {
        background: #f7f9fc;
        color: #102542;
        border-left: 4px solid #1f6aa5;
        padding: 0.55rem 0.85rem;
        margin-bottom: 0;
        border-bottom: 1px solid #d6e1ee;
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
        min-height: 38px;
        font-size: 0.95rem;
    }

    .appraisal-readonly-answer {
        min-height: 38px;
        border-radius: 0.45rem;
        border: 1px solid #d6e1ee;
        background: #fff;
        padding: 0.75rem 0.85rem;
        white-space: pre-wrap;
        color: #102542;
    }
</style>

<div class="content-body">
    <div class="container-fluid">
        @php
            $isAdmin = $isAdmin ?? false;
            $selfEditable = $selfEditable ?? false;
            $canSubmitSelf = $canSubmitSelf ?? false;
            $evaluationEditable = $evaluationEditable ?? true;
            $canSubmitEvaluation = $canSubmitEvaluation ?? true;
            $selfFormAction = $selfFormAction ?? route('stakeholders.appraisal.my.store');
            $formAction = $formAction ?? route('stakeholders.appraisal.evaluations.store', $target);
            $selfStatus = $appraisal?->self_status ?? 'draft';
            $selfPublishedAt = $appraisal?->self_published_at;
            $evaluationStatus = $appraisal?->evaluation_status ?? 'draft';
        @endphp

        <div class="appraisal-hero p-3 p-md-3 mb-3">
            <h2 class="font-weight-bold mb-1 h5">{{ $pageTitle ?? 'Evaluate' }}</h2>
            <div class="d-flex flex-wrap gap-2 mb-2">
                <span class="badge badge-primary px-2 py-2">Stakeholder Title: {{ $target->office ?? $target->designation?->name ?? 'Officer' }}</span>
                <span class="badge badge-secondary px-2 py-2">Designation: {{ $target->designation?->name ?? 'Officer' }}</span>
            </div>
            <p class="mb-0 small text-muted" style="max-width: 760px;">
                @if($isAdmin)
                    Review the published self appraisal and the evaluation responses for this stakeholder.
                @else
                    Review the published self appraisal first, then complete your evaluation questions underneath it.
                @endif
            </p>
        </div>

        @include('stakeholder.appraisal._instructions', [
            'instructionProfile' => $instructionProfile,
            'instructionTitle' => 'Appraisal Instructions',
        ])

        @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                Please fix the highlighted fields below and try again.
            </div>
        @endif

        @if($appraisalMissing ?? false)
            <div class="alert alert-warning">
                This stakeholder has not published a self appraisal yet, so the evaluation section is currently locked.
            </div>
        @endif

        @if($evaluationLocked ?? false)
            <div class="alert alert-success">
                This evaluation has already been published and is now locked.
            </div>
        @endif

        @if($selfEditable)
            <form action="{{ $selfFormAction }}" method="POST" enctype="multipart/form-data" class="mb-4">
                @csrf
                <input type="hidden" name="status" id="self_status" value="draft">

                <div class="panel-card">
                    <div class="section-heading d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-0">Self Appraisal</h4>
                        </div>
                        <i class="fa fa-file-signature"></i>
                    </div>
                    <div class="p-3 p-md-4">
                        @include('stakeholder.appraisal._form', [
                            'sections' => $selfSections,
                            'answers' => $selfAnswers,
                            'editable' => $selfEditable,
                            'audience' => 'fill',
                            'fieldPrefix' => 'answers',
                        ])

                        @if($canSubmitSelf)
                            <div class="d-flex flex-wrap justify-content-end gap-2 mt-4">
                                <button type="submit" class="btn btn-outline-primary px-4" onclick="return window.AppraisalSubmission.prepare('draft', this.form)">
                                    Save Self Draft
                                </button>
                                <button type="submit" class="btn btn-primary px-4" onclick="return window.AppraisalSubmission.prepare('published', this.form)">
                                    Publish Self Appraisal
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        @else
            <div class="panel-card mb-4">
                <div class="section-heading d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-0">Published Self Appraisal</h4>
                    </div>
                    <i class="fa fa-file-signature"></i>
                </div>
                <div class="p-3 p-md-4">
                    @include('stakeholder.appraisal._form', [
                        'sections' => $selfSections,
                        'answers' => $selfAnswers,
                        'editable' => false,
                        'audience' => 'fill',
                        'fieldPrefix' => 'answers',
                    ])
                </div>
            </div>
        @endif

        <form action="{{ $formAction }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="status" id="evaluation_status" value="draft">

            <div class="panel-card">
                <div class="section-heading d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-0">{{ $isAdmin ? 'Evaluation Responses' : 'Your Evaluation' }}</h4>
                    </div>
                    <i class="fa fa-clipboard-check"></i>
                </div>
                <div class="p-3 p-md-4">
                    @include('stakeholder.appraisal._form', [
                        'sections' => $evaluationSections,
                        'answers' => $evaluationAnswers,
                        'editable' => $evaluationEditable,
                        'audience' => $audience,
                        'fieldPrefix' => 'answers',
                        'prefillData' => $evaluationPrefillData ?? [],
                    ])

                    @if($canSubmitEvaluation)
                        <div class="d-flex flex-wrap justify-content-end gap-2 mt-4">
                            <button type="submit" class="btn btn-outline-primary px-4" onclick="return window.AppraisalSubmission.prepare('draft', this.form)">
                                Save Draft
                            </button>
                            <button type="submit" class="btn btn-primary px-4" onclick="return window.AppraisalSubmission.prepare('published', this.form)">
                                Publish Evaluation
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>
@if($canSubmitSelf || $canSubmitEvaluation)
    @include('stakeholder.appraisal._submission_script')
@endif
@endsection
