@extends('frontend.conference.template3.app')

@section('content')
<section id="section-hero" class="section-dark no-top no-bottom text-light jarallax relative mh-500">
    <img src="{{ asset('conference_templates/template2/images/hero_area/banner_bg.jpg') }}" class="jarallax-img" alt="">
    <div class="gradient-edge-bottom h-50"></div>
    <div class="sw-overlay op-5"></div>
    <div class="abs w-80 bottom-10 z-2 w-100">
        <div class="container">
            <div class="row align-items-center justify-content-between gx-5">
                <div class="col-lg-6">
                    <div class="relative wow mask-right">
                        <div class="text-start">
                            <h1 class="fs-96 text-uppercase fs-sm-10vw mb-0 lh-1">Register</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="relative">
    <div class="container">
        <div class="row align-items-center justify-content-center">

            <!-- Left Column: Event Info -->
            <div class="col-lg-6">
                <h2 class="wow fadeInUp">Begin Your Registration</h2>
                <p class="col-lg-8">Fill out your details below. After submitting, you’ll be redirected to a confirmation page to review your registration and complete the next steps.</p>

                <div class="spacer-single"></div>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="relative mb-4">
                            <i class="abs fs-28 p-3 bg-color text-light rounded-1 icofont-location-pin"></i>
                            <div class="ms-80px">
                                <h4 class="mb-0">Location</h4>
                                Gospel City. Lagos Ibadan Expressway, Ogunmakin. Ogun state
                            </div>
                        </div>

                        <div class="relative mb-4">
                            <i class="abs fs-28 p-3 bg-color text-light rounded-1 icofont-envelope"></i>
                            <div class="ms-80px">
                                <h4 class="mb-0">Contact Email</h4>
                                {{ $setting->official_email }}
                            </div>
                        </div>

                        <div class="relative mb-4">
                            <i class="abs fs-28 p-3 bg-color text-light rounded-1 icofont-phone"></i>
                            <div class="ms-80px">
                                <h4 class="mb-0">Call Us</h4>
                                {{ $setting->official_phone }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Registration Form -->
            <div class="col-lg-6">
                <div class="bg-dark-2 rounded-1 p-60 relative">
                    <form action="{{ route('checkout') }}" method="POST">
                        @csrf
                        <div class="row g-4">
                            <div class="col-lg-12">
                                <h3>{{ $title }}</h3>
                                <p>Enter your details below to begin your {{$setting->conference_theme}} registration.</p>
                                @include('includes.bootstrap5alerts')
                                @foreach($registrationFields as $field)
                                    @if($field['status'] && in_array($type, $field['registration_types']))
                                        <div class="field-set">
                                            @if($field['type'] == 'select')
                                                <label>{{ $field['label'] }}</label>
                                                <select name="{{ $field['name'] }}" class="form-underline select2"
                                                        @if(isset($field['required']) && $field['required']) required @endif
                                                        @if(isset($field['has_onchange'])) onchange="{{ $field['has_onchange'] }}" @endif>
                                                    <option value="">--Select--</option>
                                                    @foreach($field['options'] ?? [] as $key => $value)
                                                        @if(is_array($value))
                                                            <option value="{{ $value['id'] ?? $key }}">{{ $value['name'] ?? $value['title'] ?? $value['id'] ?? $key }}</option>
                                                        @else
                                                            <option value="{{ $key }}">{{ $value }}</option>
                                                        @endif
                                                    @endforeach

                                                    @if(isset($field['has_other_option']) && $field['has_other_option'])
                                                        <option value="other">Other</option>
                                                    @endif
                                                </select>
                                            @elseif($field['type'] == 'textarea')
                                                <textarea name="{{ $field['name'] }}" class="form-underline h-100px"
                                                        placeholder="{{ $field['label'] }}"
                                                        @if(isset($field['required']) && $field['required']) required @endif></textarea>
                                            @else
                                                <input type="{{ $field['type'] }}" name="{{ $field['name'] }}" class="form-underline"
                                                    placeholder="{{ $field['label'] }}"
                                                    @if(isset($field['required']) && $field['required']) required @endif>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <input type="hidden" name="type" value="{{ $type }}">
                        <div class="mt-3">
                            <button type="submit" class="btn-main w-100">Submit Details</button>
                        </div>
                    </form>

<script>
$(document).ready(function() {
    $('.select2').select2();

    $('#chapter').change(function() {
        if ($(this).val() === 'other') {
            $('#field-div').show();
            $('#field_id').prop('required', true);
        } else {
            $('#field-div').hide();
            $('#field_id').prop('required', false);
        }
    });
});
</script>

                </div>

{{-- JS for dynamic field display --}}
<script>
$(document).ready(function() {
    $('.select2').select2();

    $('#chapter').change(function() {
        if ($(this).val() === 'other') {
            $('#field-div').show();
            $('#field_id').prop('required', true);
        } else {
            $('#field-div').hide();
            $('#field_id').prop('required', false);
        }
    });
});
</script>

                </div>
            </div>

        </div>
    </div>
</section>
@endsection

@section('script')

<script>
$(document).ready(function() {
    $('.select2').select2();

    $('#chapter').change(function() {
        // If user selects 'other', you could optionally show a field for free text
        if ($(this).val() == 'other') {
            // logic to show custom campus input if needed
        }
    });
});
</script>
@endsection
