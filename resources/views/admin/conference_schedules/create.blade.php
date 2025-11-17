@extends('layouts.conference')
@section('title', isset($conferenceSchedule) ? 'Edit Conference Schedule' : 'Create Conference Schedule')

@section('content2')
<div class="content-body">
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ isset($conferenceSchedule) ? 'Edit Conference Schedule' : 'Add Conference Schedule' }}</h4>
            @include('includes.alerts')
        </div>
        <div class="card-body">
            <form action="{{ isset($conferenceSchedule) ? route('conference_schedule.update', ['conferenceSchedule' => $conferenceSchedule->id, 'edition' => $edition]) : route('conference_schedule.store', ['edition' => $edition]) }}" method="POST">
                @csrf
                @if(isset($conferenceSchedule))
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label for="day">Day</label>
                            <input type="text" name="day" id="day" value="{{ old('day', $conferenceSchedule->day ?? '') }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label for="date">Date</label>
                            <input type="date" name="date" id="date" value="{{ old('date', $conferenceSchedule->date ?? '') }}" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-2">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="">-- Select Status --</option>
                                <option value="1" {{ old('status', $conferenceSchedule->status ?? '') == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status', $conferenceSchedule->status ?? '') == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <label>Sessions</label>
                        <small class="text-muted d-block mb-2">Add each session below. You can add multiple sessions.</small>
                        
                        <div id="sessions-container">
                            @php
                                $oldSessions = old('sessions', isset($conferenceSchedule->sessions) ? $conferenceSchedule->sessions : []);
                                if(is_string($oldSessions)) {
                                    $oldSessions = json_decode($oldSessions, true) ?? [];
                                }
                            @endphp

                            @if(!empty($oldSessions))
                                @foreach($oldSessions as $index => $session)
                                    <div class="row session-row mb-2">
                                        <div class="col-md-4">
                                            <select name="sessions[{{ $index }}][speaker_id]" class="form-control" required>
                                                <option value="">-- Select Speaker --</option>
                                                @foreach($speakers as $speaker)
                                                    <option value="{{ $speaker->id }}" {{ ($session['speaker_id'] ?? '') == $speaker->id ? 'selected' : '' }}>
                                                        {{ $speaker->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" name="sessions[{{ $index }}][description]" class="form-control" placeholder="Description" value="{{ $session['description'] ?? '' }}" required>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="time" name="sessions[{{ $index }}][time]" class="form-control" value="{{ $session['time'] ?? '' }}" required>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger btn-sm remove-session">&times;</button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="row session-row mb-2">
                                    <div class="col-md-4">
                                        <select name="sessions[0][speaker_id]" class="form-control" required>
                                            <option value="">-- Select Speaker --</option>
                                            @foreach($speakers as $speaker)
                                                <option value="{{ $speaker->id }}">{{ $speaker->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="sessions[0][description]" class="form-control" placeholder="Description" required>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="time" name="sessions[0][time]" class="form-control" required>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-danger btn-sm remove-session">&times;</button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <button type="button" class="btn btn-success btn-sm mt-2" id="add-session">Add Session</button>
                    </div>

                    <script>
                        let sessionIndex = {{ count($oldSessions ?? [0]) }};

                        document.getElementById('add-session').addEventListener('click', function () {
                            const container = document.getElementById('sessions-container');
                            const row = document.createElement('div');
                            row.classList.add('row', 'session-row', 'mb-2');
                            row.innerHTML = `
                                <div class="col-md-4">
                                    <select name="sessions[${sessionIndex}][speaker_id]" class="form-control" required>
                                        <option value="">-- Select Speaker --</option>
                                        @foreach($speakers as $speaker)
                                            <option value="{{ $speaker->id }}">{{ $speaker->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="sessions[${sessionIndex}][description]" class="form-control" placeholder="Description" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="time" name="sessions[${sessionIndex}][time]" class="form-control" required>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger btn-sm remove-session">&times;</button>
                                </div>
                            `;
                            container.appendChild(row);
                            sessionIndex++;
                        });

                        // Remove session row
                        document.addEventListener('click', function (e) {
                            if(e.target.classList.contains('remove-session')){
                                e.target.closest('.session-row').remove();
                            }
                        });
                    </script>

                </div>

                <button type="submit" class="btn btn-primary mt-3">
                    {{ isset($conferenceSchedule) ? 'Update Schedule' : 'Create Schedule' }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
