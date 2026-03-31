@extends('frontend.conference.template3.app')

@section('content')
<style>
    .registration-grid{
    display:flex;
    flex-wrap:wrap;
    gap:20px;
}

.registration-card{
    flex:0 0 calc(33.333% - 14px);
    background:#ffffff;
    border-radius:10px;
    padding:22px;
    box-shadow:0 10px 28px rgba(0,0,0,0.08);
    transition:all .25s ease;
    position:relative;
}

.registration-card:hover{
    transform:translateY(-4px);
    box-shadow:0 16px 40px rgba(0,0,0,0.12);
}

.registration-success{
    border-left:6px solid #27ae60;
    background:#f4fbf6;
}

.registration-danger{
    border-left:6px solid #e74c3c;
    background:#fff6f6;
}

.registration-header{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    margin-bottom:12px;
}

.registration-conference{
    font-size:14px;
    font-weight:700;
    letter-spacing:.5px;
    text-transform:uppercase;
    color:#333;
}

.registration-plan{
    font-size:12px;
    color:#777;
}

.registration-badge{
    font-size:12px;
    font-weight:600;
    padding:5px 10px;
    border-radius:20px;
}

.badge-success{
    background:#27ae60;
    color:#fff;
}

.badge-danger{
    background:#e74c3c;
    color:#fff;
}

.registration-name{
    font-size:18px;
    font-weight:600;
    color:#222;
    margin-bottom:14px;
}

.registration-details{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:10px;
}

.registration-details span{
    display:block;
    font-size:11px;
    color:#888;
}

.registration-details strong{
    display:block;
    font-size:14px;
    color:#333;
}

/* responsive */
@media(max-width:992px){
    .registration-card{
        flex:0 0 calc(50% - 10px);
    }
}

@media(max-width:576px){
    .registration-card{
        flex:0 0 100%;
    }
}
</style>
<section id="section-hero" class="section-dark no-top no-bottom text-light jarallax relative mh-300">
    <img src="{{ asset('conference_templates/template2/images/hero_area/banner_bg.jpg') }}" class="jarallax-img" alt="">
    <div class="gradient-edge-bottom h-50"></div>
    <div class="sw-overlay op-5"></div>
    <div class="abs w-80 bottom-10 z-2 w-100">
        <div class="container">
            <div class="row align-items-center justify-content-between gx-5">
                <div class="col-md-6">
                    <div class="relative wow mask-right">
                        <div class="text-start">
                            <h1 class="fs-80 text-uppercase fs-sm-10vw mb-0 lh-1">Verify Registration</h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="section-dark text-light py-5">
    <div class="container">

        <div class="row justify-content-center mb-5">
            <div class="col-md-6">
                <h2 class="mb-3 text-center">Verify Registration</h2>
                <p class="text-center mb-4">Enter your Transaction ID or Email to view registration details.</p>

                <div class="input-group mb-3">
                    <input type="text" id="searchInput" class="form-control form-control-lg" placeholder="Transaction ID, Email, or Registration ID">
                    <button class="btn btn-primary btn-lg" id="searchBtn">Search</button>
                </div>
            </div>
        </div>

        <div id="registrationResult" class="row justify-content-center">
            <!-- Registration cards will be injected here -->
        </div>

    </div>
</section>
@endsection

@section('script')
<script>
$(document).ready(function () {

    $('#searchBtn').on('click', function (e) {
        e.preventDefault();

        const query = $('#searchInput').val().trim();

        if (!query) {
            alert('Please enter a Transaction ID, Email, or Registration ID');
            return;
        }

        $('#registrationResult').html(
            '<div style="text-align:center;padding:40px;">Searching...</div>'
        );

        fetch("{{ route('registration.search') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: JSON.stringify({ q: query })
        })
        .then(res => res.json())
        .then(data => {

            if (!data.length) {
                $('#registrationResult').html(
                    '<div style="text-align:center;padding:40px;">No registrations found.</div>'
                );
                return;
            }

            let html = '<div class="registration-grid">';

            data.forEach(tx => {
                console.log();

                const isComplete =
                    tx.status === 'Complete' &&
                    tx.registration_status === 'Complete';

                const cardClass = isComplete
                    ? 'registration-success'
                    : 'registration-danger';

                const badgeClass = isComplete
                    ? 'badge-success'
                    : 'badge-danger';

                const badgeText = isComplete
                    ? 'Verified'
                    : 'Incomplete';

                html += `
                <div class="registration-card ${cardClass}">

                    <div class="registration-header">

                        <div>
                            <div class="registration-conference">
                                ${tx.conference_name || 'Conference'}
                            </div>

                            <div class="registration-plan">
                                ${tx.plan_name || 'Registration Plan'}
                            </div>
                        </div>

                        <div class="registration-badge ${badgeClass}">
                            ${badgeText}
                        </div>

                    </div>

                    <div class="registration-name">
                        ${tx.name || 'Participant'}
                    </div>

                    <div class="registration-details">

                        <div>
                            <span>Transaction ID</span>
                            <strong>${tx.transid}</strong>
                        </div>

                        <div>
                            <span>Email</span>
                            <strong>${tx.email || '-'}</strong>
                        </div>

                        
                        <div>
                            <span>Payment Status</span>
                            <strong>${tx.status}</strong>
                        </div>

                        <div>
                            <span>Registration</span>
                            <strong>${tx.registration_status}</strong>
                        </div>

                        <div>
                            <span>Registered</span>
                            <strong>${tx.created_at}</strong>
                        </div>

                    </div>

                </div>
                `;
            });

            html += '</div>';

            $('#registrationResult').html(html);

        })
        .catch(err => {

            console.error(err);

            $('#registrationResult').html(
                '<div style="text-align:center;padding:40px;color:red;">An error occurred. Try again.</div>'
            );
        });

    });

});
</script>
@endsection
