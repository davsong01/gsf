@extends('layouts.app')
@section('title', 'Terms and Conditions')
<!-- BEGIN: Body-->
@section('content')
<div class="container">
    
        <div class="card-body">
            <div class="row m-0">
                <!-- register section left -->
                <div class="col-md-12 col-12 px-0">
                    <div class="card disable-rounded-right mb-0 p-2 h-100 d-flex justify-content-center">
                        <div class="card-header pb-1">
                            <div class="card-title">
                                <div style="text-align: center;"><img style="width: 20%;" src="{{ asset('app-assets/images/logo/logo.png') }}"></div>
                            </div>
                        </div>
                        <div class="text-center">
                            <h4>TERMS AND CONDITIONS</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                @include('includes.tac')
                                <hr>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- image section right -->
                
            </div>
        </div>
    
</div>
@endsection
<!-- END: Body-->

