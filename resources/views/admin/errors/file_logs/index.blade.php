@extends('layouts.dashboard')

@section('title', 'Error Log Files')

@section('item')
<li class="breadcrumb-item">System Logs</li>
@endsection

@section('active')
<li class="breadcrumb-item active">Error Log Files</li>
@endsection

@section('content')
<div class="content-body">
    <section>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Error Log Files</h4>
                        <form action="{{ route('error-files.deleteAll') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete all error log files?')">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">Delete All Files</button>
                        </form>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Filename</th>
                                        <th>Size</th>
                                        <th>Last Modified</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($files as $file)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $file['name'] }}</td>
                                            <td>{{ $file['size'] }}</td>
                                            <td>{{ $file['modified'] }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('error-files.download', $file['name']) }}" class="btn btn-info btn-sm" title="Download">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                                <form action="{{ route('error-files.delete', $file['name']) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this log file?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No error log files found.</td>
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
