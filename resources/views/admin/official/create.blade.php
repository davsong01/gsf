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

@section('extra_styles')
<style>
    .official-shell {
        position: relative;
    }

    .official-hero {
        background: linear-gradient(135deg, #0f2747 0%, #163d6b 45%, #1f6f8b 100%);
        color: #fff;
        border-radius: 18px;
        padding: 28px;
        box-shadow: 0 18px 40px rgba(15, 39, 71, 0.18);
        overflow: hidden;
    }

    .official-hero::after {
        content: "";
        position: absolute;
        inset: auto -120px -120px auto;
        width: 240px;
        height: 240px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.08);
        pointer-events: none;
    }

    .official-hero h2 {
        margin-bottom: 0.35rem;
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    .official-hero p {
        margin-bottom: 0;
        color: rgba(255, 255, 255, 0.82);
        max-width: 720px;
    }

    .hero-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        margin-right: 8px;
        margin-bottom: 8px;
    }

    .glass-card {
        background: #fff;
        border: 1px solid rgba(15, 39, 71, 0.08);
        border-radius: 18px;
        box-shadow: 0 10px 30px rgba(18, 34, 56, 0.06);
    }

    .glass-card .card-header {
        background: transparent;
        border-bottom: 1px solid rgba(15, 39, 71, 0.08);
        padding: 18px 20px 14px;
    }

    .glass-card .card-body {
        padding: 20px;
    }

    .section-heading {
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #6b7280;
        margin-bottom: 14px;
    }

    .field-label {
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #4b5563;
        margin-bottom: 6px;
    }

    .field-note {
        font-size: 12px;
        color: #6b7280;
    }

    .info-tile {
        border-radius: 14px;
        background: linear-gradient(180deg, #f8fbff 0%, #eef5ff 100%);
        border: 1px solid #dbe8ff;
        padding: 14px 16px;
        height: 100%;
    }

    .info-tile .label {
        display: block;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .info-tile .value {
        font-size: 16px;
        font-weight: 700;
        color: #0f2747;
    }

    .permission-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .permission-search {
        max-width: 320px;
    }

    .permission-card {
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        background: #fff;
        padding: 16px;
        margin-bottom: 16px;
    }

    .permission-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }

    .permission-title {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
        color: #111827;
    }

    .permission-subtitle {
        font-size: 12px;
        color: #6b7280;
        margin-top: 2px;
    }

    .permission-badge {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        background: #eef2ff;
        color: #3730a3;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 700;
    }

    .permission-group {
        border-top: 1px solid #eef2f7;
        padding-top: 14px;
        margin-top: 14px;
    }

    .permission-group-title {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 13px;
        font-weight: 700;
        color: #334155;
    }

    .permission-list {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 10px 14px;
    }

    .permission-item {
        border: 1px solid #edf0f5;
        border-radius: 12px;
        padding: 10px 12px;
        background: #fafafa;
        transition: border-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
    }

    .permission-item:hover {
        border-color: #bfd1ff;
        box-shadow: 0 6px 16px rgba(15, 39, 71, 0.06);
        transform: translateY(-1px);
    }

    .permission-item .form-check {
        margin: 0;
    }

    .permission-item .form-check-label {
        font-weight: 600;
        color: #111827;
    }

    .child-block {
        border-left: 3px solid #1f6f8b;
        padding-left: 14px;
        margin-bottom: 14px;
    }

    .sticky-actions {
        position: sticky;
        bottom: 14px;
        z-index: 10;
        padding: 14px;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(15, 39, 71, 0.08);
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(15, 39, 71, 0.08);
    }

    .form-control, .form-select {
        border-radius: 12px;
        min-height: 46px;
    }

    .btn-soft-primary {
        background: #163d6b;
        border-color: #163d6b;
        color: #fff;
    }

    .btn-soft-primary:hover {
        background: #0f2747;
        border-color: #0f2747;
        color: #fff;
    }

    @media (max-width: 991.98px) {
        .permission-list {
            grid-template-columns: 1fr;
        }

        .permission-search {
            max-width: none;
            width: 100%;
        }
    }

    .official-shell { max-width: 1500px; margin: 0 auto; color:#26384e; }
    .official-hero { position:relative; isolation:isolate; min-height:180px; padding:clamp(25px,4vw,40px); border:0; border-radius:22px; background:radial-gradient(circle at 90% 0,rgba(255,255,255,.13),transparent 32%),linear-gradient(120deg,#17375f 0%,#1b536b 70%,#83603a 145%); }
    .official-hero h2 { color:#fff; font-family:Georgia,serif; font-size:clamp(27px,3vw,38px); }
    .official-hero p { font-size:13px; line-height:1.65; }
    .hero-pill { border:1px solid rgba(255,255,255,.2); color:#f2d18b; background:rgba(255,255,255,.08); }
    .glass-card { overflow:hidden; border:1px solid #e5e9ec; border-radius:17px; box-shadow:0 9px 26px rgba(26,51,78,.05); }
    .glass-card .card-header { padding:20px 23px; background:#fff; }
    .glass-card .card-body { padding:23px; }
    .section-heading { color:#a36b34; font-size:9px; letter-spacing:.12em; }
    .field-label { color:#42566b; font-size:10px; letter-spacing:.07em; }
    .official-shell .form-control,.official-shell .form-select { min-height:43px; border:1px solid #dce4e9; border-radius:9px; color:#344b62; font-size:12px; }
    .official-shell .form-control:focus,.official-shell .form-select:focus { border-color:#b88a3c; box-shadow:0 0 0 3px rgba(217,179,95,.14); }
    .permission-toolbar { display:grid; grid-template-columns:minmax(0,1fr) minmax(240px,340px); gap:14px 22px; align-items:center; margin:0; }
    .permission-toolbar h4 { color:#203a55; font-family:Georgia,serif; font-size:22px; }
    .permission-subtitle { color:#8290a0; font-size:11px; line-height:1.5; }
    .permission-search { max-width:none; width:100%; }
    .permission-search input { min-height:42px; }
    .permission-tools { grid-column:1/-1; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; padding-top:12px; border-top:1px solid #edf0f2; }
    .permission-tools > div { display:flex; flex-wrap:wrap; gap:7px; }
    .permission-selected-count { color:#64778b; font-size:11px; font-weight:800; }
    .permission-tool-button { min-height:34px; padding:7px 11px; border:1px solid #dce4e9; border-radius:8px; color:#52677d; background:#f8fafb; font-size:10px; font-weight:800; cursor:pointer; }
    .permission-tool-button:hover { border-color:#c8aa72; color:#73511c; background:#fff8e9; }
    .permission-card { height:100%; padding:0; border:1px solid #e2e8eb; border-radius:14px; background:#fff; box-shadow:0 4px 14px rgba(32,58,85,.035); }
    .permission-card-header { margin:0; padding:15px 16px; border-bottom:1px solid #edf0f2; background:linear-gradient(110deg,#f9fbfb,#fff); }
    .permission-title { color:#203a55; font-size:14px; font-weight:800; }
    .permission-badge { color:#77571e; background:#fff2d6; }
    .permission-toggle { display:inline-flex; align-items:center; gap:7px; min-height:33px; padding:6px 9px; border:1px solid #dce5e7; border-radius:8px; color:#45606f; background:#f7faf9; font-size:10px; font-weight:800; cursor:pointer; white-space:nowrap; }
    .permission-toggle input,.permission-item input { width:15px; height:15px; margin:0; accent-color:#26766d; flex:0 0 auto; cursor:pointer; }
    .permission-toggle--child { color:#285f67; background:#edf6f4; }
    .permission-duplicate-label { color:#4f6478; font-size:12px; }
    .permission-duplicate-label small { display:block; margin-top:3px; color:#8a96a1; font-size:9px; font-weight:500; }
    .child-block { margin:12px; padding:13px; border:1px solid #edf0f2; border-left:3px solid #c6a563; border-radius:10px; background:#fbfcfc; }
    .permission-list { grid-template-columns:repeat(2,minmax(0,1fr)); gap:7px; }
    .permission-item { display:flex; align-items:flex-start; gap:8px; min-height:38px; margin:0; padding:9px 10px; border:1px solid #e7ecee; border-radius:8px; color:#40566b; background:#fff; font-size:10px; font-weight:700; cursor:pointer; }
    .permission-item:hover { border-color:#c8aa72; background:#fffaf0; }
    .permission-item--action { display:block; }
    .permission-item--action input { float:left; margin:1px 8px 0 0; }
    .permission-item small { clear:both; color:#8995a2!important; font-size:9px; font-weight:400; line-height:1.4; }
    .permission-group { margin-top:12px; padding:12px; border:1px solid #e8ecee; border-radius:10px; background:#f8fafb; }
    .permission-group-title { margin-bottom:9px; color:#495f72; font-size:10px; font-weight:850; letter-spacing:.07em; text-transform:uppercase; }
    .sticky-actions { bottom:10px; padding:12px 15px; border:1px solid #e0e6e8; border-radius:12px; background:rgba(255,255,255,.96); box-shadow:0 8px 24px rgba(23,55,95,.1); }
    .btn-soft-primary { min-height:39px; padding:9px 15px; border-radius:8px; color:#4c3617; background:#f2d18b; border-color:#f2d18b; font-size:11px; font-weight:850; }
    .btn-soft-primary:hover { color:#4c3617; background:#ffdda0; border-color:#ffdda0; }
    @media(max-width:760px) { .permission-toolbar { grid-template-columns:1fr; }.permission-tools { align-items:flex-start; flex-direction:column; }.permission-list { grid-template-columns:1fr; }.glass-card .card-body { padding:16px; } }
</style>
@endsection

@section('content')
@php
    $editing = isset($official);
    $userPermissions = array_values(array_unique(old('permissions', $editing ? ($official->permissions ?? []) : [])));
    $permissionSet = collect($userPermissions);
    $nodeIsSelected = function (array $node) use (&$nodeIsSelected, $permissionSet): bool {
        if ($permissionSet->contains($node['slug'])) {
            return true;
        }

        foreach ($node['children'] ?? [] as $child) {
            if ($nodeIsSelected($child)) {
                return true;
            }
        }

        return false;
    };
    $permissions = rootPermissions();
    $menuPermissionSlugs = [];
    $collectMenuSlugs = function ($nodes) use (&$collectMenuSlugs, &$menuPermissionSlugs) {
        foreach ($nodes as $node) {
            $menuPermissionSlugs[] = $node['slug'];
            if (!empty($node['children'])) {
                $collectMenuSlugs($node['children']);
            }
        }
    };
    $collectMenuSlugs($permissions->all());
    $actionPermissions = rootActionPermissions()
        ->unique('slug')
        ->reject(fn ($action) => in_array($action['slug'], $menuPermissionSlugs, true))
        ->values();
    $actionPermissionsByParent = $actionPermissions->groupBy('parent_slug');

    $orderedMenus = $permissions->sortBy(fn ($menu) => $menu['menu_order'] ?? 0)->values();

    $selectedCount = count($userPermissions);
    $renderedPermissionSlugs = [];
    $renderedActionSlugs = [];
    $passportLabel = $editing ? 'Replace passport' : 'Upload passport';
@endphp

<div class="content-body">
    <section id="basic-input" class="official-shell">
        <div class="official-hero mb-4 position-relative">
            <div class="position-relative" style="z-index: 1;">
                <div class="mb-3">
                    <span class="hero-pill">{{ $editing ? 'Editing Official' : 'New Official' }}</span>
                    <span class="hero-pill">{{ $selectedCount }} permissions selected</span>
                </div>
                <h2>{{ $editing ? 'Update official profile and access' : 'Create a new official account' }}</h2>
                <p>
                    Keep the profile details, admin role, and menu permissions in one place so the official gets the exact access they need.
                </p>
            </div>
        </div>

        @include('includes.alerts')

        <form
            action="{{ $editing ? route('officials.update', $official->id) : route('officials.store') }}"
            method="POST"
            enctype="multipart/form-data"
        >
            @csrf
            @if($editing)
                @method('PUT')
            @endif

            <div class="row g-4">
                <div class="col-lg-12 mb-2">
                    <div class="glass-card h-100">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="section-heading mb-1">Profile Details</div>
                                    <h4 class="mb-0">{{ $editing ? 'Official Information' : 'Account Setup' }}</h4>
                                </div>
                                <span class="badge bg-light text-dark border">{{ $editing ? 'Edit mode' : 'Create mode' }}</span>
                            </div>
                        </div>

                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="field-label">Full Name</label>
                                    <input type="text" class="form-control" name="name" value="{{ old('name', $official->name ?? '') }}" placeholder="Enter full name" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="field-label">Email</label>
                                    <input type="email" class="form-control" name="email" value="{{ old('email', $official->email ?? '') }}" placeholder="name@example.com" required>
                                </div>

                                <div class="col-md-6">
                                    <label class="field-label">Phone</label>
                                    <input type="tel" class="form-control" name="phone" value="{{ old('phone', $official->phone ?? '') }}" placeholder="080..." required>
                                </div>

                                <div class="col-md-6">
                                    <label class="field-label">Gender</label>
                                    <select class="form-select" name="gender" required>
                                        <option value="">Select gender</option>
                                        <option value="Male" {{ old('gender', $official->gender ?? '') === 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender', $official->gender ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="field-label">Status</label>
                                    <select class="form-select" name="status" required>
                                        <option value="">Select status</option>
                                        <option value="active" {{ old('status', $official->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $official->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="field-label">Passport</label>
                                    <input type="file" class="form-control" name="passport" accept="image/*">
                                    <div class="field-note mt-2">{{ $passportLabel }}. JPG, JPEG, or PNG only.</div>
                                </div>

                                <div class="col-12">
                                    <label class="field-label">Password</label>
                                    <input type="text" class="form-control" name="password" placeholder="Leave blank to use the phone number">
                                    <div class="field-note mt-2">If you skip this, the system will hash the phone number as the default password.</div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-lg-12">
                    <div class="glass-card h-100">
                        <div class="card-header">
                            <div class="permission-toolbar">
                                <div>
                                    <div class="section-heading mb-1">Menu Permissions</div>
                                    <h4 class="mb-1">Digital Portal Access</h4>
                                    <div class="permission-subtitle">Choose the exact menus and actions this official should see.</div>
                                </div>

                                <div class="permission-search w-100">
                                    <input
                                        type="search"
                                        id="permissionSearch"
                                        class="form-control"
                                        placeholder="Search permissions..."
                                    >
                                </div>
                                <div class="permission-tools">
                                    <span class="permission-selected-count" data-permission-count>{{ $selectedCount }} selected</span>
                                    <div>
                                        <button type="button" class="permission-tool-button" data-permission-select-all><i class="fa fa-check-square-o" aria-hidden="true"></i> Select all</button>
                                        <button type="button" class="permission-tool-button" data-permission-clear><i class="fa fa-square-o" aria-hidden="true"></i> Clear</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body" id="permissionGroups">
                            <div class="row g-3">
                                @foreach($orderedMenus as $parent)
                                <div class="col-12 col-xl-6 permission-searchable">
                                    <div class="permission-card permission-group-wrap h-100">
                                    <div class="permission-card-header">
                                        <div>
                                            <h5 class="permission-title">{{ $parent['name'] }}</h5>
                                            <div class="permission-subtitle">
                                                {{ isset($parent['children']) && count($parent['children']) ? 'Module navigation and related actions' : 'Menu item' }}
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            @if(isset($parent['children']) && count($parent['children']))
                                                <span class="permission-badge">{{ count($parent['children']) }} groups</span>
                                            @endif
                                            @if(!in_array($parent['slug'], $renderedPermissionSlugs, true))
                                                @php $renderedPermissionSlugs[] = $parent['slug']; @endphp
                                                <label class="permission-toggle">
                                                    <input
                                                        type="checkbox"
                                                        class="{{ isset($parent['children']) && count($parent['children']) ? 'js-permission-parent' : '' }}"
                                                        id="perm_{{ $parent['slug'] }}"
                                                        name="permissions[]"
                                                        value="{{ $parent['slug'] }}"
                                                        {{ $nodeIsSelected($parent) ? 'checked' : '' }}
                                                    >
                                                    <span>Access</span>
                                                </label>
                                            @endif
                                        </div>
                                    </div>

                                    @if(isset($parent['children']) && count($parent['children']))
                                        @foreach($parent['children'] as $child)
                                            <div class="child-block permission-searchable">
                                                <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                                    <div class="d-flex align-items-start gap-2">
                                                        @if(!in_array($child['slug'], $renderedPermissionSlugs, true))
                                                            @php $renderedPermissionSlugs[] = $child['slug']; @endphp
                                                            @php $childId = 'perm_' . $child['slug']; @endphp
                                                            <label class="permission-toggle permission-toggle--child" for="{{ $childId }}">
                                                                <input
                                                                    type="checkbox"
                                                                    class="js-permission-parent"
                                                                    id="{{ $childId }}"
                                                                    name="permissions[]"
                                                                    value="{{ $child['slug'] }}"
                                                                    {{ $nodeIsSelected($child) ? 'checked' : '' }}
                                                                >
                                                                <span>{{ $child['name'] }}</span>
                                                            </label>
                                                        @else
                                                            <strong class="permission-duplicate-label">{{ $child['name'] }} <small>Access is managed in another section</small></strong>
                                                        @endif
                                                    </div>
                                                    <span class="permission-badge">Section</span>
                                                </div>

                                                @if(isset($child['children']) && count($child['children']))
                                                    <div class="permission-list">
                                                        @foreach($child['children'] as $grandchild)
                                                            @php $id = 'perm_' . $grandchild['slug']; @endphp
                                                            @if(!in_array($grandchild['slug'], $renderedPermissionSlugs, true))
                                                                @php $renderedPermissionSlugs[] = $grandchild['slug']; @endphp
                                                                <label class="permission-item permission-searchable">
                                                                    <input type="checkbox" id="{{ $id }}" name="permissions[]" value="{{ $grandchild['slug'] }}" {{ in_array($grandchild['slug'], $userPermissions, true) ? 'checked' : '' }}>
                                                                    <span>{{ $grandchild['name'] }}</span>
                                                                </label>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @endif

                                                @if($actionPermissionsByParent->has($child['slug']))
                                                    <div class="permission-group">
                                                        <div class="permission-group-title">
                                                            <span>Actions</span>
                                                            <span class="permission-badge">{{ $actionPermissionsByParent->get($child['slug'])->count() }} actions</span>
                                                        </div>

                                                        <div class="permission-list">
                                                            @foreach($actionPermissionsByParent->get($child['slug']) as $actionPermission)
                                                                @if(!in_array($actionPermission['slug'], $renderedActionSlugs, true) && !in_array($actionPermission['slug'], $renderedPermissionSlugs, true))
                                                                @php $renderedActionSlugs[] = $actionPermission['slug']; @endphp
                                                                @php $id = 'perm_' . $actionPermission['slug']; @endphp
                                                                <label class="permission-item permission-searchable permission-item--action">
                                                                        <input
                                                                            type="checkbox"
                                                                            id="{{ $id }}"
                                                                            name="permissions[]"
                                                                            value="{{ $actionPermission['slug'] }}"
                                                                            {{ in_array($actionPermission['slug'], $userPermissions) ? 'checked' : '' }}
                                                                        >
                                                                        <span>{{ $actionPermission['name'] }}</span>
                                                                    @if(!empty($actionPermission['description']))
                                                                        <small class="text-muted d-block mt-1">{{ $actionPermission['description'] }}</small>
                                                                    @endif
                                                                </label>
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <div class="sticky-actions mt-4">
                                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                                    <div class="text-muted small">
                                        <span data-permission-count>{{ $selectedCount }} permissions selected</span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('officials.index') }}" class="btn btn-light border">Cancel</a>
                                        <button class="btn btn-soft-primary" type="submit">
                                            {{ $editing ? 'Update Official' : 'Create Official' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>

@endsection

@section('extra_scripts')
<script>
    (function () {
        const search = document.getElementById('permissionSearch');
        const items = Array.from(document.querySelectorAll('.permission-searchable'));
        const parentCheckboxes = Array.from(document.querySelectorAll('.js-permission-parent'));
        const permissionCheckboxes = Array.from(document.querySelectorAll('#permissionGroups input[name="permissions[]"]'));
        const selectedCounters = Array.from(document.querySelectorAll('[data-permission-count]'));

        function getDirectChildCheckboxes(container, parentCheckbox) {
            return Array.from(container.querySelectorAll(':scope > .child-block .js-permission-parent')).filter(function (checkbox) {
                return checkbox !== parentCheckbox;
            });
        }

        function getAllDescendantCheckboxes(container, parentCheckbox) {
            return Array.from(container.querySelectorAll('input[type="checkbox"]')).filter(function (checkbox) {
                return checkbox !== parentCheckbox;
            });
        }

        function updateParentState(parentCheckbox) {
            const container = parentCheckbox.closest('.permission-card, .child-block');
            if (!container) {
                return;
            }

            const descendants = getAllDescendantCheckboxes(container, parentCheckbox);
            if (!descendants.length) {
                parentCheckbox.indeterminate = false;
                return;
            }

            const checkedCount = descendants.filter(function (checkbox) {
                return checkbox.checked;
            }).length;

            if (checkedCount === 0) {
                parentCheckbox.checked = false;
                parentCheckbox.indeterminate = false;
                return;
            }

            if (checkedCount === descendants.length) {
                parentCheckbox.checked = true;
                parentCheckbox.indeterminate = false;
                return;
            }

            parentCheckbox.checked = true;
            parentCheckbox.indeterminate = true;
        }

        function syncAncestors() {
            parentCheckboxes.forEach(function (parentCheckbox) {
                updateParentState(parentCheckbox);
            });
            const selected = permissionCheckboxes.filter(function (checkbox) { return checkbox.checked; }).length;
            selectedCounters.forEach(function (counter) {
                counter.textContent = selected + ' permission' + (selected === 1 ? '' : 's') + ' selected';
            });
        }

        parentCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                const container = this.closest('.permission-card, .child-block');
                if (!container) {
                    return;
                }

                getAllDescendantCheckboxes(container, this).forEach(function (input) {
                    input.checked = checkbox.checked;
                    input.indeterminate = false;
                });

                syncAncestors();
            });
        });

        const allCheckboxes = Array.from(document.querySelectorAll('input[type="checkbox"]'));

        allCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                syncAncestors();
            });
        });

        document.querySelector('[data-permission-select-all]')?.addEventListener('click', function () {
            permissionCheckboxes.forEach(function (checkbox) { checkbox.checked = true; checkbox.indeterminate = false; });
            syncAncestors();
        });
        document.querySelector('[data-permission-clear]')?.addEventListener('click', function () {
            permissionCheckboxes.forEach(function (checkbox) { checkbox.checked = false; checkbox.indeterminate = false; });
            syncAncestors();
        });

        if (search && items.length) {
            search.addEventListener('input', function () {
                const term = this.value.trim().toLowerCase();

                items.forEach(function (item) {
                    const text = item.textContent.toLowerCase();
                    item.style.display = text.includes(term) ? '' : 'none';
                });
            });
        }

        syncAncestors();
    })();
</script>
@endsection
