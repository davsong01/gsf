@php
    $directoryMemberCount = \App\Models\User::whereHas('campus', fn ($query) => $query->actualCampus())->where('status', 'active')->where('role', '<>', 1)->count();
    $directoryChapterCount = \App\Models\Chapter::actualCampus()->count();
    $directoryChapters = \App\Models\Chapter::actualCampus()
        ->orderBy('name')
        ->limit(6)
        ->get();
    $campusesByState = \Illuminate\Support\Facades\Schema::hasColumn('chapters', 'state')
        ? \App\Models\Chapter::actualCampus()
            ->whereNotNull('state')
            ->selectRaw('state, COUNT(*) as campus_count')
            ->groupBy('state')
            ->get()
        : collect();
@endphp

<section class="directory-network" aria-labelledby="directory-network-title">
    <div class="site-container directory-network__card">
        <div class="directory-network__content">
            <div class="eyebrow"><span></span> The GSF Network</div>
            <h2 id="directory-network-title">
                <strong>{{ number_format($directoryMemberCount) }}+</strong> members across
                <strong>{{ number_format($directoryChapterCount) }}+</strong> campus fellowships.
            </h2>
            <p>Discover GSF communities, reconnect with members and explore the fellowship’s growing national footprint.</p>

            <div class="directory-network__links">
                @foreach($directoryChapters as $chapter)
                    <a href="{{ route('campus.single', $chapter->slug) }}">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s7-4.35 7-11A7 7 0 1 0 5 10c0 6.65 7 11 7 11Z"/><circle cx="12" cy="10" r="2.4"/></svg>
                        <span>{{ \Illuminate\Support\Str::limit($chapter->name, 42) }}</span>
                    </a>
                @endforeach
                <a class="directory-network__all" href="{{ route('people.campuses') }}">View all campuses <span aria-hidden="true">→</span></a>
            </div>

            <div class="directory-network__actions">
                <a class="button button--blue" href="{{ route('newalumni') }}"><span aria-hidden="true">＋</span> Add a member</a>
                <a class="text-link" href="{{ route('directory.index') }}">Explore the directory <span aria-hidden="true">→</span></a>
            </div>
        </div>

        <div class="directory-network__map" aria-label="GSF chapters across Nigeria">
            <div class="directory-network__map-frame">
                <img src="{{ asset('gsfcom/images/nigeriamap.jpg') }}" alt="Map of Nigeria representing the GSF national network">
                @foreach($campusesByState as $group)
                    @php
                        $coordinates = config('gsf_states.'.$group->state);
                    @endphp
                    @if($coordinates)
                        @php
                            // Calibrated to the illustration's Nigeria outline; positions are approximate state capitals.
                            $left = 13 + (($coordinates[1] - 2.5) / 12) * 76;
                            $top = 20 + ((13.9 - $coordinates[0]) / 9.7) * 60;
                        @endphp
                        <a class="directory-network__marker" href="{{ route('people.campuses', ['state' => $group->state]) }}"
                           style="left: {{ $left }}%; top: {{ $top }}%"
                           data-label="{{ $group->state }} · {{ $group->campus_count }} {{ \Illuminate\Support\Str::plural('campus', $group->campus_count) }}"
                           aria-label="View {{ $group->campus_count }} {{ \Illuminate\Support\Str::plural('campus', $group->campus_count) }} in {{ $group->state }}"
                           title="{{ $group->state }}: {{ $group->campus_count }} {{ \Illuminate\Support\Str::plural('campus', $group->campus_count) }}"></a>
                    @endif
                @endforeach
            </div>
            <div class="directory-network__map-label"><span></span> Connected across Nigeria</div>
        </div>
    </div>
</section>
