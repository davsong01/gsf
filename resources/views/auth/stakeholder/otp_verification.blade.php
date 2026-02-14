@extends('va.partials.app')
@section('content')
<x-breadcrumb title="OTP Verification" />
@section('content')
@include('va.partials.shape')

<section class="section bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-7">
                <!-- OTP Card -->
                <div class="card shadow-sm border-0 rounded-4 p-4">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold">{{$title}}</h3>
                        <p class="text-muted mb-0">{{$text}}</p>
                        <small class="d-block mt-2" style="color:red !important">
                            @if(!$isExpired)
                            Code expires in <span id="otpTimer"></span> minutes
                            @else
                            Code has expired, please use the resend button below to resend new OTP
                            @endif
                        </small>

                    </div>

                    <form id="verifyOtpForm" class="otp-form">
                        @csrf
                        <!-- Error Alert -->
                        <div id="otpErrors" class="alert alert-danger alert-dismissible fade" role="alert" style="display:none;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="alert-text"></span>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">X</button>
                            </div>
                        </div>


                        <!-- OTP Input -->
                        <div class="mb-4">
                            <label for="otpInput" class="form-label fw-medium">One-Time Password</label>
                            <input type="text" class="form-control text-center fs-5 rounded-3" id="otpInput" name="otp"
                                   placeholder="Enter OTP" maxlength="6" required>
                        </div>

                        <!-- Verify Button -->
                        <div class="d-grid mb-3">
                            <button type="submit" id="verifyOtpBtn" class="btn btn-primary btn-lg rounded-3">
                                <span class="btn-text">Verify OTP</span>
                                <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                        <input type="hidden" name="user_id" value="{{$user->id}}">
                        <input type="hidden" name="type" value="{{$otp->type}}">

                        <!-- Resend OTP -->
                        <div class="mt-3 text-center">
                            <p class="mb-0 text-muted">
                                Didn't receive the code?
                                <a href="javascript:void(0);"
                                id="resendOtpLink"
                                class="text-primary fw-medium"
                                data-expired="{{ $isExpired ? 1 : 0 }}">
                                {{ $isExpired ? 'Resend New OTP' : 'Resend OTP' }}
                                </a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--end row-->
    </div>
    <!--end container-->
</section>

<style>
    .otp-form input:focus {
        box-shadow: 0 0 0 0.25rem rgba(0,123,255,.25);
        border-color: #0d6efd;
    }
    .card {
        background: #fff;
        transition: all 0.3s ease-in-out;
    }
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
</style>
@endsection
@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const verifyOtpForm = document.getElementById('verifyOtpForm');
    const otpErrors = document.getElementById('otpErrors');
    const resendOtpLink = document.getElementById('resendOtpLink');
    const timerEl = document.getElementById('otpTimer');
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');

    const otpUserId = {{ $user->id }};
    const otpType = "{{ $otp->type }}";

    let expiresAt = {{ $expiresAt }} * 1000; // ms
    let interval = null;

    /* ================= OTP VERIFY ================= */
    verifyOtpForm.addEventListener('submit', function(e) {
        e.preventDefault();

        otpErrors.style.display = 'none';
        otpErrors.classList.remove('show');

        verifyOtpBtn.disabled = true;
        verifyOtpBtn.querySelector('.btn-text').classList.add('d-none');
        verifyOtpBtn.querySelector('.spinner-border').classList.remove('d-none');

        const formData = new FormData(verifyOtpForm);

        fetch("{{ route('va.process.otp') }}", {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect_url;
            } else {
                otpErrors.innerHTML = data.errors?.otp?.join('<br>') || data.message;
                otpErrors.style.display = 'block';
                otpErrors.classList.add('show');

                resetVerifyButton();
            }
        })
        .catch(() => {
            otpErrors.innerHTML = 'Server error. Please try again.';
            otpErrors.style.display = 'block';
            otpErrors.classList.add('show');

            resetVerifyButton();
        });
    });

    function resetVerifyButton() {
        verifyOtpBtn.disabled = false;
        verifyOtpBtn.querySelector('.btn-text').classList.remove('d-none');
        verifyOtpBtn.querySelector('.spinner-border').classList.add('d-none');
    }

    /* ================= RESEND OTP ================= */
    resendOtpLink.addEventListener('click', function (e) {
        e.preventDefault(); // VERY important
        verifyOtpBtn.disabled = true;
        verifyOtpBtn.querySelector('.btn-text').classList.add('d-none');
        verifyOtpBtn.querySelector('.spinner-border').classList.remove('d-none');

        fetch("{{ route('va.resend.otp') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                user_id: otpUserId,
                type: otpType
            })
        })
        .then(res => res.json())
        .then(data => {
            alert(data.message || 'OTP resent successfully!');

            if (data.expires_at) {
                expiresAt = data.expires_at * 1000; // reset timer
                startOtpCountdown();
            }

            resetVerifyButton();
        })
        .catch(() => {
            alert('Failed to resend OTP, please try again.');
            resetVerifyButton();
        });
    });

    /* ================= COUNTDOWN ================= */
    function startOtpCountdown() {
        if (interval) clearInterval(interval);

        interval = setInterval(() => {
            const now = Date.now();
            let distance = expiresAt - now;

            if (distance <= 0) {
                clearInterval(interval);
                timerEl.innerText = '00:00';
                verifyOtpForm.querySelector('button[type="submit"]').disabled = true;
                return;
            }

            const minutes = Math.floor(distance / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            timerEl.innerText =
                `${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;
        }, 1000);
    }

    startOtpCountdown();

});
</script>

@endsection
