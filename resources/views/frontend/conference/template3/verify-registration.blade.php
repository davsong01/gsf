@extends('frontend.conference.template3.app')

@section('content')
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
                <p class="text-center mb-4">Enter your Transaction ID, Email, or Registration ID to view registration details.</p>

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
$(document).ready(function() {
    $('#searchBtn').on('click', async function(e) {
        e.preventDefault();
        const query = $('#searchInput').val().trim();

        if (!query) {
            alert('Please enter a Transaction ID, Email, or Registration ID');
            return;
        }

        $('#registrationResult').html('<div class="text-center my-5">Searching...</div>');

        try {
            const res = await fetch("{{ route('registration.search') }}?q=" + encodeURIComponent(query));
            const data = await res.json();

            if (!data.length) {
                $('#registrationResult').html('<div class="text-center my-5">No registrations found.</div>');
                return;
            }

            let html = '';
            data.forEach(tx => {
                html += `
                <div class="col-md-8 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">${tx.name || 'Participant'}</h5>
                            <p class="mb-1"><strong>Transaction ID:</strong> ${tx.transid}</p>
                            <p class="mb-1"><strong>Email:</strong> ${tx.email || '-'}</p>
                            <p class="mb-1"><strong>Phone:</strong> ${tx.phone || '-'}</p>
                            <p class="mb-1"><strong>Conference:</strong> ${tx.conference_name || 'N/A'}</p>
                            <p class="mb-1"><strong>Plan:</strong> ${tx.plan_name || 'N/A'}</p>
                            <p class="mb-1"><strong>Status:</strong>
                                <span class="badge ${tx.registration_status === 'Complete' ? 'bg-success' : 'bg-warning text-dark'}">
                                    ${tx.registration_status}
                                </span>
                            </p>
                            <p class="mb-1"><strong>Registered At:</strong> ${tx.created_at}</p>
                        </div>
                    </div>
                </div>`;
            });

            $('#registrationResult').html(html);

        } catch(err) {
            console.error(err);
            $('#registrationResult').html('<div class="text-center my-5 text-danger">An error occurred. Try again.</div>');
        }
    });
});
</script>
@endsection
