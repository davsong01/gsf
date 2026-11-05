@extends('layouts.stakeholderdashboard')
@section('title', isset($report) ? 'Edit Monthly Report' : 'New Monthly Report')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route($isAdmin ? 'stakeholderreports.index': 'stakeholders.reports.index') }}">All Reports</a></li>
@endsection

@section('active')
<li class="breadcrumb-item">{{ isset($report) ? 'Edit Monthly Report' : 'New Monthly Report' }}</li>
@endsection

@include('stakeholder.create_partial')
@endsection
