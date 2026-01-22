<div class="content-body">
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">All Members</h4>
                        <div>
                            <a href="{{ $routes['create'] }}" class="btn btn-primary mt-1">Add new</a>
                            <a href="{{ $routes['import'] }}" class="btn btn-primary mt-1">Import</a>
                            @if($isAdmin)
                                <a href="{{ $routes['export'] }}" class="btn btn-primary mt-1">Export</a>
                            @endif
                        </div>
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
                                            <th>Chapter Designation</th>
                                            <th>Role</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
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
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ $routes['all'] }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                isAdmin: {{ $isAdmin ? 'true' : 'false' }}
            }
        },
        columns: [
            { data: "S/N" },
            { data: "family_id" },
            { data: "avatar" },
            { data: "details" },
            { data: "status" },
            { data: "designation" },
            { data: "role" },
            { data: "actions" }
        ]
    });
});
</script>
