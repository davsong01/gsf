@extends('layouts.dashboard')

@section('title', isset($stakeholderPermission) ? 'Update Role' : 'Create Role')

@section('item')
<li class="breadcrumb-item">
    <a href="{{ route('stakeholderpermissions.index') }}">All Stakeholder Permissions</a>
</li>
@endsection

@section('active')
<li class="breadcrumb-item">{{ isset($stakeholderPermission) ? 'Update' : 'Create' }} Stakeholder Permissions</li>
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
                                action="{{ isset($stakeholderPermission) 
                                    ? route('stakeholderpermissions.update', $stakeholderPermission->id) 
                                    : route('stakeholderpermissions.store') }}"
                                method="POST">
                                @csrf
                                @if(isset($stakeholderPermission))
                                    @method('PATCH')
                                @endif

                                {{-- ROLE DETAILS --}}
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="name" class="form-label">Permission Name</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                               value="{{ old('name', $stakeholderPermission->name ?? '') }}" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="slug" class="form-label">Permission Slug (Optional)</label>
                                        <input type="text" class="form-control" id="slug" name="slug"
                                               value="{{ old('slug', $stakeholderPermission->slug ?? '') }}">
                                    </div>
                                </div>

                                {{-- SUBMIT --}}
                                <div class="row mt-4">
                                    <div class="col-md-12">
                                        <button class="btn btn-primary w-100" type="submit">
                                            {{ isset($stakeholderPermission) ? 'Update Role' : 'Create Role' }}
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
