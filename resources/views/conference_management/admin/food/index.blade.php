@extends('layouts.conference')
@section('title', 'Service Points')
@section('active')
<li class="breadcrumb-item">Service Points</li>
@endsection
@section('content2')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">All Service Points for {{ $edition->conference_theme }}</h4>
                        <a href="{{ route('foods.create',['edition'=>$edition->id]) }}" class="btn btn-primary mt-1">Add new Food Stand</a>
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Name</th>
                                            <th>Level</th>
                                            <th>Fields</th>
                                            <th>Chapters</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($foods as $food)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td>
                                                {{ $food->name }} <br>
                                               <strong>Capacity: </strong>{{$food->capacity}} <br>
                                               <strong>Allocation: </strong>{{$food->allocation}} <br>
                                               <strong>Allotted: </strong>{{$food->payments->count()}}
                                            </td>
                                            <td>{{$food->level}}</td>
                                            <td>
                                                @if($food->fields)
                                                @foreach ($food->fields as $field)
                                                   <small> {{" - " . $field->name . "\n"}} <br></small>
                                                @endforeach
                                                <br>
                                                @endif

                                            </td>
                                            <td>
                                                @if($food->chapters)
                                                @foreach ($food->chapters as $chapter)
                                                   <small> {{" - " . $chapter->name . "\n"}}<br></small>
                                                @endforeach
                                                <br>
                                                @endif
                                            </td>
                                            
                                            
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                            <a class="actions" data-toggle="tooltip" title="View/Update food details" href="{{ route('foods.edit', ['food'=>$food->id,'edition'=>$edition->id]) }}"> <i class="bx bxs-edit actions"></i>
                                            </a>
                                           
                                            <a class="actions" data-toggle="tooltip" onclick="return confirm('Are you really sure?');" title="Delete food" href="{{ route('foods.delete', ['id'=>$food->id,'edition'=>$edition->id]) }}"> <i class="fa fa-trash"></i></
                                            </a>
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