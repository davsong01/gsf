@extends('layouts.dashboard')

@section('title', $pageTitle ?? 'View Appraisal')

@section('item')
<li class="breadcrumb-item"><a href="{{ route('stakeholderappraisals.index') }}">Appraisals</a></li>
@endsection

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
    .appraisal-action-btn {
        padding: 0.28rem 0.65rem;
        font-size: 0.78rem;
        line-height: 1.1;
        border-radius: 999px;
        font-weight: 600;
        letter-spacing: 0.01em;
    }

    .appraisal-action-btn i {
        font-size: 0.78rem;
    }
</style>

<div class="content-body">
    <div class="container-fluid">
        @php
            $isAdmin = true;
            $selfEditable = $selfEditable ?? false;
            $canSubmitSelf = $canSubmitSelf ?? false;
            $evaluationEditable = $evaluationEditable ?? false;
            $canSubmitEvaluation = $canSubmitEvaluation ?? false;
            $selfFormAction = $selfFormAction ?? route('stakeholderappraisals.self.update', $target);
            $formAction = $formAction ?? route('stakeholderappraisals.evaluation.update', $target);
            $selfStatus = $appraisal?->self_status ?? 'draft';
            $selfPublishedAt = $appraisal?->self_published_at;
            $evaluationStatus = $appraisal?->evaluation_status ?? 'draft';
        @endphp

        <div class="appraisal-hero p-4 p-md-5 mb-4">
            <small class="text-uppercase opacity-75 d-block mb-2">{{ $formModeLabel ?? 'Admin Review' }}</small>
            <h2 class="font-weight-bold mb-2">{{ $pageTitle ?? 'View Appraisal' }}</h2>
            <p class="mb-0" style="max-width: 760px;">
                Review the stakeholder's submitted form and evaluation responses on the same screen.
            </p>
        </div>

        @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
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
                        <h4 class="mb-1 text-capitalize">{{ $selfStatus }}</h4>
                        <div class="text-muted">
                            Published on {{ $selfPublishedAt ? $selfPublishedAt->format('d M, Y') : 'not yet published' }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted d-block">Evaluation Status</small>
                        <h4 class="mb-1 text-capitalize">{{ $evaluationStatus }}</h4>
                        <div class="text-muted">
                            Evaluation by: {{ $evaluationAuthorityLabel ?? 'Evaluator' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($selfEditable)
            <form action="{{ $selfFormAction }}" method="POST" enctype="multipart/form-data" class="mb-4">
                @csrf
                <input type="hidden" name="status" id="self_status" value="draft">

                <div class="panel-card">
                    <div class="section-heading d-flex align-items-center justify-content-between">
                        <h4 class="mb-0">Self Appraisal</h4>
                        <i class="fa fa-file-text-o"></i>
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
                                <button type="submit" class="btn btn-outline-primary btn-sm appraisal-action-btn" onclick="return window.AppraisalSubmission.prepare('draft', this.form)">
                                    Save Self Draft
                                </button>
                                <button type="submit" class="btn btn-primary btn-sm appraisal-action-btn" onclick="return window.AppraisalSubmission.prepare('published', this.form)">
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
                    <h4 class="mb-0">Published Self Appraisal</h4>
                    <i class="fa fa-file-text-o"></i>
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
                    <h4 class="mb-0">Evaluation Responses</h4>
                    <i class="fa fa-check-square-o"></i>
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
                            <button type="submit" class="btn btn-outline-primary btn-sm appraisal-action-btn" onclick="return window.AppraisalSubmission.prepare('draft', this.form)">
                                Save Draft
                            </button>
                            <button type="submit" class="btn btn-primary btn-sm appraisal-action-btn" onclick="return window.AppraisalSubmission.prepare('published', this.form)">
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
