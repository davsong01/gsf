@extends('frontend.site.layout')

@section('title', '429 · Too many requests | GOFAMINT Students’ Fellowship')
@section('description', 'Please wait briefly before trying again.')
@section('mainClass', 'gsf-error-main')
@section('hideDirectoryNetwork', '1')

@section('content')
@include('errors.page', [
    'code' => '429',
    'eyebrow' => 'A short pause',
    'title' => 'You’re moving a little too fast.',
    'message' => 'We received too many requests in a short time. Wait a moment, then try again.',
    'symbol' => '◷',
    'artCaption' => 'Please try again soon',
    'accent' => '#a97827',
])
@endsection
