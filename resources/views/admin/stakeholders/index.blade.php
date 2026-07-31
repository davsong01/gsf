@extends('layouts.dashboard')
@section('title', 'Stakeholder')
@section('active')
<li class="breadcrumb-item">Stakeholder</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
                        <h4 class="card-title mb-0">All stakeholders</h4>

                        <div class="btn-group mt-1">
                            <a href="{{ route('stakeholderpersonnel.create') }}" class="btn btn-primary btn-sm">
                                Add new stakeholder
                            </a>

                            <a href="{{ route('send.chapter.credentials') }}"
                            class="btn btn-success btn-sm"
                            onclick="return confirm('Send credentials to eligible stakeholders?');">
                                Send Chapter Credentials
                            </a>

                            <a href="{{ route('send.zone.credentials') }}"
                            class="btn btn-warning btn-sm"
                            onclick="return confirm('Send credentials to zonal pastors?');">
                                Send Zonal Credentials
                            </a>
                            <a href="{{ route('send.field.credentials') }}"
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('Send credentials to field pastors?');">
                                Send Field Credentials
                            </a>
                        </div>
                    </div>

                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Role/Designation</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stakeholders as $stakeholder)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if($stakeholder?->avatar)
                                                    <img
                                                        src="{{ asset($stakeholder->avatar) }}"
                                                        alt="{{ $stakeholder->name }}"
                                                        style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
                                                @endif
                                                <div>
                                                    {{ $stakeholder->name }} <br>
                                                    <small><strong>Status: </strong>{{ ucfirst($stakeholder->status) }}</small>
                                                    @if($stakeholder->credentials_sent)
                                                    <br>
                                                    <small>
                                                        @if(!empty($stakeholder->zone_id))
                                                        <strong>Zone:</strong> {{ $stakeholder->zone->name }} <br>
                                                        @endif
                                                        @if(!empty($stakeholder->field_id))
                                                        <strong>Field:</strong> {{ $stakeholder->field->name }} <br>
                                                        @endif
                                                    </small>
                                                    <small style="color:green">Login Credentials sent</small>
                                                    @endif
                                                    @if($stakeholder->last_login)
                                                    <br>
                                                    <small style="color:red">Last Login: <strong>{{$stakeholder->last_login}}</strong></small>
                                                    @endif
                                                </div>
                                            </td>

                                            <td>{{ $stakeholder->phone }}</td>
                                            <td>{{ $stakeholder->email }}</td>

                                            <td>
                                                @if(in_array($stakeholder->role_id, chapterStakeholders()) && !is_null($stakeholder->chapter_id))
                                                    <span>{{$stakeholder->role->name}}, </span>
                                                    <a target="_blank" style="color:blue" href="{{ route('chapters.edit', $stakeholder->chapter->id) }}">{{ $stakeholder->chapter->name ?? 'N/A' }}</a>
                                                @elseif(in_array($stakeholder->role_id, zoneStakeholders()) && !is_null($stakeholder->zone_id))
                                                    <span style="color:blue">{{$stakeholder->role->name}}, </span>
                                                    {{ $stakeholder->zone->name ?? 'N/A' }}
                                                @elseif(in_array($stakeholder->role_id, fieldStakeholders()) && !is_null($stakeholder->field_id))
                                                    <span style="color:blue">{{$stakeholder->role->name}}, </span>{{ $stakeholder->field->name ?? 'N/A' }}
                                                @elseif(in_array($stakeholder->role_id, fieldStakeholders()))
                                                    {{ $stakeholder->portfolio }}
                                                @else
                                                    {{ $stakeholder->role->name }}
                                                @endif
                                                <br>
                                                <strong><i>{{$stakeholder?->designation?->name}}</i></strong>
                                            </td>
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                                <a class="actions" data-toggle="tooltip"
                                                title="View/Update stakeholder details"
                                                href="{{ route('stakeholderpersonnel.edit', $stakeholder->id) }}">
                                                    <i class="bx bxs-edit actions"></i>
                                                </a>

                                                <a class="actions" data-toggle="tooltip"
                                                title="Login as stakeholder"
                                                onclick="return confirm('Login as this stakeholder?');"
                                                href="{{ route('switchuser', ['id' => $stakeholder->id, 'type' => 'stakeholder']) }}">
                                                    <i class="fa fa-unlock actions"></i>
                                                </a>

                                                <a class="actions" data-toggle="tooltip"
                                                onclick="return confirm('Are you really sure?');"
                                                title="Delete stakeholder"
                                                href="{{ route('stakeholderpersonnel.delete', $stakeholder->id) }}">
                                                    <i class="fa fa-trash actions"></i>
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
