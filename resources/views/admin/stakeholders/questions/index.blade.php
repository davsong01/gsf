@extends('layouts.dashboard')

@section('title', 'Form Structure Items')

@section('active')
<li class="breadcrumb-item">Form Structure Items</li>
@endsection

@section('content')
@php
    $selectedModule = $moduleType ?? 'all';
    $moduleLabel = match ($selectedModule) {
        'report' => 'Report',
        'appraisal' => 'Appraisal',
        'campagn' => 'Campaign',
        default => 'All Modules',
    };
@endphp

<style>
    .structure-page { --structure-ink:#24334a; --structure-muted:#77849a; --structure-line:#e7ebf1; }
    .structure-hero { position:relative; display:flex; align-items:center; justify-content:space-between; gap:22px; overflow:hidden; margin-bottom:20px; padding:25px 28px; border-radius:15px; color:#fff; background:linear-gradient(120deg,#152c52 0%,#1d4778 72%,#3479a4 140%); box-shadow:0 12px 30px rgba(25,55,95,.14); }
    .structure-hero::after { position:absolute; right:80px; top:-115px; width:260px; height:260px; border:1px solid rgba(255,255,255,.13); border-radius:50%; content:""; box-shadow:0 0 0 30px rgba(255,255,255,.035),0 0 0 60px rgba(255,255,255,.025); }
    .structure-hero__copy,.structure-hero__actions { position:relative; z-index:1; }
    .structure-hero__eyebrow { margin:0 0 5px; color:#b9d7ee; font-size:10px; font-weight:800; letter-spacing:.15em; text-transform:uppercase; }
    .structure-hero h1 { margin:0; color:#fff; font-size:25px; font-weight:750; letter-spacing:-.02em; }
    .structure-hero p { margin:5px 0 0; color:rgba(255,255,255,.76); font-size:12px; }
    .structure-hero__actions { display:flex; align-items:center; gap:12px; }
    .structure-hero__count { min-width:92px; padding:8px 13px; border:1px solid rgba(255,255,255,.2); border-radius:10px; background:rgba(255,255,255,.08); text-align:center; }
    .structure-hero__count strong { display:block; color:#fff; font-size:20px; line-height:1.1; }
    .structure-hero__count span { color:rgba(255,255,255,.7); font-size:9px; text-transform:uppercase; letter-spacing:.08em; }
    .structure-hero .btn { border:0; border-radius:9px; padding:11px 15px; color:#1a3b67; background:#fff; font-weight:700; }
    .structure-card { margin-bottom:18px; border:1px solid var(--structure-line); border-radius:13px; box-shadow:0 5px 18px rgba(31,49,75,.045); }
    .structure-card .card-header { padding:17px 20px; border-bottom:1px solid var(--structure-line); background:#fff; }
    .structure-card .card-body { padding:20px; }
    .structure-section-title { margin:0; color:var(--structure-ink); font-size:14px; font-weight:750; }
    .structure-section-note { display:block; margin-top:3px; color:var(--structure-muted); font-size:11px; }
    .structure-filter label { margin-bottom:5px; color:#68768c; font-size:10px; font-weight:750; letter-spacing:.04em; text-transform:uppercase; }
    .structure-filter .form-control,.structure-filter .form-select { min-height:39px; border-color:#dfe5ec; border-radius:8px; font-size:12px; }
    .structure-filter-actions { display:flex; align-items:flex-end; gap:8px; height:100%; padding-top:20px; }
    .structure-filter-actions .btn { flex:1 1 0; min-width:0; min-height:39px; padding:7px 8px; border-radius:8px; font-size:11px; font-weight:700; white-space:nowrap; }
    .structure-bulkbar { display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:12px; padding:11px 13px; border:1px solid var(--structure-line); border-radius:10px; background:#f8fafc; }
    .structure-bulkbar .form-select { width:auto; min-width:160px; min-height:36px; border-radius:7px; font-size:12px; }
    .structure-bulkbar .btn { min-height:36px; padding:7px 13px; border-radius:7px; font-size:12px; font-weight:700; }
    .structure-bulkbar__hint { color:var(--structure-muted); font-size:11px; }
    .structure-table-wrap { overflow:auto; border:1px solid var(--structure-line); border-radius:10px; }
    .structure-table { min-width:970px; margin:0; }
    .structure-table thead th { padding:12px 13px; border-bottom:1px solid #dfe5ec; color:#64748b; background:#f7f9fc; font-size:9px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; white-space:nowrap; }
    .structure-table tbody td { padding:13px; border-color:#edf0f4; color:#40506a; font-size:11px; vertical-align:middle; }
    .structure-table tbody tr { transition:background .15s ease,opacity .15s ease; }
    .structure-table tbody tr:hover { background:#f8fbff; }
    .structure-table tbody tr.is-dragging { opacity:.4; }
    .structure-table tbody tr.drop-before { box-shadow:inset 0 3px #4385d8; }
    .structure-table tbody tr.drop-after { box-shadow:inset 0 -3px #4385d8; }
    .structure-item { display:flex; align-items:flex-start; gap:9px; min-width:210px; }
    .structure-drag-handle { flex:0 0 auto; margin-top:1px; padding:1px 3px; border:0; color:#98a4b5; background:transparent; cursor:grab; font-size:16px; line-height:1; }
    .structure-drag-handle:active { cursor:grabbing; }
    .structure-item__label { color:#26364e; font-size:12px; font-weight:750; line-height:1.4; }
    .structure-item__slug { display:block; margin-top:3px; color:#8b96a7; font-size:10px; }
    .structure-tag { display:inline-flex; align-items:center; gap:4px; padding:4px 7px; border:1px solid #e5eaf0; border-radius:20px; color:#607089; background:#f8fafc; font-size:9px; font-weight:750; white-space:nowrap; }
    .structure-tag--module { color:#315c8d; background:#eef5ff; border-color:#dce9fb; text-transform:uppercase; }
    .structure-tag--active { color:#16734a; background:#eaf8f0; border-color:#ccebd9; }
    .structure-tag--inactive { color:#788394; background:#f1f3f6; }
    .structure-placement strong { display:block; color:#40506a; font-weight:650; }
    .structure-placement small { display:block; margin-top:3px; color:#8b96a7; font-size:10px; }
    .structure-actions { display:flex; align-items:center; gap:6px; }
    .structure-actions .btn { padding:5px 8px; border-radius:6px; font-size:10px; }
    .structure-permissions { display:flex; flex-wrap:wrap; gap:4px; max-width:190px; }
    .structure-empty { padding:50px 20px!important; color:var(--structure-muted)!important; text-align:center; }
    .structure-empty strong { display:block; margin-bottom:5px; color:#43536b; font-size:13px; }
    .structure-pagination { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-top:15px; color:var(--structure-muted); font-size:11px; }
    @media(max-width:767px) { .structure-hero { align-items:flex-start; flex-direction:column; padding:22px; } .structure-hero__actions { width:100%; justify-content:space-between; } .structure-card .card-body { padding:14px; } }
</style>

<div class="content-body structure-page">
    <section>
        <header class="structure-hero">
            <div class="structure-hero__copy">
                <p class="structure-hero__eyebrow">Form Builder · {{ $moduleLabel }}</p>
                <h1>Form Structure Items</h1>
                <p>Manage questions and fields across reports, appraisals, and campaigns.</p>
            </div>
            <div class="structure-hero__actions">
                <div class="structure-hero__count"><strong>{{ number_format($questions->total()) }}</strong><span>Items found</span></div>
                <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#structureModuleModal"><i class="fa fa-plus me-1"></i> Add item</button>
            </div>
        </header>

        @include('includes.alerts')

        <div class="card structure-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div><h2 class="structure-section-title">Find items</h2><small class="structure-section-note">Filter by module, label, status, permissions, or placement.</small></div>
                @if(request()->hasAny(['name', 'status', 'permission', 'section_id', 'sub_section_id']) || $selectedModule !== 'all')
                    <a href="{{ route('stakeholder.questions.index') }}" class="btn btn-sm btn-light border">Clear filters</a>
                @endif
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('stakeholder.questions.index') }}" class="structure-filter">
                    <div class="row g-3 align-items-end">
                        <div class="col-xl-3 col-md-4">
                            <label for="filter-module">Module</label>
                            <select id="filter-module" name="module_type" class="form-select">
                                <option value="all" @selected($selectedModule === 'all')>All modules</option>
                                <option value="report" @selected($selectedModule === 'report')>Report</option>
                                <option value="appraisal" @selected($selectedModule === 'appraisal')>Appraisal</option>
                                <option value="campagn" @selected($selectedModule === 'campagn')>Campaign</option>
                            </select>
                        </div>
                        <div class="col-xl-6 col-md-4">
                            <label for="filter-name">Item name</label>
                            <input id="filter-name" type="search" name="name" class="form-control" value="{{ request('name') }}" placeholder="Search by label">
                        </div>
                        <div class="col-xl-3 col-md-4">
                            <label for="filter-status">Status</label>
                            <select id="filter-status" name="status" class="form-select">
                                <option value="">All</option>
                                <option value="1" @selected(request('status') === '1')>Active</option>
                                <option value="0" @selected(request('status') === '0')>Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-3 align-items-end mt-1">
                        <div class="col-xl-3 col-md-4">
                            <label for="filter-permission">Permission</label>
                            <select id="filter-permission" name="permission" class="form-select">
                                <option value="">All permissions</option>
                                @foreach($permissionsForIndex as $permission)
                                    <option value="{{ $permission->id }}" @selected((string) request('permission') === (string) $permission->id)>{{ $permission->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-3 col-md-4">
                            <label for="filter-section">Section</label>
                            <select id="filter-section" name="section_id" class="form-select">
                                <option value="">All sections</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}" @selected((string) request('section_id') === (string) $section->id)>{{ $section->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-3 col-md-4">
                            <label for="filter-subsection">Subsection</label>
                            <select id="filter-subsection" name="sub_section_id" class="form-select">
                                <option value="">All subsections</option>
                                @foreach($subsections as $subsection)
                                    <option value="{{ $subsection->id }}" @selected((string) request('sub_section_id') === (string) $subsection->id)>{{ $subsection->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-3 col-md-4">
                            <div class="structure-filter-actions">
                                <button class="btn btn-primary" type="submit"><i class="fa fa-filter me-1"></i> Apply</button>
                                <a href="{{ route('stakeholder.questions.index') }}" class="btn btn-light border">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card structure-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div><h2 class="structure-section-title">Items <span class="text-muted fw-normal">({{ number_format($questions->total()) }})</span></h2><small class="structure-section-note">Drag the grip to reorder items within the same module and section.</small></div>
                <span class="structure-tag"><i class="fa fa-layer-group"></i> {{ $moduleLabel }}</span>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('stakeholder.questions.bulk-action') }}" id="questionBulkForm" class="structure-bulkbar">
                    @csrf
                    <input type="hidden" name="module_type" value="{{ $selectedModule }}">
                    <select name="action" id="questionBulkAction" class="form-select" required aria-label="Bulk action">
                        <option value="">Bulk action</option>
                        <option value="activate">Activate selected</option>
                        <option value="deactivate">Deactivate selected</option>
                        <option value="delete">Delete selected</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Apply</button>
                    <span class="structure-bulkbar__hint">Select one or more items using the checkboxes.</span>
                    <div id="questionBulkInputs"></div>
                </form>

                <div class="structure-table-wrap">
                    <table class="table structure-table align-middle">
                        <thead>
                            <tr>
                                <th style="width:38px"><input type="checkbox" id="selectAllQuestions" aria-label="Select all items"></th>
                                <th>Form item</th>
                                <th>Module</th>
                                <th>Placement</th>
                                <th>Configuration</th>
                                <th>Status / order</th>
                                <th>Permissions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="questionSortableRows" data-reorder-url="{{ route('stakeholder.questions.reorder') }}">
                            @forelse($questions as $question)
                                @php
                                    $questionModule = $question->module_type ?? 'report';
                                    $questionModuleLabel = $questionModule === 'campagn' ? 'Campaign' : ucfirst($questionModule);
                                @endphp
                                <tr data-question-id="{{ $question->id }}"
                                    data-module="{{ $questionModule }}"
                                    data-section="{{ $question->section_id ?? '' }}"
                                    data-subsection="{{ $question->sub_section_id ?? '' }}">
                                    <td><input type="checkbox" class="question-row-checkbox" value="{{ $question->id }}" aria-label="Select {{ $question->label }}"></td>
                                    <td>
                                        <div class="structure-item">
                                            <button type="button" class="structure-drag-handle" draggable="true" title="Drag to reorder" aria-label="Drag {{ $question->label }} to reorder">⠿</button>
                                            <span><span class="structure-item__label">{{ $question->label }}</span><small class="structure-item__slug">{{ $question->slug }}</small></span>
                                        </div>
                                    </td>
                                    <td><span class="structure-tag structure-tag--module">{{ $questionModuleLabel }}</span></td>
                                    <td class="structure-placement">
                                        <strong>{{ optional($question->section)->name ?? 'No section' }}</strong>
                                        <small>{{ optional($question->subsection)->name ?? 'No subsection' }}</small>
                                    </td>
                                    <td>
                                        <span class="structure-tag">{{ ucfirst(str_replace('_', ' ', $question->type)) }}</span>
                                        <small class="d-block text-muted mt-1">{{ $question->width_class ?: 'col-md-6' }} · {{ $question->is_required ? 'Required' : 'Optional' }}</small>
                                    </td>
                                    <td>
                                        <span class="structure-tag {{ $question->status ? 'structure-tag--active' : 'structure-tag--inactive' }}">{{ $question->status ? 'Active' : 'Inactive' }}</span>
                                        <small class="d-block text-muted mt-1">Order {{ $question->order ?? 0 }}</small>
                                    </td>
                                    <td>
                                        @if($question->permissions->isNotEmpty())
                                            <div class="structure-permissions">
                                                @foreach($question->permissions as $permission)
                                                    <span class="structure-tag">{{ $permission->name }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">No permissions</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="structure-actions">
                                            <a href="{{ route('stakeholder.questions.edit', ['question' => $question->id, 'module_type' => $questionModule]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="{{ route('stakeholder.questions.clone', ['question' => $question->id, 'module_type' => $questionModule]) }}" class="btn btn-sm btn-outline-secondary">Clone</a>
                                            <form action="{{ route('stakeholder.questions.destroy', ['question' => $question->id, 'module_type' => $questionModule]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this question?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="structure-empty"><strong>No form items found</strong>Try changing the filters or create a new item for {{ strtolower($moduleLabel) }}.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="structure-pagination">
                    <span>Showing {{ $questions->firstItem() ?? 0 }}–{{ $questions->lastItem() ?? 0 }} of {{ number_format($questions->total()) }} items</span>
                    {{ $questions->links() }}
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="structureModuleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Form Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label class="form-label" for="questionModuleType">Choose a module</label>
                <select id="questionModuleType" class="form-select">
                    <option value="report">Report</option>
                    <option value="appraisal">Appraisal</option>
                    <option value="campagn">Campaign</option>
                </select>
                <small class="text-muted d-block mt-2">The new item will be added to the selected form structure.</small>
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

    const selectAllQuestions = document.getElementById('selectAllQuestions');
    const questionBulkForm = document.getElementById('questionBulkForm');
    const questionBulkInputs = document.getElementById('questionBulkInputs');
    const questionSortableRows = document.getElementById('questionSortableRows');
    let draggedQuestionRow = null;

    selectAllQuestions?.addEventListener('change', function () {
        document.querySelectorAll('.question-row-checkbox').forEach(function (checkbox) {
            checkbox.checked = selectAllQuestions.checked;
        });
    });

    questionBulkForm?.addEventListener('submit', function (event) {
        const selected = Array.from(document.querySelectorAll('.question-row-checkbox:checked'));
        const action = document.getElementById('questionBulkAction')?.value;

        if (!selected.length) {
            event.preventDefault();
            alert('Please select at least one item.');
            return;
        }

        if (action === 'delete' && !confirm('Delete the selected items? This cannot be undone.')) {
            event.preventDefault();
            return;
        }

        questionBulkInputs.innerHTML = '';
        selected.forEach(function (checkbox) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'question_ids[]';
            input.value = checkbox.value;
            questionBulkInputs.appendChild(input);
        });
    });

    questionSortableRows?.querySelectorAll('tr[data-question-id]').forEach(function (row) {
        const dragHandle = row.querySelector('.structure-drag-handle');

        dragHandle?.addEventListener('dragstart', function (event) {
            draggedQuestionRow = row;
            row.classList.add('is-dragging');
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', row.dataset.questionId);
        });

        dragHandle?.addEventListener('dragend', function () {
            row.classList.remove('is-dragging');
            questionSortableRows.querySelectorAll('.drop-before,.drop-after').forEach(function (item) {
                item.classList.remove('drop-before', 'drop-after');
            });
            draggedQuestionRow = null;
        });

        row.addEventListener('dragover', function (event) {
            if (!draggedQuestionRow || row === draggedQuestionRow) return;
            event.preventDefault();
            row.classList.remove('drop-before', 'drop-after');
            const position = event.clientY < row.getBoundingClientRect().top + row.offsetHeight / 2 ? 'before' : 'after';
            row.classList.add(position === 'before' ? 'drop-before' : 'drop-after');
        });

        row.addEventListener('drop', async function (event) {
            if (!draggedQuestionRow || row === draggedQuestionRow) return;
            event.preventDefault();

            const sameGroup = ['module', 'section', 'subsection'].every(function (key) {
                return draggedQuestionRow.dataset[key] === row.dataset[key];
            });
            if (!sameGroup) {
                alert('Items can only be reordered within the same module, section, and subsection.');
                return;
            }

            const position = event.clientY < row.getBoundingClientRect().top + row.offsetHeight / 2 ? 'before' : 'after';
            const movingRow = draggedQuestionRow;
            if (position === 'before') {
                questionSortableRows.insertBefore(movingRow, row);
            } else {
                questionSortableRows.insertBefore(movingRow, row.nextSibling);
            }

            try {
                const response = await fetch(questionSortableRows.dataset.reorderUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || @json(csrf_token())
                    },
                    body: JSON.stringify({
                        question_id: movingRow.dataset.questionId,
                        target_id: row.dataset.questionId,
                        position: position
                    })
                });

                if (!response.ok) {
                    const result = await response.json().catch(() => ({}));
                    throw new Error(result.message || 'Could not save the new item order.');
                }

                window.location.reload();
            } catch (error) {
                alert(error.message || 'Could not save the new item order.');
                window.location.reload();
            }
        });
    });
</script>
@endsection
