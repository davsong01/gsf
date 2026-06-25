@foreach($awards as $awardGroup)
    @foreach($awardGroup as $award)

        {{-- Status Adjustment Modal --}}
        <div class="modal fade" id="statusAdjustModal{{ $award->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <!-- Status setting operations form content can be structured inside this container -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Adjust Status: {{ $award->reference }}</h5>
                        <button type="button" class="btn-close border-0 bg-transparent" data-dismiss="modal" aria-label="Close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <!-- Custom settings inputs matching controller handlers -->
                        <p class="font-sm text-muted">Modify verification markers for this nomination context profile.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dynamic Feedback Comment Partials Backdrops --}}
        @foreach([
            'chapter'  => ['status' => $award->chapter_status, 'field' => 'chapter_comment', 'title' => 'Chapter Office'],
            'zone'     => ['status' => $award->zone_status, 'field' => 'zone_comment', 'title' => 'Zone'],
            'field'    => ['status' => $award->field_status, 'field' => 'field_comment', 'title' => 'Field'],
            'national' => ['status' => $award->national_status, 'field' => 'national_comment', 'title' => 'National Secretariat']
        ] as $level => $config)

            @if($config['status'] == 2)
                @include('stakeholder.modals.award_rejection_comments', [
                    'level'   => $level,
                    'title'   => $config['title'],
                    'comment' => $award->{$config['field']},
                    'report'  => $award
                ])
            @endif

        @endforeach

        {{-- Single Record Destructive Action Execution Target Handler --}}
        @if($isAdmin)
        <form id="delete-award-{{ $award->id }}" action="{{ route($isAdmin ? 'awards.delete' : 'stakeholders.awards.delete', $award->id) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
        @endif
    @endforeach
@endforeach
<div class="modal fade"
     id="shortlistStageModal"
     tabindex="-1"
     role="dialog"
     aria-labelledby="shortlistStageModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered" role="document">

        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="shortlistStageModalLabel">
                    Move Selected Entries to Stage
                </h5>

                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                @foreach($shortlistStages as $stage)
                <div class="form-check mb-3">

                    <input
                        class="form-check-input"
                        type="radio"
                        name="shortlist_stage_id"
                        value="{{ $stage->id }}"
                        id="stage_{{ $stage->id }}">

                    <label
                        class="form-check-label"
                        for="stage_{{ $stage->id }}">

                        {{ $stage->title }}

                    </label>

                </div>
                @endforeach
                <hr>
                <div class="form-group mt-1">

                    <label for="remarks" class="font-sm text-muted">
                        Remarks (optional)
                    </label>

                    <textarea
                        name="remarks"
                        id="remarks"
                        class="form-control"
                        rows="3"
                        placeholder="Add notes about this stage update..."></textarea>

                </div>
            </div>

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-light"
                    data-dismiss="modal">
                    Cancel
                </button>

                <button
                    type="button"
                    class="btn btn-primary"
                    id="submit-shortlist-action">
                    Continue
                </button>

            </div>

        </div>

    </div>

</div>
