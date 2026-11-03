@extends('frontend.site.layout')
@section('title', 'Verify reset code — GOFAMINT Students’ Fellowship')
@section('description', 'Verify your one-time code to securely reset your GSF stakeholder password.')
@section('mainClass', 'public-page auth-page stakeholder-auth-page')
@section('activeSection', 'auth')

@section('content')
<section class="auth-stage auth-stage--compact">
    <div class="site-container auth-stage__container">
        <div class="auth-card auth-card--compact">
            <aside class="auth-card__story auth-card__story--stakeholder">
                <span class="auth-card__mark"><img src="{{ asset('frontend/img/logo.png') }}" alt="GSF"></span>
                <div class="eyebrow eyebrow--light"><span></span> Stakeholder account recovery</div>
                <h1>Verify it’s<br><em>really you.</em></h1>
                <p>Enter the one-time code sent to your registered email. The code can only be used once.</p>
            </aside>
            <div class="auth-card__form-wrap">
                <div class="auth-form-heading"><span>One-time verification</span><h2>{{ $title }}</h2><p>{{ $text }}</p></div>
                <p class="auth-otp-expiry">
                    @if(!$isExpired)
                        Code expires in <strong id="otpTimer"></strong>
                    @else
                        This code has expired. Request a new one below <strong id="otpTimer">00:00</strong>.
                    @endif
                </p>
                <div id="otpAlert" class="alert d-none" role="alert"></div>
                <form id="verifyOtpForm" class="auth-form otp-form">
                    @csrf
                    <div id="otpErrors" class="alert alert-danger alert-dismissible fade" role="alert" style="display:none;">
                        <div class="auth-alert-row"><span class="alert-text"></span><button type="button" class="btn-close" aria-label="Close">×</button></div>
                    </div>
                    <div class="auth-field"><label for="otpInput">One-time password</label><input type="text" class="auth-otp-input" id="otpInput" name="otp" inputmode="numeric" autocomplete="one-time-code" placeholder="Enter 6-digit code" maxlength="6" required></div>
                    <button type="submit" id="verifyOtpBtn" class="button button--blue auth-submit"><span class="btn-text">Verify code</span><span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span><span class="otp-submit-arrow" aria-hidden="true">→</span></button>
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <input type="hidden" name="type" value="{{ $otp->type }}">
                    <input type="hidden" name="otp_id" value="{{ $otp->id }}">
                    <p class="auth-otp-resend">Didn’t receive the code? <a href="javascript:void(0);" id="resendOtpLink" data-expired="{{ $isExpired ? 1 : 0 }}">{{ $isExpired ? 'Resend a new code' : 'Resend code' }}</a></p>
                </form>
            </div>
        </div>
        <a class="auth-back-link" href="{{ route('stakeholders.loginpage') }}">← Back to stakeholder sign in</a>
    </div>
</section>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {

    const verifyOtpForm = document.getElementById('verifyOtpForm');
    const otpErrors = document.getElementById('otpErrors');
    const resendOtpLink = document.getElementById('resendOtpLink');
    const timerEl = document.getElementById('otpTimer');
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');

    const otpUserId = {{ $user->id }};
    const otpType = @json($otp->type);

    let expiresAt = {{ $expiresAt ?? 0 }} * 1000; // initial expiry timestamp in ms
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

    otpErrors.querySelector('.btn-close').addEventListener('click', clearError);

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
@endpush

