@php
    $award_approval_statuses = [
        'chapter_pending'     => 'Chapter Pending',
        'chapter_approved'    => 'Chapter Approved',
        'chapter_rejected'    => 'Chapter Rejected',

        'zone_pending'     => 'Zone Pending',
        'zone_approved'    => 'Zone Approved',
        'zone_rejected'    => 'Zone Rejected',

        'field_pending'    => 'Field Pending',
        'field_approved'   => 'Field Approved',
        'field_rejected'   => 'Field Rejected',

        'national_pending' => 'National Pending',
        'national_approved'=> 'National Approved',
        'national_rejected'=> 'National Rejected',
    ];
@endphp
@foreach($awards as $awardGroup)
    @foreach($awardGroup as $award)

        {{-- Status Adjustment Modal --}}
        @if($canAdjustApprovalStatus ?? false)
        <div class="modal fade" id="awardStatusAdjustModal{{ $award->id }}" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <!-- Status setting operations form content can be structured inside this container -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Adjust Approval Status: {{ $award->reference }}</h5>
                        <button type="button" class="btn-close border-0 bg-transparent" data-dismiss="modal" aria-label="Close">&times;</button>
                    </div>
                    <form method="POST" action="{{ route('awards.adjust.status', $award->id) }}">
                        @csrf
                        <input type="hidden" name="award_id" value="{{$award->id}}">
                        <div class="modal-body">
                            <div class="form-group">
                                <label>Approval Status</label>
                                <select name="approval_status" class="form-control" required>
                                    <option value="">-- Select Status --</option>
                                    @foreach($award_approval_statuses as $key => $label)
                                        @if(!in_array($key, ['zone_pending','field_pending', 'national_pending']))
                                        <option value="{{ $key }}" @selected(request('approval_status') == $key)>
                                            {{ $label }}
                                        </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Reason</label>
                                <textarea
                                    name="rejection_reason"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Enter reason (optional)">
                                </textarea>
                            </div>

                        </div>

                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Submit</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
        @endif

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
        @if($canArchiveSubmission ?? false)
        <form id="archive-award-{{ $award->id }}" action="{{ route('awards.archive', $award->id) }}" method="POST" style="display: none;">
            @csrf
        </form>
        @endif
        @if($canDeleteSubmission ?? false)
        <form id="delete-award-{{ $award->id }}" action="{{ route($isAdmin ? 'awards.delete' : 'stakeholders.awards.delete', $award->id) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
        @endif
        <form id="restore-award-{{ $award->id }}" action="{{ route('awards.restore', $award->id) }}" method="POST" style="display: none;">
            @csrf
        </form>
        @if($canDeleteSubmission ?? false)
        <form id="permanent-delete-award-{{ $award->id }}" action="{{ route('awards.permanent.delete', $award->id) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
        @endif
        @endif
    @endforeach
@endforeach
<div class="modal fade"
     id="shortlistStageModal"
     tabindex="-1"
     role="dialog"
     aria-labelledby="shortlistStageModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">

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

            <div class="modal-body py-2" style="max-height: 60vh; overflow-y: auto;">
                @foreach($shortlistStages as $stage)
                <div class="form-check mb-2 pb-1">

                    <input
                        class="form-check-input"
                        type="radio"
                        name="shortlist_stage_id"
                        value="{{ $stage->id }}"
                        id="stage_{{ $stage->id }}">

                    <label
                        class="form-check-label mb-0"
                        for="stage_{{ $stage->id }}">

                        {{ $stage->title }}

                    </label>

                </div>
                @endforeach
                <hr>
                <div class="form-group mt-1 mb-0">

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
