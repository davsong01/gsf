@php
    $type = $type ?? '';
    $isAdmin = (bool) ($isAdmin ?? false);
    $canViewZone = $isAdmin || (isset($user) && in_array($user->role_id ?? null, array_merge(
        fieldStakeholders(), secretariatStakeholders(), ncpStakeholders()
    )));
    $zoneOptions = collect($zones ?? [])->map(fn ($zone) => ['id' => (string) $zone->id, 'name' => $zone->name])->values()->all();
@endphp

<section class="analytics-filter" aria-labelledby="analytics-filter-title">
    <div class="analytics-filter__head">
        <span class="analytics-filter__icon"><i class="fa fa-sliders" aria-hidden="true"></i></span>
        <div><h2 id="analytics-filter-title">Refine your view</h2><p>Choose a reporting period and the parts of the network you want to see.</p></div>
    </div>
    <form method="GET" class="graph-submit">
        <div class="analytics-filter__body">
            @if($type === 'section')
                <div class="analytics-filter__group">
                    <div class="analytics-filter__field analytics-filter__field--question"><label for="analytics-sections">Sections</label><select id="analytics-sections" name="sections[]" class="form-select select2 sections-select" multiple><option value="all" @selected(in_array('all', (array) request('sections', [])))>All sections</option>@foreach($sections ?? [] as $section)<option value="{{ $section->id }}" @selected(in_array((string) $section->id, array_map('strval', (array) request('sections', [])), true))>{{ $section->name }}</option>@endforeach</select></div>
                    <div class="analytics-filter__field analytics-filter__field--question"><label for="analytics-subsections">Subsections</label><select id="analytics-subsections" name="sub_sections[]" class="form-select select2 sub-sections-select" multiple></select></div>
                    <div class="analytics-filter__field analytics-filter__field--question"><label for="analytics-questions">Question items</label><select id="analytics-questions" name="questions[]" class="form-select select2 questions-select" multiple></select></div>
                </div>
            @endif
            <div class="analytics-filter__group">
                @if($canViewZone)
                    <div class="analytics-filter__field analytics-filter__field--wide"><label for="analytics-fields">Fields</label><select id="analytics-fields" name="fields[]" class="form-select" multiple>@foreach($fields ?? [] as $field)<option value="{{ $field->id }}" @selected(in_array((string) $field->id, array_map('strval', (array) request('fields', [])), true))>{{ $field->name }}</option>@endforeach</select></div>
                    <div class="analytics-filter__field analytics-filter__field--wide"><label for="analytics-zones">Zones</label><select id="analytics-zones" name="zones[]" class="form-select" multiple>@foreach($zones ?? [] as $zone)<option value="{{ $zone->id }}" @selected(in_array((string) $zone->id, array_map('strval', (array) request('zones', [])), true))>{{ $zone->name }}</option>@endforeach</select></div>
                @endif
                <div class="analytics-filter__field {{ $canViewZone ? '' : 'analytics-filter__field--half' }}"><label for="analytics-from">From</label><input id="analytics-from" type="date" name="from_date" class="form-control" value="{{ request('from_date') }}"></div>
                <div class="analytics-filter__field {{ $canViewZone ? '' : 'analytics-filter__field--half' }}"><label for="analytics-to">To</label><input id="analytics-to" type="date" name="to_date" class="form-control" value="{{ request('to_date') }}"></div>
            </div>
        </div>
        <div class="analytics-filter__actions">
            <p>Exports use the same filters as the results below.</p>
            <div class="analytics-filter__buttons">
                <button type="submit" class="btn btn-primary"><i class="fa fa-filter" aria-hidden="true"></i> Apply filters</button>
                @if($type !== 'section')<button type="submit" name="filter_type" value="pdf" class="btn btn-danger"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF</button>@endif
                <button type="submit" name="filter_type" value="excel" class="btn btn-success"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Excel</button>
                <a href="{{ url()->current() }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </div>
    </form>
</section>

<script>
$(function () {
    const fieldSelect = $('#analytics-fields');
    const zoneSelect = $('#analytics-zones');
    if (!fieldSelect.length || !zoneSelect.length) return;

    const allZones = @json($zoneOptions);
    const initialZones = @json(array_map('strval', (array) request('zones', [])));

    function setZones(zones, selected = []) {
        zoneSelect.empty();
        zones.forEach(function (zone) {
            const id = String(zone.id);
            zoneSelect.append(new Option(zone.name, id, selected.includes(id), selected.includes(id)));
        });
        zoneSelect.prop('disabled', false).trigger('change.select2');
    }

    function loadZones(selectedFields, selected = []) {
        if (!selectedFields.length) {
            setZones(allZones, selected);
            return;
        }
        zoneSelect.prop('disabled', true);
        $.getJSON('/ajax/zones-by-fields', { fields: selectedFields })
            .done(function (zones) { setZones(zones, selected); })
            .fail(function () { setZones(allZones, selected); });
    }

    loadZones(fieldSelect.val() || [], initialZones);
    fieldSelect.on('change', function () { loadZones(fieldSelect.val() || []); });
});
</script>
