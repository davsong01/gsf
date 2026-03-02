@extends('layouts.dashboard')
@section('title', 'Download Conference Materials')
@section('item')
<li class="breadcrumb-item"><a href="{{ route('conferencemanagement.show', ['conferencemanagement'=>$payment, 'edition'=>$edition->id]) }}">{{ $edition->conference_theme }}</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Conference Materials</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All {{ $edition->conference_theme }}'s' Conference materials</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($materials as $material)
                                        <tr>
                                            <td>{{ $iteration->loop }}</td>

                                            <td>{{ $material->name }}</td>
                                            <td>
                                                 <a class="actions" data-toggle="tooltip" title="Download Material" href="{{ route('materials.show', $material->id) }}"> <i class="bx bxs-download actions"></i>Download
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
