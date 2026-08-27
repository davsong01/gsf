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
</style>
@endsection

@section('content')
@php
    $editing = isset($official);
    $userPermissions = $editing ? ($official->permissions ?? []) : old('permissions', []);
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
    $actionPermissions = rootActionPermissions();
    $actionPermissionsByParent = $actionPermissions->groupBy('parent_slug');

    $parentsWithChildren = $permissions->filter(fn ($p) => isset($p['children']) && count($p['children']));
    $parentsWithoutChildren = $permissions->filter(fn ($p) => !isset($p['children']) || !count($p['children']));

    $selectedCount = count($userPermissions);
    $passportLabel = $editing ? 'Replace passport' : 'Upload passport';
    $selectedDesignation = $editing ? ($official->designation->name ?? 'Not assigned') : 'Not assigned';
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
                    Keep the profile details, fixed admin role, designation, and menu permissions in one place so the official gets the exact access they need.
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
                <div class="col-lg-5">
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
                                    <label class="field-label">Designation</label>
                                    <select class="form-select" name="designation_id">
                                        <option value="">Select designation</option>
                                        @foreach($designations as $designation)
                                            <option value="{{ $designation->id }}" {{ (string) old('designation_id', $official->designation_id ?? '') === (string) $designation->id ? 'selected' : '' }}>
                                                {{ $designation->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="field-note mt-2">Use <strong>National President</strong> for the officer who evaluates NEC members.</div>
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

                            <div class="row g-3 mt-2">
                                <div class="col-md-6">
                                    <div class="info-tile">
                                        <span class="label">Account Role</span>
                                        <div class="value">Admin</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="info-tile">
                                        <span class="label">Account State</span>
                                        <div class="value">{{ $editing ? ucfirst($official->status ?? 'active') : 'Active by default' }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="info-tile">
                                        <span class="label">Designation</span>
                                        <div class="value">{{ $selectedDesignation }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="info-tile">
                                        <span class="label">Menu Permissions</span>
                                        <div class="value">{{ $selectedCount }} selected</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
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
                            </div>
                        </div>

                        <div class="card-body" id="permissionGroups">
                            @foreach($parentsWithChildren as $parent)
                                <div class="permission-card permission-group-wrap">
                                    <div class="permission-card-header">
                                        <div>
                                            <h5 class="permission-title">{{ $parent['name'] }}</h5>
                                            <div class="permission-subtitle">Module navigation and related actions</div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="permission-badge">{{ count($parent['children']) }} groups</span>
                                            <div class="permission-item mb-0">
                                                <div class="form-check">
                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input js-permission-parent"
                                                        id="perm_{{ $parent['slug'] }}"
                                                        name="permissions[]"
                                                        value="{{ $parent['slug'] }}"
                                                        {{ $nodeIsSelected($parent) ? 'checked' : '' }}
                                                    >
                                                    <label class="form-check-label" for="perm_{{ $parent['slug'] }}">
                                                        Access
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @foreach($parent['children'] as $child)
                                        <div class="child-block permission-searchable">
                                            <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                                                <div class="d-flex align-items-start gap-2">
                                                    <div class="permission-item mb-0">
                                                        <div class="form-check">
                                                            @php $childId = 'perm_' . $child['slug']; @endphp
                                                            <input
                                                                type="checkbox"
                                                                class="form-check-input js-permission-parent"
                                                                id="{{ $childId }}"
                                                                name="permissions[]"
                                                                value="{{ $child['slug'] }}"
                                                                {{ $nodeIsSelected($child) ? 'checked' : '' }}
                                                            >
                                                            <label class="form-check-label" for="{{ $childId }}">
                                                                {{ $child['name'] }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="permission-badge">Section</span>
                                            </div>

                                            @if(isset($child['children']) && count($child['children']))
                                                <div class="permission-list">
                                                    @foreach($child['children'] as $grandchild)
                                                        @php $id = 'perm_' . $grandchild['slug']; @endphp
                                                        <div class="permission-item permission-searchable">
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
                                                            @php $id = 'perm_' . $actionPermission['slug']; @endphp
                                                            <div class="permission-item permission-searchable">
                                                                <div class="form-check">
                                                                    <input
                                                                        type="checkbox"
                                                                        class="form-check-input"
                                                                        id="{{ $id }}"
                                                                        name="permissions[]"
                                                                        value="{{ $actionPermission['slug'] }}"
                                                                        {{ in_array($actionPermission['slug'], $userPermissions) ? 'checked' : '' }}
                                                                    >
                                                                    <label class="form-check-label" for="{{ $id }}">
                                                                        {{ $actionPermission['name'] }}
                                                                    </label>
                                                                </div>
                                                                @if(!empty($actionPermission['description']))
                                                                    <small class="text-muted d-block mt-1">{{ $actionPermission['description'] }}</small>
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach

                            @if($parentsWithoutChildren->isNotEmpty())
                                <div class="permission-card">
                                    <div class="permission-card-header">
                                        <div>
                                            <h5 class="permission-title">Standalone Permissions</h5>
                                            <div class="permission-subtitle">Single-level menu permissions</div>
                                        </div>
                                        <span class="permission-badge">{{ $parentsWithoutChildren->count() }}</span>
                                    </div>

                                    <div class="permission-list">
                                        @foreach($parentsWithoutChildren as $parent)
                                            @php $id = 'perm_' . $parent['slug']; @endphp
                                            <div class="permission-item permission-searchable">
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
                                </div>
                            @endif

                            <div class="sticky-actions mt-4">
                                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center">
                                    <div class="text-muted small">
                                        {{ $selectedCount }} permission{{ $selectedCount === 1 ? '' : 's' }} selected
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
