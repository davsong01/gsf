@extends('layouts.stakeholderdashboard')
@section('title', 'Reports')
@section('active')
<li class="breadcrumb-item">Reports</li>
@endsection

@section('content')
@include('stakeholder.index_partial')
@endsection
