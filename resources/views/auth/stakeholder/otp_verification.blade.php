@extends('frontend.spaces.layouts.app')
@section('title', 'Forgot Password')
@section('ogtitle', 'Forgot Password')
@section('ogdescription')

@section('content')
<section class="section section-header bg-primary overlay-primary text-white pb-2" style="margin-top:-50px">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-8 text-center">
                <h1 class="display-2 mb-4">Forgot Password</h1>
                <p class="lead">Enter your email address and we’ll send you a reset code.</p>
            </div>
        </div>
    </div>
</section>

<section class="min-vh-80 d-flex align-items-center" style="margin-top:20px">
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-12 d-flex align-items-center justify-content-center" style="margin-bottom:20px">
                <div class="">

                    @include('includes.alerts')

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
                    <div id="otpAlert" class="alert d-none" role="alert"></div>
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
                        <input type="hidden" name="otp_id" value="{{$otp->id}}">

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
                            <p id=""></p>
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
<script>
document.addEventListener('DOMContentLoaded', () => {

    const verifyOtpForm = document.getElementById('verifyOtpForm');
    const otpErrors = document.getElementById('otpErrors');
    const resendOtpLink = document.getElementById('resendOtpLink');
    const timerEl = document.getElementById('otpTimer');
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');

    const otpUserId = {{ $user->id }};
    const otpType = "{{ $otp->type }}";

    let expiresAt = {{ $expiresAt }} * 1000; // initial expiry timestamp in ms
    let countdownInterval = null;
    let isResending = false;

    /* ====== HELPERS ====== */
    function setLoading(state) {
        verifyOtpBtn.disabled = state;
        verifyOtpBtn.querySelector('.btn-text').classList.toggle('d-none', state);
        verifyOtpBtn.querySelector('.spinner-border').classList.toggle('d-none', !state);
    }

    function showError(message) {
        otpErrors.innerHTML = message;
        otpErrors.style.display = 'block';
        otpErrors.classList.add('show');
    }

    function clearError() {
        otpErrors.style.display = 'none';
        otpErrors.classList.remove('show');
    }

    function showToast(message, type = 'success') {
        const existing = document.querySelector('.toast-msg');
        if (existing) existing.remove();

        const toast = document.createElement('div');
        toast.className = `alert alert-${type} position-fixed top-0 start-50 translate-middle-x mt-3 shadow toast-msg`;
        toast.textContent = message;
        toast.style.zIndex = 9999;
        toast.style.minWidth = '300px';
        toast.style.textAlign = 'center';
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('fade');
            setTimeout(() => toast.remove(), 500);
        }, 4000);
    }

    function enableResend() {
        resendOtpLink.classList.remove('disabled');
        resendOtpLink.style.pointerEvents = 'auto';
        resendOtpLink.innerText = 'Resend OTP';
    }

    function disableResend() {
        resendOtpLink.classList.add('disabled');
        resendOtpLink.style.pointerEvents = 'none';
        resendOtpLink.innerText = 'Resend available after expiry';
    }

    /* ====== COUNTDOWN ====== */
    function startCountdown(expiryTime) {
        if (countdownInterval) clearInterval(countdownInterval);
        disableResend();

        function updateTimer() {
            const now = Date.now();
            const diff = expiryTime - now;

            if (diff <= 0) {
                clearInterval(countdownInterval);
                timerEl.textContent = '00:00';
                enableResend();
                verifyOtpBtn.disabled = true; // prevent OTP submit after expiry
                return;
            }

            const minutes = Math.floor(diff / 60000);
            const seconds = Math.floor((diff % 60000) / 1000);

            timerEl.textContent = `${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;
        }

        updateTimer();
        countdownInterval = setInterval(updateTimer, 1000);
    }

    startCountdown(expiresAt);

    /* ====== VERIFY OTP ====== */
    verifyOtpForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        clearError();
        setLoading(true);

        try {
            const formData = new FormData(verifyOtpForm);
            const res = await fetch("{{ route('stakeholders.verify.otp') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: formData
            });

            const data = await res.json();

            if (data.success) {
                window.location.href = data.redirect_url;
            } else {
                showError(data.errors?.otp?.join('<br>') || data.message || 'Invalid OTP');
                setLoading(false);
            }
        } catch {
            showError('Server error. Please try again.');
            setLoading(false);
        }
    });

    function showAlert(message, type = 'success') {
        const alertBox = document.getElementById('otpAlert');

        alertBox.className = `alert alert-${type}`;
        alertBox.textContent = message;
        alertBox.classList.remove('d-none');

        // auto hide after 5s (optional)
        setTimeout(() => {
            alertBox.classList.add('d-none');
        }, 5000);
    }

    function hideAlert() {
        document.getElementById('otpAlert').classList.add('d-none');
    }

    /* ====== RESEND OTP ====== */
    resendOtpLink.addEventListener('click', async (e) => {
        e.preventDefault();

        if (isResending || resendOtpLink.classList.contains('disabled')) return;

        isResending = true;
        hideAlert();
        setLoading(true);

        try {
            const response = await fetch("{{ route('stakeholders.resend.otp') }}", {
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
            });

            const data = await response.json();
            console.log(data);

            if (!data.success) {
                showAlert(data.message || 'Failed to resend OTP.', 'danger');
                setLoading(false);
                isResending = false;
                return;
            }

            if (!data.expires_at) {
                showAlert('OTP resent but expiry time missing.', 'danger');
                setLoading(false);
                isResending = false;
                return;
            }

            // Success message
            showAlert(data.message || 'OTP resent successfully!', 'success');

            // Update expiry
            expiresAt = data.expires_at * 1000;

            // Update otp_id if present
            if (data.otp_id) {
                const otpInput = document.querySelector('input[name="otp_id"]');
                if (otpInput) otpInput.value = data.otp_id;
            }

            // Restart countdown
            startCountdown(expiresAt);

        } catch (error) {
            showAlert('Failed to resend OTP. Please try again.', 'danger');
        } finally {
            setLoading(false);
            isResending = false;
        }
    });

});
</script>
@endsection

