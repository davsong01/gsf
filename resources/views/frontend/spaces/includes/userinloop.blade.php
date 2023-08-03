{{-- Real block --}}
<div class="card-body" style="height: 270px;">
    <h2 class="h4 card-title mb-2">{{ ucfirst($alumni->name) }} Updated user in loop</h2><span
    class="card-subtitle text-gray font-weight-normal"><em>{{ $alumni->campus->name ?? $alumni->c_name }}</em>
    <br>
    @if($alumni->status == 0 && $alumni->rolename <> 'Admin')
        <small>
            @if($alumni->rolename == 'Member')
            ( <strong>{{ $alumni->rolename }}</strong>
            @if($alumni->matric_year && $alumni->graduation_year), {{ $alumni->matric_year . ' - ' . $alumni->graduation_year }} )
            @else) @endif
            @else
            @if($alumni->rolename)
            (<strong>{{ $alumni->rolename . ', '}}</strong>{{ $alumni->portfolio_session }})
            @endif
            @endif
        </small>
    @endif
    </span>
    <ul class="list-unstyled d-flex justify-content-center mt-3 mb-0">
        @if($alumni->facebook )
            <li><a href="{{ $alumni->facebook }}" target="_blank" aria-label="facebook social link" class="icon-facebook mr-3"><span
                class="fab fa-facebook-f"></span></a></li> 
        @endif
        @if($alumni->twitter)
        <li><a href="{{$alumni->twitter}}" target="_blank" aria-label="twitter social link" class="icon-twitter mr-3"><span
                class="fab fa-twitter"></span></a></li>
        @endif 
    </ul>
    <p class="card-text my-2"> 
        @if($alumni->status == 0)
        <a href="{{ route('user.single', $alumni->slug) }}" class="btn btn-sm btn-primary animate-up-2" style="color: white;">View details</a>
        @else
        <a href="{{ route('user.single', $alumni->slug) }}"><button class="btn btn-sm btn-primary animate-up-2">View details</button></a>
        @endif
    </p>
</div>
<div class="card-footer bg-soft border-top">
    <div class="d-flex justify-content-between">
    <div class="col pl-0"><span class="text-muted font-small d-block mb-2">Monthly</span></div>
</div>
{{-- End real block --}}