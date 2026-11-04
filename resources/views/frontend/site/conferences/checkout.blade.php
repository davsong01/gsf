@extends('frontend.site.layout')

@php
    $conferenceUrl = $setting->slug ? route('conferences.show', $setting->slug) : route('conferences.index');
    $storedProviderResponse = $transaction->api_response ?? [];
    $paymentVerificationFailed = session('payment_failed')
        || ($transaction->status !== 'Complete' && !empty($storedProviderResponse));
    $paymentProviderConfig = [
        'slug' => $paymentProvider->slug,
        'publicKey' => $paymentProvider->public_key,
        'apiKey' => $paymentProvider->api_key,
        'channels' => $paymentProvider->channels ?? [],
        'amountKobo' => (int) round((float) $transaction->total_amount * 100),
        'amountNaira' => (int) round((float) $transaction->total_amount),
        'email' => $transaction->email,
        'name' => $transaction->name,
        'reference' => $transaction->transid,
        'verifyUrl' => url('/payment/verify/'.rawurlencode($transaction->transid)),
        'contractCode' => $paymentProvider->public_key,
        'enableSubAccount' => (bool) $paymentProvider->enable_sub_account,
        'subAccountCode' => $paymentProvider->sub_account_code,
        'subAccountPercentage' => (float) ($paymentProvider->sub_account_fee_percentage ?? 0),
        'feeBearer' => ($paymentProvider->sub_account_fee_percentage ?? 0) > 85 ? 'subaccount' : 'account',
    ];
@endphp

@section('title', 'Confirm payment — '.$setting->conference_theme)
@section('description', 'Review your registration and payment details for '.$setting->conference_theme.'.')
@section('mainClass', 'public-page conference-flow-page')
@section('activeSection', 'conferences')

@push('head')
<style>
    .conference-checkout-card .conference-flow-step { font-size: 12px; }
    .conference-checkout-card .conference-flow-card__heading p { font-size: 16px; }
    .conference-checkout-card .conference-summary-grid > div { gap: 9px; padding: 17px; }
    .conference-checkout-card .conference-summary-grid span { font-size: 13px; }
    .conference-checkout-card .conference-summary-grid strong { font-size: 15px; line-height: 1.5; }
    .conference-checkout-card .conference-summary-note span { font-size: 12px; }
    .conference-checkout-card .conference-summary-note p { font-size: 14px; line-height: 1.6; }
    .conference-payment-card h2 { font-size: clamp(28px, 2.2vw, 32px); }
    .conference-payment-line span, .conference-payment-total span { font-size: 13px; }
    .conference-payment-line strong { font-size: 16px; }
    .conference-payment-total strong { font-size: 25px; }
    .conference-payment-secure { font-size: 13px; }
    .conference-payment-back { font-size: 14px; }
    .conference-payment-card .conference-flow-button { font-size: 16px; }
</style>
@endpush

@section('content')
<section class="conference-flow-hero conference-flow-hero--compact">
    <div class="site-container">
        <nav class="breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home.index') }}">Home</a><span>·</span><a href="{{ $conferenceUrl }}">{{ $setting->conference_theme }}</a><span>·</span><span>Payment</span></nav>
        <div class="eyebrow eyebrow--light"><span></span> Registration · Final step</div>
        <h1>Review and continue.</h1>
        <p>Check your registration summary before making payment.</p>
    </div>
</section>

