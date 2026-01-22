@extends('layouts.dashboard')
@section('title', 'Designation')
@section('active')
<li class="breadcrumb-item">Designations</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Designations</h4>
                        <a href="{{ route('designation.create') }}" class="btn btn-primary mt-1">Add new designation</a>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Order</th>
                                            <th>Type</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($designations as $designation)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $designation->name }}</td>
                                            <td>{{ $designation->order }}</td>
                                            <td>{{ ucfirst($designation->type) }}</td>
                                            <td>
                                                @if($designation->status == 'active')
                                                    <span class="badge badge-light-success">Active</span>
                                                @else
                                                    <span class="badge badge-light-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                            <a class="actions" title="View/Update designation details" href="{{ route('designation.edit', $designation->id) }}"> <i class="bx bxs-edit actions"></i>
                                            </a>
                                            <form action="{{ route('designation.destroy', $designation->id) }}"
                                                method="POST"
                                                style="display:inline"
                                                onsubmit="return confirm('Are you really sure?');">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="actions btn btn-link p-0"
                                                        data-toggle="tooltip"
                                                        title="Delete designation">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>

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
