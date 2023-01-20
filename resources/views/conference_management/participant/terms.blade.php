@extends('layouts.dashboard')
@section('title', 'Terms and Conditions')

@section('active')
<li class="breadcrumb-item">Terms and Conditions</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Basic Inputs start -->
<section id="basic-input">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Carefully read our terms and conditions</h4>
                    
                </div>
                <div class="card-content">
                    <div class="card-body">
                        @include('includes.tac')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
