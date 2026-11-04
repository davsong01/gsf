@include('includes.alerts')

@if(session('failures'))
    <div class="alert alert-danger" role="alert">
        <strong>Some rows could not be imported.</strong>
        <ul class="mb-0 mt-2">
            @foreach(session('failures') as $failure)
                <li>
                    Row {{ $failure['row'] }}
                    @if(isset($failure['data']['email'])) · Email: <strong>{{ $failure['data']['email'] }}</strong>@endif
                    @if(isset($failure['data']['phone'])) · Phone: <strong>{{ $failure['data']['phone'] }}</strong>@endif
                    — {{ implode(', ', $failure['errors']) }}
                </li>
            @endforeach
        </ul>
    </div>
@endif
