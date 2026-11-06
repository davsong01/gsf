@extends('layouts.dashboard')

@section('title', 'General Settings')

@section('extra_styles')
    <link rel="stylesheet" href="{{ asset('app-assets/vendors/css/file-uploaders/dropzone.min.css') }}">
    <style>
        .site-branding-dropzone { min-height: 150px; padding: 18px; border: 2px dashed #9aafd0; border-radius: 12px; background: #f7f9fd; }
        .site-branding-dropzone .dz-message { position: static; width: auto; height: auto; margin: 25px 0; color: #536783; transform: none; }
        .site-branding-dropzone .dz-preview .dz-image { border-radius: 10px; }
        .site-branding-current { display:flex; align-items:center; gap:14px; margin-top:12px; }
        .site-branding-current img { width:auto; max-width:180px; height:70px; padding:8px; border:1px solid #e5eaf2; border-radius:10px; object-fit:contain; background:#fff; }
    </style>
@endsection

@section('active')
<li class="breadcrumb-item active">General Settings</li>
@endsection

@section('content')
<div class="content-body">
    <section id="website-document-settings">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <h4 class="card-title mb-50">General Settings</h4>
                            <p class="text-muted mb-0">Manage the shared site logo, favicon, governance documents and Family Song displayed on the public website.</p>
                        </div>
                    </div>

                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('general-settings.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')

                                <div class="row">
                                    @foreach([
                                        'site_logo' => ['label' => 'Website Logo', 'dropzone' => 'site-logo-dropzone', 'types' => 'image/jpeg,image/png,image/webp', 'help' => 'PNG, JPG or WebP, up to 5 MB. Used in the admin sidebar and public website.'],
                                        'site_favicon' => ['label' => 'Website Favicon', 'dropzone' => 'site-favicon-dropzone', 'types' => 'image/jpeg,image/png,image/webp,image/x-icon,.ico', 'help' => 'PNG, JPG, WebP or ICO, up to 2 MB. Used as the site browser icon.'],
                                    ] as $field => $config)
                                        <div class="col-12 col-lg-6">
                                            <div class="form-group">
                                                <label for="{{ $config['dropzone'] }}"><strong>{{ $config['label'] }}</strong></label>
                                                <div id="{{ $config['dropzone'] }}" class="dropzone site-branding-dropzone" data-file-input="{{ $field }}"></div>
                                                <input class="d-none @error($field) is-invalid @enderror" id="{{ $field }}" name="{{ $field }}" type="file" accept="{{ $config['types'] }}">
                                                <small class="form-text text-muted">{{ $config['help'] }}</small>
                                                @error($field)<div class="text-danger mt-50">{{ $message }}</div>@enderror
                                                @if(filled($settings->{$field}))
                                                    @php
                                                        $currentImage = $settings->{$field};
                                                        $currentImageUrl = filter_var($currentImage, FILTER_VALIDATE_URL)
                                                            ? $currentImage
                                                            : (str_starts_with($currentImage, 'uploads/')
                                                                ? asset($currentImage)
                                                                : \Illuminate\Support\Facades\Storage::disk('public')->url($currentImage));
                                                    @endphp
                                                    <div class="site-branding-current"><img src="{{ $currentImageUrl }}" alt="Current {{ strtolower($config['label']) }}"><span class="text-muted">Current {{ strtolower($config['label']) }}</span></div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

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
                                <h5 class="mb-1">GSF Family Song</h5>
                                <p class="text-muted">These lyrics and audio will appear on the Mission Strategies page.</p>
                                <div class="form-group">
                                    <label for="gsf_family_song_lyrics"><strong>Family Song Lyrics</strong></label>
                                    <textarea class="form-control @error('gsf_family_song_lyrics') is-invalid @enderror" id="gsf_family_song_lyrics" name="gsf_family_song_lyrics" rows="12" maxlength="30000">{{ old('gsf_family_song_lyrics', $settings->gsf_family_song_lyrics) }}</textarea>
                                    @error('gsf_family_song_lyrics')<div class="text-danger mt-50">{{ $message }}</div>@enderror
                                </div>

                                <div class="form-group">
                                    <label for="gsf_family_song_audio"><strong>Upload Family Song Audio</strong></label>
                                    <input class="form-control-file @error('gsf_family_song_audio') is-invalid @enderror" id="gsf_family_song_audio" name="gsf_family_song_audio" type="file" accept="audio/mpeg,audio/wav,audio/ogg,audio/mp4,audio/aac,.mp3,.wav,.ogg,.m4a,.aac">
                                    <small class="form-text text-muted">MP3, WAV, OGG, M4A or AAC, up to 50 MB. Uploading replaces the current audio.</small>
                                    @error('gsf_family_song_audio')<div class="text-danger mt-50">{{ $message }}</div>@enderror
                                    <div class="mt-1">
                                        @if(filled($settings->gsf_family_song_audio_path))
                                            <audio controls preload="none" src="{{ filter_var($settings->gsf_family_song_audio_path, FILTER_VALIDATE_URL) ? $settings->gsf_family_song_audio_path : \Illuminate\Support\Facades\Storage::disk('public')->url($settings->gsf_family_song_audio_path) }}">Your browser does not support audio playback.</audio>
                                        @else
                                            <span class="badge badge-light-warning">No Family Song audio uploaded</span>
                                        @endif
                                    </div>
                                </div>

                                <hr>
                                <h5 class="mb-1">Campus Record Book Upload</h5>
                                <p class="text-muted">Control whether chapter stakeholders can access the Record Book upload page.</p>
                                <div class="form-group">
                                    <input type="hidden" name="enable_campus_record_book_upload" value="0">
                                    <label class="d-flex align-items-start" for="enable_campus_record_book_upload">
                                        <input class="mr-1 mt-25" id="enable_campus_record_book_upload" name="enable_campus_record_book_upload" type="checkbox" value="1" @checked((bool) old('enable_campus_record_book_upload', $settings->enable_campus_record_book_upload ?? true))>
                                        <span><strong>Enable Campus Record Book Upload</strong><small class="form-text text-muted">Enabled by default. When turned off, the Upload Record Book button and page are hidden from stakeholders.</small></span>
                                    </label>
                                    @error('enable_campus_record_book_upload')<div class="text-danger mt-50">{{ $message }}</div>@enderror
                                </div>

                                <hr>
                                <h5 class="mb-1">Public Contact</h5>
                                <div class="form-group">
                                    <label for="site_contact_email"><strong>Contact Us Email</strong></label>
                                    <input class="form-control @error('site_contact_email') is-invalid @enderror" id="site_contact_email" name="site_contact_email" type="email" value="{{ old('site_contact_email', $settings->site_contact_email ?: $settings->official_email) }}" placeholder="contact@gsfnational.org">
                                    <small class="form-text text-muted">Contact form messages are sent here. If blank, the existing official email is used.</small>
                                    @error('site_contact_email')<div class="text-danger mt-50">{{ $message }}</div>@enderror
                                </div>

                                <hr>
                                <button class="btn btn-primary" type="submit">
                                    <i class="bx bx-save"></i> Save General Settings
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

@section('extra_scripts')
    <script src="{{ asset('app-assets/vendors/js/extensions/dropzone.min.js') }}"></script>
    <script>
        Dropzone.autoDiscover = false;
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('#website-document-settings form');
            if (!form) return;

            document.querySelectorAll('.site-branding-dropzone').forEach(function (zone) {
                const fileInput = document.getElementById(zone.dataset.fileInput);
                if (!fileInput) return;

                new Dropzone(zone, {
                    url: form.action,
                    autoProcessQueue: false,
                    clickable: true,
                    maxFilesize: zone.dataset.fileInput === 'site_favicon' ? 2 : 5,
                    acceptedFiles: fileInput.accept,
                    addRemoveLinks: true,
                    dictDefaultMessage: 'Drop the image here or click to choose a file',
                    dictRemoveFile: 'Remove selected file',
                    init: function () {
                        this.on('addedfile', function (file) {
                            if (this.files.length > 1) this.removeFile(this.files[0]);
                            const transfer = new DataTransfer();
                            transfer.items.add(file);
                            fileInput.files = transfer.files;
                        });
                        this.on('removedfile', function () {
                            fileInput.value = '';
                        });
                    }
                });
            });
        });
    </script>
@endsection
