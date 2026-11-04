@extends('frontend.site.layout')

@section('title', '419 · Session expired | GOFAMINT Students’ Fellowship')
@section('description', 'Your session has expired. Return to the website and try again.')
@section('mainClass', 'gsf-error-main')
@section('hideDirectoryNetwork', '1')

@section('content')
@include('errors.page', [
    'code' => '419',
    'eyebrow' => 'Session expired',
    'title' => 'Your session has expired.',
    'message' => 'For your security, this session is no longer active. Return to the site and try your action again.',
    'symbol' => '◷',
    'artCaption' => 'A fresh start is ready',
    'accent' => '#a97827',
])
@endsection
