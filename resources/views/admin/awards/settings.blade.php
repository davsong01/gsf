@extends('layouts.dashboard')
@section('title', 'Award System Control Settings')
@section('active')<li class="breadcrumb-item">Settings</li>@endsection

@section('content')
<div class="content-body">
    <section id="system-settings-form">
        <form action="{{ route('award.settings.update') }}" method="POST">
            @csrf
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom pt-4 pb-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div>
                        <h4 class="card-title text-dark fw-bold mb-1">Award System Settings</h4>
                        <p class="text-muted font-xs mb-0">Manage application windows and review permissions.</p>
                    </div>
                    <button type="submit" class="btn btn-primary px-4 shadow-none font-sm rounded-2"><i class="bx bx-save font-base me-1" aria-hidden="true"></i> Save Settings</button>
                </div>

                <div class="card-body p-4">
                    @include('includes.alerts')
                    <section class="award-settings-group mb-5" aria-labelledby="award-submission-settings-title">
                        <div class="mb-3">
                            <h5 id="award-submission-settings-title" class="text-dark fw-bold mb-1">Award submissions</h5>
                            <p class="text-muted font-sm mb-0">Choose which applications are open and set their closing times.</p>
                        </div>
                        <div class="row g-4">
                            <div class="col-12 col-lg-6">
                                <div class="award-settings-card h-100">
                                    <h6 class="text-dark fw-bold">G.O. Award</h6>
                                    <div class="form-check form-switch mb-3">
                                        <input type="hidden" name="go_submission_enabled" value="0">
                                        <input class="form-check-input" type="checkbox" role="switch" id="go_submission_enabled" name="go_submission_enabled" value="1" @checked(old('go_submission_enabled', $settings->go_submission_enabled ?? true))>
                                        <label class="form-check-label fw-semibold" for="go_submission_enabled">Enable G.O. Award submissions</label>
                                    </div>
                                    <label for="first_class_awards_deadline" class="form-label text-dark fw-semibold">G.O. Award closing time</label>
                                    <input type="datetime-local" class="form-control" id="first_class_awards_deadline" name="first_class_awards_deadline" value="{{ old('first_class_awards_deadline', $settings->first_class_awards_deadline?->format('Y-m-d\\TH:i')) }}">
                                    <small class="form-text text-muted">Leave blank for no scheduled closing time.</small>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6">
                                <div class="award-settings-card h-100">
                                    <h6 class="text-dark fw-bold">Education Trust Fund (ETF)</h6>
                                    <div class="form-check form-switch mb-3">
                                        <input type="hidden" name="etf_submission_enabled" value="0">
                                        <input class="form-check-input" type="checkbox" role="switch" id="etf_submission_enabled" name="etf_submission_enabled" value="1" @checked(old('etf_submission_enabled', $settings->etf_submission_enabled ?? true))>
                                        <label class="form-check-label fw-semibold" for="etf_submission_enabled">Enable ETF submissions</label>
                                    </div>
                                    <label for="etf_awards_deadline" class="form-label text-dark fw-semibold">ETF closing time</label>
                                    <input type="datetime-local" class="form-control" id="etf_awards_deadline" name="etf_awards_deadline" value="{{ old('etf_awards_deadline', $settings->etf_awards_deadline?->format('Y-m-d\\TH:i')) }}">
                                    <small class="form-text text-muted">Leave blank for no scheduled closing time.</small>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="award-settings-group" aria-labelledby="review-permissions-title">
                        <div class="mb-3">
                            <h5 id="review-permissions-title" class="text-dark fw-bold mb-1">Review permissions</h5>
                            <p class="text-muted font-sm mb-0">Set when each leadership level can edit, comment on, or approve award submissions.</p>
                        </div>
                        <div class="row g-4">
                            @foreach([
                                'chapter' => ['Chapter', 'Allow Chapter Editing', 'Allow Chapter Comment', 'Allow Chapter Approval'],
                                'zone' => ['Zone', 'Allow Zone Editing', 'Allow Zone Comment', 'Allow Zone Approvals'],
                                'field' => ['Field', 'Allow Field Editing', 'Allow Field Comment', 'Allow Field Approvals'],
                            ] as $key => [$label, $editLabel, $commentLabel, $approvalLabel])
                                <div class="col-12 col-lg-4">
                                    <div class="award-settings-card h-100">
                                        <h6 class="text-dark fw-bold">{{ $label }} review</h6>
                                        @foreach([
                                            'edit' => [$editLabel, "allow_{$key}_edit"],
                                            'comment' => [$commentLabel, "allow_{$key}_comment"],
                                            'approval' => [$approvalLabel, "allow_{$key}_approval"],
                                        ] as [$fieldLabel, $fieldName])
                                            <div class="mb-3">
                                                <label for="{{ $fieldName }}" class="form-label text-dark fw-semibold">{{ $fieldLabel }}</label>
                                                <input type="datetime-local" class="form-control" id="{{ $fieldName }}" name="{{ $fieldName }}" value="{{ old($fieldName, $settings->{$fieldName}?->format('Y-m-d\\TH:i')) }}">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                </div>
            </div>
        </form>
    </section>
</div>
<style>
.award-settings-group + .award-settings-group { padding-top:1.5rem; border-top:1px solid #e7ebef; }
.award-settings-card { padding:20px; border:1px solid #e0e7ed; border-radius:12px; background:#fbfcfd; }
.award-settings-card h6 { margin-bottom:17px; }
.award-settings-card .form-label { margin-bottom:6px; }
</style>
@endsection
