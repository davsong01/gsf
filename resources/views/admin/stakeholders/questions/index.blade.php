@extends('layouts.dashboard')
@section('title', ucfirst($moduleType ?? 'report') . ' Items')
@section('active')
<li class="breadcrumb-item">{{ ucfirst($moduleType ?? 'report') }} Items</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All {{ ucfirst($moduleType ?? 'report') }} Items</h4>
                        <a href="{{ route('stakeholder.questions.create', ['module_type' => $moduleType ?? 'report']) }}" class="btn btn-primary mt-1">Add New Item</a>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Section</th>
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
                                        @foreach($questions as $question)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                <strong>Section: </strong>{{ optional($question->section)->name ?? '-' }} <br>
                                                <strong>Sub Section: </strong>{{ optional($question->subsection)->name ?? '-' }}
                                            </td>
                                            <td>{{ $question->status == 1 ? 'Active' : 'Inactive' }}</td>
                                            <td>{{ $question->order }}</td>
                                            <td>
                                                {{ $question->label }} <br>
                                                <small><strong>{{ $question->slug}}</strong></small>
                                            </td>
                                            <td>{{ ucfirst($question->type) }}</td>
                                            <td>{{ $question->is_required ? 'Yes' : 'No' }}</td>

                                            {{-- QUESTION PERMISSIONS --}}
                                            <td>
                                                @if($question->permissions->isNotEmpty())
                                                    <ul class="mb-0">
                                                        @foreach($question->permissions as $permission)
                                                            <li>{{ $permission->name }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <em>No permissions assigned</em>
                                                @endif
                                            </td>

                                            <td>
                                                <a href="{{ route('stakeholder.questions.edit', ['question' => $question->id, 'module_type' => $moduleType ?? 'report']) }}"
                                                class="btn btn-sm btn-info mb-1">
                                                    Edit
                                                </a>

                                                <form action="{{ route('stakeholder.questions.destroy', ['question' => $question->id, 'module_type' => $moduleType ?? 'report']) }}"
                                                    method="POST"
                                                    class="d-inline"
                                                    onsubmit="return confirm('Are you sure you want to delete this question?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="btn btn-sm btn-danger mb-1">Delete</button>
                                                </form>

                                                <a href="{{ route('stakeholder.questions.clone', ['question' => $question->id, 'module_type' => $moduleType ?? 'report']) }}"
                                                class="btn btn-sm btn-primary mb-1">
                                                    Clone
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ Zero configuration table -->
</div>
@endsection
