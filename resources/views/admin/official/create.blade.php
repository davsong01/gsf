@extends('layouts.dashboard')

@section('title', isset($official) ? 'Edit Official' : 'Add Official')

@section('item')
<li class="breadcrumb-item">
    <a href="{{ route('officials.index') }}">Participants</a>
</li>
@endsection

@section('active')
<li class="breadcrumb-item">
    {{ isset($official) ? 'Edit Official' : 'Create Official' }}
</li>
@endsection

@section('content')
<div class="content-body">
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    @include('includes.alerts')
                    <div class="card-header">
                        <h4 class="card-title">
                            {{ isset($official) ? 'Edit Official' : 'Create New Official' }}
                        </h4>
                    </div>

                    <div class="card-content">
                        <div class="card-body">

                            @php
                                $editing = isset($official);

                                $userPermissions = $editing
                                    ? ($official->permissions ?? [])
                                    : old('permissions', []);

                                $permissions = rootPermissions();

                                $parentsWithChildren = $permissions->filter(
                                    fn ($p) => isset($p['children']) && count($p['children'])
                                );

                                $parentsWithoutChildren = $permissions->filter(
                                    fn ($p) => !isset($p['children']) || !count($p['children'])
                                );
                            @endphp

                            <form
                                action="{{ $editing ? route('officials.update', $official->id) : route('officials.store') }}"
                                method="POST"
                                enctype="multipart/form-data"
                            >
                                @csrf
                                @if($editing)
                                    @method('PUT')
                                @endif

                                {{-- BASIC DETAILS --}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" class="form-control" name="name"
                                                value="{{ old('name', $official->name ?? '') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" class="form-control" name="email"
                                                value="{{ old('email', $official->email ?? '') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Phone</label>
                                            <input type="tel" class="form-control" name="phone"
                                                value="{{ old('phone', $official->phone ?? '') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Gender</label>
                                            <select class="form-control" name="gender" required>
                                                <option value="">-- Select --</option>
                                                <option value="Male"
                                                    {{ old('gender', $official->gender ?? '') === 'Male' ? 'selected' : '' }}>
                                                    Male
                                                </option>
                                                <option value="Female"
                                                    {{ old('gender', $official->gender ?? '') === 'Female' ? 'selected' : '' }}>
                                                    Female
                                                </option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Passport</label>
                                            <input type="file" class="form-control" name="passport" accept="image/*">
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select class="form-control" name="status" required>
                                                <option value="">-- Select --</option>
                                                <option value="1"
                                                    {{ old('status', $official->status ?? '') === '1' ? 'selected' : '' }}>
                                                    Active
                                                </option>
                                                <option value="0"
                                                    {{ old('status', $official->status ?? '') === '0' ? 'selected' : '' }}>
                                                    Inactive
                                                </option>
                                            </select>
                                        </div>
                                    </div> --}}
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Password</label>
                                            <small class="text-muted">
                                                Leave blank to use phone number
                                            </small>
                                            <input type="text" class="form-control" name="password">
                                        </div>
                                    </div>
                                </div>

                                {{-- PERMISSIONS --}}
                                <h6 class="mt-3">Menu Permissions</h6>

                                {{-- Parents with children --}}
                                @foreach($parentsWithChildren as $parent)
                                    <div class="row">
                                        <div class="col-12 fw-bold mb-1" style="font-weight: bolder;">
                                            {{ $parent['name'] }}
                                        </div>
                                    </div>

                                    <div class="row">
                                        @foreach($parent['children'] as $child)

                                            @if(isset($child['children']) && count($child['children']))
                                                <div class="col-12 ms-3 fw-semibold" style="margin-bottom: 5px;color: rebeccapurple;">
                                                    {{ $child['name'] }}
                                                </div>

                                                @foreach($child['children'] as $grandchild)
                                                    @php $id = 'perm_'.$grandchild['slug']; @endphp

                                                    <div class="col-md-3 ms-4" style="margin-bottom: 5px">
                                                        <div class="form-check">
                                                            <input
                                                                type="checkbox"
                                                                class="form-check-input"
                                                                id="{{ $id }}"
                                                                name="permissions[]"
                                                                value="{{ $grandchild['slug'] }}"
                                                                {{ in_array($grandchild['slug'], $userPermissions) ? 'checked' : '' }}
                                                            >
                                                            <label class="form-check-label" for="{{ $id }}">
                                                                {{ $grandchild['name'] }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach

                                            @else
                                                @php $id = 'perm_'.$child['slug']; @endphp
                                                    <div class="col-md-4 ms-3">
                                                        <div class="form-check">
                                                            <input
                                                                type="checkbox"
                                                                class="form-check-input"
                                                                id="{{ $id }}"
                                                                name="permissions[]"
                                                                value="{{ $child['slug'] }}"
                                                                {{ in_array($child['slug'], $userPermissions) ? 'checked' : '' }}
                                                            >
                                                            <label class="form-check-label" for="{{ $id }}">
                                                                {{ $child['name'] }}
                                                            </label>
                                                        </div>
                                                    </div>

                                            @endif

                                        @endforeach
                                    </div>

                                    <hr>
                                @endforeach

                                {{-- Parents without children --}}
                                <div class="row">
                                    @foreach($parentsWithoutChildren as $parent)
                                        @php $id = 'perm_'.$parent['slug']; @endphp

                                        <div class="col-md-3">
                                            <div class="form-check">
                                                <input
                                                    type="checkbox"
                                                    class="form-check-input"
                                                    id="{{ $id }}"
                                                    name="permissions[]"
                                                    value="{{ $parent['slug'] }}"
                                                    {{ in_array($parent['slug'], $userPermissions) ? 'checked' : '' }}
                                                >
                                                <label class="form-check-label" for="{{ $id }}">
                                                    {{ $parent['name'] }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>

                                {{-- SUBMIT --}}
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <button class="btn btn-primary">
                                            {{ $editing ? 'Update Official' : 'Create Official' }}
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
