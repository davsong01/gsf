@extends('frontend.site.layout')

@section('title', '403 · Access restricted | GOFAMINT Students’ Fellowship')
@section('description', 'Your account does not have permission to view this page.')
@section('mainClass', 'gsf-error-main')
@section('hideDirectoryNetwork', '1')

@section('content')
@include('errors.page', [
    'code' => '403',
    'eyebrow' => 'Access restricted',
    'title' => 'You don’t have access to this page.',
    'message' => 'Your current account does not have permission to view this area. If you think you should have access, contact your administrator.',
    'symbol' => '⌑',
    'artCaption' => 'Your account is protected',
    'accent' => '#a97827',
])
@endsection
