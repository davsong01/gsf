@extends('frontend.site.layout')

@section('title', '401 · Sign in required | GOFAMINT Students’ Fellowship')
@section('description', 'Sign in with an eligible account to continue.')
@section('mainClass', 'gsf-error-main')
@section('hideDirectoryNetwork', '1')

@section('content')
@include('errors.page', [
    'code' => '401',
    'eyebrow' => 'Sign-in required',
    'title' => 'Please sign in to continue.',
    'message' => 'This page is available to signed-in members and stakeholders. Sign in with the account that should have access.',
    'symbol' => '⌑',
    'artCaption' => 'Your account is one step away',
    'showLogin' => true,
])
@endsection
