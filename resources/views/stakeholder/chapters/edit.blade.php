@extends('layouts.stakeholderdashboard')
@section('title', 'My Chapter Details')
@section('active')
<li class="breadcrumb-item">My Chapter Details</li>
@endsection
@section('extra_styles')
<style>
    .stakeholder-chapter-header { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 14px; margin-bottom: 24px; }
    .stakeholder-chapter-header h3 { margin: 0; color: #183251; font-size: 19px; font-weight: 700; }
    .stakeholder-chapter-banner { display: flex; align-items: center; gap: 18px; padding: 15px; border: 1px dashed #cbd7e3; border-radius: 12px; background: #f8fafc; }
    .stakeholder-chapter-banner__preview { display: block; flex: 0 0 auto; width: 170px; height: 104px; max-width: 40%; object-fit: cover; border: 1px solid #e2e9f0; border-radius: 10px; }
    .stakeholder-chapter-banner__upload { flex: 1; min-width: 0; }
    .stakeholder-chapter-banner__upload small { display: block; margin-bottom: 9px; color: #78889c; font-size: 11px; }
    @media (max-width: 767.98px) {
        .stakeholder-chapter-banner { align-items: flex-start; flex-direction: column; }
        .stakeholder-chapter-banner__preview { width: 100%; max-width: 100%; height: 150px; }
    }
</style>
@endsection
@section('content')
<script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
<div class="content-body">
    <section id="basic-input">
        {{-- Field & Zone Summary Card --}}
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="stakeholder-chapter-header">
                                <h3>{{ isset($chapter) ? 'Chapter details' : 'New chapter' }}</h3>
                                @if(isset($chapter))
                                    <a target="_blank" rel="noopener" href="{{ route('campus.single', strtolower($chapter->slug)) }}" class="btn btn-outline-primary">
                                        <i class="bx bx-link-external mr-50"></i>Portal view
                                    </a>
                                @endif
                            </div>
                            <form action="{{ isset($chapter) ? route('stakeholders.chapters.update', $chapter->id) : route('stakeholders.chapters.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @if(isset($chapter))
                                    @method('PUT')
                                @endif

                                <div class="row">

                                        {{-- Name --}}
                                        <fieldset class="form-group col-md-6">
                                            <label for="name">Name</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="name"

                                                value="{{ old('name', $chapter->name ?? '') }}"
                                                placeholder="Enter name"
                                                disabled
                                            >
                                        </fieldset>
                                        <fieldset class="form-group col-md-6">
                                            <label for="slug">Campus URL slug</label>
                                            <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $chapter->slug ?? '') }}" placeholder="Enter a unique URL slug" required>
                                            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                            <small class="form-text text-muted">The URL uses lowercase letters. Slugs must be unique.</small>
                                        </fieldset>
                                        {{-- Address, Email, Phone --}}
                                        <fieldset class="form-group col-md-6">
                                            <label for="address">Address (Optional)</label>
                                            <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $chapter->address ?? '') }}" placeholder="Enter address">
                                        </fieldset>

                                        <fieldset class="form-group col-md-6">
                                            <label for="email">Email (Optional)</label>
                                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $chapter->email ?? '') }}" placeholder="Enter email">
                                        </fieldset>

                                        <fieldset class="form-group col-md-6">
                                            <label for="phone">Phone (Optional)</label>
                                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $chapter->phone ?? '') }}" placeholder="Enter phone">
                                        </fieldset>

                                        {{-- Social Links --}}
                                        <fieldset class="form-group col-md-6">
                                            <label for="facebook">Facebook Link (Optional)</label>
                                            <input type="text" class="form-control" id="facebook" name="facebook" value="{{ old('facebook', $chapter->facebook ?? '') }}" placeholder="Enter Facebook link">
                                        </fieldset>

                                        <fieldset class="form-group col-md-6">
                                            <label for="twitter">Twitter Link (Optional)</label>
                                            <input type="text" class="form-control" id="twitter" name="twitter" value="{{ old('twitter', $chapter->twitter ?? '') }}" placeholder="Enter Twitter link">
                                        </fieldset>

                                        {{-- About --}}
                                        <fieldset class="form-group col-12">
                                            <label for="about">About</label><small class="form-text text-muted d-block mb-1">Write a brief biography of the chapter, including fellowship service periods if useful.</small>
                                            <textarea class="form-control" id="about" rows="3" name="about">{!! old('about', $chapter->about ?? '') !!}</textarea>
                                        </fieldset>

                                        {{-- Banner --}}
                                        <fieldset class="form-group col-12">
                                            <label for="chapter_banner">{{ isset($chapter) ? 'Change banner' : 'Upload banner' }}</label>
                                            <div class="stakeholder-chapter-banner">
                                                @if(isset($chapter))
                                                    <img src="{{ versionedAsset(campusImagePath($chapter)) }}" alt="Current campus image for {{ $chapter->name }}" class="stakeholder-chapter-banner__preview">
                                                @else
                                                    <div class="stakeholder-chapter-banner__preview d-flex align-items-center justify-content-center text-muted">No banner yet</div>
                                                @endif
                                                <div class="stakeholder-chapter-banner__upload">
                                                    <small>{{ isset($chapter) ? (filled($chapter->banner) ? 'Current custom banner. Choose a new image to replace it.' : 'Current campus image. Upload a custom banner to replace this image.') : 'Choose an image to use as the chapter banner.' }}</small>
                                                    <input type="file" accept="image/*" class="form-control" name="chapter_banner" id="chapter_banner">
                                                </div>
                                            </div>
                                        </fieldset>

                                </div>

                                @include('includes.chapter_meetings', ['meetingRoutePrefix' => 'stakeholders.chapters.meetings'])

                                {{-- Submit --}}
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <button class="btn btn-primary w-100" type="submit">
                                            {{ isset($chapter) ? 'Update' : 'Create' }}
                                        </button>
                                    </div>
                                </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    CKEDITOR.replace('about');
</script>
@endsection
