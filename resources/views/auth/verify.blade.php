@extends('layouts.app')
@section('title', 'Verify Email')
<!-- BEGIN: Body-->
@section('content')
<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="content-wrapper">
        <div class="content-header row">
        </div>
        <div class="content-body">
            <!-- register section starts -->
            <section class="row flexbox-container">
                <div class="col-xl-8 col-10">
                    <div class="card bg-authentication mb-0">
                        <div class="row m-0">
                            <!-- register section left -->
                            <div class="col-md-12 col-12 px-0">
                                <div class="card disable-rounded-right mb-0 p-2 h-100 d-flex justify-content-center">
                                    <div class="card-header pb-1">
                                        <div class="card-title">
                                            <div style="text-align: center;"><img style="width: 50%;"
                                                    src="{{ asset('app-assets/images/logo/logo.png') }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <p> <small>
                                                {{ __('Before proceeding, please check your email for a verification link.') }}
                                                {{ __('If you did not receive the email') }},
                                                <a
                                                    href="{{ route('verification.resend') }}">{{ __('click here to request another') }}</a>.</small>
                                        </p>
                                        @if(session('resent'))
                                        <div class="alert alert-success" role="alert">
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            <strong>A New verification link has been sent to your email address. </strong>
                                        </div>
                                        @endif
                                        
                                    </div>

                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </section>
            <!-- register section endss -->
        </div>
    </div>
</div>
@endsection
<!-- END: Body-->
{{-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Verify Your Email Address') }}</div>

<div class="card-body">
    @if(session('resent'))
        <div class="alert alert-success" role="alert">
            {{ __('A fresh verification link has been sent to your email address.') }}
        </div>
    @endif

    {{ __('Before proceeding, please check your email for a verification link.') }}
    {{ __('If you did not receive the email') }}, <a
        href="{{ route('verification.resend') }}">{{ __('click here to request another') }}</a>.
</div>
</div>
</div>
</div>
</div> --}}