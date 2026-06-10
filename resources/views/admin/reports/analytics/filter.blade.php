@php
if (!$isAdmin) {
    $canViewZone = in_array($user->role_id, array_merge(
        fieldStakeholders(),
        secretariatStakeholders(),
        ncpStakeholders()
    ));
} else {
    $canViewZone = true;
}
@endphp

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">

        <form method="GET" class="graph-submit">

            <div class="row g-3 align-items-end">

                @if($canViewZone)
                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <label class="form-label">Fields</label>
                        <select name="fields[]" class="form-select" multiple>
                            @foreach($fields ?? [] as $field)
                                <option value="{{ $field->id }}"
                                    {{ in_array($field->id, request('fields', [])) ? 'selected' : '' }}>
                                    {{ $field->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-xl-4 col-lg-4 col-md-6">
                        <label class="form-label">Zones</label>
                        <select name="zones[]" class="form-select" multiple></select>
                    </div>
                @endif

                <div class="col-xl-2 col-md-6">
                    <label class="form-label">From</label>
                    <input type="date"
                           name="from_date"
                           class="form-control"
                           value="{{ request('from_date') }}">
                </div>

                <div class="col-xl-2 col-md-6">
                    <label class="form-label">To</label>
                    <input type="date"
                           name="to_date"
                           class="form-control"
                           value="{{ request('to_date') }}">
                </div>

            </div>

            <hr class="my-3">

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">

                <div>
                    <h6 class="mb-0">Report Filters</h6>
                    <small class="text-muted">Filter and export report data</small>
                </div>

                <div class="d-flex flex-wrap gap-2">

                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-filter me-1"></i>
                        Filter
                    </button>

                    {{-- KEEP OLD LOGIC (GET-based export) --}}
                    <button type="submit"
                            name="filter_type"
                            value="pdf"
                            class="btn btn-danger">
                        PDF
                    </button>

                    <button type="submit"
                            name="filter_type"
                            value="excel"
                            class="btn btn-success">
                        Excel
                    </button>

                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
                        Reset
                    </a>

                </div>

            </div>

        </form>

    </div>
</div>
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
