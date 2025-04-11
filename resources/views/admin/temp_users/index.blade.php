@section('extra_styles')
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
.t_header {
  font-weight: bold;
}

.action-button{
   margin: 5px;
}

.modal-dialog {
    max-width: 80%; /* Optional: adjust width of the modal */
}

.modal-body {
    max-height: 400px;  /* Set a maximum height for the modal body */
    overflow-y: auto;   /* Enable vertical scrolling */
}
</style>
@endsection
@extends('layouts.conference')
@section('title', 'Transactions')
@section('active')
<li class="breadcrumb-item">Attempted/Not Completed Transactions</li>
@endsection
@section('content2')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Attempted Transactions</h4> <br>
                        <button id="verify" class="btn btn-primary btn-sm" onclick="if(confirm('Are you sure?')) verifyall();" class="btn btn-warning btn-xs hidden-print" style="margin: 0"><span id="requery-text">Re-query selected payments &nbsp<span id="before-fetch"></span></button>

                        <button id="verify-all-real" class="btn btn-info btn-sm" onclick="if(confirm('Are you sure?')) verifyAllReal();" class="btn btn-warning btn-xs hidden-print" style="margin: 0">Requery All Payments</button>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            @if(count($participants) > 0)
                                <div class="table-responsive">
                                    <table class="table zero-configuration">
                                        <thead>
                                            <tr>
                                                <th>S/N</th>
                                                <th>Details</th>
                                                <th>Status</th>
                                                <th>Payment Response</th>
                                                <th>Date</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php $allparticipants = $participants->pluck('transid'); ?>
                                            {{-- {{dd($participants->pluck('transid')->toArray())}} --}}
                                
                                            @foreach($participants as $participant)
                                            <tr id="tr-{{$participant->transid}}" style="font-weight: lighter;">
                                                <td>{{ $count++ }} 
                                                    <span style="float: right;">
                                                        <input class="form-check-input" name="reference" type="checkbox" value="{{ $participant->transid }}" id="check-{{ $participant->transid }}">
                                                    </span>
                                                </td>
                                            
                                                <td>
                                                    <div style="width:350px !important">
                                                        Name: {{ $participant->name }} <br>
                                                        Email: {{ $participant->email }} <br>
                                                        Phone: {{ $participant->phone }} <br>
                                                        Campus: {{ isset($participant->campus) ? $participant->campus->name : 'N/A'}} <br>
                                                        Trans ID: {{ $participant->transid }} <br>
                                                        Payment Type: @if($participant->type == 1)Individual
                                                        @elseif($participant->type == 2)Fellowship
                                                        @elseif($participant->type == 3)Alumni
                                                        @elseif($participant->type == 4)Nec
                                                        @elseif($participant->type == 5)Donation
                                                        @endif <br>
                                                        <span style="color:blue">Payment Location: <strong>{{ $participant->location ?? 'N/A' }}</strong></span> <br>
                                                        <span id="single-{{$participant->transid}}"><span>
                                                    </div>
                                                </td>
                                                <td>{{ $participant->status }}</td>

                                                <td>
                                                    @if(isset($participant->gateway_response) && !empty($participant->gateway_response ))
                                                        <pre class="response"><span >{!! $participant->gateway_response !!}</span></pre>
                                                    @endif
                                                </td>
                                                <td>{{ $participant->created_at->format('Y-m-d') }}</td>

                                                <td class="text-center align-middle">
                                                    <div class="d-flex flex-column align-items-stretch" style="min-width: 180px;">

                                                        {{-- Change Reference ID --}}
                                                        <button type="button" class="btn btn-primary btn-sm action-button text-nowrap" data-toggle="modal" data-target="#myModal{{ $participant->id }}">
                                                            <i class="fa fa-pencil me-1"></i> Change Reference
                                                        </button>

                                                        {{-- Requery Payment --}}
                                                        <a href="{{ route('tempusers.requery', ['id' => $participant->id, 'reference' => $participant->transid]) }}"
                                                        class="btn btn-secondary btn-sm action-button text-nowrap">
                                                            <i class="fa fa-refresh me-1"></i> Requery Payment
                                                        </a>

                                                        {{-- Confirm Transfer (Superadmin only) --}}
                                                        @if (auth()->user()->role == 1 && auth()->user()->conference_role == 'superadmin')
                                                            <a href="{{ route('tempusers.transfer.confirm', $participant->id) }}"
                                                            class="btn btn-warning btn-sm action-button text-dark text-nowrap"
                                                            onclick="return confirm('Are you really sure?');">
                                                                <i class="fa fa-exchange me-1"></i> Confirm Transfer
                                                            </a>
                                                        @endif

                                                        {{-- Confirm On Site Payment --}}
                                                        @if(auth()->user()->conference_role == 'superadmin')
                                                        <a href="{{ route('tempusers.onsite.confirm', $participant->id) }}"
                                                        class="btn btn-success btn-sm action-button text-nowrap"
                                                        onclick="return confirm('Are you really sure to confirm On Site payment?');">
                                                            <i class="fa fa-money me-1"></i> Confirm On-Site
                                                        </a>
                                                        @endif

                                                        {{-- Fetch Transactions --}}
                                                        <button type="button" class="btn btn-info btn-sm action-button text-nowrap fetch-transactions"
                                                                data-email="{{ $participant->email }}">
                                                            <i class="fa fa-history me-1"></i> Fetch Transactions
                                                        </button>

                                                        {{-- Delete User --}}
                                                        @if(auth()->user()->conference_role == 'superadmin')
                                                        <a href="{{ route('tempusers.destroy', $participant->id) }}"
                                                        class="btn btn-danger btn-sm text-nowrap"
                                                        onclick="return confirm('Are you really sure?');">
                                                            <i class="fa fa-trash me-1"></i> Delete Entry
                                                        </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            <div class="modal" id="myModal{{ $participant->id }}">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">

                                                        <!-- Modal Header -->
                                                        <div class="modal-header">
                                                            <h4 class="modal-title">Edit Transaction from {{ $participant->email }}</h4>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                        </div>

                                                        <!-- Modal body -->
                                                        <div class="modal-body">
                                                            <form action="{{ route('tempusers.update', $participant->id) }}" method="POST" enctype="multipart/form-data">
                                                                @csrf
                                                                @method('PATCH')
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <fieldset class="form-group">
                                                                            <label for="conference_id">Transaction ID</label>
                                                                            <input type="text" class="form-control" name="transid" id="transid" value="{{ $participant->transid }}" required>
                                                                        </fieldset>
                                                                    </div>
                                                                    <div class="col-md-12 col-sm-12">
                                                                        <button class="btn btn-primary" style="width:100%" type="submit">Update</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>

                                                        <!-- Modal footer -->
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </tbody>
                                    </table>
                                   {{-- <td>
                                        <a href="${verifyUrl}" class="btn btn-sm btn-success set-reference" data-ref="${tx.reference}">
                                            Set as Reference ID
                                        </a>
                                    </td> --}}
                                    <!-- Transaction Modal -->
                                    <div class="modal" id="transactionModal" aria-labelledby="transactionModalLabel">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Transaction Details</h5>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body">
                                                    <table class="table table-striped table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Payment Details</th>
                                                                <th>Gateway Response</th>
                                                                <th>Conference</th>
                                                                <th>Email</th>
                                                                <th>Created At</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="transactionTableBody">
                                                            <!-- Filled via JS -->
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- The Modal -->

    </section>
