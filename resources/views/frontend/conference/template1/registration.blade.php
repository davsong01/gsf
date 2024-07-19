@extends('frontend.conference.template1.index')
@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
</style>
@endsection
@section('sec-content')
<section class="bg-100 py-7" id="register">
    <div class="container-lg" style="margin-top:40px">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-5 text-center mb-3">
                <h2>Kindly fill form below and click the payment button</h2>
            </div>
        </div>
        @include('includes.alerts')
        @if(isset($setting->lock_online_payment) && $setting->lock_online_payment == 'yes')
        <div class="alert alert-warning" role="alert">
            <strong>NOTE:</strong> Online Payment has closed. Please fill the form below and pay at the venue.
        </div>
        @endif
        <div class="row h-100 justify-content-center">
            <div class="contact-form">
                <form action="{{ route('pay') }}" method="POST">
                    @csrf
                    @if(isset($type))
                        @if($type == 1 or $type == 2 or $type == 3)
                            <div class="control-group">
                                <label>Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                name="name" placeholder="Enter your full name" required="required">
                            </div>
                            <div class="control-group">
                                <label>Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" placeholder="Enter your email" required="required">
                                @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="control-group">
                                <label>Phone</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone"
                                    name="phone" placeholder="Enter your phone number" required="required">
                                @error('phone')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                            <div class="control-group">
                                <label for="gender">Gender</label><br>
                                <select name="gender" class="form-control" id="gender" required>
                                    <option value="">--Select</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            @if($type == 1 or $type == 2)
                            <div class="control-group">
                                <label for="chapter">Campus/Assesmbly</label> <small style="color:blue">(Please Select "other" if you
                                    are registering from an assembly)</small><br>
                                <select name="chapter" class="chapter form-control" required>
                                    <option value="">--Select--</option>
                                    @foreach($chapters as $chapter)
                                    <option value="{{ $chapter->id }}" {{ old('chapter') == $chapter->id ? 'selected' : ''}}>
                                        {{ $chapter->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            @if($type == 2)
                            <div class="control-group">
                                <label>No of Participants you want to register</label>
                                <input type="number" class="form-control" id="participants" name="participants"
                                    placeholder="Number of participants from your fellowship" required="required">
                            </div>
                            @endif
                            @if($type == 3)
                            <div class="control-group">
                                <label>Old alumni/New alumni?</label>
                                <select name="alumni_type" class="form-control select2"
                                    onchange="document.querySelector('#alumni_amount').value = alumnis_amount[this.value]?alumnis_amount[this.value]*100:''"
                                    required>
                                    <option value="">Select alumni type</option>
                                    <option value="new_alumni_registration_fee">Fresh Graduate/Alumni (&#8358;{{ $setting->new_alumni_registration_fee }}) </option>
                                    <option value="alumni_registration_fee">Old Alumni (&#8358;{{ $setting->alumni_registration_fee }})</option>
                                </select>
                            </div>
                            @endif
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
                        
                        @if($type == 5)
                        <div class="control-group">
							<label>Full Name</label>
							<input type="text" class="form-control" value="{{ old('name') }}" id="name" name="name" placeholder="Enter your full name"
								required="required">
						</div>

						<div class="control-group">
							<label>Email</label>
							<input type="email" value="{{ old('email') }}" class="form-control" id="email" name="email" placeholder="Enter your email"
								required="required">
						</div>
						{{-- <div class="control-group">
							<label>State</label>
							<input type="text" class="form-control" id="state" name="state" placeholder="Your Location"
								required="required">
						</div> --}}
						<div class="control-group">
							<label>Phone Number</label>
							<input type="text" class="form-control" value="{{ old('phone') }}" id="phone" name="phone" placeholder="Enter your phone number"
								required="required">
						</div>

						<div class="control-group">
							<label>Enter amount</label>
							<input type="number" class="form-control" id="amount" name="amount" value="{{ old('amount') }}"
								placeholder="Amount you want to donate" min="1000" required="required">
						</div>
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