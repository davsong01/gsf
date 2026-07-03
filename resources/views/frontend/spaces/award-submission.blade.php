@extends('frontend.spaces.layouts.app')

@section(
    'title',
    $type === 'go'
        ? 'First Class Award Application'
        : 'Educational Trust Fund (ETF) Application'
)

@section('css')

<link
    href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
    rel="stylesheet"
/>

<style>

.award-hero {
    background:
        linear-gradient(
            135deg,
            rgba(9,32,63,.92),
            rgba(83,120,149,.90)
        );
    color: white;
    padding: 120px 0 90px;
}

.award-card {
    border: 0;
    border-radius: 24px;
    overflow: hidden;
    box-shadow:
        0 20px 50px rgba(0,0,0,.08);
}

.award-header {
    padding: 40px;
    background: linear-gradient(
        135deg,
        #f8fafc,
        #ffffff
    );
    border-bottom: 1px solid #eef2f7;
}

.award-badge {
    display: inline-block;
    padding: 8px 18px;
    border-radius: 999px;
    background: rgba(84,125,155,.12);
    color: #547d9b;
    font-weight: 700;
    font-size: .85rem;
    letter-spacing: .5px;
}

.scripture-box {
    background: #f8fbff;
    border-left: 5px solid #547d9b;
    border-radius: 14px;
    padding: 25px;
    font-style: italic;
    color: #334155;
}

.form-section-title {
    font-weight: 700;
    margin-bottom: 25px;
    color: #0f172a;
}

.form-label {
    font-weight: 600;
}

.btn-submit {
    border-radius: 14px;
    padding: 15px 30px;
    font-weight: 700;
}

/* Custom Select2 styling to match .form-control */
.select2-container--default .select2-selection--single {
    display: block;
    width: 100%;
    height: calc(1.5em + 1.2625rem) !important;
    padding: .6rem .75rem !important;
    font-size: 1rem;
    font-weight: 300;
    line-height: 1.5;
    color: #424767;
    background-color: #fff;
    border: .0625rem solid #edf0f7 !important;
    border-radius: .45rem !important;
    box-shadow: 0 4px 30px rgba(0, 0, 0, .05);
    transition: all .3s ease-in-out;
}

/* Remove default Select2 arrow styling conflicts */
.select2-container--default .select2-selection--single .select2-selection__rendered {
    padding: 0 !important;
    line-height: 1.5 !important;
    color: #424767;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: calc(1.5em + 1.2625rem) !important;
    right: 10px;
}

/* Focus state */
.select2-container--default.select2-container--focus .select2-selection--single {
    border-color: #b3b3b3 !important; /* Adjust if you have a specific focus border color */
    box-shadow: 0 4px 30px rgba(0, 0, 0, .08);
}

/* Dropdown appearance */
.select2-dropdown {
    border: .0625rem solid #edf0f7 !important;
    border-radius: .45rem;
    box-shadow: 0 4px 30px rgba(0, 0, 0, .05);
}
/* .select2-container .select2-selection--single{
    height:45px !important
} */
</style>

@endsection


@section('content')

@php

$awardMeta = [
    'go' => [
        'title' => 'First Class Award Registration',
        'subtitle' => 'Celebrating excellence, diligence and godly character.',
        'verse' => 'Daniel 1:20',
        'quote' => 'In every matter of wisdom and understanding... he found them ten times better.',
    ],

    'etf' => [
        'title' => 'Educational Trust Fund Application',
        'subtitle' => 'Supporting students in pursuit of academic excellence.',
        'verse' => 'Proverbs 4:7',
        'quote' => 'Wisdom is the principal thing; therefore get wisdom.',
    ],
];

$meta = $awardMeta[$type];

@endphp


<section class="award-hero">

    <div class="container">

        <div class="row justify-content-center text-center">

            <div class="col-lg-9">

                <h1 class="display-3 mt-4 mb-4">
                    {{ $meta['title'] }}
                </h1>

                <p class="lead mb-0">
                    {{ $meta['subtitle'] }}
                </p>

            </div>

        </div>

    </div>

</section>


<section class="py-6">

    <div class="container">

        <div class="row justify-content-center">

            <div class="col-xl-12">

                @include('includes.alerts')

                <div class="award-card bg-white">

                    <div class="award-header">

                        <div class="row align-items-center">

                            <div class="col-lg-8">

                                <h3 class="mb-3">
                                    {{ $meta['title'] }}
                                </h3>

                                <p class="text-muted mb-0">
                                    Kindly complete all required fields accurately.
                                    Submitted information will be reviewed by the
                                    official committee.
                                </p>

                            </div>

                            <div class="col-lg-4">

                                <div class="scripture-box mt-4 mt-lg-0">

                                    <strong>
                                        {{ $meta['verse'] }}
                                    </strong>

                                    <hr>

                                    "{{ $meta['quote'] }}"

                                </div>

                            </div>

                        </div>

                    </div>


                    <div class="p-4 p-lg-5">

                        <form
                            method="POST"
                            enctype="multipart/form-data"
                            action="{{ route('award.submit', $type) }}"
                        >

                            @csrf

                            <input
                                type="hidden"
                                name="type"
                                value="{{ $type }}"
                            >

                            <h5 class="form-section-title">
                                Personal Information
                            </h5>

                            @include('admin.awards.award_form',
                                [
                                    'fields' => $fields,
                                    'award' => null,
                                    'canEdit' => true,
                                    'isAdmin' => false,
                                    'required' => true,
                                ]
                            )

                            <div class="mt-5 text-center">

                                <button
                                    type="submit"
                                    class="btn btn-primary btn-submit px-5"
                                >
                                    <i class="fa fa-paper-plane me-2"></i>
                                    Submit Application
                                </button>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

@endsection


@section('js')

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>

$(function () {
    $('select').each(function () {
        if ($(this).find('option').length > 1) {
            $(this).select2({
                width: '100%'
            });
        }
    });
});

</script>

@endsection
