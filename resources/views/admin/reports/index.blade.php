@extends('layouts.dashboard')
@section('title', 'Edit Payment Provider')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('stakeholderreports.index') }}">Monthly Reports</a></li>
@endsection

@section('content')
@include('stakeholder.index_partial')
@endsection