<section class="conference-flow-section">
    <div class="site-container">
        @if(session('message'))
            <div class="conference-flow-alert mb-4" role="status">{{ session('message') }}</div>
        @endif
        @if($paymentVerificationFailed)
            <div class="conference-flow-alert conference-flow-alert--error mb-4" role="alert">
                <strong>We couldn’t verify this payment.</strong>
                <p>{{ session('error') ?: data_get($transaction->api_response, 'message', 'Your registration is still pending.') }}</p>
                <p>If your account has already been charged, contact the conference team before making another payment.</p>
                @if($transaction->status !== 'Complete')
                    <a class="conference-flow-button mt-2" href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('checkout.retry', now()->addMinutes(60), ['transaction' => $transaction->id]) }}">Try again with a fresh transaction <span aria-hidden="true">→</span></a>
                @endif
            </div>
        @endif
    </div>
    <div class="site-container conference-checkout-layout">
        <article class="conference-flow-card conference-checkout-card">
            <div class="conference-flow-card__heading"><span class="conference-flow-step">02 <i></i> PAYMENT SUMMARY</span><h2>Your registration</h2><p>Review the information below, then continue to the secure payment window.</p></div>
            <div class="conference-summary-grid">
                <div><span>Participant</span><strong>{{ $transaction->name ?? '—' }}</strong></div>
                <div><span>Email address</span><strong>{{ $transaction->email ?? '—' }}</strong></div>
                <div><span>Phone number</span><strong>{{ $transaction->phone ?? '—' }}</strong></div>
                <div><span>Registration type</span><strong>{{ $transaction->conferenceplan->title ?? 'Conference registration' }}</strong></div>
                @if($transaction->gender)<div><span>Gender</span><strong>{{ ucfirst($transaction->gender) }}</strong></div>@endif
                @if($transaction->location)<div><span>Payment method</span><strong>{{ $transaction->location }}</strong></div>@endif
            </div>
            @if($transaction->remarks)<div class="conference-summary-note"><span>Additional information</span><p>{{ $transaction->remarks }}</p></div>@endif
        </article>

        <aside class="conference-payment-card">
            <div class="eyebrow"><span></span> {{ $setting->conference_theme }}</div>
            <h2>Payment details</h2>
            <div class="conference-payment-line"><span>Registration fee</span><strong>₦{{ number_format((float) $transaction->amount_paid, 2) }}</strong></div>
            @if($paymentProvider->customer_pays_provider_charge)
                <div class="conference-payment-line"><span>Payment service charge</span><strong>₦{{ number_format((float) $transaction->provider_charge, 2) }}</strong></div>
            @endif
            <div class="conference-payment-total"><span>Total payable</span><strong>₦{{ number_format((float) $transaction->total_amount, 2) }}</strong></div>
            {{-- <p class="conference-payment-secure"><span aria-hidden="true">✓</span> Your payment is processed securely by {{ ucfirst($paymentProvider->name ?? $paymentProvider->slug) }}.</p> --}}
            @if(!$paymentVerificationFailed && $transaction->status !== 'Complete')
                <button id="proceedBtn" class="conference-flow-button conference-flow-button--full" type="button">Proceed to payment <span aria-hidden="true">→</span></button>
            @elseif($transaction->status === 'Complete')
                <p class="conference-flow-alert">This transaction is already complete.</p>
            @else
                <p class="conference-payment-secure">Use the fresh transaction link above to make another attempt.</p>
            @endif
            <a class="conference-payment-back" href="{{ route('conference.registration', $transaction->conference_plan_id) }}">← Back to registration details</a>
        </aside>
    </div>
</section>
@endsection

@push('scripts')
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="https://sdk.monnify.com/plugin/monnify.js"></script>
    <script>
        (() => {
            const config = @json($paymentProviderConfig);
            const button = document.getElementById('proceedBtn');
            if (!button) return;

            button.addEventListener('click', () => {
                button.disabled = true;
                if (config.slug === 'paystack' && window.PaystackPop) {
                    const options = {
                        key: config.publicKey,
                        email: config.email,
                        amount: config.amountKobo,
                        currency: 'NGN',
                        ref: config.reference,
                        channels: config.channels,
                        metadata: {
                            custom_fields: [
                                { display_name: 'Customer Name', variable_name: 'customer_name', value: config.name },
                                { display_name: 'Customer Phone', variable_name: 'customer_phone', value: @json($transaction->phone) },
                                { display_name: 'Transaction ID', variable_name: 'transaction_id', value: config.reference },
                                { display_name: 'Conference Edition', variable_name: 'conference_edition_id', value: @json((string) $transaction->conference_edition_id) }
                            ],
                            conference_edition_id: @json((string) $transaction->conference_edition_id),
                            transid: config.reference
                        },
                        callback: response => {
                            if (response.status === 'success') window.location.href = config.verifyUrl;
                            else { button.disabled = false; window.alert('Payment was not completed. Please try again.'); }
                        },
                        onClose: () => { button.disabled = false; }
                    };
                    @if($paymentProvider->enable_sub_account)
                        options.subaccount = config.subAccountCode;
                        options.bearer = config.feeBearer;
                        options.transaction_charge = 0;
                    @endif
                    window.PaystackPop.setup(options).openIframe();
                    return;
                }

                if (config.slug === 'monnify' && window.MonnifySDK) {
                    const options = {
                        amount: config.amountNaira,
                        currency: 'NGN',
                        reference: config.reference,
                        customerFullName: config.name,
                        customerEmail: config.email,
                        apiKey: config.apiKey,
                        contractCode: config.contractCode,
                        paymentDescription: 'Conference Registration',
                        paymentMethods: config.channels,
                        onComplete: () => { window.location.href = config.verifyUrl; },
                        onClose: () => { button.disabled = false; }
                    };
                    @if($paymentProvider->enable_sub_account)
                        options.incomeSplitConfig = [{ subAccountCode: config.subAccountCode, feePercentage: config.subAccountPercentage, feeBearer: config.feeBearer }];
                    @endif
                    window.MonnifySDK.initialize(options);
                    return;
                }

                button.disabled = false;
                window.alert('The configured payment window could not be loaded. Please contact the conference team.');
            });
        })();
    </script>
@endpush
