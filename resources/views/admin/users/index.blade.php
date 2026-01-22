@extends('layouts.dashboard')
@section('title', 'Community Users')
@section('active')
<li class="breadcrumb-item">All Members</li>
@endsection
@section('content')
<x-users.index
    :routes="$routes"
    :is-admin="$isAdmin"
/>
@endsection
