@forelse($data as $field => $chapters)
    <div class="field-title">{{ $field }}</div>

    <ul>
        @foreach($chapters as $chapter => $months)
            <li>
                {{ $chapter ?? '-' }} -

                @if(is_array($months))
                    {{ implode(', ', $months) }}
                @else
                    {{ $months }}
                @endif
            </li>
        @endforeach
    </ul>
@empty
    <div class="empty">No records found</div>
@endforelse