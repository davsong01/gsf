@extends('layouts.dashboard')

@section('title', $pageTitle ?? 'View Appraisal')

@section('item')
<li class="breadcrumb-item"><a href="{{ route('stakeholderappraisals.index') }}">Appraisals</a></li>
@endsection

@section('content')
<style>
    .appraisal-workspace { max-width:1440px; margin:0 auto; padding-bottom:48px; color:#26384f; }
    .appraisal-workspace .container-fluid { padding:0; }
    .appraisal-hero { position:relative; display:grid; grid-template-columns:minmax(0,1.3fr) minmax(280px,.7fr); align-items:end; gap:28px; overflow:hidden; margin-bottom:20px; padding:clamp(28px,4vw,48px); border-radius:23px; color:#fff; background:linear-gradient(125deg,#17375f 0%,#184b69 62%,#9b6834 145%); box-shadow:0 20px 48px rgba(30,48,70,.15); }
    .appraisal-hero::after { position:absolute; top:-110px; right:-70px; width:300px; height:300px; border:1px solid rgba(255,255,255,.12); border-radius:50%; box-shadow:0 0 0 50px rgba(255,255,255,.035),0 0 0 100px rgba(255,255,255,.025); content:''; pointer-events:none; }
    .appraisal-hero > * { position:relative; z-index:1; }
    .appraisal-hero__eyebrow { display:inline-flex; align-items:center; gap:9px; margin-bottom:11px; color:#f3cf8b; font-size:10px; font-weight:850; letter-spacing:.14em; text-transform:uppercase; }
    .appraisal-hero h1 { margin:0 0 10px; color:#fff; font-size:clamp(29px,3vw,42px); font-weight:750; line-height:1.1; letter-spacing:-.035em; }
    .appraisal-hero p { max-width:680px; margin:0; color:#d7e5ed; font-size:13px; line-height:1.65; }
    .appraisal-hero__summary { display:grid; gap:7px; padding:8px; border:1px solid rgba(255,255,255,.18); border-radius:15px; background:rgba(255,255,255,.1); backdrop-filter:blur(8px); }
    .appraisal-hero__summary div { padding:9px 11px; border-radius:10px; background:rgba(8,28,54,.22); }
    .appraisal-hero__summary span,.appraisal-hero__summary strong { display:block; }
    .appraisal-hero__summary span { margin-bottom:3px; color:#b9d3df; font-size:9px; font-weight:800; letter-spacing:.1em; text-transform:uppercase; }
    .appraisal-hero__summary strong { color:#fff; font-size:12px; line-height:1.35; }
    .appraisal-jump { display:flex; flex-wrap:wrap; gap:8px; margin:0 0 20px; }
    .appraisal-jump a { display:inline-flex; align-items:center; gap:8px; padding:9px 13px; border:1px solid #e4e8ed; border-radius:9px; color:#33526b; background:#fff; font-size:11px; font-weight:750; }
    .appraisal-jump a:hover { border-color:#d0b474; color:#755321; background:#fff9ef; }
    .appraisal-form-errors { display:flex; gap:12px; margin-bottom:18px; padding:14px 16px; border:1px solid #e7b6ad; border-radius:13px; color:#7f302a; background:#fff1ed; }
    .appraisal-form-errors i { margin-top:2px; color:#a64238; font-size:17px; }
    .appraisal-form-errors strong { display:block; margin-bottom:5px; font-size:12px; }
    .appraisal-form-errors ul { margin:0; padding-left:18px; font-size:11px; line-height:1.6; }
    .appraisal-form-panel { overflow:hidden; margin-bottom:21px; border:1px solid #e5ebf2; border-radius:19px; background:#fff; box-shadow:0 12px 30px rgba(25,48,77,.06); }
    .appraisal-form-panel > .section-heading { display:flex; align-items:center; gap:14px; padding:20px 24px; border-bottom:1px solid #e8edf2; color:#183251; background:#fff; }
    .appraisal-form-panel > .section-heading h2 { margin:0 0 3px; color:#183251; font-size:19px; font-weight:750; }
    .appraisal-form-panel > .section-heading p { margin:0; color:#8190a2; font-size:11px; }
    .appraisal-panel-number { display:grid; flex:0 0 40px; width:40px; height:40px; place-items:center; border-radius:11px; color:#805b17; background:#fff0ce; font-size:12px; font-weight:850; }
    .appraisal-panel-number--teal { color:#20675f; background:#e7f4ee; }
    .appraisal-panel-state { margin-left:auto; padding:6px 9px; border-radius:99px; color:#65788d; background:#f1f4f7; font-size:10px; font-weight:800; white-space:nowrap; }
    .appraisal-panel-state--published { color:#20675f; background:#e7f4ee; }
    .appraisal-form-body { padding:22px 24px; counter-reset:appraisal-section; }
    .appraisal-section-card { overflow:hidden; margin:0 0 18px!important; border:1px solid #eadcc7; border-radius:16px; background:#fffaf2; box-shadow:0 8px 22px rgba(75,55,35,.04); counter-increment:appraisal-section; }
    .appraisal-section-card:nth-child(even) { border-color:#d9e7e2; background:#f7fbf9; }
    .appraisal-section-card > .section-heading { display:flex; align-items:center; gap:12px; margin:0; padding:15px 18px; border-bottom:1px solid rgba(155,111,57,.13); background:rgba(238,214,174,.48); }
    .appraisal-section-card:nth-child(even) > .section-heading { background:#e8f3ef; }
    .appraisal-section-card > .section-heading::before { display:grid; flex:0 0 35px; width:35px; height:35px; place-items:center; border-radius:9px; color:#fff; background:#946735; content:counter(appraisal-section,decimal-leading-zero); font-size:11px; font-weight:850; }
    .appraisal-section-card:nth-child(even) > .section-heading::before { background:#3e7d72; }
    .appraisal-section-card > .section-heading h4 { margin:0; color:#3e3327; font-size:15px; font-weight:800; }
    .appraisal-section-card > .p-2 { padding:16px!important; }
    .appraisal-section-card .subsection-block { margin-bottom:13px; padding:17px; border:1px solid rgba(180,154,119,.2); border-radius:13px; background:rgba(255,255,255,.87); }
    .appraisal-section-card .subsection-block:last-child { margin-bottom:0; }
    .appraisal-section-card .subsection-heading { margin:0 0 14px!important; padding-left:12px; border-left:3px solid #c68d4c; }
    .appraisal-section-card .subsection-heading h5 { color:#4a3a2c; font-size:13px; font-weight:800; }
    .appraisal-section-card .row { row-gap:9px; }
    .appraisal-field { min-width:0; height:100%; padding:2px; }
    .appraisal-question-label { margin-bottom:7px; color:#405269; font-size:11px; font-weight:750; line-height:1.45; }
    .appraisal-control { width:100%; min-height:42px; padding:10px 12px; border:1px solid #d9d4cb; border-radius:9px; color:#26384f; background:#fff; box-shadow:none; font-size:12px; }
    textarea.appraisal-control { min-height:100px; resize:vertical; }
    .appraisal-control:focus { border-color:#b47a3d; box-shadow:0 0 0 3px rgba(180,122,61,.13); }
    .appraisal-control:disabled { color:#607185; background:#f4f5f4; opacity:1; }
    .appraisal-final-comment { margin-top:10px; padding:18px; border:1px solid #d9e7e2; border-radius:13px; background:#f7fbf9; }
    .appraisal-final-comment label { margin-bottom:8px; color:#275c56; font-size:12px; font-weight:800; }
    .appraisal-final-comment p { margin:0 0 10px; color:#748794; font-size:11px; }
    .appraisal-actions { display:flex; align-items:center; justify-content:space-between; gap:18px; margin-top:20px; padding:17px 18px; border:1px solid #e3dfd4; border-radius:13px; background:#fffdf8; }
    .appraisal-actions__copy strong,.appraisal-actions__copy span { display:block; }
    .appraisal-actions__copy strong { color:#314459; font-size:12px; }
    .appraisal-actions__copy span { margin-top:3px; color:#82909f; font-size:10px; }
    .appraisal-actions__buttons { display:flex; flex-wrap:wrap; justify-content:flex-end; gap:8px; }
    .appraisal-action-btn { display:inline-flex; align-items:center; justify-content:center; min-height:39px; padding:9px 13px; border-radius:9px; font-size:11px; font-weight:800; line-height:1.2; }
    .appraisal-action-btn--draft { border-color:#d3a85e; color:#674b20; background:#f8dfad; }
    .appraisal-action-btn--draft:hover { color:#563b18; background:#ffe9bc; }
    .appraisal-action-btn--publish { border-color:#246d68; color:#fff; background:#246d68; box-shadow:0 8px 18px rgba(36,109,104,.16); }
    .appraisal-action-btn--publish:hover { border-color:#195a56; color:#fff; background:#195a56; }
    @media(max-width:850px) { .appraisal-hero{grid-template-columns:1fr}.appraisal-hero__summary{grid-template-columns:repeat(3,minmax(0,1fr))}.appraisal-actions{align-items:stretch;flex-direction:column}.appraisal-actions__buttons{justify-content:flex-start} }
    @media(max-width:600px) { .appraisal-hero{padding:25px 21px;border-radius:17px}.appraisal-hero__summary{grid-template-columns:1fr}.appraisal-form-panel > .section-heading{padding:17px}.appraisal-form-body{padding:15px}.appraisal-section-card > .p-2{padding:11px!important}.appraisal-section-card .subsection-block{padding:13px}.appraisal-panel-state{display:none}.appraisal-actions__buttons{display:grid;grid-template-columns:1fr;width:100%}.appraisal-action-btn{width:100%} }
</style>

<div class="content-body appraisal-workspace">
    <div class="container-fluid">
        @php
            $isAdmin = true;
            $selfEditable = $selfEditable ?? false;
            $canSubmitSelf = $canSubmitSelf ?? false;
            $evaluationEditable = $evaluationEditable ?? false;
            $canSubmitEvaluation = $canSubmitEvaluation ?? false;
            $selfFormAction = $selfFormAction ?? route('stakeholderappraisals.self.update', $target);
            $selfSilentSaveAction = route('stakeholderappraisals.self.silent-save', $target);
            $formAction = $formAction ?? route('stakeholderappraisals.evaluation.update', $target);
            $evaluationSilentSaveAction = route('stakeholderappraisals.evaluation.silent-save', $target);
            $selfStatus = $appraisal?->self_status ?? 'draft';
            $selfPublishedAt = $appraisal?->self_published_at;
            $evaluationStatus = $appraisal?->evaluation_status ?? 'draft';
        @endphp

        <header class="appraisal-hero">
            <div>
                <span class="appraisal-hero__eyebrow"><i class="fa fa-clipboard" aria-hidden="true"></i> Stakeholder appraisal</span>
                <h1>{{ $pageTitle ?? 'View Appraisal' }}</h1>
                <p>Review the self appraisal and complete your evaluation for {{ $target->name }}. Save progress before publishing when you need more time.</p>
            </div>
            <div class="appraisal-hero__summary" aria-label="Appraisal summary">
                <div><span>Officer</span><strong>{{ $target->name }} · {{ $target->office ?? $target->designation?->name ?? 'Officer' }}</strong></div>
                <div><span>Self appraisal</span><strong>{{ ucfirst($selfStatus) }}{{ $selfPublishedAt ? ' · '.$selfPublishedAt->format('d M Y') : '' }}</strong></div>
                <div><span>Evaluation</span><strong>{{ ucfirst($evaluationStatus) }} · {{ $evaluationAuthorityLabel ?? 'Evaluator' }}</strong></div>
            </div>
        </header>

        @if(session('message'))
            <div class="alert alert-success">{{ session('message') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if ($errors->any())
            <div class="appraisal-form-errors" role="alert">
                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                <div><strong>Please check the highlighted fields</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            </div>
        @endif

        <nav class="appraisal-jump" aria-label="Appraisal sections">
            <a href="#self-appraisal"><i class="fa fa-file-text-o" aria-hidden="true"></i> Self appraisal</a>
            <a href="#evaluation-responses"><i class="fa fa-check-square-o" aria-hidden="true"></i> Evaluation responses</a>
            <a href="{{ route('stakeholderappraisals.index') }}"><i class="fa fa-arrow-left" aria-hidden="true"></i> All appraisals</a>
        </nav>

        @if($selfEditable)
            <form action="{{ $selfFormAction }}" method="POST" enctype="multipart/form-data" class="mb-4">
                @csrf
                <input type="hidden" name="status" id="self_status" value="draft">

                <div class="panel-card appraisal-form-panel" id="self-appraisal">
                    <div class="section-heading">
                        <span class="appraisal-panel-number">01</span>
                        <div><h2>Self appraisal</h2><p>Responses submitted by the stakeholder.</p></div>
                        <span class="appraisal-panel-state {{ $selfStatus === 'published' ? 'appraisal-panel-state--published' : '' }}">{{ ucfirst($selfStatus) }}</span>
                    </div>
                    <div class="appraisal-form-body">
                        @include('stakeholder.appraisal._form', [
                            'sections' => $selfSections,
                            'answers' => $selfAnswers,
                            'editable' => $selfEditable,
                            'audience' => 'fill',
                            'fieldPrefix' => 'answers',
                        ])

                        @if($canSubmitSelf)
                            <div class="appraisal-actions">
                                <div class="appraisal-actions__copy"><strong>Ready to save?</strong><span>Keep this as a draft or publish the completed self appraisal.</span></div>
                                <div class="appraisal-actions__buttons">
                                    @if($isAdmin)
                                        <button type="submit" formaction="{{ $selfSilentSaveAction }}" class="btn btn-outline-secondary appraisal-action-btn">Save changes (keep status)</button>
                                    @endif
                                    <button type="submit" class="btn appraisal-action-btn appraisal-action-btn--draft" onclick="return window.AppraisalSubmission.prepare('draft', this.form)">Save self draft</button>
                                    <button type="submit" class="btn appraisal-action-btn appraisal-action-btn--publish" onclick="return window.AppraisalSubmission.prepare('published', this.form)">Publish self appraisal</button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        @else
            <div class="panel-card appraisal-form-panel" id="self-appraisal">
                <div class="section-heading">
                    <span class="appraisal-panel-number">01</span>
                    <div><h2>Published self appraisal</h2><p>Read-only responses from the stakeholder.</p></div>
                    <span class="appraisal-panel-state appraisal-panel-state--published">Published</span>
                </div>
                <div class="appraisal-form-body">
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

            <div class="panel-card appraisal-form-panel" id="evaluation-responses">
                <div class="section-heading">
                    <span class="appraisal-panel-number appraisal-panel-number--teal">02</span>
                    <div><h2>Evaluation responses</h2><p>Complete the evaluation and add your final comment.</p></div>
                    <span class="appraisal-panel-state {{ $evaluationStatus === 'published' ? 'appraisal-panel-state--published' : '' }}">{{ ucfirst($evaluationStatus) }}</span>
                </div>
                <div class="appraisal-form-body">
                    @include('stakeholder.appraisal._form', [
                        'sections' => $evaluationSections,
                        'answers' => $evaluationAnswers,
                        'editable' => $evaluationEditable,
                        'audience' => $audience,
                        'fieldPrefix' => 'answers',
                        'prefillData' => $evaluationPrefillData ?? [],
                    ])

                    @php
                        $finalComment = old(
                            'answers.' . \App\Services\AppraisalService::FINAL_COMMENT_SLUG,
                            $evaluationAnswers->get(\App\Services\AppraisalService::FINAL_COMMENT_SLUG)?->answer_value ?? ''
                        );
                    @endphp
                    <div class="appraisal-final-comment">
                        <label for="{{ \App\Services\AppraisalService::FINAL_COMMENT_SLUG }}" class="appraisal-question-label d-block">
                            Final Comment <small class="text-muted">(Optional)</small>
                        </label>
                        <p>Summarise your assessment and any next steps for this officer.</p>
                        <textarea
                            class="form-control appraisal-control"
                            id="{{ \App\Services\AppraisalService::FINAL_COMMENT_SLUG }}"
                            name="answers[{{ \App\Services\AppraisalService::FINAL_COMMENT_SLUG }}]"
                            rows="4"
                            @if(! $evaluationEditable) disabled @endif
                        >{{ $finalComment }}</textarea>
                    </div>

                    @if($canSubmitEvaluation)
                        <div class="appraisal-actions">
                            <div class="appraisal-actions__copy"><strong>Ready to continue?</strong><span>Save a draft or publish the completed evaluation.</span></div>
                            <div class="appraisal-actions__buttons">
                                @if($isAdmin)
                                    <button type="submit" formaction="{{ $evaluationSilentSaveAction }}" class="btn btn-outline-secondary appraisal-action-btn">Save changes (keep status)</button>
                                @endif
                                <button type="submit" class="btn appraisal-action-btn appraisal-action-btn--draft" onclick="return window.AppraisalSubmission.prepare('draft', this.form)">Save draft</button>
                                <button type="submit" class="btn appraisal-action-btn appraisal-action-btn--publish" onclick="return window.AppraisalSubmission.prepare('published', this.form)">Publish evaluation</button>
                            </div>
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
