@extends('layouts.dashboard')
@section('title', isset($chapter) ? 'Update Chapter' : 'Add Chapter')

@section('item')
<li class="breadcrumb-item"> <a href="{{ route('chapters.index') }}">Chapters</a></li>
@endsection

@section('active')
<li class="breadcrumb-item">{{ isset($chapter) ? (auth()->user()->isAdmin() ? 'Update Chapter' : $chapter->name) : 'Add Chapter' }}</li>
@endsection

@section('content')
<script src="https://cdn.ckeditor.com/4.16.0/standard/ckeditor.js"></script>
<div class="content-body">
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">

                            <form action="{{ isset($chapter) ? route('chapters.update', $chapter->id) : route('chapters.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @if(isset($chapter))
                                    @method('PATCH')
                                @endif

                                <div class="row">
                                    <div class="col-md-12 col-sm-12">

                                        {{-- Name --}}
                                        <fieldset class="form-group">
                                            <label for="name">Name</label>
                                            <input
                                                @if(isset($chapter) && !auth()->user()->isAdmin()) readonly @endif
                                                type="text"
                                                class="form-control"
                                                id="name"
                                                name="name"
                                                value="{{ old('name', $chapter->name ?? '') }}"
                                                placeholder="Enter name"
                                                required
                                            >
                                        </fieldset>

                                        {{-- Field --}}
                                        @if(auth()->user()->isAdmin())
                                        <fieldset class="form-group">
                                            <label for="field_id">Field</label>
                                            <select class="form-control" name="field_id" id="field_id" required>
                                                <option value="">--Select Field--</option>
                                                @foreach($fields as $field)
                                                    <option value="{{ $field->id }}"
                                                        {{ isset($chapter) && $chapter->zone->field_id == $field->id ? 'selected' : '' }}>
                                                        {{ $field->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                        @else
                                        <fieldset class="form-group">
                                            <label for="field_id">Field</label>
                                            <input class="form-control" type="text" readonly
                                                value="{{ $chapter->zone->field->name ?? '' }}">
                                        </fieldset>
                                        @endif

                                        {{-- Zone --}}
                                        @if(auth()->user()->isAdmin())
                                        <fieldset class="form-group">
                                            <label for="zone_id">Zone</label>
                                            <select class="form-control" name="zone_id" id="zone_id" required>
                                                <option value="">--Select Zone--</option>
                                                @if(isset($chapter) && $chapter->zone->field)
                                                    @foreach($chapter->zone->field->zones as $zone)
                                                        <option value="{{ $zone->id }}"
                                                            {{ $chapter->zone_id == $zone->id ? 'selected' : '' }}>
                                                            {{ $zone->name }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                        </fieldset>
                                        @else
                                        <fieldset class="form-group">
                                            <label for="zone_id">Zone</label>
                                            <input class="form-control" type="text" readonly
                                                value="{{ $chapter->zone->name ?? '' }}">
                                        </fieldset>
                                        @endif


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
@if(auth()->user()->isAdmin())
<script>
$(document).ready(function() {
    $('#field_id').on('change', function() {
        var fieldId = $(this).val();
        var $zoneSelect = $('#zone_id');

        // Reset zone select
        $zoneSelect.html('<option value="">--Select Zone--</option>');

        if (!fieldId) return;

        $.ajax({
            url: '/ajax/field/' + fieldId + '/zones',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                $.each(data, function(index, zone) {
                    $zoneSelect.append('<option value="' + zone.id + '">' + zone.name + '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.error('Error fetching zones:', error);
            }
        });
    });
});
</script>
@endif


@endsection
