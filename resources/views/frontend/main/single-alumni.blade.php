@extends('frontend.main.mainlayout')
@section('og')
<meta property="og:url" content="{{ route('student.single', $alumni->slug)}}">
<meta property="og:title" content="{{ $alumni->name }}"> 
<meta property="og:image" content="{{ !is_null($alumni->passport) ? asset($alumni->passport) : asset('frontend/passports/avatar.jpg') }}"/> 
@endsection
@section('title', $alumni->name)
@section('content')
<div class="page-bread mb70">
    <div class="container">
        <div class="row">
            <div class="col-sm-6">
                <h3>{{ $alumni->name }}</h3>
            </div>
            <div class="col-sm-6">

            </div>
        </div>
    </div>
</div>

<div class="">
    <div class="container">
        <div class="row">
            <div class="col-md-9 mb40">
                <div class="row mb30">
                    <div class="col-sm-4 mb40">
                        {!! renderAvatar($alumni, 180, 'img-responsive img-thumbnail') !!}
                        @if($alumni->open_to_work)
                        <button disabled style="width:100%; cursor: auto;" class="btn btn-primary">Open to work</button>
                        @endif
                    </div>
                    <div class="col-sm-8 mb40">
                        <h2 class="font300">{{ ucwords($alumni->name) }}</h2>
                      
                        <p class="sinle-details">
                            <i class="fa fa-university" data-toggle="tooltip" data-html="true" title="Chapter" aria-hidden="true"></i> &nbsp;<b>GSF</b> - {{ $alumni->campus->name }} 
                            <br>
                            @if($alumni->course)<i class="fa fa-book" data-toggle="tooltip" data-html="true" title="Discipline" aria-hidden="true"></i> &nbsp; {{ $alumni->course }}  @if($alumni->status == 1 && $alumni->graduation_year != NULL )({{ $alumni->matric_year . ' - ' . $alumni->graduation_year }})@endif<br> @endif
                            @if($alumni->skills)<i class="fa fa-address-card" data-toggle="tooltip" data-html="true" title="Skills" aria-hidden="true"> &nbsp; {{ $alumni->skills }} @endif
                               </i> <br>
                            <i data-toggle="tooltip" data-html="true" title="Portfolio" class="fa fa-briefcase" aria-hidden="true"> &nbsp; {{ $alumni->rolename }}@if($alumni->rolename <> 'Admin' && $alumni->rolename <> 'Member')<em>{{ ', ' . $alumni->portfolio_session }}</em>
                                               
                                @endif </i> <br>
                            @if(!is_null($alumni->dob))
                            <i class="fa fa-birthday-cake" aria-hidden="true" data-toggle="tooltip" data-html="true" title="Birthday"> &nbsp;{{ \Carbon\Carbon::parse($alumni->dob)->format('jS\, F') }} </i>
                            @endif
                            @if($alumni->show_email == 1)
                            <i class="fa fa-envelope" aria-hidden="true" data-toggle="tooltip" data-html="true" title="Email"> &nbsp; {{ $alumni->email }}</i> <br>
                            @endif
                            @if($alumni->show_phone == 1)
                            <i class="fa fa-phone" aria-hidden="true" data-toggle="tooltip" data-html="true" title="Phone"> &nbsp;&nbsp;&nbsp;{{ $alumni->phone }}</i>
                            @endif
                        </p>

                        @if(!is_null($alumni->facebook) && !is_null($alumni->twitter) )
                        @if(!is_null($alumni->twitter)) 
                        <p class="social-inline single-socials"><strong>Social:</strong>
                        @endif
                        @if(!is_null($alumni->facebook)) 
                        <a href="{{ $alumni->facebook }}" target="_blank"><i class="fa fa-facebook-square"></i></a>
                        <a href="{{ $alumni->twitter }}"><i class="fa fa-twitter-square" target="_blank"></i></a>
                        @endif
                        @endif
                        </p>
                    </div>
                </div>           
            </div><!--/col-->
            @if(!empty($alumni->email))
            <div class="col-md-3 mb60">
                <h4 class="left-title mb20">Contact me</h4>
                <div class="mb40">
                    <form action="{{ route('alumni.contact') }}" method="POST" class="finder-contact">
                        @csrf
                        <div class="row">
                            <div class="col-sm-12"> 
                                <div class="row">
                                    <div class="col-sm-12 mb15">
                                        <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Your Full Name...." required/>
                                    </div>
                                    <div class="col-sm-12 mb15">
                                        <input type="emai;" name="email" value="{{ old('email') }}" class="form-control" placeholder="Email Address...." required/>
                                    </div>
                                    <div class="col-sm-12 mb15">
                                        <input type="text" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="Phone number...." required/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mb15">
                                <textarea name="message" value="{{ old('message') }}" class="form-control" rows="5" placeholder="Message...." required></textarea><input type="hidden" name="alumni_id" value="{{ $alumni->id }}">          
                            </div>
                        </div>
                        <div class="row mb15">
                            <div class="col-sm-12 text-center">
                                <div class="data-status"></div> <!-- data submit status -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 text-right">
                                <button type="submit" name="submit" class="btn btn-primary btn-lg" style="width:100%">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
