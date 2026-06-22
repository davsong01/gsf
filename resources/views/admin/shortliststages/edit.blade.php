@extends('layouts.dashboard')
@section('title', isset($awardShortlistStage->id) ? 'Edit Shortlist Stage' : 'Create Shortlist Stage')

@section('item')
<li class="breadcrumb-item">
    <a href="{{ route('shortlist.index') }}">Award Shortlist Stages</a>
</li>
@endsection

@section('active')
<li class="breadcrumb-item">
    {{ isset($awardShortlistStage->id) ? 'Edit Stage' : 'Create Stage' }}
</li>
@endsection

@section('content')
<div class="content-body">
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        @include('includes.alerts')
                        <h4 class="card-title mt-1">
                            {{ isset($awardShortlistStage->id) ? 'Modify Workflow Stage' : 'Configure New Shortlist Stage' }}
                        </h4>
                    </div>

                    <div class="card-content">
                        <div class="card-body">
                            <form
                                action="{{ isset($awardShortlistStage->id) ? route('shortlist.update', $awardShortlistStage->id) : route('shortlist.store') }}"
                                method="POST"
                            >
                                @csrf
                                @if(isset($awardShortlistStage->id))
                                    @method('PATCH')
                                @endif

                                <div class="row">
                                    {{-- Left Column: Core Identity --}}
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="title">Stage Title</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="title"
                                                name="title"
                                                value="{{ old('title', $awardShortlistStage->title ?? '') }}"
                                                placeholder="e.g., First Review, Semi-Finals"
                                                required
                                            >
                                            @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="slug">Custom URL Slug (Optional)</label>
                                            <input
                                                type="text"
                                                class="form-control font-monospace"
                                                id="slug"
                                                name="slug"
                                                value="{{ old('slug', $awardShortlistStage->slug ?? '') }}"
                                                placeholder="Leave empty to auto-generate from title"
                                            >
                                            <small class="text-muted d-block mt-0.5">Unique structural tracking key.</small>
                                            @error('slug') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>
                                    </div>

                                    {{-- Right Column: Routing Rules & Pipeline Position --}}
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="position">Processing Priority Order (Position)</label>
                                            <input
                                                type="number"
                                                min="1"
                                                class="form-control"
                                                id="position"
                                                name="position"
                                                value="{{ old('position', $awardShortlistStage->position ?? 1) }}"
                                                required
                                            >
                                            <small class="text-muted d-block mt-0.5">Determines pipeline layout flow order (ascending).</small>
                                            @error('position') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="active">Stage Availability Status</label>
                                            <select class="form-control" name="active" id="active" required>
                                                <option value="1" {{ old('active', $awardShortlistStage->active ?? 1) == 1 ? 'selected' : '' }}>Active (Visible in Pipeline)</option>
                                                <option value="0" {{ old('active', $awardShortlistStage->active ?? 1) == 0 ? 'selected' : '' }}>Inactive (Disabled)</option>
                                            </select>
                                            @error('active') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>

                                        <fieldset class="form-group mt-2">
                                            <label for="mark_as_final">Pipeline Finality Status</label>
                                            <select class="form-control" name="mark_as_final" id="mark_as_final" required>
                                                <option value="0" {{ old('mark_as_final', $awardShortlistStage->mark_as_final ?? 0) == 0 ? 'selected' : '' }}>Intermediary Stage</option>
                                                <option value="1" {{ old('mark_as_final', $awardShortlistStage->mark_as_final ?? 0) == 1 ? 'selected' : '' }}>Final Stage (Locks Submissions upon Entry)</option>
                                            </select>
                                            @error('mark_as_final') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <button class="btn btn-primary w-100 py-1 font-weight-bold" type="submit">
                                            {{ isset($awardShortlistStage->id) ? 'Save Workflow Adjustments' : 'Initialize Shortlist Stage' }}
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
@endsection
