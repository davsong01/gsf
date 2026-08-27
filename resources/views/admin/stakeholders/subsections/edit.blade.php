@extends('layouts.dashboard')

@section('title', isset($subSection) ? 'Update ' . ucfirst($moduleType ?? 'report') . ' Sub Section' : 'Create ' . ucfirst($moduleType ?? 'report') . ' Sub Section')

@section('item')
<li class="breadcrumb-item">
    <a href="{{ route('stakeholderreportsubsection.index', ['module_type' => $moduleType ?? 'report']) }}">All {{ ucfirst($moduleType ?? 'report') }} Sub Sections</a>
</li>
@endsection

@section('active')
<li class="breadcrumb-item">
    {{ isset($subSection) ? 'Update' : 'Create' }} {{ ucfirst($moduleType ?? 'report') }} Sub Section
</li>
@endsection

@section('content')
<div class="content-body">
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">@include('includes.alerts')</div>

                    <div class="card-body">
                            <form
                                action="{{ isset($subSection)
                                ? route('stakeholderreportsubsection.update', ['stakeholderreportsubsection' => $subSection->id, 'module_type' => $moduleType ?? 'report'])
                                : route('stakeholderreportsubsection.store', ['module_type' => $moduleType ?? 'report']) }}"
                            method="POST">
                            @csrf
                            @isset($subSection)
                                @method('PATCH')
                            @endisset

                            {{-- BASIC DETAILS --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">SubSection Name</label>
                                    <input type="text"
                                           class="form-control"
                                           name="name"
                                           value="{{ old('name', $subSection->name ?? '') }}"
                                           required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Slug</label>
                                    <input type="text"
                                           class="form-control"
                                           name="slug"
                                           value="{{ old('slug', $subSection->slug ?? '') }}"
                                           placeholder="auto-generated if left blank">
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Status</label>
                                    <select class="form-control" name="status" required>
                                        <option value="">-- Select --</option>
                                        <option value="1" {{ old('status', $subSection->status ?? '') == 1 ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $subSection->status ?? '') == 0 ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                            </div>

                            {{-- PARENT SECTION --}}
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Parent Section</label>
                                    <select class="form-control" name="section_id" required>
                                        <option value="">-- Select Section --</option>
                                        @foreach($sections as $sec)
                                            <option value="{{ $sec->id }}"
                                                {{ old('section_id', $subSection->section_id ?? '') == $sec->id ? 'selected' : '' }}>
                                                {{ $sec->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- ACCESS ROLES --}}
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label class="form-label">Assign Roles</label>
                                </div>

                                @foreach($roles as $role)
                                    <div class="col-md-4">
                                        <div class="form-check mb-1">
                                            <input type="checkbox"
                                                   class="form-check-input"
                                                   id="role_{{ $role->id }}"
                                                   name="access_roles[]"
                                                   value="{{ $role->id }}"
                                                   {{ in_array(
                                                        $role->id,
                                                        old('access_roles', $subSection->access_roles ?? [])
                                                   ) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="role_{{ $role->id }}">
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
                                        {{ isset($subSection) ? 'Update Sub Section' : 'Create Sub Section' }}
                                    </button>
                                </div>
                            </div>
                            <input type="hidden" name="module_type" value="{{ $moduleType ?? 'report' }}">

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
