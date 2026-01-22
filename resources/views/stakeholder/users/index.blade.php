@extends('layouts.stakeholderdashboard')
@section('title', 'All Members')
@section('active')
<li class="breadcrumb-item">{{$chapter->name}} Members</li>
@endsection

@section('content')
<x-users.index
    :routes="$routes"
    :is-admin="$isAdmin"
/>
@endsection


