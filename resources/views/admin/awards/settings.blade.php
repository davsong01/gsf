@extends('layouts.dashboard')
@section('css')
<style>
    .form-label {
        margin-top: 10px;
        margin-bottom: 5px !important;
    }
</style>
@endsection
@section('title', 'Award System Control Settings')
@section('active')
<li class="breadcrumb-item">Settings</li>
@endsection

@section('content')
<div class="content-body">
    <section id="system-settings-form">
        <!-- Unified Form targeting your dynamic settings resource update -->
        <form action="{{ route('award.settings.update') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">

                        <!-- Header Bar with Save Controls -->
                        <div class="card-header bg-white border-bottom pt-4 pb-3 px-4 d-flex align-items-center justify-content-between">
                            <div>
                                <h4 class="card-title text-dark fw-bold mb-1">Global Permissions Configuration</h4>
                                <p class="text-muted font-xs mb-0">Adjust window system permissions, structural viewing access, and approval timelines.</p>
                            </div>
                            <button type="submit" class="btn btn-primary px-4 shadow-none font-sm rounded-2">
                                <i class="bx bx-save font-base me-0.5" style="vertical-align: middle;"></i> Save Settings
                            </button>
                        </div>

                        <div class="card-body p-4">
                            <div class="row g-4">
                                <!-- NEW: Deadline Fields -->
                                <div class="col-12 col-md-4">
                                    <div class="form-group mb-0">
                                        <label for="first_class_awards_deadline" class="form-label text-dark fw-semibold font-sm mb-1">First Class Awards open until</label>
                                        <input type="datetime-local" class="form-control w-100" id="first_class_awards_deadline" name="first_class_awards_deadline" value="{{ isset($settings->first_class_awards_deadline) ? \Carbon\Carbon::parse($settings->first_class_awards_deadline)->format('Y-m-d\TH:i') : '' }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group mb-0">
                                        <label for="etf_awards_deadline" class="form-label text-dark fw-semibold font-sm mb-1">ETF Awards open until</label>
                                        <input type="datetime-local" class="form-control w-100" id="etf_awards_deadline" name="etf_awards_deadline" value="{{ isset($settings->etf_awards_deadline) ? \Carbon\Carbon::parse($settings->etf_awards_deadline)->format('Y-m-d\TH:i') : '' }}">
                                    </div>
                                </div>

                                <!-- ==================== CHAPTER CONFIGURATIONS ==================== -->
                                <div class="col-12 col-md-4">
                                    <div class="form-group mb-0">
                                        <label for="allow_chapter_edit" class="form-label text-dark fw-semibold font-sm mb-1">Allow Chapter Editing</label>
                                        <input type="datetime-local" class="form-control w-100" id="allow_chapter_edit" name="allow_chapter_edit" value="{{ isset($settings->allow_chapter_edit) ? \Carbon\Carbon::parse($settings->allow_chapter_edit)->format('Y-m-d\TH:i') : '' }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group mb-0">
                                        <label for="allow_chapter_comment" class="form-label text-dark fw-semibold font-sm mb-1">Allow Chapter Comment</label>
                                        <input type="datetime-local" class="form-control w-100" id="allow_chapter_comment" name="allow_chapter_comment" value="{{ isset($settings->allow_chapter_comment) ? \Carbon\Carbon::parse($settings->allow_chapter_comment)->format('Y-m-d\TH:i') : '' }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group mb-0">
                                        <label for="allow_chapter_approval" class="form-label text-dark fw-semibold font-sm mb-1">Allow Chapter Approval</label>
                                        <input type="datetime-local" class="form-control w-100" id="allow_chapter_approval" name="allow_chapter_approval" value="{{ isset($settings->allow_chapter_approval) ? \Carbon\Carbon::parse($settings->allow_chapter_approval)->format('Y-m-d\TH:i') : '' }}">
                                    </div>
                                </div>

                                <!-- ==================== ZONE CONFIGURATIONS ==================== -->
                                <div class="col-12 col-md-4">
                                    <div class="form-group mb-0">
                                        <label for="allow_zone_edit" class="form-label text-dark fw-semibold font-sm mb-1">Allow Zone Editing</label>
                                        <input type="datetime-local" class="form-control w-100" id="allow_zone_edit" name="allow_zone_edit" value="{{ isset($settings->allow_zone_edit) ? \Carbon\Carbon::parse($settings->allow_zone_edit)->format('Y-m-d\TH:i') : '' }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group mb-0">
                                        <label for="allow_zone_comment" class="form-label text-dark fw-semibold font-sm mb-1">Allow Zone Comment</label>
                                        <input type="datetime-local" class="form-control w-100" id="allow_zone_comment" name="allow_zone_comment" value="{{ isset($settings->allow_zone_comment) ? \Carbon\Carbon::parse($settings->allow_zone_comment)->format('Y-m-d\TH:i') : '' }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group mb-0">
                                        <label for="allow_zone_approval" class="form-label text-dark fw-semibold font-sm mb-1">Allow Zone Approvals</label>
                                        <input type="datetime-local" class="form-control w-100" id="allow_zone_approval" name="allow_zone_approval" value="{{ isset($settings->allow_zone_approval) ? \Carbon\Carbon::parse($settings->allow_zone_approval)->format('Y-m-d\TH:i') : '' }}">
                                    </div>
                                </div>

                                <!-- ==================== FIELD CONFIGURATIONS ==================== -->
                                <div class="col-12 col-md-4">
                                    <div class="form-group mb-0">
                                        <label for="allow_field_edit" class="form-label text-dark fw-semibold font-sm mb-1">Allow Field Editing</label>
                                        <input type="datetime-local" class="form-control w-100" id="allow_field_edit" name="allow_field_edit" value="{{ isset($settings->allow_field_edit) ? \Carbon\Carbon::parse($settings->allow_field_edit)->format('Y-m-d\TH:i') : '' }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group mb-0">
                                        <label for="allow_field_comment" class="form-label text-dark fw-semibold font-sm mb-1">Allow Field Comment</label>
                                        <input type="datetime-local" class="form-control w-100" id="allow_field_comment" name="allow_field_comment" value="{{ isset($settings->allow_field_comment) ? \Carbon\Carbon::parse($settings->allow_field_comment)->format('Y-m-d\TH:i') : '' }}">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="form-group mb-0">
                                        <label for="allow_field_approval" class="form-label text-dark fw-semibold font-sm mb-1">Allow Field Approvals</label>
                                        <input type="datetime-local" class="form-control w-100" id="allow_field_approval" name="allow_field_approval" value="{{ isset($settings->allow_field_approval) ? \Carbon\Carbon::parse($settings->allow_field_approval)->format('Y-m-d\TH:i') : '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>
@endsection
