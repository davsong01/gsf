@extends('layouts.dashboard')
@section('title', 'Form Structure Items')
@section('active')
<li class="breadcrumb-item">Form Structure Items</li>
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
                            <h4 class="card-title mb-1">Form Structure Items</h4>
                            <small class="text-muted">Manage both report and appraisal items from one list.</small>
                        </div>

                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <form method="GET" action="{{ route('stakeholder.questions.index') }}" class="d-flex gap-2 align-items-center">
                                <select name="module_type" class="form-select form-select-sm" style="min-width: 180px;" onchange="this.form.submit()">
                                    <option value="all" @selected($selectedModule === 'all')>All Modules</option>
                                    <option value="report" @selected($selectedModule === 'report')>Report</option>
                                    <option value="appraisal" @selected($selectedModule === 'appraisal')>Appraisal</option>
                                </select>
                            </form>

                            <button type="button" class="btn btn-primary mt-0" data-bs-toggle="modal" data-bs-target="#structureModuleModal">
                                Add New Item
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        @include('includes.alerts')

                        <form method="GET" action="{{ route('stakeholder.questions.index') }}" class="row g-3 align-items-end mb-4">
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
                                <input type="text" name="name" class="form-control" value="{{ request('name') }}" placeholder="Search by label">
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
                                    @foreach($permissionsForIndex as $permission)
                                        <option value="{{ $permission->id }}" @selected((string) request('permission') === (string) $permission->id)>{{ $permission->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Section</label>
                                <select name="section_id" class="form-select">
                                    <option value="">All</option>
                                    @foreach($sections as $section)
                                        <option value="{{ $section->id }}" @selected((string) request('section_id') === (string) $section->id)>{{ $section->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Sub Section</label>
                                <select name="sub_section_id" class="form-select">
                                    <option value="">All</option>
                                    @foreach($subsections as $subsection)
                                        <option value="{{ $subsection->id }}" @selected((string) request('sub_section_id') === (string) $subsection->id)>{{ $subsection->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 d-flex gap-2">
                                <button class="btn btn-primary w-100" type="submit">Filter</button>
                                <a href="{{ route('stakeholder.questions.index') }}" class="btn btn-light border w-100">Reset</a>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>S/N</th>
                                        <th>Section</th>
                                        <th>Sub Section</th>
                                        <th>Module</th>
                                        <th>Status</th>
                                        <th>Order</th>
                                        <th>Label</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Permissions</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($questions as $question)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ optional($question->section)->name ?? '-' }}</td>
                                            <td>{{ optional($question->subsection)->name ?? '-' }}</td>
                                            <td><span class="badge bg-light text-dark border text-uppercase">{{ $question->module_type ?? 'report' }}</span></td>
                                            <td><span class="badge {{ $question->status ? 'bg-success' : 'bg-secondary' }}">{{ $question->status ? 'Active' : 'Inactive' }}</span></td>
                                            <td>{{ $question->order }}</td>
                                            <td>
                                                <div class="fw-semibold">{{ $question->label }}</div>
                                                <small class="text-muted">{{ $question->slug }}</small>
                                            </td>
                                            <td>{{ ucfirst($question->type) }}</td>
                                            <td>{{ $question->is_required ? 'Yes' : 'No' }}</td>
                                            <td>
                                                @if($question->permissions->isNotEmpty())
                                                    <ul class="mb-0 ps-3">
                                                        @foreach($question->permissions as $permission)
                                                            <li>{{ $permission->name }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <em class="text-muted">No permissions assigned</em>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <a href="{{ route('stakeholder.questions.edit', ['question' => $question->id, 'module_type' => $question->module_type ?? 'report']) }}" class="btn btn-sm btn-info">Edit</a>
                                                    <form action="{{ route('stakeholder.questions.destroy', ['question' => $question->id, 'module_type' => $question->module_type ?? 'report']) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this question?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-sm btn-danger">Delete</button>
                                                    </form>
                                                    <a href="{{ route('stakeholder.questions.clone', ['question' => $question->id, 'module_type' => $question->module_type ?? 'report']) }}" class="btn btn-sm btn-primary">Clone</a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center text-muted py-4">No item records found for {{ strtolower($moduleLabel) }}.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            {{ $questions->links() }}
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
                <h5 class="modal-title">Create Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Module Type</label>
                <select id="questionModuleType" class="form-select">
                    <option value="report">Report</option>
                    <option value="appraisal">Appraisal</option>
                </select>
                <small class="text-muted d-block mt-2">Choose which structure module this item belongs to.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="continueQuestionCreate">Continue</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra_scripts')
<script>
    (function () {
        const continueButton = document.getElementById('continueQuestionCreate');
        const moduleSelect = document.getElementById('questionModuleType');

        if (continueButton && moduleSelect) {
            continueButton.addEventListener('click', function () {
                const moduleType = moduleSelect.value || 'report';
                const url = new URL(@json(route('stakeholder.questions.create')), window.location.origin);
                url.searchParams.set('module_type', moduleType);
                window.location.href = url.toString();
            });
        }
    })();
</script>
@endsection
