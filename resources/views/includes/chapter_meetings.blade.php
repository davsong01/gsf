<section class="chapter-meetings" data-meeting-manager
    data-store-url="{{ route($meetingRoutePrefix.'.store', $chapter) }}"
    data-update-url="{{ route($meetingRoutePrefix.'.update', ['chapter' => $chapter->id, 'meeting' => '__ID__']) }}"
    data-destroy-url="{{ route($meetingRoutePrefix.'.destroy', ['chapter' => $chapter->id, 'meeting' => '__ID__']) }}">
    <div class="chapter-meetings__heading"><div><h3>Meeting details</h3><p>Add a title, day, time and venue for each regular campus gathering.</p></div><span>Save without reloading</span></div>
    <div class="chapter-meetings__labels"><span>Meeting title</span><span>Day of the week</span><span>Time</span><span>Venue</span></div>
    <div class="chapter-meetings__list" data-meeting-list>
        @foreach($chapter->meetings as $meeting)
            <div class="chapter-meetings__row" data-meeting-row data-meeting-id="{{ $meeting->id }}">
                <input data-field="title" aria-label="Meeting title" maxlength="255" class="form-control" placeholder="Meeting title" value="{{ $meeting->title ?: 'Fellowship meeting' }}">
                <select data-field="day_of_week" aria-label="Day of the week" class="form-control">@foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)<option value="{{ $day }}" @selected($meeting->day_of_week === $day)>{{ $day }}</option>@endforeach</select>
                <input data-field="time" type="time" aria-label="Meeting time" class="form-control" value="{{ substr($meeting->time, 0, 5) }}">
                <input data-field="venue" aria-label="Meeting venue" maxlength="255" class="form-control" placeholder="Venue" value="{{ $meeting->venue }}">
                <button type="button" class="btn btn-outline-primary" data-save-meeting>Save</button>
                <button type="button" class="btn btn-outline-danger" data-remove-meeting aria-label="Remove {{ $meeting->day_of_week }} meeting"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
            </div>
        @endforeach
    </div>
    <div class="chapter-meetings__row chapter-meetings__row--new" data-new-meeting>
        <input data-field="title" aria-label="Meeting title" maxlength="255" class="form-control" placeholder="Meeting title">
        <select data-field="day_of_week" aria-label="Day of the week" class="form-control"><option value="">Select day</option>@foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)<option value="{{ $day }}">{{ $day }}</option>@endforeach</select>
        <input data-field="time" type="time" aria-label="Meeting time" class="form-control">
        <input data-field="venue" aria-label="Meeting venue" maxlength="255" class="form-control" placeholder="Venue or meeting location" value="{{ $chapter->address ?: '' }}">
        <button type="button" class="btn btn-primary" data-add-meeting><i class="fa fa-plus" aria-hidden="true"></i> Add</button>
    </div>
    <p class="chapter-meetings__feedback" data-meeting-feedback role="status" aria-live="polite"></p>
    <template data-meeting-template><div class="chapter-meetings__row" data-meeting-row>
        <input data-field="title" aria-label="Meeting title" maxlength="255" class="form-control" placeholder="Meeting title">
        <select data-field="day_of_week" aria-label="Day of the week" class="form-control">@foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $day)<option value="{{ $day }}">{{ $day }}</option>@endforeach</select>
        <input data-field="time" type="time" aria-label="Meeting time" class="form-control">
        <input data-field="venue" aria-label="Meeting venue" maxlength="255" class="form-control" placeholder="Venue">
        <button type="button" class="btn btn-outline-primary" data-save-meeting>Save</button>
        <button type="button" class="btn btn-outline-danger" data-remove-meeting aria-label="Remove meeting"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
    </div></template>
