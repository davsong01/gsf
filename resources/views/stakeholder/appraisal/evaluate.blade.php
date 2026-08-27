@extends('layouts.stakeholderdashboard')

@section('title', $pageTitle ?? 'Evaluate')

@section('content')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
    .appraisal-hero {
        background: linear-gradient(135deg, #102542 0%, #1f6aa5 58%, #6a8fc7 100%);
        border-radius: 1.25rem;
        color: #fff;
        overflow: hidden;
        position: relative;
        box-shadow: 0 18px 40px rgba(16, 37, 66, 0.18);
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
        <div class="appraisal-hero p-4 p-md-5 mb-4">
            <small class="text-uppercase opacity-75 d-block mb-2">Evaluation Workspace</small>
            <h2 class="font-weight-bold mb-2">{{ $pageTitle ?? 'Evaluate' }}</h2>
            <p class="mb-0" style="max-width: 760px;">
                Review the published self appraisal first, then complete your evaluation questions underneath it.
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

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted d-block">Officer being evaluated</small>
                        <h4 class="mb-1">{{ $target->name }}</h4>
                        <div class="text-muted">{{ $target->designation?->name ?? 'Officer' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted d-block">Self Status</small>
                        <h4 class="mb-1 text-capitalize">{{ $appraisal->self_status ?? 'draft' }}</h4>
                        <div class="text-muted">
                            Published on {{ optional($appraisal->self_published_at)->format('d M, Y') ?? 'not yet published' }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted d-block">Your evaluation status</small>
                        <h4 class="mb-1 text-capitalize">{{ $appraisal->evaluation_status ?? 'draft' }}</h4>
                        <div class="text-muted">
                            Evaluation by: {{ $evaluationAuthorityLabel ?? 'Evaluator' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

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

        <form action="{{ route('stakeholders.appraisal.evaluations.store', $target) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="status" id="evaluation_status" value="draft">

            <div class="panel-card">
                <div class="section-heading d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-0">Your Evaluation</h4>
                    </div>
                    <i class="fa fa-clipboard-check"></i>
                </div>
                <div class="p-3 p-md-4">
                    @include('stakeholder.appraisal._form', [
                        'sections' => $evaluationSections,
                        'answers' => $evaluationAnswers,
                        'editable' => true,
                        'audience' => $audience,
                        'fieldPrefix' => 'answers',
                    ])

                    <div class="d-flex flex-wrap justify-content-end gap-2 mt-4">
                        <button type="submit" class="btn btn-outline-primary px-4" onclick="return window.AppraisalSubmission.prepare('draft', this.form)">
                            Save Draft
                        </button>
                        <button type="submit" class="btn btn-primary px-4" onclick="return window.AppraisalSubmission.prepare('published', this.form)">
                            Publish Evaluation
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@include('stakeholder.appraisal._submission_script')
@endsection
