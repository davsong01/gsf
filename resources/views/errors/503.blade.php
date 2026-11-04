@extends('frontend.site.layout')

@section('title', '503 · Temporarily unavailable | GOFAMINT Students’ Fellowship')
@section('description', 'The service is temporarily unavailable. Please try again shortly.')
@section('mainClass', 'gsf-error-main')
@section('hideDirectoryNetwork', '1')

@section('content')
@include('errors.page', [
    'code' => '503',
    'eyebrow' => 'Temporarily unavailable',
    'title' => 'We’ll be back shortly.',
    'message' => 'The service is temporarily unavailable, usually while maintenance is in progress. Please try again soon.',
    'symbol' => '◷',
    'artCaption' => 'Thank you for your patience',
    'accent' => '#a97827',
])
@endsection
