@extends('layouts.dashboard')

@section('title', 'Website Documents')

@section('active')
<li class="breadcrumb-item active">Website Documents</li>
@endsection

@section('content')
<div class="content-body">
    <section id="website-document-settings">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h4 class="card-title mb-50">Website Documents</h4>
                            <p class="text-muted mb-0">Upload the governance documents shown on the Mission Strategies page.</p>
                        </div>
                    </div>

                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('site-settings.documents.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')

                                <div class="row">
                                    <div class="col-12 col-lg-6">
                                        <div class="form-group">
                                            <label for="gsf_constitution_document"><strong>GSF Constitution</strong></label>
                                            <input
                                                class="form-control-file @error('gsf_constitution_document') is-invalid @enderror"
                                                id="gsf_constitution_document"
                                                name="gsf_constitution_document"
                                                type="file"
                                                accept="application/pdf,.pdf"
                                            >
                                            <small class="form-text text-muted">PDF only, up to 20 MB. Uploading a file replaces the current document.</small>
                                            @error('gsf_constitution_document')
                                                <div class="text-danger mt-50">{{ $message }}</div>
                                            @enderror

                                            <div class="mt-1">
                                                @if(filled($settings->gsf_constitution_path))
                                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('site.documents.show', 'constitution') }}" target="_blank" rel="noopener">
                                                        <i class="fa fa-external-link"></i> View current constitution
                                                    </a>
                                                @else
                                                    <span class="badge badge-light-warning">No constitution uploaded</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-lg-6">
                                        <div class="form-group">
                                            <label for="gsf_code_of_conduct_document"><strong>GSF Code of Conduct</strong></label>
                                            <input
                                                class="form-control-file @error('gsf_code_of_conduct_document') is-invalid @enderror"
                                                id="gsf_code_of_conduct_document"
                                                name="gsf_code_of_conduct_document"
                                                type="file"
                                                accept="application/pdf,.pdf"
                                            >
                                            <small class="form-text text-muted">PDF only, up to 20 MB. Uploading a file replaces the current document.</small>
                                            @error('gsf_code_of_conduct_document')
                                                <div class="text-danger mt-50">{{ $message }}</div>
                                            @enderror

                                            <div class="mt-1">
                                                @if(filled($settings->gsf_code_of_conduct_path))
                                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('site.documents.show', 'code-of-conduct') }}" target="_blank" rel="noopener">
                                                        <i class="fa fa-external-link"></i> View current code of conduct
                                                    </a>
                                                @else
                                                    <span class="badge badge-light-warning">No code of conduct uploaded</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <button class="btn btn-primary" type="submit">
                                    <i class="bx bx-upload"></i> Save website documents
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