</div>
@if(count($participants) > 0)
<script>
    
    $(document).on('click', '.fetch-transactions', function () {
        const $btn = $(this);
        const email = $(this).data('email');
        const editionId = "{{ $edition->id }}";

        const $icon = $btn.find('i');
        const originalIconClass = $icon.attr('class');

        $icon.removeClass().addClass('fa fa-spinner fa-spin');

        $.ajax({
            url: "{{ route('admin.transactions.fetch') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                email: email,
                edition_id: editionId
            },
            success: function (response) {
                let html = '';
                if (response.success) {
                    response.transactions.forEach(tx => {
                        const verifyUrl = "{{ route('set-and-verify-reference', ['reference' => ':reference', 'temp_id' => ':temp_id']) }}"
                        .replace(':reference', tx.reference)
                        .replace(':temp_id', '{{ $participant->id }}');

                        html += `
                            <tr>
                                <td>
                                    <span class="t_header">Reference: </span>${tx.reference} <br>
                                    <span class="t_header">Amount: </span>${(tx.amount / 100).toFixed(2)} NGN <br>
                                    <span class="t_header">Status: </span>${tx.status} <br>
                                    <span class="t_header">Channel: </span>${tx.channel} <br>
                                </td>
                                <td>${tx.gateway_response}</td>
                                <td>${tx.conference_edition}</td>
                                <td>${tx.customer.email}</td>
                                <td>${new Date(tx.created_at).toLocaleString()}</td>
                                
                            </tr>
                        `;
                    });

                    $('#transactionTableBody').html(html);
                    $('#transactionModal').modal('show');
                } else {
                    alert('Error: ' + response.message);
                }
            },

            error: function (xhr) {
                alert('An error occurred fetching transactions.');
            },
            complete: function () {
                // Restore the original icon
                $icon.removeClass().addClass(originalIconClass);
            }
        });
    });

    function verifyall(){
        let obj = [];
        $("input:checkbox[name=reference]:checked").each(function(){
            obj.push($(this).val());
        });
        if(obj.length < 1){
            return alert('You must select one or more payments to verify');
        }
        process(obj,'single');
    }

    function verifyAllReal(){
        let obj = [];
        let all = <?php echo json_encode($allparticipants); ?>;
        
        for(var i =0; i<=all.length;i++){
            obj.push(
                all[i]
            );
        }

        process(obj,'all');
    }

    function process(obj, type){
        let baseUrl = '/verify-multiple-payments';

        $.ajax({
			url : baseUrl,
			type : 'POST',
            data: {
                "_token": "{{ csrf_token() }}",
                "obj": obj,
            },
			beforeSend: function (){
				$('#before-fetch').html("<i class='fa fa-spinner fa-spin' style='font-size: x-large; !important'></i>");
                 obj.forEach(function(item){
                    $("#single-"+item).html("<i class='fa fa-spinner fa-spin'></i>");
                });
			},
            
			success:function (data, textStatus, jQxhr) {
                data.forEach(function(item){
                    if(type == 'single'){
                        obj.forEach(function(item){
                            $("#check-"+item).prop("checked", false);
                        });
                    }
                    if(item.status == 'success'){
                        $('#tr-'+item.reference).remove();
                    }

                    if(item.status == 'Failed'){
                        $('#single-'+item.reference).html("<span style='color:red'>Payment Status: " +item.status+"</span");
                    }else{
                        $('#single-'+item.reference).html("<span style='color:orange'>Payment Status: " +item.status+"</span");
                    }
                });
                $('#requery-text').html("Requery Complete");
                $('#before-fetch').html("<i class='fa fa-check' style='font-size: x-large; !important'></i>");
               
			},

		});
    }
</script>

@endif
@endsection