</section>
<style>
.chapter-meetings { margin-top:22px; padding:23px; border:1px solid #dce5ee; border-radius:13px; background:#fff; }
.chapter-meetings__heading { display:flex; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:17px; }
.chapter-meetings__heading h3 { margin:0 0 4px; color:#183251; font-size:19px; }
.chapter-meetings__heading p { margin:0; color:#748397; font-size:12px; }
.chapter-meetings__heading span { color:#2c7099; font-size:11px; font-weight:700; }
.chapter-meetings__list { display:grid; gap:10px; margin-bottom:14px; }
.chapter-meetings__labels { display:grid; grid-template-columns:minmax(150px,1.25fr) minmax(115px,.8fr) minmax(100px,.7fr) minmax(160px,1.5fr) auto auto; gap:9px; margin-bottom:7px; color:#5e7085; font-size:11px; font-weight:800; }
.chapter-meetings__row { display:grid; grid-template-columns:minmax(150px,1.25fr) minmax(115px,.8fr) minmax(100px,.7fr) minmax(160px,1.5fr) auto auto; align-items:center; gap:9px; margin:0; }
.chapter-meetings__row--new { grid-template-columns:minmax(150px,1.25fr) minmax(115px,.8fr) minmax(100px,.7fr) minmax(160px,1.5fr) auto; }
.chapter-meetings__row .form-control { min-width:0; height:40px; }
.chapter-meetings__row .btn { min-height:40px; white-space:nowrap; }
.chapter-meetings__feedback { min-height:18px; margin:12px 0 0; color:#2b765f; font-size:12px; }
.chapter-meetings__feedback.is-error { color:#b42332; }
@media(max-width:767px) { .chapter-meetings { padding:17px; }.chapter-meetings__labels { display:none; }.chapter-meetings__row,.chapter-meetings__row--new { grid-template-columns:1fr 1fr; }.chapter-meetings__row input[data-field=venue] { grid-column:1 / -1; }.chapter-meetings__row .btn { min-width:0; } }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var manager = document.querySelector('[data-meeting-manager]');
    if (!manager) return;
    var list = manager.querySelector('[data-meeting-list]');
    var feedback = manager.querySelector('[data-meeting-feedback]');
    var csrf = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    function report(message, error) { feedback.textContent = message; feedback.classList.toggle('is-error', !!error); }
    function details(row) {
        var values = {};
        row.querySelectorAll('[data-field]').forEach(function (field) { values[field.dataset.field] = field.value.trim(); });
        if (!values.title || !values.day_of_week || !values.time || !values.venue) throw new Error('Enter a title, day, time and venue.');
        return values;
    }
    async function send(url, method, data) {
        var response = await fetch(url, { method: method, headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }, body: data ? JSON.stringify(data) : undefined });
        var result = await response.json();
        if (!response.ok) throw new Error(Object.values(result.errors || {}).flat().join(' ') || result.message || 'Could not save the meeting.');
        return result;
    }
    async function saveRow(row, isNew) {
        var values = details(row);
        var url = isNew ? manager.dataset.storeUrl : manager.dataset.updateUrl.replace('__ID__', row.dataset.meetingId);
        var result = await send(url, isNew ? 'POST' : 'PUT', values);
        if (isNew) {
            var savedRow = manager.querySelector('[data-meeting-template]').content.firstElementChild.cloneNode(true);
            savedRow.dataset.meetingId = result.meeting.id;
            savedRow.querySelectorAll('[data-field]').forEach(function (field) {
                field.value = field.dataset.field === 'time' ? result.meeting.time.slice(0, 5) : result.meeting[field.dataset.field];
            });
            list.appendChild(savedRow);
            row.querySelectorAll('[data-field]').forEach(function (field) { if (field.dataset.field !== 'venue') field.value = ''; });
        } else {
            delete row.dataset.dirty;
        }
    }
    manager.addEventListener('input', function (event) {
        var row = event.target.closest('[data-meeting-row]');
        if (row) row.dataset.dirty = '1';
    });
    manager.addEventListener('change', function (event) {
        var row = event.target.closest('[data-meeting-row]');
        if (row) row.dataset.dirty = '1';
    });
    manager.addEventListener('click', async function (event) {
        var addButton = event.target.closest('[data-add-meeting]');
        var saveButton = event.target.closest('[data-save-meeting]');
        var removeButton = event.target.closest('[data-remove-meeting]');
        var button = addButton || saveButton || removeButton;
        if (!button) return;
        event.preventDefault();
        var row = button.closest('[data-new-meeting], [data-meeting-row]');
        button.disabled = true;
        report(removeButton ? 'Removing meeting…' : 'Saving meeting…');
        try {
            if (removeButton) {
                await send(manager.dataset.destroyUrl.replace('__ID__', row.dataset.meetingId), 'DELETE');
                row.remove();
                report('Meeting removed.');
                return;
            }
            var isNew = !!addButton;
            await saveRow(row, isNew);
            report(isNew ? 'Meeting added.' : 'Meeting updated.');
        } catch (error) { report(error.message, true); }
        finally { button.disabled = false; }
    });
    var mainForm = manager.closest('form');
    var addressInput = mainForm.querySelector('#address');
    var newVenue = manager.querySelector('[data-new-meeting] [data-field="venue"]');
    if (addressInput) {
        var initialAddress = newVenue.value;
        addressInput.addEventListener('input', function () {
            if (newVenue.value === initialAddress) newVenue.value = addressInput.value;
            initialAddress = addressInput.value;
        });
    }
    var resumeSubmit = false;
    mainForm.addEventListener('submit', async function (event) {
        if (resumeSubmit) { resumeSubmit = false; return; }
        var dirtyRows = Array.from(manager.querySelectorAll('[data-meeting-row][data-dirty]'));
        var newRow = manager.querySelector('[data-new-meeting]');
        var newRowStarted = ['title', 'day_of_week', 'time'].some(function (field) { return newRow.querySelector('[data-field="' + field + '"]').value.trim() !== ''; });
        if (!dirtyRows.length && !newRowStarted) return;
        event.preventDefault();
        report('Saving meeting details…');
        try {
            for (var row of dirtyRows) await saveRow(row, false);
            if (newRowStarted) await saveRow(newRow, true);
            report('Meeting details saved.');
            resumeSubmit = true;
            mainForm.requestSubmit(event.submitter || undefined);
        } catch (error) { report(error.message, true); }
    });
});
</script>
