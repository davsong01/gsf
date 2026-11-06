@extends('layouts.stakeholderdashboard')
@section('title', 'Review Extracted Records')
@section('item')<li class="breadcrumb-item"><a href="{{ $memberIndexUrl }}">{{ $recordType === 'alumni' ? 'Chapter Alumni' : 'Chapter Members' }}</a></li>@endsection
@section('active')<li class="breadcrumb-item">Review extracted records</li>@endsection
@section('extra_styles')<link rel="stylesheet" href="{{ versionedAsset('assets/css/admin-user-extraction.css') }}">@endsection
@section('content')@include('record-book.extracted-records-content')@endsection
