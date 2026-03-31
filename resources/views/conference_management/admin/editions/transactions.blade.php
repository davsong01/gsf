@extends('layouts.conference')
@section('title', 'Conference Transactions')
@section('active')
<li class="breadcrumb-item">{{ $edition->conference_theme }} Transactions</li>
@endsection
@section('content2')
<style>
    .response{
        font-size: 11px;
        border: 1px solid black;
        max-width: 450px;
        display: inline-block;
        padding: 6px;
        white-space: pre-line;
        overflow: scroll;
        text-overflow: ellipsis;
    }
</style>
<div class="content-body">
    <div class="card-header">
        <h4 class="card-title">All Transactions </h4>
        {{-- <div class="">
            <a href="{{ route('conferenceusers.export',  ['edition'=>$edition->id]) }}" class="btn btn-primary mt-1">Export</a>
        </div> --}}
    </div>
    <!-- Zero configuration table -->
    <form method="GET" class="row align-items-end" style="padding:0 25px;">
        <div class="col-md-3 mb-2">
            <label class="form-label">Transaction ID</label>
            <input type="text" name="transid" class="form-control" value="{{old('transid')}}">
        </div>
        <div class="col-md-3 mb-2">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="{{old('name')}}">
        </div>
        <div class="col-md-3 mb-2">
            <label class="form-label">Email</label>
            <input type="text" name="email" class="form-control" value="{{old('email')}}">
        </div>
        <!-- From Date -->
        <div class="col-md-3 mb-2">
            <label class="form-label">From</label>
            <input type="date" name="from_date" class="form-control" value="{{old('from_date')}}">
        </div>

        <!-- To Date -->
        <div class="col-md-3 mb-2">
            <label class="form-label">To</label>
            <input type="date" name="to_date" class="form-control">
        </div>
        <div class="col-md-3 mb-2">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="">All Status</option>
                <option value="Initiated" {{ request()->status == 'Initiated' ? 'selected' : '' }}>Initiated</option>
                <option value="Pending" {{ request()->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Complete" {{ request()->status =='Complete' ? 'selected' : '' }}>Complete</option>
            </select>
        </div>
        <!-- Field -->
        <div class="col-md-3 mb-2">
            <label class="form-label">Plan</label>
            <select name="conference_plan_id" class="form-control">
                <option value="">All Plans</option>
                @foreach ($edition->conferenceplans as $plan)
                    <option value="{{ $plan->id }}" {{ request()->conference_plan_id == $plan->id ? 'selected' : '' }}>{{ $plan->title }}</option>
                @endforeach
            </select>
        </div>

        <!-- Zone -->
        <div class="col-md-3 mb-2">
            <label class="form-label">Registration Status</label>
            <select name="registration_status" class="form-control">
                <option value="">All Statuses</option>
                <option value="Pending" {{ request()->registration_status == 'Pending' ? 'selected' : '' }}>
                    Pending
                </option>
                <option value="Complete" {{ request()->registration_status == 'Complete' ? 'selected' : '' }}>
                    Complete
                </option>
            </select>
        </div>

        <div class="col-md-3 mb-2">
            <label class="form-label">Registration User Type</label>
            <select name="registration_user_type" class="form-control">
                <option value="">All</option>
                <option value="moderator" {{ request()->registration_user_type == 'moderator' ? 'selected' : '' }}>
                    Moderator
                </option>
                <option value="participant" {{ request()->registration_user_type == 'participant' ? 'selected' : '' }}>
                    Participant
                </option>
            </select>
        </div>
        <!-- Chapter -->
        @if($edition->ministry->code == 'gsf')
        <div class="col-md-3 mb-2">
            <label class="form-label">Field</label>
            <select name="field_id" class="form-control">
                <option value="">All Fields</option>
                @foreach ($fields as $field)
                    <option value="{{ $field->id }}">{{ $field->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 mb-2">
            <label class="form-label">Zone</label>
            <select name="zone_id" class="form-control">
                <option value="">All Zones</option>
                @foreach ($zones as $zone)
                    <option value="{{ $zone->id }}">{{ $zone->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 mb-2">
            <label class="form-label">Chapter</label>
            <select name="chapter_id" class="form-control">
                <option value="">All Chapters</option>
                @foreach ($chapters as $chapter)
                    <option value="{{ $chapter->id }}">{{ $chapter->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <!-- Button -->
        <div class="col-md-2 mb-2">
            <button type="submit" name="action" value="filter" class="btn btn-secondary w-100">
                Filter
            </button>
        </div>

        <div class="col-md-1 mb-2">
            <button type="submit" name="action" value="export" class="btn btn-info w-100">
                <i class="fa fa-download"></i>
            </button>
        </div>

    </form>
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <hr style="padding-bottom: 20px">
                    <form style="padding-left: 25px;" id="bulkActionForm">
                        @csrf
                        <div class="row mb-2">
                            <div class="col-md-3">
                                <select name="action" class="form-control" required>
                                    <option value="">-- Select Action --</option>
                                    <option value="delete">Delete Selected</option>
                                    <option value="resolve">Resolve Selected</option>

                                </select>
                            </div>

                            <div class="col-md-1 d-flex align-items-center">
                                <button onclick="return confirm('Are you sure you want to perform this action')" type="submit" class="btn btn-primary me-2">
                                    Apply
                                </button>
                                <div id="bulkSpinner" class="spinner-border text-primary" style="display:none; width:1.2rem; height:1.2rem;" role="status">
                                    <span class="visually-hidden"></span>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="card-content">
                        <div class="card-body card-dashboard" style="padding:0 25px;">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="selectAll"></th>
                                            <th>#</th>
                                            <th>Transaction</th>
                                            <th>User</th>
                                            <th>Payment</th>
                                            <th>Date & Plan</th>
                                            <th>Response</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <!-- Checkbox -->
                                            <td>
                                                <input type="checkbox" name="transactions[]" value="{{ $transaction->id }}" class="rowCheckbox">
                                            </td>

                                            <!-- SN -->
                                            <td>{{ $loop->iteration }}</td>

                                            <!-- Transaction Info -->
                                            <td>
                                                <strong>ID:</strong> {{ $transaction->transid }} <br>
                                                @if(!empty($transaction->provider_reference))
                                                    <small><strong>Prov Ref:</strong> {{ $transaction->provider_reference }}</small><br>
                                                @endif
                                                @if($transaction->resolved_at)
                                                <span style="color: red;">
                                                    <span class="badge badge-success">Resolved</span>
                                                    by: {{ $transaction->resolvedBy->fullname ?? 'CRON' }} <br>
                                                    at: {{ $transaction->resolved_at ? $transaction->resolved_at->format('Y-m-d h:i a') : '' }}<br>
                                                </span>
                                                @endif
                                                @if($transaction->fix_status)
                                                <strong>Fix Status: </strong>{{$transaction->fix_status}} <br>
                                                @endif
                                                {{-- {{dd($transaction)}} --}}
                                                <span class="badge badge-info">{{ $transaction->status }}</span>
                                                <span class="badge badge-secondary">{{ $transaction->registration_status }}</span>
                                            </td>

                                            <!-- User Info -->
                                            <td>
                                                <strong>Name: </strong>{{ $transaction->name }} <br>
                                                <strong>Email: </strong>{{ $transaction->email }}
                                            </td>

                                            <!-- Payment Info -->
                                            <td>
                                                <strong>Total:</strong> ₦{{ number_format($transaction->total_amount ?? $transaction->amount_paid) }} <br>
                                                <small>Amount: ₦{{ number_format($transaction->amount_paid) }}</small><br>
                                                <small>Charge: ₦{{ number_format($transaction->provider_charge) }}</small><br>
                                                <small>Provider: {{ $transaction?->paymentprovider?->name }}</small>
                                            </td>

                                            <!-- Date & Plan -->
                                            <td>
                                                {{ $transaction->created_at->format('Y-m-d h:i a') }} <br>
                                                <strong>Plan:</strong>
                                                <a href="{{ route('conference_plans.index', $edition->id) }}">
                                                    {{ $transaction->conferenceplan->title }}
                                                </a><br>
                                                <span>
                                                    <strong>Reg. Type:</strong><span class="badge" style="background-color:{{$transaction->registration_user_type == 'moderator' ? 'teal' : '#f700ff'}}"> {{ ucfirst($transaction->registration_user_type) }} </span>
                                                </span>
                                                @if($transaction->registration_user_type == 'moderator') <br>
                                                    <span>
                                                        <strong style="color:blue">Slots:</strong> {{ $transaction->slot }} <br>
                                                        <strong style="color:blue">Slots Available:</strong> {{ $transaction->slot - $transaction->slot_filled }}
                                                    </span>
                                                @endif
                                                <br>
                                                <small>Location: {{ $transaction->location ?? 'Online' }}</small>
                                            </td>

                                            <!-- API Response -->
                                            <td>
                                                @if(!empty($transaction->api_response))
                                                    <button
                                                        class="btn btn-success btn-sm"
                                                        data-toggle="modal"
                                                        data-target="#responseModal{{ $transaction->id }}">
                                                        View
                                                    </button>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>

                                            <!-- Actions -->
                                            <td>
                                                @if(auth()->user()->conference_role == 'superadmin')
                                                @if($transaction->status != 'Complete')
                                                    <button
                                                        style="margin-bottom: 5px;"
                                                        class="btn btn-info btn-sm"
                                                        data-toggle="modal"
                                                        data-target="#editModal{{ $transaction->id }}">
                                                        Update
                                                    </button>
                                                    <a style="margin-bottom: 5px;" onclick="return confirm('Are you sure you want to resolve this transaction')" href="{{ route('conference.transactions.bulkAction', ['transactions'=>[$transaction->id],'edition'=>$edition->id, 'action'=>'resolve']) }}"
                                                    class="btn btn-success btn-sm">
                                                        Resolve
                                                    </a> <br>
                                                    @endif
                                                    <a onclick="return confirm('Are you sure you want to delete this transaction?')" href="{{ route('conference.transactions.bulkAction', ['transactions'=>[$transaction->id],'edition'=>$edition->id, 'action' => 'delete']) }}"
                                                    class="btn btn-danger btn-sm">
                                                        Delete
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>

                                        {{-- Modal --}}
                                        @if(!empty($transaction->api_response))
                                        <div class="modal fade" id="responseModal{{ $transaction->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                                <div class="modal-content">

                                                    <div class="modal-header">
                                                        <h5 class="modal-title">API Response ({{ $transaction->transid }})</h5>
                                                        <button type="button" class="close" data-dismiss="modal">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>

                                                    <div class="modal-body">
                                                        <pre class="bg-light p-3 rounded" style="max-height:400px; overflow:auto;">
                                                            {!! json_encode($transaction->api_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) !!}
                                                        </pre>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                            Close
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @endforeach
                                    </tbody>
                                </table>
                                {{-- Edit Transaction Modals --}}
                                @foreach($transactions as $transaction)
                                <div class="modal fade" id="editModal{{ $transaction->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                        <div class="modal-content">

                                            <form method="POST" action="{{ route('conference.transactions.update', $transaction->id) }}">
                                                @csrf

                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        Update Transaction - {{ $transaction->transid }}
                                                    </h5>
                                                    <button type="button" class="close" data-dismiss="modal">
                                                        <span>&times;</span>
                                                    </button>
                                                </div>

                                                <div class="modal-body">

                                                    <div class="row">

                                                        <div class="col-md-6 mb-2">
                                                            <label>Name</label>
                                                            <input type="text" name="name"
                                                                value="{{ $transaction->name }}"
                                                                class="form-control">
                                                        </div>

                                                        <div class="col-md-6 mb-2">
                                                            <label>Email</label>
                                                            <input type="email" name="email"
                                                                value="{{ $transaction->email }}"
                                                                class="form-control">
                                                        </div>

                                                        <div class="col-md-6 mb-2">
                                                            <label>Phone</label>
                                                            <input type="text" name="phone"
                                                                value="{{ $transaction->phone }}"
                                                                class="form-control">
                                                        </div>

                                                        <div class="col-md-6 mb-2">
                                                            <label>Transaction ID</label>
                                                            <input type="text" name="transid"
                                                                value="{{ $transaction->transid }}"
                                                                class="form-control">
                                                        </div>

                                                        <div class="col-md-6 mb-2">
                                                            <label>Amount Paid</label>
                                                            <input type="number" step="0.01"
                                                                name="amount_paid"
                                                                value="{{ $transaction->amount_paid }}"
                                                                class="form-control">
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label>Total Amount</label>
                                                            <input type="number" step="0.01"
                                                                name="total_amount"
                                                                value="{{ $transaction->total_amount }}"
                                                                class="form-control">
                                                        </div>

                                                        <div class="col-md-6 mb-2">
                                                            <label>Provider Charge</label>
                                                            <input type="number" step="0.01"
                                                                name="provider_charge"
                                                                value="{{ $transaction->provider_charge }}"
                                                                class="form-control">
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label>Slot</label>
                                                            <input type="number"
                                                                name="slot"
                                                                value="{{ $transaction->slot }}"
                                                                class="form-control">
                                                        </div>
                                                        <div class="col-md-6 mb-2">
                                                            <label>Registration User Type</label>
                                                            <select name="registration_user_type" class="form-control">
                                                                <option value="">-- Select --</option>
                                                                <option value="participant" {{ $transaction->registration_user_type == 'participant' ? 'selected' : '' }}>Participant</option>
                                                                <option value="moderator" {{ $transaction->registration_user_type == 'moderator' ? 'selected' : '' }}>Moderator</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button class="btn btn-primary">
                                                        Update Transaction
                                                    </button>

                                                    <button type="button"
                                                            class="btn btn-secondary"
                                                            data-dismiss="modal">
                                                        Cancel
                                                    </button>
                                                </div>

                                            </form>

                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            {{ $transactions->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<script>

$(document).ready(function() {

    $('#bulkActionForm').on('submit', function(e) {
        e.preventDefault(); // prevent normal form submit

        let selectedIds = $('.rowCheckbox:checked').map(function() {
            return $(this).val();
        }).get();

        let action = $(this).find('select[name="action"]').val();
        let token = $('input[name="_token"]').val();

        if(selectedIds.length === 0){
            alert('Please select at least one transaction.');
            return;
        }

        if(!action){
            alert('Please select an action.');
            return;
        }

        // Show spinner
        $('#bulkSpinner').show();

        $.ajax({
            url: "{{ route('conference.transactions.bulkAction', $edition->id) }}",
            type: 'POST',
            data: {
                _token: token,
                action: action,
                transactions: selectedIds
            },
            success: function(response){
                alert(response.message || 'Bulk action completed successfully!');

                // Deselect all checkboxes
                $('.rowCheckbox, #selectAll').prop('checked', false);

                // Hide spinner
                $('#bulkSpinner').hide();

                // Optional: reload page or remove deleted rows dynamically
                location.reload();
            },
            error: function(xhr){
                alert(xhr.responseJSON?.message || 'An error occurred.');
                $('#bulkSpinner').hide();
            }
        });
    });

    // Select all checkbox
    $('#selectAll').on('click', function(){
        $('.rowCheckbox').prop('checked', this.checked);
    });

});


</script>

@endsection
