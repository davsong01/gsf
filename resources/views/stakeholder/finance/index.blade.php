@extends('layouts.stakeholderdashboard')
@section('title', 'Financial Reports')
@section('active')
<li class="breadcrumb-item">Financial Reports</li>
@endsection

@section('content')
@include('stakeholder.finance.index_partial')
@endsection
