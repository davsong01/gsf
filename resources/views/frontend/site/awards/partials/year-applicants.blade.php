<section class="public-award-year-panel">
    <div class="public-award-year-panel__header">
        <div>
            <h2>{{ $year }} applications</h2>
            <p>{{ number_format($applicants->count()) }} {{ \Illuminate\Support\Str::plural('application', $applicants->count()) }} received</p>
        </div>
        <div class="public-award-year-metrics" aria-label="{{ $year }} summary">
            <span class="public-award-year-metric">{{ number_format($approvedCount) }} approved</span>
            <span class="public-award-year-metric">{{ number_format($chapterCount) }} campuses</span>
            <span class="public-award-year-metric">{{ number_format($zoneCount) }} zones</span>
            <span class="public-award-year-metric">{{ number_format($fieldCount) }} fields</span>
        </div>
    </div>

    <div class="public-award-beneficiary-heading">
        <h3><i class="fa fa-users" style="color:#c38d38" aria-hidden="true"></i> Applicants</h3>
        <span>{{ number_format($approvedCount) }} approved in {{ $year }}</span>
    </div>

    @if($applicants->isNotEmpty())
        <div class="public-award-beneficiary-grid" id="awardApplicantList">
            @foreach($applicants as $applicant)
                @php
                    $applicantName = $applicant->name;
                    $initials = collect(preg_split('/\s+/', trim($applicantName)))->filter()->take(2)->map(fn ($part) => \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($part, 0, 1)))->implode('');
                    $chapterName = $applicant->chapter?->name ?? $applicant->entry?->select_institution ?? 'Campus not recorded';
                    $isApplicantApproved = (int) $applicant->national_status === 1 || (bool) $applicant->currentShortlistStage?->stage?->mark_as_final;
                    $courseOfStudy = collect([
                        $applicant->entry?->course_of_study,
                        $applicant->entry?->course_of_study_,
                        $applicant->entry?->course,
                    ])->first(fn ($value) => filled($value));
                    $facultyName = $applicant->entry?->faculty_name ?? $applicant->entry?->facultyschool;
                    $cgpa = $applicant->entry?->cgpa;
                    $searchText = \Illuminate\Support\Str::lower(implode(' ', array_filter([
                        $applicantName,
                        $chapterName,
                        $applicant->zone?->name,
                        $applicant->field?->name,
                        $courseOfStudy,
                        $facultyName,
                    ])));
                @endphp
                <article class="public-award-beneficiary" data-applicant-row data-applicant-search="{{ $searchText }}">
                    <span class="public-award-beneficiary__initials" aria-hidden="true">{{ $initials ?: 'GS' }}</span>
                    <div class="public-award-beneficiary__info">
                        <strong class="public-award-beneficiary__name" title="{{ $applicantName }}">{{ $applicantName }}</strong>
                        <p class="public-award-beneficiary__location"><strong>Campus:</strong> {{ $chapterName }}</p>
                        @if($isApplicantApproved)
                            <div class="public-award-recognition">
                                <span class="public-award-approved-badge"><i class="fa fa-check-circle" aria-hidden="true"></i> Approved</span>
                                @if(filled($cgpa))
                                    <span class="public-award-cgpa"><span class="public-award-cgpa__label">CGPA</span><strong class="public-award-cgpa__value">{{ $cgpa }}</strong></span>
                                @endif
                            </div>
                        @endif
                        @if($applicant->zone?->name)<span class="public-award-chip">Zone: {{ $applicant->zone->name }}</span>@endif
                        @if($applicant->field?->name)<span class="public-award-chip">Field: {{ $applicant->field->name }}</span>@endif
                        @if($courseOfStudy)
                            <p class="public-award-course"><strong>Course of study:</strong> {{ $courseOfStudy }}{{ $facultyName ? ' · ' . $facultyName : '' }}</p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
        <div class="public-award-results-empty" id="awardYearResultsEmpty">No applicants in {{ $year }} match your search.</div>
    @else
        <div class="public-award-empty">There are no applications for this year.</div>
    @endif
</section>
