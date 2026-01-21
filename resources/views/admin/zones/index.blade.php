@extends('layouts.dashboard')
@section('title', 'Zones')
@section('active')
<li class="breadcrumb-item">Zones</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Zones</h4>
                        <a href="{{ route('zones.create') }}" class="btn btn-primary mt-1">Add new zone</a>
                        @include('includes.alerts')

                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Pastor</th>
                                            <th>Field</th>
                                            <th>Chapter Count</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($zones as $zone)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $zone->name }}</td>
                                            <td>
                                                @if ($zone->stakeholder)
                                                    <a href="{{route('stakeholderpersonnel.edit', $zone->stakeholder->id)}}">{{ $zone->stakeholder->name }}</a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ !is_null($zone->field) ? $zone->field->name : 'N/A'}}</td>
                                            <td>{{ $zone->chapters->count() }}</td>

                                            <td style="padding-left: 5px;padding-right: 5px;">
                                            <a class="actions" data-toggle="tooltip" title="View/Update zone details" href="{{ route('zones.edit', $zone->id) }}"> <i class="bx bxs-edit actions"></i>
                                            </a>

                                            <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete zone" href="{{ route('zones.delete', $zone->id) }}"> <i class="fa fa-trash"></i></
                                            </a>
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
