@extends('layouts.stakeholderdashboard')
@section('title', 'Add/Edit Report')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route($isAdmin ? 'stakeholderreports.index': 'stakeholders.reports.index') }}">All Reports</a></li>
@endsection

@section('active')
<li class="breadcrumb-item">{{ isset($report) ? 'Edit Report' : 'Add New Report' }}</li>
@endsection

@include('stakeholder.create_partial')
@endsection
