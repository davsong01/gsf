<form method="GET" class="row g-2 align-items-end graph-submit">
    <div class="col-md-4 mt-1">
        <label>Fields (Start Typing)</label>
        <select name="fields[]" class="form-select" multiple>
            @foreach($fields ?? [] as $field)
                <option value="{{ $field->id }}"
                    {{ in_array($field->id, request('fields', [])) ? 'selected' : '' }}>
                    {{ $field->name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4 mt-1">
        <label>Zones (Start Typing)</label>
        <select name="zones[]" class="form-select" multiple>
            <!-- Options will be loaded via AJAX -->
        </select>
    </div>

    <div class="col-md-2 mt-1">
        <label>From</label>
        <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
    </div>

    <div class="col-md-2 mt-1">
        <label>To</label>
        <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
    </div>

    <div class="col-md-2 d-flex gap-2 mt-1">
        <button type="submit" class="btn btn-primary w-100">Filter</button>
        <button type="submit" name="filter_type" value="download" class="btn btn-success"
                formmethod="post" formaction="{{ route(Route::currentRouteName(), $type) }}">
            Download
        </button>
        <a href="{{ url()->current() }}" class="btn btn-outline-danger w-100">Reset</a>
    </div>
</form>

<script>
$(document).ready(function () {
    const zoneSelect = $('select[name="zones[]"]');
    const fieldSelect = $('select[name="fields[]"]');

    function loadZones(selectedFields = [], preselectedZones = []) {
        zoneSelect.prop('disabled', true).empty();

        if (selectedFields.length === 0) {
            zoneSelect.prop('disabled', false);
            return;
        }

        $.ajax({
            url: '/ajax/zones-by-fields',
            type: 'GET',
            data: { fields: selectedFields },
            dataType: 'json',
            success: function (zones) {
                zones.forEach(zone => {
                    const option = new Option(zone.name, zone.id, preselectedZones.includes(zone.id), preselectedZones.includes(zone.id));
                    zoneSelect.append(option);
                });
                zoneSelect.prop('disabled', false);
            },
            error: function () {
                zoneSelect.prop('disabled', false);
            }
        });
    }

    // Initial load if page has selected fields
    const initialFields = fieldSelect.val() || [];
    const initialZones = @json(request('zones', []));
    if (initialFields.length > 0) {
        loadZones(initialFields, initialZones);
    }

    // On field change
    fieldSelect.on('change', function () {
        const selected = $(this).val() || [];
        loadZones(selected);
    });
});
</script>
