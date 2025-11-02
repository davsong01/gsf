@extends('layouts.dashboard')
@section('title', isset($ministry) ? 'Edit Ministry' : 'Create Ministry')
@section('content')
<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ isset($ministry) ? 'Edit Ministry' : 'Add Ministry' }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ isset($ministry) ? route('ministry.update', $ministry->id) : route('ministry.store') }}" method="POST">
                @csrf
                @if(isset($ministry))
                    @method('PUT')
                @endif

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" value="{{ old('name', $ministry->name ?? '') }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="code">Code</label>
                    <input type="text" name="code" value="{{ old('code', $ministry->code ?? '') }}" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" class="form-control">{{ old('description', $ministry->description ?? '') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" class="form-control">
                        <option value="active" {{ (old('status', $ministry->status ?? '') == 'active') ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ (old('status', $ministry->status ?? '') == 'inactive') ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary mt-2">{{ isset($ministry) ? 'Update' : 'Create' }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
