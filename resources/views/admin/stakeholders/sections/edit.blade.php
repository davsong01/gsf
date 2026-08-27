@extends('layouts.dashboard')

@section('title', isset($section) ? 'Update ' . ucfirst($moduleType ?? 'report') . ' Section' : 'Create ' . ucfirst($moduleType ?? 'report') . ' Section')

@section('item')
<li class="breadcrumb-item">
    <a href="{{ route('stakeholderreportsection.index', ['module_type' => $moduleType ?? 'report']) }}">All {{ ucfirst($moduleType ?? 'report') }} Sections</a>
</li>
@endsection

@section('active')
<li class="breadcrumb-item">{{ isset($section) ? 'Update' : 'Create' }} {{ ucfirst($moduleType ?? 'report') }} Sections</li>
@endsection

@section('content')
<div class="content-body">
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">@include('includes.alerts')</div>
                    <div class="card-content">
                        <div class="card-body">
                            <form 
                                action="{{ isset($section) 
                                    ? route('stakeholderreportsection.update', ['stakeholderreportsection' => $section->id, 'module_type' => $moduleType ?? 'report']) 
                                    : route('stakeholderreportsection.store', ['module_type' => $moduleType ?? 'report']) }}"
                                method="POST">
                                @csrf
                                @if(isset($section))
                                    @method('PATCH')
                                @endif

                                {{-- ROLE DETAILS --}}
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Section Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $section->name ?? '') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="slug" class="form-label">Slug</label>
                                        <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug', $section->slug ?? '') }}" placeholder="auto-generated if left blank">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="chapter_id">Status</label>
                                        <select class="form-control" name="status" id="status" required>
                                            <option value="">--Select--</option>
                                            <option value="1" {{ old('role', $section->status ?? '') == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('role', $section->status ?? '') == '0' ? 'selected' : '' }}>InActive</option>
                                        </select>
                                    </div>
                                </div>
                                
                                {{-- PERMISSIONS --}}
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Assign Roles</label>
                                    </div>

                                    @foreach($roles as $role)
                                        <div class="col-md-4">
                                            <div class="form-check mb-1">
                                                <input type="checkbox" class="form-check-input" id="perm_{{ $role->id }}" name="access_roles[]" value="{{ $role->id }}" {{ (isset($section->access_roles) && in_array($role->id, $section->access_roles)) || (is_array(old('access_roles')) && in_array($role->id, old('access_roles'))) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm_{{ $role->id }}">
                                                    {{ $role->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- SUBMIT --}}
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <button class="btn btn-primary w-100" type="submit">
                                            {{ isset($section) ? 'Update Section' : 'Create Section' }}
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="module_type" value="{{ $moduleType ?? 'report' }}">

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
