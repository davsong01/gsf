@extends('layouts.dashboard')
@section('title', 'GSF events')
@section('active')
<li class="breadcrumb-item">GSF Events</li>
@endsection
@section('content')
<div class="content-body">
    <!-- Zero configuration table -->
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">GSF Events</h4>
                        <div class="">
                            <a href="{{ route('events.create') }}" class="btn btn-primary mt-1">Add new</a>
                        </div>
                        @include('includes.alerts')
                    </div>
                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Image Preview</th>
                                            <th>Title</th>
                                            <th>Date</th>
                                            <th>Venue</th>
                                            <th>Time</th>
                                            @if(auth()->user()->isAdmin())
                                            <th>Chapter</th>
                                            @endif
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($events as $event)
                                        <tr>
                                            <td>{{ $count++ }}</td>
                                            <td><img class="mr-1" src="{{ asset($event->banners) }}" alt="avatar" height="40" width="auto"></td>
                                            <td>{{ $event->title }}</td>
                                            <td>{{ $event->date }}</td>
                                            <td>{{ $event->venue }}</td>
                                            <td>{{ $event->time }}</td>
                                            @if(auth()->user()->isAdmin())
                                            <td>{{ isset($event->chapter) ? $event->chapter->name : 'All Chapters' }}</th>
                                            @endif
                                            <td style="padding-left: 5px;padding-right: 5px;">
                                                <a class="actions" data-toggle="tooltip" title="Delete record" href="#" onclick="event.preventDefault(); if(confirm('You are about to delete this record'))document.getElementById('delete-form-{{ $event->id }}').submit();"> <i class="fa fa-trash" style="padding: 8px;"></i>
                                                </a>
                                                <form id="delete-form-{{ $event->id }}" action="{{ route('events.destroy', $event->id) }}" method="POST" style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>  
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                   
                </div>
            </div>
        </div>
    </section>
</div>
@endsection