@extends('layouts.dashboard')
@section('title', 'Ministry Fields')
@section('content')
<div class="content-body">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">Fields for Ministry: {{ $ministry->name }}</h4>
            <a href="{{ route('ministries.fields.create', $ministry->id) }}" class="btn btn-primary">Add New Field</a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name / Label</th>
                            <th>Order</th>
                            <th>Type</th>
                            <th>Usage</th>
                            <th>Registration Types</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fields as $index => $field)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $field->label }}</strong><br>
                                <small>Key: {{ $field->name }}</small>
                            </td>
                            <td>{{ ucfirst($field->display_order) }}</td>
                            <td>{{ ucfirst($field->type) }}</td>
                            <td>{{ ucfirst($field->field_usage) }}</td>
                            @php
                                $names = collect($field->registration_types ?? [])
                                    ->map(function ($id) {
                                        $types = registrationTypeNames();
                                        return $types[$id] ?? null;
                                    })
                                    ->filter()
                                    ->implode(', ');
                            @endphp

                            <td>{{ $names ?: '—' }}</td>

                            <td>{{ $field->status ? 'Active' : 'Inactive' }}</td>
                            <td>
                                <a href="{{ route('ministries.fields.edit', [$ministry->id, $field->id]) }}" class="btn btn-sm btn-info">Edit</a>
                                <form action="{{ route('ministries.fields.destroy', [$ministry->id, $field->id]) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                        @if($fields->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center">No fields added yet.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
