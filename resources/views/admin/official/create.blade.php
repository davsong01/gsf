@extends('layouts.dashboard')
@section('title', 'Add Participant')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('officials.index') }}">Participants</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Create an official</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Basic Inputs start -->
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Create new official</h4>
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{ route('officials.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                        @php
                            $editing = isset($user); // Check if this is edit mode
                            $userPermissions = $editing ? $user->permissions->pluck('slug')->toArray() : old('permissions', []);
                            $rootPermissions = rootPermissions(); // hierarchical array of permissions
                        @endphp

                        <form action="{{ $editing ? route('users.update', $user->id) : route('users.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @if($editing)
                                @method('PUT')
                            @endif

                            <div class="row">
                                <!-- Name -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" name="name" id="name" class="form-control"
                                            value="{{ old('name', $editing ? $user->name : '') }}"
                                            placeholder="Enter name" required>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="email" name="email" id="email" class="form-control"
                                            value="{{ old('email', $editing ? $user->email : '') }}"
                                            placeholder="Enter email" required>
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Phone</label>
                                        <input type="tel" name="phone" id="phone" class="form-control"
                                            value="{{ old('phone', $editing ? $user->phone : '') }}"
                                            placeholder="Enter phone number" required>
                                    </div>
                                </div>

                                <!-- Gender -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gender">Gender</label>
                                        <select name="gender" id="gender" class="form-control" required>
                                            <option value="">--Select Gender--</option>
                                            <option value="Male" {{ old('gender', $editing ? $user->gender : '') == 'Male' ? 'selected' : '' }}>Male</option>
                                            <option value="Female" {{ old('gender', $editing ? $user->gender : '') == 'Female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Passport Upload -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="passport">Upload Passport</label>
                                        <input type="file" name="passport" id="passport" class="form-control" accept="image/*">
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <small class="text-muted"><i style="color:red">Leave blank to use the participant's phone number as password</i></small>
                                        <input type="text" name="password" id="password" class="form-control"
                                            value="{{ old('password') }}" placeholder="Enter password">
                                    </div>
                                </div>
                            </div>

                            <!-- Permissions -->
                            @php
                                $permissions = rootPermissions();
                                $parentsWithChildren = $permissions->filter(fn($p) => isset($p['children']) && count($p['children']));
                                $parentsWithoutChildren = $permissions->filter(fn($p) => !isset($p['children']) || !count($p['children']));
                            @endphp

                            {{-- <div class="container mt-1"> --}}
                                 <h6>Menu Permissions</h5>
                                {{-- Parents with children --}}
                                @foreach($parentsWithChildren as $parent)
                                    {{-- Parent label --}}
                                    <div class="row mb-1">
                                        <div class="col-12">
                                            <span class="fw-bold">{{ $parent['name'] }}</span>
                                        </div>
                                    </div>

                                    {{-- Children row --}}
                                    <div class="row mb-1">
                                        @foreach($parent['children'] as $child)
                                            @if(isset($child['children']) && count($child['children']))
                                                {{-- Child label --}}
                                                <div class="col-12 mb-1">
                                                    <span class="fw-semibold ms-3">{{ $child['name'] }}</span>
                                                </div>

                                                {{-- Grandchildren in col-md-4 --}}
                                                @foreach($child['children'] as $grandchild)
                                                    <div class="col-md-3 mb-1 ms-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input me-2" type="checkbox"
                                                                name="permissions[]"
                                                                value="{{ $grandchild['slug'] }}"
                                                                id="perm_{{ $grandchild['slug'] }}"
                                                                {{ in_array($grandchild['slug'], $userPermissions ?? []) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="perm_{{ $grandchild['slug'] }}">
                                                                {{ $grandchild['name'] }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                {{-- Child without grandchildren --}}
                                                <div class="col-md-4 mb-1">
                                                    <div class="form-check">
                                                        <input class="form-check-input me-2" type="checkbox"
                                                            name="permissions[]"
                                                            value="{{ $child['slug'] }}"
                                                            id="perm_{{ $child['slug'] }}"
                                                            {{ in_array($child['slug'], $userPermissions ?? []) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="perm_{{ $child['slug'] }}">
                                                            {{ $child['name'] }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>

                                    {{-- Horizontal rule --}}
                                    <hr>
                                @endforeach

                                {{-- Parents without children or grandchildren --}}
                                @if($parentsWithoutChildren->count())
                                    <div class="row mb-1">
                                        @foreach($parentsWithoutChildren as $parent)
                                            <div class="col-md-3 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input me-2" type="checkbox"
                                                        name="permissions[]"
                                                        value="{{ $parent['slug'] }}"
                                                        id="perm_{{ $parent['slug'] }}"
                                                        {{ in_array($parent['slug'], $userPermissions ?? []) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="perm_{{ $parent['slug'] }}">
                                                        {{ $parent['name'] }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                            {{-- </div> --}}

                            <div class="row mt-4">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">{{ $editing ? 'Update' : 'Create' }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Inputs end -->
</div>
@endsection

