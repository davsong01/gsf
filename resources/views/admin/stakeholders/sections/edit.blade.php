@extends('layouts.dashboard')

@section('title', isset($section) ? 'Update Form Structure Section' : 'Create Form Structure Section')

@section('item')
<li class="breadcrumb-item">
    <a href="{{ route('stakeholderreportsection.index', ['module_type' => $moduleType ?? 'report']) }}">All Form Structure Sections</a>
</li>
@endsection

@section('active')
<li class="breadcrumb-item">{{ isset($section) ? 'Update' : 'Create' }} Form Structure Section</li>
@endsection

@section('content')
@php
    $moduleType = old('module_type', $moduleType ?? 'report');
@endphp
<div class="content-body">
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">@include('includes.alerts')</div>
                    <div class="card-content">
                        <div class="card-body">
                            <form id="module-type-switcher" method="GET" action="{{ url()->current() }}" class="mb-1">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="module_type" class="form-label">Module Type</label>
                                        <select class="form-control" id="module_type" name="module_type" required onchange="document.getElementById('module-type-switcher').submit();">
                                            <option value="report" {{ $moduleType === 'report' ? 'selected' : '' }}>Report</option>
                                            <option value="appraisal" {{ $moduleType === 'appraisal' ? 'selected' : '' }}>Appraisal</option>
                                        </select>
                                    </div>
                                </div>
                            </form>

                            <form
                                action="{{ isset($section)
                                    ? route('stakeholderreportsection.update', ['stakeholderreportsection' => $section->id, 'module_type' => $moduleType ?? 'report'])
                                    : route('stakeholderreportsection.store', ['module_type' => $moduleType ?? 'report']) }}"
                                method="POST">
                                @csrf
                                @if(isset($section))
                                    @method('PATCH')
                                @endif

                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Section Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $section->name ?? '') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="slug" class="form-label">Slug</label>
                                        <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug', $section->slug ?? '') }}" placeholder="auto-generated if left blank">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="status">Status</label>
                                        <select class="form-control" name="status" id="status" required>
                                            <option value="">--Select--</option>
                                            <option value="1" {{ old('role', $section->status ?? '') == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ old('role', $section->status ?? '') == '0' ? 'selected' : '' }}>InActive</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">
                                            {{ $moduleType === 'appraisal' ? 'Appraisal Permissions' : 'Access Roles' }}
                                        </label>
                                    </div>

                                    @foreach($permissions as $permission)
                                        <div class="col-md-4">
                                            <div class="form-check mb-1">
                                                <input type="checkbox" class="form-check-input" id="perm_{{ $permission->id }}" name="access_roles[]" value="{{ $permission->id }}" {{ (isset($section->access_roles) && in_array($permission->id, $section->access_roles)) || (is_array(old('access_roles')) && in_array($permission->id, old('access_roles'))) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- SUBMIT --}}
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <button class="btn btn-primary w-100" type="submit">
                                            {{ isset($section) ? 'Update Form Structure Section' : 'Create Form Structure Section' }}
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="module_type" value="{{ $moduleType }}">

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
