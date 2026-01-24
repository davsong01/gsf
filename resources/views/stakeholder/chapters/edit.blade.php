@extends('layouts.stakeholderdashboard')
@section('title', 'My Chapter Details')
@section('active')
<li class="breadcrumb-item">My Chapter Details</li>
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
                            <form action="{{ isset($chapter) ? route('stakeholders.chapters.update', $chapter->id) : route('stakeholders.chapters.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @if(isset($chapter))
                                    @method('PUT')
                                @endif

                                <div class="row">
                                    <div class="col-md-12 col-sm-12">

                                        {{-- Name --}}
                                        <fieldset class="form-group">
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
                                        {{-- Address, Email, Phone --}}
                                        <fieldset class="form-group">
                                            <label for="address">Address (Optional)</label>
                                            <input type="text" class="form-control" id="address" name="address" value="{{ old('address', $chapter->address ?? '') }}" placeholder="Enter address">
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="email">Email (Optional)</label>
                                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $chapter->email ?? '') }}" placeholder="Enter email">
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="phone">Phone (Optional)</label>
                                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $chapter->phone ?? '') }}" placeholder="Enter phone">
                                        </fieldset>

                                        {{-- Social Links --}}
                                        <fieldset class="form-group">
                                            <label for="facebook">Facebook Link (Optional)</label>
                                            <input type="text" class="form-control" id="facebook" name="facebook" value="{{ old('facebook', $chapter->facebook ?? '') }}" placeholder="Enter Facebook link">
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="twitter">Twitter Link (Optional)</label>
                                            <input type="text" class="form-control" id="twitter" name="twitter" value="{{ old('twitter', $chapter->twitter ?? '') }}" placeholder="Enter Twitter link">
                                        </fieldset>

                                        {{-- About --}}
                                        <label for="about">About</label><small><br>Write brief biography of chapter, you can include fellowship service periods</small>
                                        <fieldset class="form-group position-relative has-icon-left">
                                            <textarea class="form-control" id="about" rows="3" name="about">{!! old('about', $chapter->about ?? '') !!}</textarea>
                                        </fieldset>

                                        {{-- Banner --}}
                                        <fieldset class="form-group">
                                            <label for="chapter_banner">{{ isset($chapter) ? 'Change Banner' : 'Upload Banner' }}</label><br>
                                            @if(isset($chapter) && $chapter->banner)
                                                <img style="width:100px" src="{{ asset($chapter->banner) }}" alt="{{ $chapter->name . ' banner' }}" class="mb-2 responsive-image"><br>
                                            @endif
                                            <input type="file" accept="image/*" class="form-control" name="chapter_banner" id="chapter_banner">
                                        </fieldset>

                                    </div>
                                </div>

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
