@extends('layouts.dashboard')

@section('title', 'Conference Registration')
@section('item')
<li class="breadcrumb-item"><a href="{{ route('account') }}">Dashboard</a></li>
<li class="breadcrumb-item"><a href="{{ route('conferencemanagement.index', ['edition' => $edition->id]) }}">My Conferences</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">{{ $edition->conference_theme }}</li>
@endsection

@section('content')
@php
    $member = $payment->user ?: auth()->user();
    $canUpdateProfile = $edition->status === 'active';
    $registrationState = $payment->registration_status ?: $payment->status;
    $customRegistrationFields = $payment->allocationFields->reject(function ($field) {
        return in_array(strtolower($field->key), [
            'name', 'email', 'phone', 'gender', 'participants', 'no_of_participants',
            'hostel', 'hostel_id', 'food', 'food_id', 'service_point', 'service_point_id',
            'hostel_allocation_number', 'service_point_allocation_number',
        ], true);
    });
@endphp
<style>
    .member-conference-detail { max-width:1500px; margin:0 auto; color:#24314a; }
    .member-conference-detail__hero { position:relative; display:flex; align-items:flex-start; justify-content:space-between; gap:22px; overflow:hidden; margin-bottom:20px; padding:28px 31px; border-radius:19px; color:#fff; background:linear-gradient(120deg,#091a3d 0%,#153f78 68%,#2366b1 100%); box-shadow:0 17px 38px rgba(15,39,77,.16); }
    .member-conference-detail__hero::after { content:''; position:absolute; right:-75px; bottom:-120px; width:270px; height:270px; border:1px solid rgba(255,255,255,.14); border-radius:50%; box-shadow:0 0 0 42px rgba(255,255,255,.035),0 0 0 84px rgba(255,255,255,.025); }
    .member-conference-detail__hero > * { position:relative; z-index:1; }
    .member-conference-detail__eyebrow { margin-bottom:8px; color:#e2c46f; font-size:10px; font-weight:850; letter-spacing:.16em; text-transform:uppercase; }
    .member-conference-detail__hero h1 { margin:0 0 6px; color:#fff; font-family:Georgia,serif; font-size:clamp(27px,3.6vw,39px); font-weight:500; letter-spacing:-.035em; }
    .member-conference-detail__date { margin:0; color:rgba(255,255,255,.75); font-size:13px; }
    .member-conference-detail__badges { display:flex; flex-wrap:wrap; gap:7px; margin-top:14px; }
    .member-conference-detail__badge { display:inline-flex; padding:5px 9px; border:1px solid rgba(255,255,255,.18); border-radius:999px; color:#fff; background:rgba(255,255,255,.1); font-size:9px; font-weight:800; text-transform:uppercase; }
    .member-conference-detail__badge.is-success { color:#b9f4d8; background:rgba(20,143,94,.2); }
    .member-conference-detail__tools { display:flex; flex-wrap:wrap; justify-content:flex-end; gap:8px; }
    .member-conference-detail__tools a { padding:9px 12px; border:1px solid rgba(255,255,255,.35); border-radius:8px; color:#fff; background:rgba(255,255,255,.08); font-size:10px; font-weight:750; }
    .member-conference-detail__tools a:hover { color:#12274d; background:#fff; }
    .member-conference-detail__summary { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; margin-bottom:20px; }
    .member-conference-detail__summary > div { min-width:0; padding:15px 17px; border:1px solid #e2e8f0; border-radius:13px; background:#fff; box-shadow:0 7px 20px rgba(15,39,77,.045); }
    .member-conference-detail__summary span,.member-conference-detail__fact span { display:block; margin-bottom:5px; color:#7a879b; font-size:9px; font-weight:850; letter-spacing:.07em; text-transform:uppercase; }
    .member-conference-detail__summary strong,.member-conference-detail__fact strong { display:block; overflow-wrap:anywhere; color:#21324e; font-size:12px; font-weight:750; }
    .member-conference-detail__layout { display:grid; grid-template-columns:minmax(0,1.15fr) minmax(300px,.85fr); gap:18px; align-items:start; }
    .member-conference-panel { overflow:hidden; border:1px solid #e2e8f0; border-radius:16px; background:#fff; box-shadow:0 10px 28px rgba(15,39,77,.055); }
    .member-conference-panel + .member-conference-panel { margin-top:16px; }
    .member-conference-panel__head { padding:18px 21px; border-bottom:1px solid #edf1f5; }
    .member-conference-panel__head h2 { margin:0 0 4px; color:#12274d; font-family:Georgia,serif; font-size:20px; font-weight:600; }
    .member-conference-panel__head p { margin:0; color:#7a879b; font-size:11px; line-height:1.6; }
    .member-conference-panel__body { padding:19px 21px; }
    .member-conference-detail__facts { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:15px 18px; }
    .member-conference-detail__fact { min-width:0; }
    .member-conference-detail__fact--wide { grid-column:1/-1; }
    .member-conference-detail__form { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:15px; }
    .member-conference-detail__field { min-width:0; }
    .member-conference-detail__field--wide { grid-column:1/-1; }
    .member-conference-detail__field label { display:block; margin-bottom:6px; color:#35445e; font-size:11px; font-weight:750; }
    .member-conference-detail__field .form-control { width:100%; height:46px; padding:0 13px; border:1px solid #d8e1ec; border-radius:9px; color:#172641; background:#fbfcfe; box-shadow:none; font-size:13px; line-height:44px; }
    .member-conference-detail__field select.form-control { padding-right:35px; line-height:normal; }
    .member-conference-detail__field input[type=file].form-control { padding:5px; line-height:normal; color:#68768d; }
    .member-conference-detail__field input[type=file]::file-selector-button { height:34px; margin-right:10px; padding:0 12px; border:0; border-radius:6px; color:#fff; background:#174a83; font-weight:700; }
    .member-conference-detail__field .form-control:focus { border-color:#2366b1; background:#fff; box-shadow:0 0 0 3px rgba(35,102,177,.1); }
    .member-conference-detail__field .form-control:disabled { color:#68768d; background:#f1f4f7; }
    .member-conference-detail__hint { margin-top:5px; color:#8490a3; font-size:10px; line-height:1.5; }
    .member-conference-detail__save { grid-column:1/-1; min-height:47px; border:0; border-radius:9px; color:#fff; background:#174a83; font-size:12px; font-weight:850; }
    .member-conference-detail__save:hover { background:#102f5b; }
    .member-conference-detail__locked { padding:12px 14px; border:1px solid #e9e0c9; border-radius:9px; color:#74613a; background:#fffaf0; font-size:11px; line-height:1.6; }
    .member-conference-detail__empty { grid-column:1/-1; margin:0; color:#7a879b; font-size:12px; }
    @media(max-width:1000px) { .member-conference-detail__summary { grid-template-columns:repeat(2,minmax(0,1fr)); }.member-conference-detail__layout { grid-template-columns:1fr; } }
    @media(max-width:767px) { .member-conference-detail__hero { flex-direction:column; padding:23px; border-radius:15px; }.member-conference-detail__tools { justify-content:flex-start; }.member-conference-detail__tools a { flex:1 1 auto; text-align:center; } }
    @media(max-width:520px) { .member-conference-detail__summary { grid-template-columns:1fr 1fr; gap:8px; }.member-conference-detail__summary > div { padding:12px; }.member-conference-detail__form,.member-conference-detail__facts { grid-template-columns:1fr; }.member-conference-detail__field--wide,.member-conference-detail__fact--wide { grid-column:auto; }.member-conference-panel__body { padding:16px; } }
</style>

<main class="content-body member-conference-detail">
    @include('includes.alerts')
    <section class="member-conference-detail__hero">
        <div>
            <div class="member-conference-detail__eyebrow">Your conference registration</div>
            <h1>{{ $edition->conference_theme }}</h1>
            <p class="member-conference-detail__date">{{ $edition->start_date ? formatDates($edition->start_date, $edition->end_date) : 'Conference dates to be announced' }}</p>
            <div class="member-conference-detail__badges">
                <span class="member-conference-detail__badge {{ $edition->status === 'active' ? 'is-success' : '' }}">{{ ucfirst($edition->status) }} conference</span>
                <span class="member-conference-detail__badge">{{ $payment->level ?: 'Participant' }}@if($payment->registration_user_type && strtolower($payment->registration_user_type) !== 'participant') · {{ ucfirst($payment->registration_user_type) }}@endif</span>
                <span class="member-conference-detail__badge {{ $registrationState === 'Complete' ? 'is-success' : '' }}">{{ $registrationState }}</span>
            </div>
        </div>
        <div class="member-conference-detail__tools">
            <a href="{{ route('conferencemanagement.index', ['edition' => $edition->id]) }}"><i class="fa fa-arrow-left mr-1"></i> My Conferences</a>
            @if($payment->registration_status === 'Complete' && $payment->hostel && $payment->food && !empty($edition->template_settings))
                <a href="{{ route('participants.card', ['id' => $payment->id, 'edition' => $edition->id]) }}"><i class="fa fa-ticket mr-1"></i> View badge</a>
            @endif
            @if($edition->material && $edition->material->isNotEmpty())
                <a href="{{ route('materials.index', ['edition' => $edition->id, 'payment_id' => $payment->id]) }}"><i class="fa fa-file-text-o mr-1"></i> Materials</a>
            @endif
        </div>
    </section>

    <section class="member-conference-detail__summary" aria-label="Registration summary">
        <div><span>Member login ID</span><strong>{{ $member->family_id ?: '—' }}</strong></div>
        <div><span>Registration plan</span><strong>{{ $payment->conferenceplan->title ?? '—' }}</strong></div>
        <div><span>Transaction reference</span><strong>{{ $payment->transid ?: '—' }}</strong></div>
        <div><span>Amount paid</span><strong>{!! currency_symbol() !!}{{ number_format((float) $payment->total_amount ?: (float) $payment->amount_paid, 2) }}</strong></div>
    </section>

    <div class="member-conference-detail__layout">
        <section class="member-conference-panel">
            <header class="member-conference-panel__head"><h2>Personal details</h2><p>Update the name, phone number and gender used for this registration. Your email address appears under Registration contact.</p></header>
            <div class="member-conference-panel__body">
                @if($canUpdateProfile)
                    <form action="{{ route('conferencemanagement.update', ['conferencemanagement' => $payment->id, 'edition' => $edition->id]) }}" method="POST" enctype="multipart/form-data" class="member-conference-detail__form">
                        @csrf
                        @method('PATCH')
                        <div class="member-conference-detail__field">
                            <label for="registration-name">Full name</label>
                            <input class="form-control @error('registration_fields.name') is-invalid @enderror" type="text" id="registration-name" name="registration_fields[name]" value="{{ old('registration_fields.name', $member->name) }}" autocomplete="name" required>
                        </div>
                        <div class="member-conference-detail__field">
                            <label for="registration-phone">Phone number</label>
                            <input class="form-control @error('registration_fields.phone') is-invalid @enderror" type="tel" id="registration-phone" name="registration_fields[phone]" value="{{ old('registration_fields.phone', $member->phone) }}" autocomplete="tel" required>
                        </div>
                        <div class="member-conference-detail__field">
                            <label for="registration-gender">Gender</label>
                            <select class="form-control" id="registration-gender" name="registration_fields[gender]" required>
                                <option value="">Select gender</option>
                                <option value="Male" @selected(old('registration_fields.gender', $member->gender) === 'Male')>Male</option>
                                <option value="Female" @selected(old('registration_fields.gender', $member->gender) === 'Female')>Female</option>
                            </select>
                        </div>
                        <div class="member-conference-detail__field">
                            <label for="registration-passport">Update passport photo</label>
                            <input class="form-control" type="file" accept="image/jpeg,image/png" name="passport" id="registration-passport">
                            <div class="member-conference-detail__hint">JPG or PNG image, up to 200 KB.</div>
                        </div>
                        <div class="member-conference-detail__field member-conference-detail__field--wide">
                            <label for="registration-password">Change password <span class="text-muted font-weight-normal">(optional)</span></label>
                            <input class="form-control" type="password" name="password" id="registration-password" autocomplete="new-password" placeholder="Leave blank to keep your current password">
                        </div>
                        <button class="member-conference-detail__save" type="submit"><i class="fa fa-check mr-1"></i> Save personal details</button>
                    </form>
                @else
                    <div class="member-conference-detail__facts">
                        <div class="member-conference-detail__fact"><span>Name</span><strong>{{ $member->name }}</strong></div>
                        <div class="member-conference-detail__fact"><span>Email address</span><strong>{{ $member->email ?: '—' }}</strong></div>
                        <div class="member-conference-detail__fact"><span>Phone number</span><strong>{{ $member->phone ?: '—' }}</strong></div>
                        <div class="member-conference-detail__fact"><span>Gender</span><strong>{{ $member->gender ?: '—' }}</strong></div>
                        <div class="member-conference-detail__locked member-conference-detail__fact--wide">This conference registration is closed for editing. You can still update your general member profile from the <a href="{{ route('member.profile') }}">Profile page</a>.</div>
                    </div>
                @endif
            </div>
        </section>

        <div>
            <section class="member-conference-panel">
                <header class="member-conference-panel__head"><h2>Conference arrangements</h2><p>Allocation and registration information recorded for this conference.</p></header>
                <div class="member-conference-panel__body member-conference-detail__facts">
                    <div class="member-conference-detail__fact"><span>Accommodation</span><strong>{{ $payment->hostel->name ?? 'Not assigned' }}</strong></div>
                    <div class="member-conference-detail__fact"><span>Accommodation number</span><strong>{{ $payment->hostel_allocation_number ?: 'Not assigned' }}</strong></div>
                    <div class="member-conference-detail__fact"><span>Service point</span><strong>{{ $payment->food->name ?? 'Not assigned' }}</strong></div>
                    <div class="member-conference-detail__fact"><span>Service point number</span><strong>{{ $payment->service_point_allocation_number ?: 'Not assigned' }}</strong></div>
                    @foreach($customRegistrationFields as $field)
                        @php
                            $fieldKey = strtolower($field->key);
                            $fieldLabel = \Illuminate\Support\Str::headline(preg_replace('/_id$/', '', $fieldKey));
                            $fieldValue = $field->value;
                            if (in_array($fieldKey, ['chapter', 'chapter_id'], true)) {
                                $fieldValue = $chapters->firstWhere('id', $field->value)?->name ?: 'Not assigned';
                                $fieldLabel = 'Campus chapter';
                            }
                        @endphp
                        <div class="member-conference-detail__fact"><span>{{ $fieldLabel }}</span><strong>{{ $fieldValue ?: '—' }}</strong></div>
                    @endforeach
                </div>
            </section>

            <section class="member-conference-panel">
                <header class="member-conference-panel__head"><h2>Registration contact</h2><p>Details used for conference updates and receipts.</p></header>
                <div class="member-conference-panel__body member-conference-detail__facts">
                    <div class="member-conference-detail__fact"><span>Email</span><strong>{{ $payment->email ?: $member->email ?: '—' }}</strong></div>
                    <div class="member-conference-detail__fact"><span>Registered</span><strong>{{ $payment->created_at?->format('d M Y') ?: '—' }}</strong></div>
                    @if($payment->uploaded_by)<div class="member-conference-detail__fact member-conference-detail__fact--wide"><span>Registered by</span><strong>{{ $payment->moderator->name ?? 'Conference team' }}</strong></div>@endif
                </div>
            </section>
        </div>
    </div>
</main>
@endsection
