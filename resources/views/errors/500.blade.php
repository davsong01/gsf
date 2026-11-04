@extends('frontend.site.layout')

@section('title', '500 · Server error | GOFAMINT Students’ Fellowship')
@section('description', 'The page could not be completed right now. Please try again shortly.')
@section('mainClass', 'gsf-error-main')
@section('hideDirectoryNetwork', '1')

@section('content')
@include('errors.page', [
    'code' => '500',
    'eyebrow' => 'Something went wrong',
    'title' => 'We hit an unexpected problem.',
    'message' => 'The page could not be completed right now. Please try again in a moment.',
    'symbol' => '↻',
    'artCaption' => 'Our team can take it from here',
    'accent' => '#a94b56',
])
@endsection
