@if($canAct && $approveRoute)
<!-- Floating Action Button Controls Frame -->
<div class="sticky-action-buttons">
    @if($isAdmin)

        <button type="button"
                class="btn-circle bg-primary text-white d-flex align-items-center justify-content-center"
                data-toggle="modal"
                data-target="#shortlistStageModal"
                title="Move to Stage">

            <i class="fa fa-random"></i>

            <span class="btn-label">Shortlist</span>
        </button>

    @endif
    <a href="{{ $approveRoute }}"
       class="btn-circle bg-success text-white d-flex align-items-center justify-content-center"
       title="{{ $tooltipApprove }}"
       onclick="return confirm('Are you sure you want to approve this submission record?');">
        <i class="fa fa-check"></i>
        <span class="btn-label">{{ $tooltipApprove }}</span>
    </a>

    <button type="button"
            class="btn-circle bg-danger text-white d-flex align-items-center justify-content-center"
            data-toggle="modal"
            data-target="#rejectModal"
            title="{{ $tooltipReject }}">
        <i class="fa fa-times"></i>
        <span class="btn-label">{{ $tooltipReject }}</span>
    </button>
</div>

<!-- Rejection Criteria Context Form Modal Backdrop Box -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ $rejectRoute }}" method="POST">
            @csrf
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark font-base" id="rejectModalLabel">Reject Submission Record</h5>
                    <button type="button" class="btn-close shadow-none border-0 bg-transparent text-muted" data-dismiss="modal" aria-label="Close" style="font-size:1.25rem;">&times;</button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-2">
                        <label for="rejection_reason" class="form-label font-sm fw-medium text-secondary mb-1">Reason explaining the rejection context</label>
                        <textarea name="rejection_reason" id="rejection_reason" class="form-control font-sm rounded-2" rows="4" placeholder="Provide details why you have to reject..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light font-sm px-3 rounded-2" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger font-sm px-3 rounded-2">Reject Submission</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
<!-- Zoom System Modal Frame Container -->
<div class="modal fade" id="imageZoomSystemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
        <div class="modal-content border-0 shadow-lg bg-white">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-semibold text-dark text-truncate" id="zoomModalLabel">File Asset Focus</h6>
                <button type="button" class="btn-close shadow-none border-0 bg-transparent text-muted" data-dismiss="modal" aria-label="Close" style="font-size:1.25rem;">&times;</button>
            </div>
            <div class="modal-body text-center p-4 d-flex align-items-center justify-content-center" style="min-height: 350px;">
                <img src="data:image/svg+xml;charset=utf-8,%3Csvg xmlns%3D'http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg' viewBox%3D'0 0 1 1'%2F%3E"
                     id="zoomedTargetImageElement"
                     class="img-fluid rounded border shadow-sm"
                     style="max-height: 65vh; max-width: 100%; object-fit: contain; display: none;"
                     alt="Focus Workspace">

                <div id="zoomModalSpinnerElement" class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading asset data structure...</span>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-between">
                <span class="text-muted font-xs">Press ESC to close full view mode</span>
                <button type="button" class="btn btn-light font-sm px-3 rounded-2" data-dismiss="modal">Close View</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade"
     id="shortlistHistoryModal"
     tabindex="-1"
     aria-labelledby="shortlistHistoryModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">

        <div class="modal-content border-0 shadow">

            <div class="modal-header border-bottom-0 pb-0">

                <h5 class="modal-title">
                    Shortlist History - {{ $award->name }}
                </h5>

                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close">

                    <span aria-hidden="true">&times;</span>

                </button>

            </div>

            <div class="modal-body py-3">

                @php
                    $histories = $award->shortlists()->with('stage')->latest()->get();
                @endphp

                @if($histories->count())

                    <div class="timeline">

                        @foreach($histories as $history)

                            <div class="mb-2 p-2 rounded"
                                 style="background: #f8f9fa; border-left: 3px solid #0d6efd;">

                                <div class="d-flex justify-content-between">

                                    <div>

                                        <span class="badge badge-primary">
                                            {{ $history->stage->title }}
                                        </span>

                                        <div class="mt-1">
                                            <strong>Shortlisted by:</strong>
                                            {{ $history->shortlistedBy->name ?? 'System' }}
                                        </div>

                                        @if($history->remarks)
                                            <div class="small text-muted mt-1">
                                                {{ $history->remarks }}
                                            </div>
                                        @endif

                                    </div>

                                    <small class="text-muted">
                                        {{ $history->created_at->format('d M Y, h:i A') }}
                                    </small>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <p class="text-muted mb-0">
                        No shortlist history available.
                    </p>

                @endif

            </div>

            <div class="modal-footer border-top-0 pt-0">

                <button type="button"
                        class="btn btn-light"
                        data-dismiss="modal">

                    Cancel

                </button>

            </div>

        </div>

    </div>
</div>

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
                    Shortlist {{ $award->name }}
                </h5>

                <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('awards.shortlist') }}">
                @csrf

                @foreach($shortlistStages as $stage)
                <div class="form-check mb-1">

                    <input
                        class="form-check-input"
                        type="radio"
                        name="shortlist_stage_id"
                        value="{{ $stage->id }}"
                        id="stage_{{ $stage->id }}"
                        {{ optional($award->currentShortlistStage)->award_shortlist_stage_id == $stage->id ? 'checked' : '' }}
                    >

                    <input type="hidden" name="ids[]" value="{{$award->id}}">
                    <input type="hidden" name="shortlist_stage_id" value="{{ $stage->id }}">

                    <label class="form-check-label" for="stage_{{ $stage->id }}">
                        {{ $stage->title }} @if($stage->mark_as_final)<span style="color:red">(Final Stage)</span>@endif
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
                </form>
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
                    Update
                </button>

            </div>

        </div>

    </div>

</div>
