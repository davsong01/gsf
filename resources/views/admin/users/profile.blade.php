@extends('layouts.dashboard')

@section('title', 'My Profile')

@section('item')
<li class="breadcrumb-item">
    <a href="/account">Dashboard</a>
</li>
@endsection

@section('active')
<li class="breadcrumb-item">My Profile</li>
@endsection

@section('content')
<style>
    .admin-profile-shell { position: relative; max-width: 1500px; margin: 0 auto; }

    .admin-profile-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1.35rem;
        background: linear-gradient(120deg, #091a3d 0%, #153f78 67%, #2366b1 100%);
        color: #fff;
        box-shadow: 0 18px 45px rgba(15, 23, 42, 0.18);
    }

    .admin-profile-hero::after {
        content: '';
        position: absolute;
        inset: auto -4rem -5rem auto;
        width: 16rem;
        height: 16rem;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.18) 0%, rgba(255, 255, 255, 0) 68%);
        pointer-events: none;
    }

    .admin-profile-avatar {
        width: 6rem;
        height: 6rem;
        flex: 0 0 6rem;
        border-radius: 50%;
        overflow: hidden;
        border: 3px solid rgba(255, 255, 255, 0.25);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.18);
        background: rgba(255, 255, 255, 0.08);
    }

    .admin-profile-section {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1.25rem;
        background: #fff;
        box-shadow: 0 12px 35px rgba(15, 23, 42, 0.06);
    }

    .admin-profile-section .section-title {
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #64748b;
        margin-bottom: 1rem;
    }

    .admin-profile-field label {
        font-size: 0.85rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.5rem;
        display: block;
    }

    .admin-profile-field { margin-bottom: 18px; }

    .admin-profile-field .form-control {
        width: 100%;
        height: 50px;
        padding: 0 15px;
        border: 1px solid #d8e1ec;
        border-radius: 0.8rem;
        color: #172641;
        background-color: #fbfcfe;
        box-shadow: none;
        font-size: 0.95rem;
        line-height: 48px;
        transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
    }

    .admin-profile-field select.form-control {
        padding-right: 42px;
        line-height: normal;
        appearance: none;
        -webkit-appearance: none;
        background-image: linear-gradient(45deg, transparent 50%, #64748b 50%), linear-gradient(135deg, #64748b 50%, transparent 50%);
        background-position: calc(100% - 19px) 22px, calc(100% - 14px) 22px;
        background-size: 5px 5px, 5px 5px;
        background-repeat: no-repeat;
    }

    .admin-profile-field input[type="file"].form-control {
        padding: 6px;
        line-height: normal;
        color: #64748b;
    }

    .admin-profile-field input[type="file"]::file-selector-button {
        height: 36px;
        margin-right: 12px;
        padding: 0 15px;
        border: 0;
        border-radius: 0.55rem;
        color: #fff;
        background: #173f78;
        font-weight: 700;
        cursor: pointer;
    }

    .admin-profile-field .form-control:focus {
        border-color: #2366b1;
        background-color: #fff;
        box-shadow: 0 0 0 0.2rem rgba(35, 102, 177, 0.12);
    }

    .admin-profile-field textarea.form-control { height: 120px; padding-top: 13px; line-height: 1.6; resize: vertical; }
    .admin-profile-info { min-height: 50px; padding: 12px 15px; border: 1px solid #e1e7ef; border-radius: .8rem; color: #475569; background: #f5f7fa; font-size: .92rem; }
    .admin-profile-switches { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 12px; }
    .admin-profile-switch { display: flex; align-items: flex-start; gap: 10px; padding: 14px; border: 1px solid #dfe6ef; border-radius: .8rem; background: #fbfcfe; cursor: pointer; }
    .admin-profile-switch input { margin-top: 3px; accent-color: #153f78; }
    .admin-profile-switch strong { display: block; color: #24314a; font-size: .85rem; }
    .admin-profile-switch small { display: block; margin-top: 3px; color: #7a879b; line-height: 1.45; }

    .admin-profile-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border-radius: 999px;
        padding: 0.35rem 0.7rem;
        font-size: 0.78rem;
        font-weight: 700;
        background: rgba(255, 255, 255, 0.12);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.14);
    }

    .admin-profile-help {
        color: #64748b;
        font-size: 0.85rem;
    }

    .admin-profile-heading { color: #12274d; font-family: Georgia, serif; font-size: 1.65rem; }
    .admin-profile-save { min-height: 50px; border: 0; border-radius: .8rem; background: #123f78; font-size: .95rem; font-weight: 800; box-shadow: 0 8px 20px rgba(18,63,120,.18); }
    .admin-profile-save:hover { background: #0b2d5b; }
    .admin-profile-public { border: 1px solid rgba(255,255,255,.55); color: #fff; background: rgba(255,255,255,.1); }
    .admin-profile-public:hover { color: #12274d; background: #fff; }
    @media (max-width: 767px) {
        .admin-profile-hero { border-radius: 1rem; }
        .admin-profile-avatar { width: 4.75rem; height: 4.75rem; flex-basis: 4.75rem; }
        .admin-profile-hero h3 { font-size: 1.45rem; }
        .admin-profile-switches { grid-template-columns: 1fr; }
    }
</style>

<div class="content-body">
    <section class="admin-profile-shell">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="admin-profile-hero p-4">
                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4 position-relative">
                        <div class="d-flex align-items-center gap-3">
                            <div class="admin-profile-avatar">
                                {!! renderAvatar($user, 160, 'w-100 h-100') !!}
                            </div>
                            <div>
                                <div class="admin-profile-badge mb-2">
                                    <i class="fa fa-user-circle"></i>
                                    Member Profile
                                </div>
                                <h3 class="mb-1 text-white">{{ $user->name }}</h3>
                                <div class="text-white-50">{{ $user->email }}</div>
                                <div class="mt-2 d-flex flex-wrap gap-2">
                                    <span class="badge bg-light text-dark">{{ $user->rolename }}</span>
                                    @if($user->campus)
                                        <span class="badge bg-light text-dark">GSF, {{ $user->campus->name }}</span>
                                    @endif
                                    @if($user->designation)
                                        <span class="badge bg-light text-dark">{{ $user->designation->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('user.single', $user->slug) }}" target="_blank" class="btn btn-sm admin-profile-public">
                                <i class="fa fa-eye me-1"></i> View Public Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12">
                <div class="admin-profile-section p-4">
                    <div class="d-flex align-items-start justify-content-between flex-wrap gap-2 mb-4">
                        <div>
                            <h4 class="mb-1 admin-profile-heading">My Profile</h4>
                            <div class="admin-profile-help">Keep your member details and profile photo up to date.</div>
                        </div>
                    </div>

                    @include('includes.alerts')

                    <form action="{{ route('users.profile.save', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="row g-3">
                            <div class="col-12">
                                <div class="admin-profile-section p-3 p-lg-4">
                                    <div class="section-title">Personal Details</div>
                                    <div class="row g-3">
                                        <div class="col-md-6 admin-profile-field">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" placeholder="Enter name">
                                        </div>
                                        <div class="col-md-6 admin-profile-field">
                                            <label for="email">Email</label>
                                            <input type="email" id="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                                        </div>
                                        <div class="col-md-6 admin-profile-field">
                                            <label for="phone">Phone</label>
                                            <input type="tel" id="phone" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" required>
                                        </div>
                                        <div class="col-md-6 admin-profile-field">
                                            <label for="gender">Gender</label>
                                            <select class="form-control" name="gender" id="gender" required>
                                                <option value="Male" {{ $user->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ $user->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 admin-profile-field">
                                            <label for="dob">Date of Birth</label>
                                            <input type="date" id="dob" name="dob" class="form-control" value="{{ old('dob', $user->dob) }}">
                                        </div>
                                        <div class="col-md-6 admin-profile-field">
                                            <label for="passport">Change Passport</label>
                                            <input type="file" accept="image/*" class="form-control" name="passport" id="passport">
                                        </div>
                                        <div class="col-md-6 admin-profile-field">
                                            <label for="password">Password</label>
                                            <small class="text-muted d-block mb-1">Leave blank to keep the current password.</small>
                                            <input type="password" class="form-control" name="password" id="password" value="{{ old('password') }}" placeholder="Enter a new password">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="admin-profile-section p-3 p-lg-4">
                                    <div class="section-title">GSF Membership</div>
                                    <div class="row">
                                        <div class="col-md-6 admin-profile-field"><label>Campus chapter</label><div class="admin-profile-info">{{ $user->campus->name ?? 'Not assigned' }}</div></div>
                                        <div class="col-md-6 admin-profile-field"><label>Current designation</label><div class="admin-profile-info">{{ $user->designation->name ?? $user->rolename }}</div></div>
                                    </div>
                                    <div class="admin-profile-help">Contact your chapter administrator if your chapter or official designation needs to change.</div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="admin-profile-section p-3 p-lg-4">
                                    <div class="section-title">Academic and Career Details</div>
                                    <div class="row">
                                        <div class="col-md-6 admin-profile-field">
                                            <label for="is_graduated">Membership stage</label>
                                            <select class="form-control" name="is_graduated" id="is_graduated">
                                                <option value="0" @selected((string) old('is_graduated', $user->is_graduated) === '0')>Student</option>
                                                <option value="1" @selected((string) old('is_graduated', $user->is_graduated) === '1')>Alumni</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 admin-profile-field">
                                            <label for="open_to_work">Career availability</label>
                                            <select class="form-control" name="open_to_work" id="open_to_work">
                                                <option value="0" @selected((string) old('open_to_work', $user->open_to_work) === '0')>Not currently open to work</option>
                                                <option value="1" @selected((string) old('open_to_work', $user->open_to_work) === '1')>Open to work</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6 admin-profile-field">
                                            <label for="program">Programme type</label>
                                            <select class="form-control" name="program" id="program">
                                                <option value="">Select programme</option>
                                                @foreach(['PHD','PGD','MSC','BSC','HND','OND','NCE'] as $program)
                                                    <option value="{{ $program }}" @selected(old('program', $user->program) === $program)>{{ $program }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6 admin-profile-field">
                                            <label for="course">Course of study</label>
                                            <select class="form-control" name="course" id="course">
                                                <option value="">Select course</option>
                                                @foreach(coursesOfStudy() as $course)
                                                    <option value="{{ $course }}" @selected(old('course', $user->course) === $course)>{{ $course }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4 admin-profile-field"><label for="course_duration">Course duration</label><input class="form-control" type="number" min="1" max="15" id="course_duration" name="course_duration" value="{{ old('course_duration', $user->course_duration) }}" placeholder="Years"></div>
                                        <div class="col-md-4 admin-profile-field">
                                            <label for="matric_year">Matriculation session</label>
                                            <select class="form-control" name="matric_year" id="matric_year"><option value="">Select session</option>@foreach($sessions as $value)<option value="{{ $value.'/'.($value + 1) }}" @selected(old('matric_year', $user->matric_year) === $value.'/'.($value + 1))>{{ $value.'/'.($value + 1) }}</option>@endforeach</select>
                                        </div>
                                        <div class="col-md-4 admin-profile-field">
                                            <label for="graduation_year">Graduation session</label>
                                            <select class="form-control" name="graduation_year" id="graduation_year"><option value="">Select session</option>@foreach($sessions as $value)<option value="{{ $value.'/'.($value + 1) }}" @selected(old('graduation_year', $user->graduation_year) === $value.'/'.($value + 1))>{{ $value.'/'.($value + 1) }}</option>@endforeach</select>
                                        </div>
                                        <div class="col-12 admin-profile-field"><label for="motto">My motto</label><input class="form-control" type="text" name="motto" id="motto" maxlength="500" value="{{ old('motto', $user->motto) }}" placeholder="A phrase or principle that guides you"></div>
                                        <div class="col-12 admin-profile-field"><label for="skills">Skills and professional interests</label><textarea class="form-control" name="skills" id="skills" placeholder="Add your skills, separated by commas">{{ old('skills', $user->skills) }}</textarea></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="admin-profile-section p-3 p-lg-4">
                                    <div class="section-title">Public Directory and Social Links</div>
                                    <div class="row">
                                        <div class="col-md-6 admin-profile-field"><label for="facebook">Facebook profile</label><input class="form-control" type="url" id="facebook" name="facebook" value="{{ old('facebook', $user->facebook) }}" placeholder="https://facebook.com/your-profile"></div>
                                        <div class="col-md-6 admin-profile-field"><label for="twitter">X / Twitter profile</label><input class="form-control" type="url" id="twitter" name="twitter" value="{{ old('twitter', $user->twitter) }}" placeholder="https://x.com/your-handle"></div>
                                    </div>
                                    <div class="admin-profile-switches">
                                        <label class="admin-profile-switch"><input type="checkbox" name="show_email" value="1" @checked(old('show_email', $user->show_email))><span><strong>Show my email publicly</strong><small>Allow members to contact you by email from your directory profile.</small></span></label>
                                        <label class="admin-profile-switch"><input type="checkbox" name="show_phone" value="1" @checked(old('show_phone', $user->show_phone))><span><strong>Show my phone publicly</strong><small>Allow members to see your phone number on your directory profile.</small></span></label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <button class="btn btn-primary w-100 admin-profile-save" type="submit">
                                    Update Profile
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
