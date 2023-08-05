<div class="col-12 col-md-6 col-lg-4 mb-7">
    <div class="card border-light text-center shadow">
        <div class="profile-thumbnail mx-auto mt-n6">
            <img class="card-img-top rounded-circle border-0" src="{{ !is_null($user->passport) ? asset($user->passport) : asset('frontend/passports/avatar.jpg') }}" alt="{{ $user->passport }}" class="img-responsive alumni-img">
        </div>

        <div class="card-body" style="height: 350px;">
            <a href="{{ route('user.single', $user->slug) }}"><h2 class="h4 card-title mb-2">{{ ucfirst($user->name) }}</h2></a>
            @if($user->stakeholder)
                {{-- @if(!in_array($user->stakeholder->role, $unused) )
                    @if($user->stakeholder->role == 'Portfolio')
                    <strong style="color:green">GSF National {{ $user->stakeholder->role }}</strong>
                    @else 
                    <strong style="color:green">{{ $user->stakeholder->role }} {{ $user->stakeholder->portfolio ? ', GSF '.$user->stakeholder->portfolio  : ''}}</strong>
                    @endif
                @endif --}}
                @if($user->stakeholder->role == 'President' && !is_null($user->stakeholder->chapter_id))<span class="portfolio">President, </span>{{ $user->stakeholder->chapter->name ?? 'N/A' }}@endif
                @if($user->stakeholder->role == 'Zonal Pastor' && !is_null($user->stakeholder->zone_id))<span class="portfolio">Zonal Pastor, </span>{{ $user->stakeholder->zone->name ?? 'N/A' }}@endif
                @if($user->stakeholder->role == 'Field Pastor' && !is_null($user->stakeholder->field_id)) <span class="portfolio">Field Pastor, </span>{{ $user->stakeholder->field->name ?? 'N/A' }}@endif
                @if($user->stakeholder->role == 'Portfolio') <span class="portfolio" >{{ 'GSF National '.$user->stakeholder->portfolio }} </span> @endif
            @endif
            <br>
            @if(!empty($user->campus->id) && $user->campus->id != 86)
            <span class="card-subtitle text-gray font-weight-normal"><em>
                {{ $user->campus->name ?? $user->c_name }}
            </em>
            <br>
            @endif
            @if($user->status == 0 && $user->rolename <> 'Admin')
                <small>
                    @if($user->rolename == 'Member')
                    ( <strong>{{ $user->rolename }}</strong>
                    @if($user->matric_year && $user->graduation_year), {{ $user->matric_year . ' - ' . $user->graduation_year }} )
                    @else) @endif
                    @else
                    @if($user->rolename)
                    (<strong>{{ $user->rolename . ', '}}</strong>{{ $user->portfolio_session }})
                    @endif
                    @endif
                </small>
            @endif
            @if($user->show_phone == 1) 
            <br>
            <li class="list-group-item small p-0"><span class="fa fa-mobile" data-toggle="tooltip" data-html="true" title="Phone" aria-hidden="true"></span> {{ $user->phone }}
            @endif
            @if($user->show_email== 1)
            <br>
            <li class="list-group-item small p-0"><span class="fa fa-envelope" data-toggle="tooltip" data-html="true" title="Email" aria-hidden="true"></span> {{ $user->email }}
            </li>
            @endif
            </span>
            @if(!is_null($user->skills))
            <li class="list-group-item small p-0"><span class="fas fa-bullseye mr-2" data-toggle="tooltip" data-html="true" title="Skills" aria-hidden="true"></span>{{ $user->skills }}
            </li>
            @endif
            
            <ul class="list-unstyled d-flex justify-content-center mt-3 mb-0">
                @if($user->facebook )
                <li><a href="{{ $user->facebook }}" target="_blank" aria-label="facebook social link" class="icon-facebook mr-3"><span
                    class="fab fa-facebook-f"></span></a></li> 
                @endif
                @if($user->twitter)
                <li><a href="{{$user->twitter}}" target="_blank" aria-label="twitter social link" class="icon-twitter mr-3"><span
                        class="fab fa-twitter"></span></a>
                </li>
                @endif 
            </ul>
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