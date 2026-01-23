@extends('layouts.dashboard')

@section('title', isset($stakeholder) ? 'Update Stakeholder' : 'Create Stakeholder')

@section('item')
<li class="breadcrumb-item">
    <a href="{{ route('stakeholderpersonnel.index') }}">All Stakeholders</a>
</li>
@endsection

@section('active')
<li class="breadcrumb-item">{{ isset($stakeholder) ? 'Update' : 'Create' }} Stakeholder</li>
@endsection

@section('content')
<div class="content-body">
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">@include('includes.alerts')</div>

                    <div class="card-content">
                        <div class="card-body">
                            <form
                                action="{{ isset($stakeholder)
                                    ? route('stakeholderpersonnel.update', $stakeholder->id)
                                    : route('stakeholderpersonnel.store') }}"
                                method="POST"
                                enctype="multipart/form-data"
                            >
                                @csrf
                                @if(isset($stakeholder))
                                    @method('PATCH')
                                @endif

                                {{-- PERSONAL DETAILS --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="sections">Personal Details</label><br>
                                    </div>

                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <label for="name">Name</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                value="{{ old('name', $stakeholder->name ?? '') }}"
                                                placeholder="Enter name" required>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <label for="phone">Phone</label>
                                            <input type="text" class="form-control" id="phone" name="phone"
                                                value="{{ old('phone', $stakeholder->phone ?? '') }}"
                                                placeholder="Enter phone">
                                        </fieldset>
                                    </div>

                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <label for="email">Email</label>
                                            <input type="email" class="form-control" id="email" name="email"
                                                value="{{ old('email', $stakeholder->email ?? '') }}"
                                                placeholder="Enter email" required>
                                        </fieldset>
                                    </div>
                                   <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <label for="gender">Gender</label>
                                            <select class="form-control" name="gender" id="gender">
                                                <option value="">--Select--</option>
                                                <option value="Male" {{ old('gender', $stakeholder->gender ?? '') === 'Male' ? 'selected' : '' }}>Male</option>
                                                <option value="Female" {{ old('gender', $stakeholder->gender ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-4 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="avatar">Avatar</label>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                @if(!empty($stakeholder?->avatar))
                                                    <img
                                                        src="{{ asset($stakeholder->avatar) }}"
                                                        alt="Current Avatar"
                                                        style="height: 55px; width: 55px; border-radius: 50%; object-fit: cover;">
                                                @endif
                                                <input
                                                    type="file"
                                                    class="form-control"
                                                    id="avatar"
                                                    name="avatar"
                                                    placeholder="Upload avatar"
                                                    style="flex: 1;">
                                            </div>
                                        </fieldset>
                                    </div>

                                </div>

                                {{-- BIRTHDAY DETAILS --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="sections">Birthday Details</label><br>
                                    </div>

                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <label for="day">Day</label>
                                            <select class="form-control" name="day" id="day">
                                                <option value="">--Select--</option>
                                                @foreach(range(1, 31) as $day)
                                                    <option value="{{ $day }}"
                                                        {{ old('day', $stakeholder->day ?? '') == $day ? 'selected' : '' }}>
                                                        {{ $day }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <label for="month">Month</label>
                                            <select class="form-control" name="month" id="month">
                                                <option value="">--Select--</option>
                                                @foreach($months as $month => $value)
                                                    <option value="{{ $value }}"
                                                        {{ old('month', $stakeholder->month ?? '') == $value ? 'selected' : '' }}>
                                                        {{ $month }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <label for="year">Year (Optional)</label>
                                            <input type="text" class="form-control" id="year"
                                                name="year" pattern="^\d{4}$"
                                                value="{{ old('year', $stakeholder->year ?? '') }}"
                                                placeholder="Enter year">
                                        </fieldset>
                                    </div>

                                </div>

                                {{-- OFFICIAL DETAILS --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="sections">Digital Portal Details</label><br>
                                    </div>

                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="role_id">Role</label>

                                            <select class="form-control" name="role_id" id="role_id" required>
                                                <option value="">--Select--</option>
                                                @foreach($roles as $role)
                                                    <option value="{{ $role->id }}" data-slug="{{ $role->slug }}"
                                                        {{ old('role_id', $stakeholder->role_id ?? '') == $role->id ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6 selectfield" style="display:none;">
                                        {{-- Field Pastor --}}
                                        <fieldset class="form-group">
                                            <label for="field_id">Field</label>
                                            <select class="form-control" name="field_id" id="field_id">
                                                <option value="">--Select--</option>
                                                @foreach($fields as $field)
                                                    <option value="{{ $field->id }}"
                                                        {{ old('field_id', $stakeholder->field_id ?? '') == $field->id ? 'selected' : '' }}>
                                                        {{ $field->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    {{-- Zonal Pastor --}}
                                    <div class="col-md-6 selectzone" style="display:none;">
                                        <fieldset class="form-group" >
                                            <label for="zone_id">Zone</label>
                                            <select class="form-control" name="zone_id" id="zone_id">
                                                <option value="">--Select--</option>
                                                @foreach($zones as $zone)
                                                    <option value="{{ $zone->id }}"
                                                        {{ old('zone_id', $stakeholder->zone_id ?? '') == $zone->id ? 'selected' : '' }}>
                                                        {{ $zone->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 selectportfolio" style="display:none;">
                                        <fieldset class="form-group">
                                            <label for="portfolio">Office</label>
                                            <select class="form-control" name="portfolio" id="portfolio"
                                                data-current="{{ $stakeholder->designation_id ?? '' }}">
                                                <option value="">--Select--</option>
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-6 selectchapter" style="display:none;">
                                        <fieldset class="form-group">
                                            <label for="chapter_id">Chapter</label>
                                            <select class="form-control" name="chapter_id" id="chapter_id">
                                                <option value="">--Select--</option>
                                                @foreach($chapters as $chapter)
                                                    <option value="{{ $chapter->id }}"
                                                        {{ old('chapter_id', $stakeholder->chapter_id ?? '') == $chapter->id ? 'selected' : '' }}>
                                                        {{ $chapter->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6 selectdesignation" style="display:none;">
                                        <fieldset class="form-group">
                                            <label for="designation_id">Designation</label>
                                            <select class="form-control" name="designation_id" id="designation_id"
                                                data-current="{{ $stakeholder->designation_id ?? '' }}">
                                                <option value="">--Select--</option>
                                            </select>
                                        </fieldset>
                                    </div>
                                </div>

                                {{-- PASSWORD & SIGNATURE --}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="chapter_id">Status</label>
                                            <select class="form-control" name="status" id="status">
                                                <option value="">--Select--</option>
                                                <option value="active" {{ old('status', $stakeholder->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('status', $stakeholder->status ?? '') == 'inactive' ? 'selected' : '' }}>InActive</option>
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-6">
                                        <fieldset class="form-group">
                                            <label for="password">
                                                {{ isset($stakeholder) ? 'Change Password' : 'Password (Default: 12345@GSF0101)' }}
                                            </label>
                                            <input type="password" class="form-control" id="password" name="password" autocomplete="new-password">

                                        </fieldset>
                                    </div>
                                </div>

                                {{-- SUBMIT --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <button class="btn btn-primary w-100" type="submit">
                                            {{ isset($stakeholder) ? 'Update' : 'Save' }}
                                        </button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection

@section('extra_scripts')
<script>
$(document).ready(function () {
    const domain = window.location.hostname;

    const $name        = $('#name');
    const $email       = $('#email');
    const $role        = $('#role_id');
    const $designation = $('#designation_id');
    const $portfolio   = $('#portfolio');
    const $zone        = $('#zone_id');
    const $field       = $('#field_id');

    /* -------------------------------
     * Email autofill
     * ----------------------------- */
    $name.on('input', function () {
        if ($email.data('manual')) return;

        const rawName = $(this).val().trim();
        if (!rawName) return $email.val('');

        const parts = rawName.toLowerCase()
            .replace(/[^a-z\s]/g, '')
            .split(/\s+/)
            .filter(Boolean);

        const first = parts[0] || '';
        const last  = parts.length > 1 ? parts.at(-1) : '';

        $email.val([first, last].filter(Boolean).join('_') + '@' + domain);
    });

    $email.on('input', () => $email.data('manual', true));

    /* -------------------------------
     * Load designations (POST)
     * ----------------------------- */
   function loadDesignations(roleSlug, selectedId = null) {
        if (!roleSlug) return;

        const payload = {
            role: roleSlug,
            zone_id: $zone.val() || null,
            field_id: $field.val() || null,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $designation.closest('.selectdesignation').hide();
        $designation.html('<option value="">--Loading--</option>');

        $.post('/ajax/designations-by-role', payload)
            .done(function (data) {
                $designation.empty();

                if (!Array.isArray(data) || !data.length) return;

                let hasSelected = false;

                data.forEach((d, index) => {
                    let selected = '';

                    // 1️⃣ Edit mode: honor existing selection
                    if (selectedId && selectedId == d.id) {
                        selected = 'selected';
                        hasSelected = true;
                    }

                    // 2️⃣ Create mode OR fallback → select first
                    if (!selectedId && index === 0) {
                        selected = 'selected';
                    }

                    $designation.append(
                        `<option value="${d.id}" ${selected}>${d.name}</option>`
                    );
                });

                // 3️⃣ If edit mode ID not found, fallback to first
                if (selectedId && !hasSelected) {
                    $designation.find('option:first').prop('selected', true);
                }

                $designation.closest('.selectdesignation').show();
            })
            .fail(() => {
                $designation.html('<option value="">--Error loading--</option>');
            });
    }


    /* -------------------------------
     * Load offices (portfolio / nec)
     * ----------------------------- */
    function loadOffices(roleSlug, selectedOffice = null) {
        console.log('loadOffices firing for:', roleSlug);

        if (!['portfolio', 'nec', 'nec-member'].includes(roleSlug)) {
            console.warn('Role not allowed for offices:', roleSlug);
            return;
        }

        const url = `/ajax/offices/${roleSlug}`;
        console.log('Request URL:', url);

        $portfolio.closest('.selectportfolio').hide();
        $portfolio.html('<option value="">--Loading--</option>');

        $.ajax({
            url,
            method: 'GET',
            dataType: 'json'
        })
        .done(function (data) {
            console.log('Offices response:', data);

            $portfolio.empty();

            if (!data.length) {
                $portfolio.append('<option value="">No offices found</option>');
            } else {
                data.forEach(o => {
                    const selected =
                        selectedOffice && selectedOffice == o.id ? 'selected' : '';
                    $portfolio.append(
                        `<option value="${o.id}" ${selected}>${o.name}</option>`
                    );
                });
            }

            $portfolio.closest('.selectportfolio').show();
        })
        .fail(function (xhr, status, error) {
            console.error('Office AJAX failed:', status, error);
            console.error('Response:', xhr.responseText);

            $portfolio.html('<option value="">--Error loading offices--</option>');
        })
        .always(function () {
            console.log('Office AJAX completed');
        });
    }


    /* -------------------------------
     * Visibility logic
     * ----------------------------- */
    function updateVisibility(roleSlug) {
        $('.selectfield, .selectzone, .selectportfolio, .selectchapter, .selectdesignation').hide();

        if (roleSlug === 'field-pastor') $('.selectfield').show();
        if (roleSlug === 'zonal-pastor') $('.selectzone').show();
        if (roleSlug === 'chapter-representative') $('.selectchapter').show();
        if (['portfolio', 'nec', 'nec-member'].includes(roleSlug)) $('.selectportfolio').show();

        const rolesWithDesignation = [
            'portfolio', 'chapter-representative', 'field-pastor',
            'nec', 'nec-member', 'zonal-pastor', 'secretariat', 'ncp'
        ];

        if (rolesWithDesignation.includes(roleSlug)) {
            loadDesignations(roleSlug, $designation.data('current'));
        }

        if (['portfolio', 'nec', 'nec-member'].includes(roleSlug)) {
            loadOffices(roleSlug, $portfolio.data('current'));
        }
    }

    /* -------------------------------
     * Triggers
     * ----------------------------- */
    $role.on('change', function () {
        updateVisibility($(this).find(':selected').data('slug'));
    });

    // Only reload designations when zone / field changes
    $zone.add($field).on('change', function () {
        const roleSlug = $role.find(':selected').data('slug');
        if (roleSlug) loadDesignations(roleSlug, $designation.data('current'));
    });

    // Initial edit load
    const initialRole = $role.find(':selected').data('slug');
    if (initialRole) updateVisibility(initialRole);
});
</script>
@endsection

