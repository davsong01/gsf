@extends('layouts.dashboard')

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
                                
                                <!-- ==================== CHAPTER CONFIGURATIONS ==================== -->
                                <div class="col-12 col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="allow_chapter_edit" class="form-label text-dark fw-semibold font-sm mb-1">
                                            Allow Chapter Editing
                                        </label>
                                        <select class="form-select w-100" id="allow_chapter_edit" name="allow_chapter_edit">
                                            <option value="1" {{ ($settings->allow_chapter_edit ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ ($settings->allow_chapter_edit ?? 0) == 0 ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- <div class="col-12 col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="allow_chapter_comment" class="form-label text-dark fw-semibold font-sm mb-1">
                                            Allow Chapter Comment
                                        </label>
                                        <select class="form-select w-100" id="allow_chapter_comment" name="allow_chapter_comment">
                                            <option value="1" {{ ($settings->allow_chapter_comment ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ ($settings->allow_chapter_comment ?? 0) == 0 ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div> --}}

                                <div class="col-12 col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="allow_chapter_approval" class="form-label text-dark fw-semibold font-sm mb-1">
                                            Allow Chapter Approval
                                        </label>
                                        <select class="form-select w-100" id="allow_chapter_approval" name="allow_chapter_approval">
                                            <option value="1" {{ ($settings->allow_chapter_approval ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ ($settings->allow_chapter_approval ?? 0) == 0 ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- ==================== ZONE CONFIGURATIONS ==================== -->
                                <div class="col-12 col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="allow_zone_edit" class="form-label text-dark fw-semibold font-sm mb-1">
                                            Allow Zone Editing
                                        </label>
                                        <select class="form-select w-100" id="allow_zone_edit" name="allow_zone_edit">
                                            <option value="1" {{ ($settings->allow_zone_edit ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ ($settings->allow_zone_edit ?? 0) == 0 ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- <div class="col-12 col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="allow_zone_comment" class="form-label text-dark fw-semibold font-sm mb-1">
                                            Allow Zone Comment
                                        </label>
                                        <select class="form-select w-100" id="allow_zone_comment" name="allow_zone_comment">
                                            <option value="1" {{ ($settings->allow_zone_comment ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ ($settings->allow_zone_comment ?? 0) == 0 ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div> --}}

                                <div class="col-12 col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="allow_zone_approval" class="form-label text-dark fw-semibold font-sm mb-1">
                                            Allow Zone Approvals
                                        </label>
                                        <select class="form-select w-100" id="allow_zone_approval" name="allow_zone_approval">
                                            <option value="1" {{ ($settings->allow_zone_approval ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ ($settings->allow_zone_approval ?? 0) == 0 ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- ==================== FIELD CONFIGURATIONS ==================== -->
                                <div class="col-12 col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="allow_field_edit" class="form-label text-dark fw-semibold font-sm mb-1">
                                            Allow Field Editing
                                        </label>
                                        <select class="form-select w-100" id="allow_field_edit" name="allow_field_edit">
                                            <option value="1" {{ ($settings->allow_field_edit ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ ($settings->allow_field_edit ?? 0) == 0 ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>

                                {{-- <div class="col-12 col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="allow_field_comment" class="form-label text-dark fw-semibold font-sm mb-1">
                                            Allow Field Comment
                                        </label>
                                        <select class="form-select w-100" id="allow_field_comment" name="allow_field_comment">
                                            <option value="1" {{ ($settings->allow_field_comment ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ ($settings->allow_field_comment ?? 0) == 0 ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div> --}}

                                <div class="col-12 col-md-6">
                                    <div class="form-group mb-0">
                                        <label for="allow_field_approval" class="form-label text-dark fw-semibold font-sm mb-1">
                                            Allow Field Approvals
                                        </label>
                                        <select class="form-select w-100" id="allow_field_approval" name="allow_field_approval">
                                            <option value="1" {{ ($settings->allow_field_approval ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                                            <option value="0" {{ ($settings->allow_field_approval ?? 0) == 0 ? 'selected' : '' }}>No</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- PLACEHOLDER: Easily add your future config properties by appending extra col-md-6 blocks here -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>
@endsection