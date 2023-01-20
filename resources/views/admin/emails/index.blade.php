@extends('layouts.dashboard')
@section('title', 'Emails')
@section('active')
<li class="breadcrumb-item">Emails</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All sent Emails</h4>
                        <a href="{{ route('useremails.create') }}" class="btn btn-primary mt-1">Send new Email</a>
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Date</th>
                                            <th>Recipient</th>
                                            <th>Subject</th>
                                            <th>Mesage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($emails as $email)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>{{ $email->created_at->format('d/m/Y:h-s-i') }}</td>
                                            <td>{{ $email->recipient }}</td>
                                            <td>{{ $email->subject }}</td>
                                            <td>{!! $email->content !!}</td>
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