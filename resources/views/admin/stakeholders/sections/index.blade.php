@extends('layouts.dashboard')
@section('title', 'Form Structure Sections')
@section('active')
<li class="breadcrumb-item">Form Structure Sections</li>
@endsection

@section('content')
@php
    $selectedModule = $moduleType ?? 'all';
    $moduleLabel = $selectedModule === 'all' ? 'All Modules' : ucfirst($selectedModule);
@endphp
<div class="content-body">
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="card-title mb-1">Form Structure Sections</h4>
                            <small class="text-muted">Manage the section groups used by both reports and appraisals.</small>
                        </div>

                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <form method="GET" action="{{ route('stakeholderreportsection.index') }}" class="d-flex gap-2 align-items-center">
                                <select name="module_type" class="form-select form-select-sm" style="min-width: 180px;" onchange="this.form.submit()">
                                    <option value="all" @selected($selectedModule === 'all')>All Modules</option>
                                    <option value="report" @selected($selectedModule === 'report')>Report</option>
                                    <option value="appraisal" @selected($selectedModule === 'appraisal')>Appraisal</option>
                                </select>
                            </form>

                            <button type="button" class="btn btn-primary mt-0" data-bs-toggle="modal" data-bs-target="#structureModuleModal">
                                Add Section
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        @include('includes.alerts')

                        <form method="GET" action="{{ route('stakeholderreportsection.index') }}" class="row g-3 align-items-end mb-4">
                            <div class="col-md-2">
                                <label class="form-label">Module Type</label>
                                <select name="module_type" class="form-select">
                                    <option value="all" @selected($selectedModule === 'all')>All Modules</option>
                                    <option value="report" @selected($selectedModule === 'report')>Report</option>
                                    <option value="appraisal" @selected($selectedModule === 'appraisal')>Appraisal</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" value="{{ request('name') }}" placeholder="Search by name">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All</option>
                                    <option value="1" @selected(request('status') === '1')>Active</option>
                                    <option value="0" @selected(request('status') === '0')>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Permission</label>
                                <select name="permission" class="form-select">
                                    <option value="">All</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" @selected((string) request('permission') === (string) $role->id)>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button class="btn btn-primary w-100" type="submit">Filter</button>
                                <a href="{{ route('stakeholderreportsection.index') }}" class="btn btn-light border w-100">Reset</a>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Name</th>
                                        <th>Module</th>
                                        <th>Status</th>
                                        <th>Roles with Access</th>
                                        <th>Sub Sections</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sections as $section)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td class="fw-semibold">{{ $section->name }}</td>
                                            <td>
                                                <span class="badge bg-light text-dark border text-uppercase">{{ $section->module_type ?? 'report' }}</span>
                                            </td>
                                            <td>
                                                <span class="badge {{ $section->status ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $section->status ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if(!empty($section->access_roles) && count($section->access_roles) > 0)
                                                    <ul class="mb-0 ps-3">
                                                        @foreach($section->access_roles as $roleId)
                                                            @php($role = \App\Models\StakeholderRole::find($roleId))
                                                            @if($role)
                                                                <li>{{ $role->name }}</li>
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <em class="text-muted">No roles assigned</em>
                                                @endif
                                            </td>
                                            <td>{{ $section->subsections_count ?? 0 }}</td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <a href="{{ route('stakeholderreportsection.edit', ['stakeholderreportsection' => $section->id, 'module_type' => $section->module_type ?? 'report']) }}" class="btn btn-sm btn-info">Edit</a>
                                                    <form action="{{ route('stakeholderreportsection.destroy', ['stakeholderreportsection' => $section->id, 'module_type' => $section->module_type ?? 'report']) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">No section records found for {{ strtolower($moduleLabel) }}.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            {{ $sections->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="structureModuleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Section</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Module Type</label>
                <select id="sectionModuleType" class="form-select">
                    <option value="report">Report</option>
                    <option value="appraisal">Appraisal</option>
                </select>
                <small class="text-muted d-block mt-2">Choose which structure module this section belongs to.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="continueSectionCreate">Continue</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_scripts')
<script>
    (function () {
        const continueButton = document.getElementById('continueSectionCreate');
        const moduleSelect = document.getElementById('sectionModuleType');

        if (continueButton && moduleSelect) {
            continueButton.addEventListener('click', function () {
                const moduleType = moduleSelect.value || 'report';
                const url = new URL(@json(route('stakeholderreportsection.create')), window.location.origin);
                url.searchParams.set('module_type', moduleType);
                window.location.href = url.toString();
            });
        }
    })();
</script>
@endsection
