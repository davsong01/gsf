@extends('layouts.dashboard')

@section('title', isset($stakeholderRole) ? 'Update Role' : 'Create Role')

@section('item')
<li class="breadcrumb-item">
    <a href="{{ route('stakeholderroles.index') }}">All Stakeholder Roles</a>
</li>
@endsection

@section('active')
<li class="breadcrumb-item">{{ isset($stakeholderRole) ? 'Update' : 'Create' }} Stakeholder Role</li>
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
                                action="{{ isset($stakeholderRole) 
                                    ? route('stakeholderroles.update', $stakeholderRole->id) 
                                    : route('stakeholderroles.store') }}"
                                method="POST">
                                @csrf
                                @if(isset($stakeholderRole))
                                    @method('PATCH')
                                @endif

                                {{-- ROLE DETAILS --}}
                                <div class="row">
                                    <div class="col-md-6 mb-1">
                                        <label for="name" class="form-label">Role Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $stakeholderRole->name ?? '') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label for="slug" class="form-label">Role Slug (Optional)</label>
                                        <input type="text" class="form-control" id="slug" name="slug" value="{{ old('slug', $stakeholderRole->slug ?? '') }}">
                                    </div>
                                    <div class="col-md-6 mb-1">
                                        <label for="chapter_id">Status</label>
                                        <select class="form-control" name="status" id="status" required>
                                            <option value="">--Select--</option>
                                            <option value="active" {{ old('role', $stakeholderRole->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('role', $stakeholderRole->status ?? '') == 'inactive' ? 'selected' : '' }}>InActive</option>
                                        </select>
                                    </div>

                                </div>

                             
                                {{-- PERMISSIONS --}}
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Assign Report Permissions</label>
                                    </div>

                                    @foreach($permissions as $permission)
                                        <div class="col-md-4">
                                            <div class="form-check mb-1">
                                                <input type="checkbox" 
                                                    class="form-check-input" 
                                                    id="perm_{{ $permission->id }}" 
                                                    name="permissions[]" 
                                                    value="{{ $permission->id }}"
                                                    {{ isset($stakeholderRole) && $stakeholderRole->permissions->contains($permission->id) ? 'checked' : '' }}>
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
                                            {{ isset($stakeholderRole) ? 'Update Role' : 'Create Role' }}
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
