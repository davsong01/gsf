@extends('layouts.dashboard')
@section('title', 'Dashboard')
@section('active', ' Dashboard')

@section('content')
<div class="content-body">
    <!-- Dashboard Analytics Start -->
    <section id="dashboard-analytics">
        <div class="row">
            <!-- Website Analytics Starts-->
            <div class="col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                    </div>
                    <div class="card-content">
                        <div class="row">
                            <div class="col-12">

                            </div>
                            @if(auth()->user()->isAdmin())
                            <div class="col-sm-4 col-12 dashboard-users-success">
                                <div class="card text-center">
                                    <a href="{{ route('fields.index') }}">
                                        <div class="card-content">
                                            <div class="card-body py-1">
                                                <div class="badge-circle badge-circle-lg badge-circle-light-primary mx-auto mb-50">
                                                    {{ $fields->count() }}
                                                </div>
                                                <div class="text-muted line-ellipsis">Fields</div>
                                                <h3 class="mb-0"></h3>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            <div class="col-sm-4 col-12 dashboard-users-success">
                                <div class="card text-center">
                                    <a href="{{ route('zones.index') }}">
                                        <div class="card-content">
                                            <div class="card-body py-1">
                                                <div class="badge-circle badge-circle-lg badge-circle-light-primary mx-auto mb-50">
                                                    {{ $zones->count() }}
                                                </div>
                                                <div class="text-muted line-ellipsis">Zones</div>
                                                <h3 class="mb-0"></h3>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            @endif
                            <div class="col-sm-4 col-12 dashboard-users-success">
                                <div class="card text-center">
                                    <a href="{{ route('users.index') }}">
                                        <div class="card-content">
                                            <div class="card-body py-1">
                                                <div class="badge-circle badge-circle-lg badge-circle-light-primary mx-auto mb-50">
                                                    {{ $alumnis->count() }}
                                                </div>
                                                <div class="text-muted line-ellipsis">Alumni</div>
                                                <h3 class="mb-0"></h3>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            @if(auth()->user()->isAdmin())
                            <div class="col-sm-4 col-12 dashboard-users-success">
                                <div class="card text-center">
                                    <a href="{{ route('chapters.index') }}">
                                        <div class="card-content">
                                            <div class="card-body py-1">
                                                <div class="badge-circle badge-circle-lg badge-circle-light-primary mx-auto mb-50">
                                                    {{ $chapters->count() }}
                                                </div>
                                                <div class="text-muted line-ellipsis">GSF Chapters</div>
                                                <h3 class="mb-0"></h3>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                            @endif
                            <div class="col-sm-4 col-12 dashboard-users-success">
                                <div class="card text-center">
                                    <a href="{{ route('users.index') }}">
                                        <div class="card-content">
                                            <div class="card-body py-1">
                                                <div class="badge-circle badge-circle-lg badge-circle-light-primary mx-auto mb-50">
                                                    {{ $users->count() }}
                                                </div>
                                                <div class="text-muted line-ellipsis">Active Members</div>
                                                <h3 class="mb-0"></h3>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>
@endsection
