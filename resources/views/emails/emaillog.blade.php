@extends('layouts.dashboard')

@section('extra_styles')
<style>
    .btn i{
        top: 0px !important;
    }
    .email-log-preview { display:inline-block; max-width:360px; color:#53647a; font-size:12px; line-height:1.5; }
    .email-log-modal .modal-content { overflow:hidden; border:0; border-radius:14px; }
    .email-log-modal .modal-header,.email-log-modal .modal-footer { padding:15px 20px; border-color:#e8edf1; }
    .email-log-modal__body { max-height:70vh; overflow:auto; padding:20px; }
    .email-log-modal__meta { display:grid; gap:8px; }
    .email-log-modal__meta div { display:grid; grid-template-columns:100px minmax(0,1fr); gap:10px; font-size:12px; }
    .email-log-modal__meta span { color:#718096; font-weight:700; }
    .email-log-modal__meta strong { color:#243b56; overflow-wrap:anywhere; }
    .email-log-modal__content { color:#283b51; font-size:14px; line-height:1.65; overflow-wrap:anywhere; }
    .email-log-modal__content table { max-width:100%; border-collapse:collapse; }
    .email-log-modal__content td { padding:5px 9px 5px 0; vertical-align:top; }
</style>
@endsection

@section('title', 'Email Logs')

@section('active')
<li class="breadcrumb-item">Email Logs</li>
@endsection

@section('content')
<div class="content-body">
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Email Logs</h4>
                    </div>

                    <div class="card-content">
                        <div class="card-body card-dashboard">

                            {{-- Bulk Action Form --}}
                            <form method="POST" action="{{ route('criticalEmail.bulkAction') }}" id="bulkForm">
                                @csrf

                                <div class="row mb-2">
                                    <div class="col-md-4">
                                        <select name="action" class="form-control" required>
                                            <option value="">-- Select Action --</option>
                                            <option value="resend">Resend Selected</option>
                                            <option value="delete">Delete Selected</option>
                                        </select>
                                    </div>

                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary"
                                            onclick="return confirm('Are you sure you want to perform this action?')">
                                            Apply
                                        </button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>
                                                    <input type="checkbox" id="selectAll">
                                                </th>
                                                <th>S/N</th>
                                                <th>Type</th>
                                                <th>Subject</th>
                                                <th>Recipient</th>
                                                <th>Preview</th>
                                                <th>Error(s)</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(isset($emails) && !empty($emails))
                                                @foreach($emails as $email)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" name="emails[]" value="{{ $email->id }}" class="row-checkbox">
                                                    </td>

                                                    <td>{{ $count++ }}</td>

                                                    <td>
                                                        {{ $email->type }} <br>
                                                        <small>
                                                            @if($email->status == 0)
                                                                <span style="color:red">Pending...</span><br>
                                                            @else
                                                                <span style="color:green">Sent at {{ $email->sent_at }}</span><br>
                                                            @endif
                                                            <br>
                                                            Logged at: {{ $email->created_at }}
                                                        </small>
                                                    </td>

                                                    <td>{{ $email->subject }}</td>

                                                    <td>{{ $email->recipient }}</td>

                                <td>
                                    <span class="email-log-preview">{{ \Illuminate\Support\Str::limit(strip_tags($email->content), 150) }}</span><br>
                                    <button type="button" class="btn btn-info btn-sm mt-1" data-target="#emailModal{{ $email->id }}" data-toggle="modal">
                                                            <i class="fa fa-eye"></i>
                                        View full email
                                    </button>
                                                    </td>

                                                    <td><span style="color:red">{{ $email->errors }}</span></td>

                                                    <td style="padding-left: 5px;padding-right: 5px;">
                                                        <a class="actions" data-toggle="tooltip" title="Resend"
                                                           href="{{ route('criticalEmail.show', $email->id) }}">
                                                            <i class="fa fa-envelope actions"></i>
                                                        </a>

                                                        <a class="actions" data-toggle="tooltip"
                                                           onclick="return confirm('Are you really sure?');"
                                                           href="{{ route('CriticalEmail.delete', $email->id) }}">
                                                            <i class="fa fa-trash actions"></i>
                                                        </a>
                                                    </td>
                                                </tr>

                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </form>
                            {{-- End Bulk Form --}}

                            @foreach($emails as $email)
                                <div class="modal fade email-log-modal" id="emailModal{{ $email->id }}" tabindex="-1" role="dialog" aria-labelledby="emailModalTitle{{ $email->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="emailModalTitle{{ $email->id }}">Email preview</h4>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            </div>
                                            <div class="modal-body email-log-modal__body">
                                                <div class="email-log-modal__meta">
                                                    <div><span>Subject</span><strong>{{ $email->subject }}</strong></div>
                                                    <div><span>Recipient</span><strong>{{ $email->recipient }}</strong></div>
                                                    <div><span>Type</span><strong>{{ $email->type }}</strong></div>
                                                </div>
                                                <hr>
                                                <div class="email-log-modal__content">{!! $email->content !!}</div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light border" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('extra_scripts')
<script>
document.getElementById('selectAll').addEventListener('change', function () {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
@endsection
