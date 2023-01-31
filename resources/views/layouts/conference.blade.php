@extends('layouts.dashboard')
@section('content')
<div class="content-body">
    @include('includes.alerts')
    <!-- Dashboard Analytics Start -->
    <section id="dashboard-analytics">
        
        <div class="row">
            <!-- Website Analytics Starts-->
            <div class="col-md-12 col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <h5 style="color:blue">{{ $edition->conference_theme }} Conference Edition</h5>
                        <ul class="nav nav-tabs">
                          @if(isset($edition))
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('conferenceparticipants/Participant*') ? 'active' : '' }}" href="{{ route('conference.participants',['type'=>'Participant', 'edition'=>$edition->id]) }}">Participants</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('conferenceparticipants/Moderator*') ? 'active' : '' }}" href="{{ route('conference.participants',['type'=>'Moderator', 'edition'=>$edition->id]) }}">Moderators</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('conferenceparticipants/Choir*') ? 'active' : '' }}" href="{{ route('conference.participants',['type'=>'Choir', 'edition'=>$edition->id]) }}">Choristers</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('conferenceparticipants/Medic*') ? 'active' : '' }}" href="{{ route('conference.participants', ['type'=>'Medical', 'edition'=>$edition->id]) }}">Medics</a>
                            </li>
                             <li class="nav-item">
                              <a class="nav-link {{ Request::is('conferenceparticipants/Alumni*') ? 'active' : '' }}" href="{{ route('conference.participants', ['type'=>'Alumni', 'edition'=>$edition->id]) }}">Alumni</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('conferenceparticipants/Nec*') ? 'active' : '' }}" href="{{ route('conference.participants',['type'=>'Nec', 'edition'=>$edition->id]) }}">Nec</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('conferenceparticipants/Official*') ? 'active' : '' }}" href="{{ route('conference.participants', ['type'=>'Official', 'edition'=>$edition->id]) }}">Officials</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('tempusers*') ? 'active' : '' }}" href="{{ route('tempusers.index',['edition'=>$edition->id]) }}">Attempted Transactions</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('donations*') ? 'active' : '' }}" href="{{ route('donations.index',['edition'=>$edition->id]) }}">Donations</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('hostels*') ? 'active' : '' }}" href="{{ route('hostels.index',['edition'=>$edition->id]) }}">Hostels</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('foods*') ? 'active' : '' }}" href="{{ route('foods.index',['edition'=>$edition->id]) }}">Food stands</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('trashed*') ? 'active' : '' }}" href="{{ route('conferenceparticipants.trashed',['edition'=>$edition->id]) }}">Trashed Participants</a>
                            </li>
                            
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('email*') ? 'active' : '' }}" href="{{ route('email.index') }}">Email Participants</a>
                            </li>

                            <li class="nav-item">
                               <a class="nav-link {{ Request::is('materials*') ? 'active' : '' }}" href="{{ route('materials.index',['edition'=>$edition->id]) }}">Materials</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('import/export*') ? 'active' : '' }}" href="{{ route('edit.conference.edition', $edition->id) }}">Import/Export</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('conferenceeditions*') ? 'active' : '' }}" href="{{ route('edit.conference.edition', $edition->id) }}">Settings</a>
                            </li>
                          @endif
                          </ul>
                    </div>
                   
                   @yield('content2')
                </div>

            </div>
        </div>
    </section>
       
</div>
@endsection