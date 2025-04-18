@extends('layouts.conference')
@section('title', 'Utility Tools')
@section('active')
<li class="breadcrumb-item">Utility Tools</li>
@endsection
@section('content2')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <a href="{{ route('edition.fix.attempted',['edition'=>$edition->id]) }}" class="btn btn-primary mt-1">Fix Attempted Registration ({{$count}})</a>        
                            {{-- <a href="{{ route('hostels.repair.allocation',['edition'=>$edition->id]) }}" onclick="return confirm('Are you sure?')" class="btn btn-info mt-1">Repair Hostel Allocation</a>    --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/ Zero configuration table -->         
</div>

@endsection