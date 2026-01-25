<style>
    .alumni-card{
        border-radius: 16px;
        padding-top: 40px;
        transition: all .3s ease;
        box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    }

    .alumni-card:hover{
        transform: translateY(-6px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.12);
    }

    .alumni-img{
        width:120px;
        height:120px;
        object-fit:cover;
        border:4px solid #fff;
        box-shadow:0 4px 12px rgba(0,0,0,.15);
    }

    .role-badge{
        background: linear-gradient(45deg,#1f3c88,#4facfe);
        color:#fff;
        padding:4px 14px;
        border-radius:20px;
        font-size:12px;
    }

    .social-icon{
        display:inline-flex;
        align-items:center;
        justify-content:center;
        width:36px;
        height:36px;
        border-radius:50%;
        background:#f2f4f8;
        color:#1f3c88;
        transition:.3s;
    }

    .social-icon.facebook:hover{ background:#1877f2;color:#fff; }
    .social-icon.twitter:hover{ background:#1da1f2;color:#fff; }

</style>
<div class="col-12 col-md-6 col-lg-4 mb-5">
    <div class="card alumni-card border-0 text-center">

        {{-- Profile Image --}}
        <div class="profile-thumbnail mx-auto mt-n5">
            <img
                src="{{ !is_null($user->passport) ? asset($user->passport) : asset('frontend/passports/avatar.jpg') }}"
                alt="{{ $user->name }}"
                class="rounded-circle alumni-img">
        </div>

        <div class="card-body pt-3">

            {{-- Name --}}
            <a href="{{ route('user.single', $user->slug) }}">
                <h5 class="fw-bold mb-1">{{ ucfirst($user->name) }}</h5>
            </a>

            {{-- Role --}}
            @if($user->stakeholder)
                <span class="role-badge mb-2 d-inline-block">
                    @if($user->stakeholder->role == 'President' && $user->stakeholder->chapter_id)
                        President, {{ $user->stakeholder->chapter->name ?? 'N/A' }}
                    @elseif($user->stakeholder->role == 'Zonal Pastor' && $user->stakeholder->zone_id)
                        Zonal Pastor, {{ $user->stakeholder->zone->name ?? 'N/A' }}
                    @elseif($user->stakeholder->role == 'Field Pastor' && $user->stakeholder->field_id)
                        Field Pastor, {{ $user->stakeholder->field->name ?? 'N/A' }}
                    @elseif($user->stakeholder->role == 'Portfolio')
                        GSF National {{ $user->stakeholder->portfolio }}
                    @endif
                </span>
            @else
                <span class="role-badge mb-2 d-inline-block">
                    {{ $user->rolename }}
                </span>
            @endif

            {{-- Campus --}}
            @if(!empty($user->campus->id) && $user->campus->id != 86)
                <p class="text-muted small mb-1">
                    <i class="fas fa-university text-primary"></i>
                    {{ $user->campus->name ?? $user->c_name }}
                </p>
            @endif

            {{-- Session --}}
            @if($user->rolename !== 'Admin')
                <p class="text-muted small">
                    @if($user->is_graduated && $user->matric_year && $user->graduation_year)
                        ({{ $user->matric_year }} - {{ $user->graduation_year }})
                    @elseif(!$user->is_graduated && $user->matric_year)
                        ({{ $user->matric_year }} - {{ now()->year }})
                    @endif
                </p>
            @endif

            {{-- Skills --}}
            @if(!is_null($user->skills))
                <p class="small mt-2">
                    <i class="fas fa-bullseye text-success"></i> {{ $user->skills }}
                </p>
            @endif

            {{-- Contact --}}
            @if($user->show_phone == 1)
                <p class="small mb-0">
                    <i class="fas fa-phone text-primary"></i> {{ $user->phone }}
                </p>
            @endif

            @if($user->show_email == 1)
                <p class="small">
                    <i class="fas fa-envelope text-primary"></i> {{ $user->email }}
                </p>
            @endif

            {{-- Social --}}
            <ul class="list-inline mt-3 mb-2">
                @if($user->facebook)
                    <li class="list-inline-item">
                        <a href="{{ $user->facebook }}" target="_blank" class="social-icon facebook">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    </li>
                @endif
                @if($user->twitter)
                    <li class="list-inline-item">
                        <a href="{{ $user->twitter }}" target="_blank" class="social-icon twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </li>
                @endif
            </ul>

            {{-- Button --}}
            <a href="{{ route('user.single', $user->slug) }}" class="btn btn-sm btn-primary rounded-pill px-4">
                View Profile
            </a>

        </div>
    </div>
</div>
