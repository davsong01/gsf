@extends('layouts.dashboard')
@section('title', 'Award Shortlist Stages')

@section('active')
<li class="breadcrumb-item">Award Shortlist Stages</li>
@endsection

@section('content')
<div class="content-body">
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Award Shortlist Stages</h4>
                        <a href="{{ route('shortlist.create') }}" class="btn btn-primary">Add New Stage</a>
                    </div>

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th style="width: 60px;">S/N</th>
                                            <th>Stage Title</th>
                                            <th>Slug</th>
                                            <th>Award Type</th>
                                            <th>Engine</th>
                                            <th class="text-center">Entries</th>
                                            <th class="text-center">Position</th>
                                            <th>Status</th>
                                            <th>Pipeline Finality</th>
                                            <th>Created At</th>
                                            <th class="text-end" style="min-width: 120px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @forelse($stages as $stage)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>

                                                <td>
                                                    <strong>{{ $stage->title }}</strong>
                                                    @if($stage->description)
                                                        <small class="text-muted d-block mt-25">{{ $stage->description }}</small>
                                                    @endif
                                                </td>

                                                <td>
                                                    <span class="font-monospace text-muted font-sm">{{ $stage->slug }}</span>
                                                </td>

                                                <td>{{ strtoupper($stage->award_type ?: 'both') }}</td>

                                                <td>
                                                    <span class="badge badge-{{ $stage->stage_engine === 'system' ? 'info' : 'light' }}">
                                                        {{ ucfirst($stage->stage_engine ?? 'manual') }}
                                                    </span>
                                                </td>

                                                <td class="text-center">
                                                    <span class="badge badge-light-primary font-weight-bold">
                                                        {{ $stage->awards_count }}
                                                    </span>
                                                </td>

                                                <td class="text-center">
                                                    <span class="badge badge-light-secondary font-weight-bold">
                                                        {{ $stage->position }}
                                                    </span>
                                                </td>

                                                <td>
                                                    <span class="badge badge-{{ $stage->active ? 'success' : 'secondary' }}">
                                                        {{ $stage->active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>

                                                <td>
                                                    @if($stage->mark_as_final)
                                                        <span class="badge badge-danger d-inline-flex align-items-center gap-1">
                                                            <i class="bx bx-lock-alt"></i> Final Stage
                                                        </span>
                                                    @else
                                                        <span class="badge badge-light text-muted">Intermediary</span>
                                                    @endif
                                                </td>

                                                <td>{{ $stage->created_at->format('d M, Y') }}</td>

                                                <td>
                                                    <div class="btn-group">
                                                        @if($stage->stage_engine === 'system')
                                                            <form method="POST"
                                                                  action="{{ route('shortlist.move-matching-awards', $stage->id) }}"
                                                                  onsubmit="return confirm('Move all awards that currently meet this stage criteria into this stage?');"
                                                                  style="display: inline;">
                                                                @csrf
                                                                <button type="submit"
                                                                        class="btn btn-sm btn-outline-success"
                                                                        data-toggle="tooltip"
                                                                        title="Move matching awards into this stage">
                                                                    <i class="bx bx-transfer-alt"></i>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <button type="button"
                                                                    class="btn btn-sm btn-outline-secondary"
                                                                    disabled
                                                                    data-toggle="tooltip"
                                                                    title="Set this stage engine to System before moving awards by criteria">
                                                                <i class="bx bx-transfer-alt"></i>
                                                            </button>
                                                        @endif

                                                        <a href="{{ route('shortlist.edit', $stage->id) }}"
                                                           class="btn btn-sm btn-outline-primary"
                                                           data-toggle="tooltip" title="Edit">
                                                            <i class="bx bxs-edit"></i>
                                                        </a>

                                                        <form method="POST"
                                                              action="{{ route('shortlist.destroy', $stage->id) }}"
                                                              onsubmit="return confirm('Are you sure you want to completely remove this shortlist stage workflow block?');"
                                                              style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit"
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    data-toggle="tooltip" title="Delete">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="11" class="text-center text-muted py-4">
                                                    <i class="bx bx-layer-plus d-block font-large-1 mb-1 text-light"></i>
                                                    No award shortlist configurations found in parameters.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>
@endsection
