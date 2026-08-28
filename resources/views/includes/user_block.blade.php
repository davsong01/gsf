<div class="col-sm-4 mb30">
    <div class="team-card">
        {!! renderAvatar($user, 96, 'img-responsive alumni-img') !!}
        <div class="team-overlay">
            <ul class="list-inline">
                <li><a href="{{ $user->facebook }}" target="_blank"><i class="fa fa-facebook"></i></a></li>
                <li><a href="{{ $user->twitter }}" target="_blank"><i class="fa fa-twitter"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="team-content">
        <a href="{{ route('student.single', $user->slug) }}">{{ ucfirst($user->name) }}</a><br>
        <em>{{ isset($user->campus) ? $user->campus->name : ''}}</em> <br>
        @if($user->matric_year != NULL)
        <em>
            @if($user->is_graduated == 1 && $user->graduation_year!= NULL)
                @if( $user->rolename == 'Member' )({{ $user->rolename . ', '}}{{ $user->matric_year . ' - ' . $user->graduation_year }})
                @endif
            @endif
            @if($user->is_graduated == 0 && $user->rolename <> 'Admin')
                @if($user->rolename == 'Member')
                ({{ $user->rolename . ', ' . $user->matric_year . ' - ' . date('Y')}})
                @else
                ({{ $user->rolename . ', '}}{{ $user->portfolio_session }})
                @endif
            @endif
        </em>
        <br>
        @else
        <br>
        @endif

        @if($user->is_graduated == 0)
        <a href="{{ route('student.single', $user->slug) }}"><button class="btn btn-info view-campus-details">View details</button></a>
        @else
        <a href="{{ route('alumni.single', $user->slug) }}"><button class="btn btn-info view-campus-details">View details</button></a>
        @endif
    </div>
</div>
