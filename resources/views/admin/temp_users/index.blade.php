@section('extra_styles')
<style>
.response{
    font-size: 11px;
    border: 1px solid black;
    max-width: 500px;
    display: inline-block;
    padding: 6px;
    white-space: pre-line;
    overflow: scroll;
    text-overflow: ellipsis;
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
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Details</th>
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
                                                <span id="single-{{$participant->transid}}"><span>
                                            </td>
                                            <td>
                                                @if(isset($participant->gateway_response) && !empty($participant->gateway_response ))
                                                    <pre class="response"><span >{!! $participant->gateway_response !!}</span></pre>
                                                @endif
                                            </td>
                                            <td>{{ $participant->created_at->format('Y-m-d') }}</td>
                                                                                        
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                                <a class="actions" data-toggle="tooltip" title="Requery Payment" href="{{ route('tempusers.requery', ['id'=>$participant->id, 'reference'=>$participant->transid]) }}"> <i style="padding: 5px;" class="fa fa-refresh"></i></a>
                                                <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete User" href="{{ route('tempusers.destroy', $participant->id) }}"> <i style="padding: 5px;" class="fa fa-trash"></i>
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
</div>
<script>
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
                if(type == 'single'){
                    obj.forEach(function(item){
                        $("#check-"+item).prop("checked", false);
                    });
                }
			},
            
			success:function (data, textStatus, jQxhr) {
                data.forEach(function(item){
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
@endsection
