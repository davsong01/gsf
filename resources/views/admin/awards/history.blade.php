<div class="modal fade"
                                                id="shortlistHistoryModal_{{ $award->id }}"
                                                tabindex="-1"
                                                aria-hidden="true">

                                                <div class="modal-dialog modal-dialog-centered modal-lg">

                                                    <div class="modal-content">

                                                        <div class="modal-header">
                                                            <h5 class="modal-title">
                                                                Shortlist History - {{ $award->name }}
                                                            </h5>

                                                            <button type="button"
                                                                    class="btn-close"
                                                                    data-bs-dismiss="modal"></button>
                                                        </div>

                                                        <div class="modal-body">

                                                            @php
                                                                $histories = $award->shortlists()->with('stage')->latest()->get();
                                                            @endphp

                                                            @if($histories->count())

                                                                <ul class="list-group list-group-flush">

                                                                    @foreach($histories as $history)

                                                                        <li class="list-group-item">

                                                                            <div class="d-flex justify-content-between">

                                                                                <div>
                                                                                    <span class="badge bg-primary">
                                                                                        {{ $history->stage->title }}
                                                                                    </span>

                                                                                    @if($history->remarks)
                                                                                        <div class="text-muted font-xs mt-1">
                                                                                            {{ $history->remarks }}
                                                                                        </div>
                                                                                    @endif
                                                                                </div>

                                                                                <small class="text-muted">
                                                                                    {{ $history->created_at->format('d M Y H:i') }}
                                                                                </small>

                                                                            </div>

                                                                        </li>

                                                                    @endforeach

                                                                </ul>

                                                            @else

                                                                <p class="text-muted mb-0">No history available.</p>

                                                            @endif

                                                        </div>

                                                    </div>

                                                </div>

                                            </div>
