@extends('frontend.conference.template3.app')
@include('includes.alerts')

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
                            <h1 class="text-uppercase fs-sm-10vw mb-0 lh-1">Confirm Your Registration</h1>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 wow fadeInRight" data-wow-delay=".3s">
                    <p class="mb-0">
                        Please review your details below before proceeding with payment.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="relative py-5">
    <div class="container">
        <div class="row justify-content-center text-light">
            <div class="col-lg-8">
                <div class="card bg-dark p-4 border-0 rounded-4 shadow-lg">
                    <h3 class="text-uppercase mb-4">Registration Summary</h3>

                    <div class="border-bottom pb-3 mb-3">
                        <p class="mb-1"><strong>Name:</strong> {{ $transaction->name ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $transaction->email ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Phone:</strong> {{ $transaction->phone ?? 'N/A' }}</p>
                        <p class="mb-1"><strong>Gender:</strong> {{ ucfirst($transaction->gender ?? 'N/A') }}</p>
                        @if(!empty(registrationTypeNames([$transaction->type])[0]))
                        <p class="mb-1"><strong>Registration Type:</strong> 
                            {{ registrationTypeNames([$transaction->type])[0] }}
                        </p>
                        @endif
                        @if(!empty($transaction->location))
                            <p class="mb-1"><strong>Location:</strong> {{ $transaction->location }}</p>
                        @endif
                    </div>

                    <h4 class="mb-3 text-uppercase">Payment Details</h4>
                    
                    <div class="border-bottom pb-3 mb-3">
                        <p class="mb-1"><strong>Amount:</strong> ₦{{ number_format($transaction->amount_paid, 2) }}</p>

                        @if($paymentProvider->customer_pays_provider_charge)
                            <p class="mb-1"><strong>Service Charge:</strong> ₦{{ number_format($paymentProvider->provider_charge, 2) }}</p>
                        @endif

                        <div class="mt-3 py-2 px-3 rounded bg-dark text-light d-flex justify-content-between align-items-center" style="border: 2px solid #ffc107;">
                            <strong class="fs-5 text-uppercase">Total Payable:</strong>
                            <span class="fs-4 fw-bold text-warning">
                                ₦{{ number_format(($transaction->amount_paid + ($paymentProvider->provider_charge ?? 0)), 2) }}
                            </span>
                        </div>

                        <hr class="my-3">

                        <p class="mb-1"><strong>Status:</strong> 
                            <span class="badge {{ $transaction->status === 'Complete' ? 'bg-success' : 'bg-warning text-dark' }}">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </p>

                        @if(!empty($transaction->transid))
                            <p class="mb-1"><strong>Transaction ID:</strong> {{ $transaction->transid }}</p>
                        @endif

                        @if(!empty($transaction->remarks))
                            <p class="mb-0"><strong>Remarks:</strong> {{ $transaction->remarks }}</p>
                        @endif
                    </div>
                    
                    <h4 class="mb-3 text-uppercase">Conference Edition</h4>
                    <p class="mb-1"><strong>Payment for:</strong> {{ $setting->conference_theme ?? 'N/A' }}</p>
 
                    <div id="proceedBtn" class="d-flex justify-content-between align-items-center mt-4">
                        <a href="#" class="btn-main fx-slide"  data-provider="{{ $paymentProvider->slug }}">
                            <span>Proceed to Payment</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
@endsection
@section('script')
<!-- Paystack Inline -->
<script src="https://js.paystack.co/v1/inline.js"></script>

<!-- Monnify Inline -->
<script type="text/javascript" src="https://sdk.monnify.com/plugin/monnify.js"></script>

<script>
document.getElementById('proceedBtn').addEventListener('click', function() {
    const provider = "{{$paymentProvider->slug}}";

    if(provider === 'paystack') {
        // Open Paystack modal
        let handler = PaystackPop.setup({
            key: '{{ env("PAYSTACK_PUBLIC_KEY") }}',
            email: '{{ $transaction->email }}',
            amount: {{ ($transaction->amount_paid + ($paymentProvider->provider_charge ?? 0)) * 100 }},
            currency: 'NGN',
            ref: '{{ $transaction->transid }}',
            channels: @json($paymentProvider->channels),
            metadata: {
                custom_fields: [
                    {
                        display_name: "Customer Name",
                        variable_name: "customer_name",
                        value: "{{ $transaction->name }}"
                    },
                    {
                        display_name: "Customer Phone",
                        variable_name: "customer_phone",
                        value: "{{ $transaction->phone }}"
                    },
                    {
                        display_name: "Customer Email",
                        variable_name: "customer_email",
                        value: "{{ $transaction->email }}"
                    }
                ]
            },
            callback: function(response){
                window.location.href = "/payment/verify/" + response.reference;
            },
            onClose: function(){
                alert('Payment window closed');
            }
        });
        handler.openIframe();
    }

    if(provider === 'monnify') {
        // Open Monnify modal
        MonnifySDK.initialize({
            amount: {{ $transaction->amount_paid + ($paymentProvider->provider_charge ?? 0) }},
            currency: "NGN",
            reference: "{{ $transaction->reference }}",
            customerName: "{{ $transaction->name }}",
            customerEmail: "{{ $transaction->email }}",
            apiKey: "{{ env('MONNIFY_API_KEY') }}",
            contractCode: "{{ env('MONNIFY_CONTRACT_CODE') }}",
            paymentDescription: "Conference Registration",
            metadata: {
                transactionId: "{{ $transaction->id }}"
            },
            onComplete: function(response) {
                if (response.status === "PAID") {
                    window.location.href = "/payment/verify/monnify/" + response.paymentReference;
                } else {
                    alert("Payment not completed");
                }
            },
            onClose: function(data) {
                console.log("Monnify modal closed", data);
            }
        });
    }
});
</script>

@endsection
