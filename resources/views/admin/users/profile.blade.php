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
    .admin-profile-shell {
        position: relative;
    }

    .admin-profile-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1.1rem;
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 55%, #2563eb 100%);
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
        width: 5.5rem;
        height: 5.5rem;
        border-radius: 1.2rem;
        overflow: hidden;
        border: 3px solid rgba(255, 255, 255, 0.25);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.18);
        background: rgba(255, 255, 255, 0.08);
    }

    .admin-profile-stat {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1rem;
        background: linear-gradient(135deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
    }

    .admin-profile-section {
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: 1rem;
        background: #fff;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.05);
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
        margin-bottom: 0.35rem;
    }

    .admin-profile-field .form-control,
    .admin-profile-field .form-select {
        border-radius: 0.85rem;
        border-color: #dbe3ee;
        box-shadow: none;
    }

    .admin-profile-field .form-control:focus,
    .admin-profile-field .form-select:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 0.18rem rgba(37, 99, 235, 0.12);
    }

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
</style>

<div class="content-body">
    <section class="admin-profile-shell">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="admin-profile-hero p-4 p-lg-5">
                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4 position-relative">
                        <div class="d-flex align-items-center gap-3">
                            <div class="admin-profile-avatar">
                                {!! renderAvatar($user, 160, 'w-100 h-100') !!}
                            </div>
                            <div>
                                <div class="admin-profile-badge mb-2">
                                    <i class="fa fa-user-shield"></i>
                                    Admin Profile
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
                            <a href="{{ route('user.single', $user->slug) }}" target="_blank" class="btn btn-light btn-sm">
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
                            <h4 class="mb-1">My Profile</h4>
                            <div class="admin-profile-help">Update your admin account information and visibility preferences.</div>
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
                                            <select class="form-select" name="gender" id="gender" required>
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
                                            <input type="text" class="form-control" name="password" id="password" value="{{ old('password') }}" placeholder="Enter password">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <button class="btn btn-primary w-100 py-2" type="submit">
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
