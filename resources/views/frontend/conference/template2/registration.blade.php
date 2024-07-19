@extends('frontend.conference.template2.app')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
    .form-control{
        padding-left: 10px !important;
    }
    .select2-selection__rendered {
        line-height: 31px !important;
    }
    .select2-container .select2-selection--single {
        height: 35px !important;
    }
    .select2-selection__arrow {
        height: 34px !important;
    }
    .form-label {
        margin-top: 10px;
        margin-bottom: 5px;
    }
</style>
@endsection
{{-- {{dd($setting->conference_logo)}} --}}
@section('content')
<div id="page-banner-area" class="page-banner-area" style="background-image:url({{ asset('conference_templates/template2/images/hero_area/banner_bg.jpg') }})">
    <!-- Subpage title start -->
    <div class="page-banner-title">
    <div class="text-center">
        <h2>Register</h2>
    </div>
    </div><!-- Subpage title end -->
</div><!-- Page Banner end -->
<!-- ts intro start -->
<section class="ts-intro-content">
    <div class="container">
        @include('includes.alerts')
    <div class="row">
        <div class="col-lg-12">
                <h2 class="column-title">
                    {{-- <span>Register</span> --}}
                    Kindly fill form below and click the payment button
                </h2>
            <div class="intro-content-text">
                @if(isset($setting->lock_online_payment) && $setting->lock_online_payment == 'yes')
                <div class="alert alert-warning" role="alert">
                    <strong>NOTE:</strong> Online Payment has closed. Please fill the form below and pay at the venue.
                </div>
                @endif
                <form action="{{ route('pay') }}" method="POST">
                    @csrf
                    
                    @if(isset($type))
                        @if($type == 1 or $type == 2 or $type == 3)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="control-group">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control form-control-name" id="name"
                                    name="name" placeholder="Enter your full name" required="required">
                                </div>
                                <div class="control-group">
                                    <label class="form-label">Phone</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="Enter your phone number" required="required">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="control-group">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control form-control-name" id="email"
                                        name="email" placeholder="Enter your email" required="required">
                                </div>
                                <div class="control-group">
                                    <label class="form-label" for="gender">Gender</label><br>
                                    <select name="gender" class="form-control" id="gender" required>
                                        <option value="">--Select</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                            </div>

                            @if($type == 1 or $type == 2)
                            <div class="col-md-12">
                                <div class="control-group">
                                    <label class="form-label" for="chapter">GSF Campus/GOFAMINT Assesmbly</label> <small style="color:blue">(Select "other" if you
                                        are registering from an assembly)</small><br>
                                    <select name="chapter" class="chapter form-control" required>
                                        <option value="">--Select Campus</option>
                                        @foreach($chapters as $chapter)
                                        <option value="{{ $chapter->id }}" {{ old('chapter') == $chapter->id ? 'selected' : ''}}>
                                            {{ $chapter->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            @endif
                        
                            @if($type == 2)
                            <div class="col-md-12">
                                <div class="control-group">
                                    <label class="form-label">No of Participants you want to register</label>
                                    <input type="number" class="form-control" id="participants" name="participants"
                                        placeholder="Number of participants from your fellowship" required="required">
                                </div>
                            </div>
                            @endif

                            @if($type == 3)
                            <div class="col-md-12">
                                <div class="control-group">
                                    <label class="form-label">Old alumni/New alumni?</label>
                                    <select name="alumni_type" class="form-control select2"
                                        onchange="document.querySelector('#alumni_amount').value = alumnis_amount[this.value]?alumnis_amount[this.value]*100:''"
                                        required>
                                        <option value="">Select alumni type</option>
                                        <option value="new_alumni_registration_fee">Fresh Graduate/Alumni (&#8358;{{ $setting->new_alumni_registration_fee }}) </option>
                                        <option value="alumni_registration_fee">Old Alumni (&#8358;{{ $setting->alumni_registration_fee }})</option>
                                    </select>
                                </div>
                            </div>
                            @endif
                        </div> 
                        @endif
                        <input type="hidden" name="metadata" value="{{ json_encode($array = ['type' => $type]) }}">
                        <input type="hidden" name="type" value="{{ $type }}">
                        @if($type == 1)
                        <input type="hidden" name="amount" value="{{ $setting->registration_fee }}">
                        <input type="hidden" name="quantity" value="1">
                        <input type="hidden" name="currency" value="NGN">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        @endif

                        @if($type == 2)
                        <script>
                            var participants = document.getElementById('participants');
                            var amount = document.getElementById('amount');
                            
                            participants.addEventListener('input', function() {
                                amount.value = this.value * {{ $setting->registration_fee * 100 }};
                            });
                            
                            amount.addEventListener('input', function() {
                                participants.value = this.value;
                            });
                        </script>
                        @endif
                    
                    @endif
                    
                    <br>
                    <div class="control-group" style="margin-top:30px">
                        <button class="btn btn-danger hover-top btn-glow rounded-pill border-0" type="submit" style="width:100%">Make Payment</button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
    </div>
</section>
@endsection
@section('script')
<script>
    $(document).ready(function() {
        $('.chapter').select2();
    });
    $(document).on('select2:open', function(e) {
        window.setTimeout(function () {
            document.querySelector('input.select2-search__field').focus();
        }, 0);
    });

</script>
@endsection