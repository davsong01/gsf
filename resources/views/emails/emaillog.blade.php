@extends('layouts.dashboard')
@section('extra_styles')
<style>
    .btn i{
        top: 0px !important;
    }
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
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
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
                                                <td>{{ $count ++}}</td>
                                                <td>{{ $email->type }} <br>
                                                <small>
                                                    @if($email->status == 0)
                                                    <span style="color:red">Pending...</span><br>
                                                    @else
                                                    <span style="color:green">Sent at {{ $email->sent_at }}</span><br>
                                                    @endif
                                                </small>
                                                </td>
                                                <td>{{ $email->subject }}</td>
                                                <td>{{ $email->recipient }}</td>
                                                <td>{!! mb_strimwidth($email->content, 0, 100, " ...") !!} <br>
                                                        <a class="btn btn-info btn-sm" data-target="#emailModal{{ $email->id }}" data-toggle="modal" id="{{ $email->id }}"> <i class="fa fa-eye"></i> </a>
                                                </td>
                                                <td><span style="color:red">{{ $email->errors }}</span></td>
                                                <td style="padding-left: 5px;padding-right: 5px;">
                                                    <a class="actions" data-toggle="tooltip" title="Resend" href="{{ route('criticalEmail.show', $email->id)}}" data-original-title="View/Update stakeholder details"> <i class="fa fa-envelope actions"></i>
                                                    </a>
                                                
                                                    <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="" href="{{ route('CriticalEmail.delete', $email->id)}}" data-original-title="Delete email"> <i class="fa fa-trash actions"></i></a>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="emailModal{{ $email->id }}" role="dialog">
                                                <div class="modal-dialog modal-md  modal-dialog-scrollable">
                                                
                                                <!-- Modal content-->
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                    <h4 class="modal-title">Email Preview</h4>
                                                    </div>
                                                    <div class="modal-body">
                                                    {!! $email->content !!}
                                                    </div>
                                                    <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                                
                                                </div>
                                            </div>
                                           
                                            @endforeach
                                        @endif
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