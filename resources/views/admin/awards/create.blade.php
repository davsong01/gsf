@extends('layouts.dashboard')
@section('title', 'Edit Report')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('stakeholderreports.index') }}">Monthly Reports</a></li>
@endsection

@include('stakeholder.create_partial')
@endsection
