@extends('layouts.dashboard')
@section('title', isset($speaker) ? 'Edit Conference Speaker' : 'Create Conference Speaker')

@section('content')
<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ isset($speaker) ? 'Edit Conference Speaker' : 'Add Conference Speaker' }}</h4>
            @include('includes.alerts')
        </div>
        <div class="card-body">
            <form action="{{ isset($speaker) ? route('conference_speakers.update', $speaker->id) : route('conference_speakers.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if(isset($speaker))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label for="name">Speaker Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $speaker->name ?? '') }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label for="title">Speaker Title</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $speaker->title ?? '') }}" class="form-control">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="1" {{ old('status', $speaker->status ?? '') == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $speaker->status ?? '') == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label for="image">Speaker Image</label>
                            <input type="file" name="image" id="image" class="form-control">
                            @if(isset($speaker) && $speaker->image)
                                <img src="{{ asset($speaker->image) }}" alt="{{ $speaker->name }}" class="mt-2" style="height:80px; width:auto;">
                            @endif
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-3">
                    {{ isset($speaker) ? 'Update Speaker' : 'Create Speaker' }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
