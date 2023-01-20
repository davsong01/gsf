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
                        <ul class="nav nav-tabs">
                          @if(\App\Setting::first()->value('enable_conference') == 1)
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('conferencemanagement*') ? 'active' : '' }}" aria-current="page" href="{{ route('conferencemanagement.index') }}">Conference Dashboad</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('conferenceparticipants*') ? 'active' : '' }}" href="{{ route('conference.participants') }}">Participants</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('conferenceparticipants*') ? 'active' : '' }}" href="{{ route('conference.participants') }}">Moderators</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('conferenceparticipants*') ? 'active' : '' }}" href="{{ route('conference.participants') }}">Choristers</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('conferenceparticipants*') ? 'active' : '' }}" href="{{ route('conference.participants') }}">Medics</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link" href="#">Alumni</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('conferenceparticipants*') ? 'active' : '' }}" href="{{ route('conference.participants') }}">Nec</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('conferenceparticipants*') ? 'active' : '' }}" href="{{ route('conference.participants') }}">Officials</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('conferenceparticipants*') ? 'active' : '' }}" href="{{ route('conference.participants') }}">Attempted Transactions</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('conferenceparticipants*') ? 'active' : '' }}" href="{{ route('conference.participants') }}">Donations</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('hostels*') ? 'active' : '' }}" href="{{ route('hostels.index') }}">Hostels</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('foods*') ? 'active' : '' }}" href="{{ route('foods.index') }}">Food stands</a>
                            </li>
                            <li class="nav-item">
                              <a class="nav-link{{ Request::is('users/trashed') ? 'active' : '' }}" href="{{ route('conferenceparticipants.trashed') }}">Trashed Participants</a>
                            </li>
                            
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('emai*') ? 'active' : '' }}" href="{{ route('email.index') }}">Email Participants</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="#">Materials</a>
                            </li>
                          @endif
                            <li class="nav-item">
                              <a class="nav-link {{ Request::is('conferencesettings') ? 'active' : '' }}" href="{{ route('conferencesettings.index') }}">Settings</a>
                            </li>
                          </ul>
                    </div>
                   
                   @yield('content2')
                </div>

            </div>
        </div>
    </section>
       
</div>
@endsection