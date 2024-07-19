@extends('layouts.conference')
@section('title', 'Hostel')
@section('active')
<li class="breadcrumb-item">Hostels</li>
@endsection
@section('content2')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Hostels for {{ $edition->conference_theme }}</h4>
                        <a href="{{ route('hostels.create',['edition'=>$edition->id]) }}" class="btn btn-primary mt-1">Add new Hostel</a>                        
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Gender</th>
                                            <th>Level</th>
                                            <th>Fields</th>
                                            <th>Chapters</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($hostels as $hostel)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                           <td>
                                                {{ $hostel->name }} <br>
                                               <strong>Capacity: </strong>{{$hostel->capacity}} <br>
                                               <strong>Allocation: </strong>{{$hostel->allocation}} <br>
                                               <strong>Allotted: </strong>{{$hostel->payments->count()}}
                                            </td>
                                            <td>{{ $hostel->type }}</td>
                                            <td>{{ $hostel->level }}</td>
                                            <td>
                                                @if($hostel->fields)
                                                @foreach ($hostel->fields as $field)
                                                   <small> {{" - " . $field->name . "\n"}} <br></small>
                                                @endforeach
                                                <br>
                                                @endif
                                            </td>
                                            <td>
                                                @if($hostel->chapters)
                                                @foreach ($hostel->chapters as $chapter)
                                                   <small> {{" - " . $chapter->name . "\n"}}<br></small>
                                                @endforeach
                                                <br>
                                                @endif
                                            </td>
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                                <a class="actions" data-toggle="tooltip" title="View/Update hostel details" href="{{ route('hostels.edit', ['hostel'=>$hostel->id, 'edition'=>$edition->id, ]) }}"> <i class="bx bxs-edit actions"></i></a>
                                                <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete Hostel" href="{{ route('hostels.delete', ['id'=>$hostel->id,'edition'=>$edition->id]) }}"> <i style="padding: 5px;" class="fa fa-trash"></i></a>
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