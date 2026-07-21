@extends('layouts.dashboard')

@section('title', 'Database Error Logs')

@section('item')
<li class="breadcrumb-item">System Logs</li>
@endsection

@section('active')
<li class="breadcrumb-item active">Database Logs</li>
@endsection

@section('content')
<div class="content-body">
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Database Error Logs</h4>
                        <form action="{{ route('errors.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear all database logs?')">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fa fa-times"></i> Clear All Logs
                            </button>
                        </form>
                    </div>

                    <div class="card-body">
                        <div class="mb-3">
                            <h6>Recurring Errors</h6>
                            @if($recurring->count())
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Message</th>
                                                <th width="120">Count</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recurring as $error)
                                                <tr>
                                                    <td>{{ $error->message }}</td>
                                                    <td>{{ $error->total }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0">No recurring errors recorded yet.</p>
                            @endif
                        </div>

                        <hr>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Level</th>
                                        <th>Message</th>
                                        <th>Context</th>
                                        <th>Stack Trace</th>
                                        <th>Source</th>
                                        <th>Logged At</th>
                                        <th width="90">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($logs as $log)
                                        <tr>
                                            <td>{{ ($logs->currentPage() - 1) * $logs->perPage() + $loop->iteration }}</td>
                                            <td><span class="badge badge-danger">{{ $log->level }}</span></td>
                                            <td style="white-space: normal; word-break: break-word;">{{ \Illuminate\Support\Str::limit($log->message, 180) }}</td>
                                            <td>
                                                @if(!empty($log->context))
                                                    <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#contextModal{{ $log->id }}">View</button>
                                                    <div class="modal fade" id="contextModal{{ $log->id }}" tabindex="-1" role="dialog">
                                                        <div class="modal-dialog modal-lg" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Error Context</h5>
                                                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <pre style="white-space: pre-wrap; word-break: break-word;">{{ json_encode($log->context, JSON_PRETTY_PRINT) }}</pre>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($log->stack_trace)
                                                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#stackModal{{ $log->id }}">View</button>
                                                    <div class="modal fade" id="stackModal{{ $log->id }}" tabindex="-1" role="dialog">
                                                        <div class="modal-dialog modal-xl" role="document">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Stack Trace</h5>
                                                                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <pre style="white-space: pre-wrap; word-break: break-word;">{{ $log->stack_trace }}</pre>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>{{ $log->source }}</td>
                                            <td>{{ optional($log->logged_at)->format('Y-m-d H:i:s') }}</td>
                                            <td>
                                                <a href="{{ route('error.delete', $log->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Delete this log?')">
                                                    Delete
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">No database error logs found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{ $logs->links() }}
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
