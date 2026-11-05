@extends('layouts.stakeholderdashboard')

@php($isEdit = isset($user))
@php($isAlumniFlow = request()->routeIs('stakeholders.alumni.create'))
@section('title', $isEdit ? 'Edit Member' : ($isAlumniFlow ? 'Add Alumnus' : 'Add Member'))
@section('item')<li class="breadcrumb-item"><a href="{{ route($isAlumniFlow ? 'stakeholders.alumni.index' : 'stakeholders.users.index') }}">{{ $isAlumniFlow ? 'Chapter Alumni' : 'Chapter Members' }}</a></li>@endsection
@section('active')<li class="breadcrumb-item">{{ $isEdit ? 'Edit Member' : ($isAlumniFlow ? 'Add Alumnus' : 'Add Member') }}</li>@endsection
@section('extra_styles')
<link rel="stylesheet" href="{{ versionedAsset('assets/css/stakeholder-members.css') }}">
@endsection

@section('content')
<div class="content-body members-workspace">
    <header class="members-hero members-hero--form">
        <div class="members-hero__copy">
            <span class="members-eyebrow"><i class="fa fa-user-plus" aria-hidden="true"></i> Campus directory</span>
            <h1>{{ $isEdit ? 'Edit member profile' : ($isAlumniFlow ? 'Add an alumnus' : 'Add a member') }}</h1>
            <p>{{ $isEdit ? 'Keep this member’s contact, study and fellowship details up to date.' : 'Create a profile for a student or alumnus in your campus fellowship.' }}</p>
        </div>
        <div class="members-hero__actions"><a class="members-button members-button--light" href="{{ route($isAlumniFlow ? 'stakeholders.alumni.index' : 'stakeholders.users.index') }}"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to {{ $isAlumniFlow ? 'alumni' : 'members' }}</a></div>
    </header>

    @if($errors->any())
        <div class="members-alert" role="alert"><strong>Please check the form.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    @if($isEdit)
    <section class="members-profile" aria-label="Current member overview">
        <img src="{{ asset($user->passport ?: 'frontend/passports/avatar.jpg') }}" alt="" class="members-profile__photo">
        <div class="members-profile__copy"><span class="members-section-label">Member profile</span><h2>{{ $user->name }}</h2><p>{{ $user->campus?->name ?: 'Campus not assigned' }} · {{ $user->is_graduated ? 'Alumni' : 'Student' }}</p><div class="members-profile__meta"><span><i class="fa fa-id-card-o" aria-hidden="true"></i> {{ $user->family_id ?: 'No login ID' }}</span><span><i class="fa fa-circle" aria-hidden="true"></i> {{ ucfirst($user->status ?: 'Inactive') }}</span></div></div>
        @if(filled($user->slug))<a class="members-button members-button--outline" href="{{ route('user.single', $user->slug) }}" target="_blank" rel="noopener"><i class="fa fa-external-link" aria-hidden="true"></i> Public profile</a>@endif
    </section>
    @endif

    <form class="members-editor" action="{{ $isEdit ? route('stakeholders.users.update', $user->id) : route('stakeholders.users.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($isEdit)@method('PATCH')@endif

        <section class="members-form-panel" aria-labelledby="member-identity-heading">
            <div class="members-form-panel__head"><span class="members-form-panel__icon"><i class="fa fa-address-card-o" aria-hidden="true"></i></span><div><span class="members-section-label">01 · The essentials</span><h2 id="member-identity-heading">Personal &amp; contact details</h2><p>How we identify and reach this member.</p></div></div>
            <div class="members-form-grid">
                <div class="members-field"><label for="name">Full name <b>*</b></label><input id="name" type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}" placeholder="First name and surname" required></div>
                <div class="members-field"><label for="email">Email address <b>*</b></label><input id="email" type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}" placeholder="name@example.com" required></div>
                <div class="members-field"><label for="phone">Phone number <b>*</b></label><input id="phone" type="tel" name="phone" class="form-control" value="{{ old('phone', $user->phone ?? '') }}" placeholder="0800 000 0000" required></div>
                <div class="members-field"><label for="gender">Gender <b>*</b></label><select id="gender" name="gender" class="form-control" required><option value="">Select gender</option><option value="Male" @selected(old('gender', $user->gender ?? '') === 'Male')>Male</option><option value="Female" @selected(old('gender', $user->gender ?? '') === 'Female')>Female</option></select></div>
                <div class="members-field"><label for="dob">Date of birth</label><input id="dob" type="date" name="dob" class="form-control" value="{{ old('dob', $user->dob ?? '') }}"></div>
                <div class="members-field"><label for="passport">{{ $isEdit ? 'Change profile photo' : 'Profile photo' }}</label><input id="passport" type="file" name="passport" class="form-control" accept="image/*"><small>Choose an image to help others recognize this member.</small></div>
            </div>
        </section>

        <section class="members-form-panel" aria-labelledby="member-study-heading">
            <div class="members-form-panel__head"><span class="members-form-panel__icon members-form-panel__icon--gold"><i class="fa fa-graduation-cap" aria-hidden="true"></i></span><div><span class="members-section-label">02 · Campus journey</span><h2 id="member-study-heading">Studies &amp; fellowship</h2><p>Record academic status and any campus responsibility.</p></div></div>
            <div class="members-form-grid">
                <div class="members-field"><label for="is_graduated">Member category <b>*</b></label><select id="is_graduated" name="is_graduated" class="form-control" required><option value="">Select category</option><option value="0" @selected((string)old('is_graduated', $user->is_graduated ?? ($isAlumniFlow ? '1' : '')) === '0')>Current student</option><option value="1" @selected((string)old('is_graduated', $user->is_graduated ?? ($isAlumniFlow ? '1' : '')) === '1')>Alumnus</option></select></div>
                <div class="members-field"><label for="designation_id">Campus designation</label><select id="designation_id" name="designation_id" class="form-control"><option value="">No designation</option>@foreach($campusDesignations as $designation)<option value="{{ $designation->id }}" @selected(old('designation_id', $user->designation_id ?? '') == $designation->id)>{{ $designation->name }}</option>@endforeach</select></div>
                <div class="members-field"><label for="portfolio_session">Portfolio session</label><select id="portfolio_session" name="portfolio_session" class="form-control"><option value="">Select session</option>@foreach($sessions as $value)<option value="{{ $value.'/'.($value + 1) }}" @selected(old('portfolio_session', $user->portfolio_session ?? '') === $value.'/'.($value + 1))>{{ $value.'/'.($value + 1) }}</option>@endforeach</select></div>
                <div class="members-field"><label for="program">Program <b>*</b></label><select id="program" name="program" class="form-control" required>@foreach(['PHD','PGD','MSC','BSC','HND','OND','NCE'] as $program)<option value="{{ $program }}" @selected(old('program', $user->program ?? '') === $program)>{{ $program }}</option>@endforeach</select></div>
                <div class="members-field"><label for="course">Course of study</label><select id="course" name="course" class="form-control"><option value="">Select course</option>@foreach(coursesOfStudy() as $course)<option value="{{ $course }}" @selected(old('course', $user->course ?? '') === $course)>{{ $course }}</option>@endforeach</select></div>
                <div class="members-field"><label for="course_duration">Course duration (years) <b>*</b></label><input id="course_duration" type="number" min="1" name="course_duration" class="form-control" value="{{ old('course_duration', $user->course_duration ?? '') }}" placeholder="e.g. 4" required></div>
                <div class="members-field"><label for="matric_year">Matriculation session</label><select id="matric_year" name="matric_year" class="form-control"><option value="">Select session</option>@foreach($sessions as $value)<option value="{{ $value.'/'.($value + 1) }}" @selected(old('matric_year', $user->matric_year ?? '') === $value.'/'.($value + 1))>{{ $value.'/'.($value + 1) }}</option>@endforeach</select></div>
                <div class="members-field"><label for="graduation_year">Graduation session</label><select id="graduation_year" name="graduation_year" class="form-control"><option value="">Select session</option>@foreach($sessions as $value)<option value="{{ $value.'/'.($value + 1) }}" @selected(old('graduation_year', $user->graduation_year ?? '') === $value.'/'.($value + 1))>{{ $value.'/'.($value + 1) }}</option>@endforeach</select></div>
            </div>
        </section>

        <section class="members-form-panel" aria-labelledby="member-presence-heading">
            <div class="members-form-panel__head"><span class="members-form-panel__icon members-form-panel__icon--teal"><i class="fa fa-heart-o" aria-hidden="true"></i></span><div><span class="members-section-label">03 · Profile &amp; access</span><h2 id="member-presence-heading">Directory presence</h2><p>Set portal access and add the details that make this profile useful.</p></div></div>
            <div class="members-form-grid">
                <div class="members-field"><label for="status">Portal status <b>*</b></label><select id="status" name="status" class="form-control" required><option value="">Select status</option><option value="active" @selected(old('status', $user->status ?? '') === 'active')>Active</option><option value="inactive" @selected(old('status', $user->status ?? '') === 'inactive')>Inactive</option></select></div>
                <div class="members-field"><label for="open_to_work">Open to work? <b>*</b></label><select id="open_to_work" name="open_to_work" class="form-control" required><option value="1" @selected((string)old('open_to_work', $user->open_to_work ?? '') === '1')>Yes</option><option value="0" @selected((string)old('open_to_work', $user->open_to_work ?? '') === '0')>No</option></select></div>
                <div class="members-field"><label for="password">{{ $isEdit ? 'New password' : 'Password' }}</label><input id="password" type="password" name="password" class="form-control" autocomplete="new-password" placeholder="Leave blank to use phone number"><small>{{ $isEdit ? 'Leave blank to keep the current password.' : 'If blank, the phone number becomes the initial password.' }}</small></div>
                <div class="members-field"><label for="facebook">Facebook profile</label><input id="facebook" type="url" name="facebook" class="form-control" value="{{ old('facebook', $user->facebook ?? '') }}" placeholder="https://facebook.com/..."></div>
                <div class="members-field"><label for="twitter">X / Twitter profile</label><input id="twitter" type="url" name="twitter" class="form-control" value="{{ old('twitter', $user->twitter ?? '') }}" placeholder="https://x.com/..."></div>
                <div class="members-field members-field--wide"><label for="skills">Skills</label><textarea id="skills" name="skills" class="form-control" rows="4" placeholder="Share relevant skills or interests">{{ old('skills', $user->skills ?? '') }}</textarea></div>
            </div>
        </section>

        <div class="members-savebar"><span><i class="fa fa-info-circle" aria-hidden="true"></i> Fields marked * are required.</span><div><a class="members-button members-button--outline" href="{{ route($isAlumniFlow ? 'stakeholders.alumni.index' : 'stakeholders.users.index') }}">Cancel</a><button class="members-button members-button--submit" type="submit"><i class="fa fa-check" aria-hidden="true"></i> {{ $isEdit ? 'Save changes' : ($isAlumniFlow ? 'Create alumnus' : 'Create member') }}</button></div></div>
    </form>
</div>
@endsection
