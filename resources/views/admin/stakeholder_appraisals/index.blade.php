@extends('layouts.dashboard')

@section('title', 'Appraisals')

@section('active')
<li class="breadcrumb-item">Appraisals</li>
@endsection

@section('content')
<div class="content-body">
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h4 class="card-title mb-1">Stakeholder Appraisals</h4>
                            <small class="text-muted">Track self appraisal and evaluation status, then reopen a published form when needed.</small>
                        </div>
                        <span class="badge bg-light text-dark border">{{ $stakeholders->count() }} stakeholders</span>
                    </div>

                    <div class="card-body">
                        @if(session('message'))
                            <div class="alert alert-success">{{ session('message') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th>Self Status</th>
                                        <th>Evaluation Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stakeholders as $stakeholder)
                                        @php
                                            $appraisal = $stakeholder->appraisal;
                                            $dashboardAccess = $appraisalService->dashboardAccess($stakeholder);
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="fw-semibold">{{ $stakeholder->name }}</div>
                                                <small class="text-muted">{{ $stakeholder->email }}</small>
                                            </td>
                                            <td>
                                                <div>{{ $stakeholder->role?->name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $stakeholder->designation?->name ?? 'No designation' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge {{ ($appraisal?->self_status ?? 'draft') === 'published' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $appraisal?->self_status ?? 'draft' }}
                                                </span>
                                                <div class="small text-muted mt-1">
                                                    {{ $appraisal?->self_published_at ? 'Published ' . $appraisal->self_published_at->format('d M Y, h:i A') : 'Not published yet' }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge {{ ($appraisal?->evaluation_status ?? 'draft') === 'published' ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $appraisal?->evaluation_status ?? 'draft' }}
                                                </span>
                                                <div class="small text-muted mt-1">
                                                    {{ $appraisal?->evaluation_published_at ? 'Published ' . $appraisal->evaluation_published_at->format('d M Y, h:i A') : 'Not published yet' }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @if(($dashboardAccess['my_appraisal'] ?? false))
                                                        <a href="{{ route('switchuser', ['id' => $stakeholder->id, 'type' => 'stakeholder', 'target' => 'appraisal-my']) }}"
                                                           class="btn btn-outline-primary btn-sm"
                                                           onclick="return confirm('Switch to this stakeholder and open the self appraisal form?');">
                                                            Open Self Form
                                                        </a>
                                                    @endif

                                                    @if(($dashboardAccess['evaluations'] ?? false))
                                                        <a href="{{ route('switchuser', ['id' => $stakeholder->id, 'type' => 'stakeholder', 'target' => 'appraisal-evaluations']) }}"
                                                           class="btn btn-outline-info btn-sm"
                                                           onclick="return confirm('Switch to this stakeholder and open the evaluation workspace?');">
                                                            Open Evaluations
                                                        </a>
                                                    @endif

                                                    @if(($appraisal?->self_status ?? 'draft') === 'published')
                                                        <form action="{{ route('stakeholderappraisals.unlock-self', $stakeholder) }}" method="POST" onsubmit="return confirm('Reopen this self appraisal?');">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-warning btn-sm">Reopen Self</button>
                                                        </form>
                                                    @endif

                                                    @if(($appraisal?->evaluation_status ?? 'draft') === 'published')
                                                        <form action="{{ route('stakeholderappraisals.unlock-evaluation', $stakeholder) }}" method="POST" onsubmit="return confirm('Reopen this evaluation?');">
                                                            @csrf
                                                            <button type="submit" class="btn btn-outline-warning btn-sm">Reopen Eval</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">No stakeholders found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
