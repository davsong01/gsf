<div class="content-body">
    <section id="reports-dashboard">
        <h4 class="card-title">All Reports</h4>
        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-12">
                <form method="GET" class="row g-2 align-items-end">

                    {{-- Date range --}}
                    <div class="col-md-2 mb-2">
                        <label class="form-label">From</label>
                        <input type="date" name="from_date" class="form-control"
                            value="{{ request('from_date') ?? now()->startOfMonth()->format('Y-m-d') }}">
                    </div>

                    <div class="col-md-2 mb-2">
                        <label class="form-label">To</label>
                        <input type="date" name="to_date" class="form-control"
                            value="{{ request('to_date') ?? now()->format('Y-m-d') }}">
                    </div>

                    {{-- Field (highest first) --}}
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Field</label>
                        <select name="field_filter" class="form-control">
                            <option value="">All Fields</option>
                            @foreach($fields as $field)
                                <option value="{{ $field->id }}" @selected(request('field_filter') == $field->id)>
                                    {{ $field->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Zone --}}
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Zone</label>
                        <select name="zone_filter" class="form-control">
                            <option value="">All Zones</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" @selected(request('zone_filter') == $zone->id)>
                                    {{ $zone->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Chapter --}}
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Chapter</label>
                        <select name="chapter_filter" class="form-control">
                            <option value="">All Chapters</option>
                            @foreach($chapters as $chapter)
                                <option value="{{ $chapter->id }}" @selected(request('chapter_filter') == $chapter->id)>
                                    {{ $chapter->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Approval Status</label>
                        <select name="status_filter" class="form-control">
                            <option value="">All Status</option>
                            <option value="zone_pending" @selected(request('status_filter')=='zone_pending')>Zone Pending</option>
                            <option value="zone_approved" @selected(request('status_filter')=='zone_approved')>Zone Approved</option>
                            <option value="zone_rejected" @selected(request('status_filter')=='zone_rejected')>Zone Rejected</option>
                            <option value="field_pending" @selected(request('status_filter')=='field_pending')>Field Pending</option>
                            <option value="field_approved" @selected(request('status_filter')=='field_approved')>Field Approved</option>
                            <option value="field_rejected" @selected(request('status_filter')=='field_rejected')>Field Rejected</option>
                            <option value="national_pending" @selected(request('status_filter')=='national_pending')>National Pending</option>
                            <option value="national_approved" @selected(request('status_filter')=='national_approved')>National Approved</option>
                            <option value="national_rejected" @selected(request('status_filter')=='national_rejected')>National Rejected</option>
                        </select>
                    </div>

                    {{-- Button --}}
                    <div class="col-md-2 mb-2">
                        <button type="submit" class="btn btn-secondary w-100">
                            Filter
                        </button>
                    </div>

                    {{-- Download --}}
                    <div class="col-md-2 mb-2">
                        <button
                            type="submit"
                            name="action"
                            value="download"
                            class="btn btn-success w-100"
                        >
                            Download
                        </button>
                    </div>
                </form>
            </div>
        </div>


        <!-- Reports Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body table-responsive">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead >
                                    <tr>
                                        <th>S/N</th>
                                        <th>Chapter</th>
                                        <th>Month/Year</th>
                                        <th>Academic Session</th>
                                        <th>Created On</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($reports as $report)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $report->chapter->name ?? '—' }}</td>
                                        <td>{{ date('F', mktime(0, 0, 0, $report->month, 10)) }}, {{ $report->year }}</td>
                                        <td>{{ $report->session }}</td>
                                        <td>{{ $report->created_at->format('d M Y, h:i A') }}</td>

                                        <td class="text-center">
                                            {{-- Download --}}
                                            <a
                                                href="{{ route('stakeholders.financial.reports.download', $report->id) }}"
                                                target="_blank"
                                                class="text-success mx-1"
                                                title="Download Report"
                                            >
                                                <i class="fa fa-download"></i> Download
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="mt-3">
                                {{ $reports->links() }}
                            </div>
                        </div>

                        <!-- Pagination -->
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
