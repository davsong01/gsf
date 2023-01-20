@extends('layouts.dashboard')
@section('title', 'Community Users')
@section('active')
<li class="breadcrumb-item">GSF Community users</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Users</h4>
                        <div class="">
                            <a href="{{ route('users.create') }}" class="btn btn-primary mt-1">Add new</a>
                            <a href="{{ route('users.import.index') }}" class="btn btn-primary mt-1">Import</a>
                            @if(auth()->user()->isAdmin())<a href="" class="btn btn-primary mt-1">Export</a>@endif
                        </div>
                        @include('includes.alerts')
                        
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration" id="users">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>ID</th>
                                            <th>Avatar</th>
                                            <th>Details</th>
                                            <th>Status</th>
                                            <th>Role</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>
    $(document).ready(function () {
        $('#users').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax":{
                     "url": "{{ route('users.all')}}",
                     "dataType": "json",
                     "type": "POST",
                     "data":{ _token: "{{csrf_token()}}"}
                   },
            "columns": [
                { "data": "S/N" },
                { "data": "family_id" },
                { "data": "avatar" },
                { "data": "details" },
                { "data": "status" },
                { "data": "role" },
                { "data": "actions" }
            ]	 

        });
    });
</script>
@endsection