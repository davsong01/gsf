<div class="col-12 col-md-6 col-lg-4 mb-7">
    <div class="card border-light text-center">
    <div class="profile-thumbnail mx-auto mt-n6">
        <img class="card-img-top rounded-circle border-0" src="{{ !is_null($user->passport) ? asset($user->passport) : asset('frontend/passports/avatar.jpg') }}" alt="{{ $user->passport }}" class="img-responsive alumni-img">
    </div>
    <div class="card-body" style="height: 270px;">
        <h2 class="h4 card-title mb-2">{{ ucfirst($user->name) }}</h2><span
        class="card-subtitle text-gray font-weight-normal"><em>{{ $user->campus->name ?? $user->c_name }}</em>
        <br>
        
        @if($user->status == 0 && $user->rolename <> 'Admin')
            <small>
                @if($user->rolename == 'Member')
                (<strong>{{ $user->rolename }},</strong> {{ $user->matric_year . ' - ' . date('Y')}})
                @else
                @if($user->rolename)
                ({{ $user->rolename . ', '}} <strong>{{ $user->portfolio_session }})</strong>
                @endif
                @endif
            </small>
        @endif
    
    </span>
        <ul class="list-unstyled d-flex justify-content-center mt-3 mb-0">
            @if($user->facebook )
                <li><a href="{{ $user->facebook }}" target="_blank" aria-label="facebook social link" class="icon-facebook mr-3"><span
                    class="fab fa-facebook-f"></span></a></li> 
            @endif
            @if($user->twitter)
            <li><a href="{{$user->twitter}}" target="_blank" aria-label="twitter social link" class="icon-twitter mr-3"><span
                    class="fab fa-twitter"></span></a></li>
            @endif 
        </ul>
        {{-- <li><a href="#" target="_blank" aria-label="slack social link" class="icon-slack mr-3"><span
                class="fab fa-slack-hash"></span></a></li>
        <li><a href="#" target="_blank" aria-label="dribbble social link" class="icon-dribbble mr-3"><span
                class="fab fa-dribbble"></span></a></li>
        </ul> --}}
        <p class="card-text my-2"> 
            @if($user->status == 0)
            <a href="{{ route('user.single', $user->slug) }}" class="btn btn-sm btn-primary animate-up-2" style="color: white;">View details</a>
            @else
            <a href="{{ route('user.single', $user->slug) }}"><button class="btn btn-sm btn-primary animate-up-2">View details</button></a>
            @endif
        </p>
    </div>
    </div>
</div>