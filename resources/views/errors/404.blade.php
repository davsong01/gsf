@extends('frontend.site.layout')

@section('title', '404 · Page not found | GOFAMINT Students’ Fellowship')
@section('description', 'The page could not be found. Return to the GOFAMINT Students’ Fellowship website.')
@section('mainClass', 'gsf-error-main')
@section('hideDirectoryNetwork', '1')

@section('content')
@include('errors.page', [
    'code' => '404',
    'eyebrow' => 'Page not found',
    'title' => 'We couldn’t find that page.',
    'message' => 'The address may be incorrect, or the page may have moved. Let’s get you back to the GSF website.',
    'symbol' => '⌕',
    'artCaption' => 'This page has wandered off',
])
@endsection
