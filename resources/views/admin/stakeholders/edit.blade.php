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
                                </div>

                                {{-- BIRTHDAY DETAILS --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <label class="sections">Birthday Details</label><br>
                                    </div>

                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <label for="day">Day</label>
                                            <select class="form-control" name="day" id="day" required>
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
                                            <select class="form-control" name="month" id="month" required>
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
                                        <label class="sections">Official Details</label><br>
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

                                    <div class="col-md-6">
                                        {{-- Field Pastor --}}
                                        <fieldset class="form-group selectfield" style="display:none;">
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

                                        {{-- Zonal Pastor --}}
                                        <fieldset class="form-group selectzone" style="display:none;">
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

                                        <fieldset class="form-group selectportfolio" style="display:none;">
                                            <label for="portfolio">Portfolio</label>
                                            <select class="form-control" name="portfolio" id="portfolio">
                                                <option value="">--Select--</option>
                                                @foreach($portfolios as $portfolio)
                                                    <option value="{{ $portfolio }}"
                                                        {{ old('portfolio', $stakeholder->portfolio ?? '') == $portfolio ? 'selected' : '' }}>
                                                        {{ $portfolio }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                        <fieldset class="form-group selectchapter" style="display:none;">
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
                                </div>

                                {{-- PASSWORD & SIGNATURE --}}
                                <div class="row">
                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <label for="chapter_id">Status</label>
                                            <select class="form-control" name="status" id="status">
                                                <option value="">--Select--</option>
                                                <option value="active" {{ old('status', $stakeholder->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('status', $stakeholder->status ?? '') == 'inactive' ? 'selected' : '' }}>InActive</option>
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <label for="password">
                                                {{ isset($stakeholder) ? 'Change Password' : 'Password (Default: 12345@GSF0101)' }}
                                            </label>
                                            <input type="password" class="form-control" id="password" name="password" autocomplete="new-password">

                                        </fieldset>
                                    </div>

                                    {{-- @if(isset($stakeholder))
                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <label for="signature">Signature</label>
                                            <div class="d-flex align-items-center gap-3">
                                                @if(!empty($stakeholder->signature))
                                                    <div class="border rounded bg-light">
                                                        <img src="{{ $stakeholder->signature }}"
                                                            alt="Signature"
                                                            style="width:auto; height:40px;">
                                                    </div>
                                                @endif
                                                <div class="flex-grow-1">
                                                    <input type="file" class="form-control" id="signature" name="signature">
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>

                                    @else
                                    <div class="col-md-4">
                                        <fieldset class="form-group">
                                            <label for="signature">Upload Signature</label>
                                            <input type="file" class="form-control" id="signature" name="signature" required>
                                        </fieldset>
                                    </div>
                                    @endif --}}
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
    const $name = $('#name');
    const $email = $('#email');

    // Autofill email
    $name.on('input', function () {
        if ($email.data('manual')) return;
        const name = $(this).val().trim().toLowerCase().replace(/[^a-z\s]/g, '');
        if (!name) return $email.val('');
        const parts = name.split(/\s+/).filter(Boolean);
        const first = parts[0] || '';
        const last = parts.length > 1 ? parts[parts.length - 1] : '';
        $email.val(`${[first, last].filter(Boolean).join('_')}@${domain}`);
    });

    $email.on('input', () => $email.data('manual', true));

    // Role logic
    // $('#role').on('change', function () {
    //     const role = $(this).val();
    //     $('.selectfield, .selectzone, .selectportfolio, .selectchapter').hide();
    //     if(in_array(role, ['secretariat']))
    //     if (role === 'Chapter President' || role === 'Chapter Secretary' || role === 'Chapter Financial Secretary') $('.selectchapter').show();
    //     if (role === 'Zonal Pastor') $('.selectzone').show();
    //     if (role === 'Field Pastor') $('.selectfield').show();
    //     if (role === 'Portfolio') $('.selectportfolio').show();
    // }).trigger('change'); // initialize
    $('#role_id').on('change', function () {
        const roleSlug = $(this).find('option:selected').data('slug');

        // Hide all conditional fields first
        $('.selectfield, .selectzone, .selectportfolio, .selectchapter').hide();

        // Show fields based on slug
        if (['chapter-representative'].includes(roleSlug)) {
            $('.selectchapter').show();
        }
        if (roleSlug === 'zonal-pastor') {
            $('.selectzone').show();
        }
        if (roleSlug === 'field-pastor') {
            $('.selectfield').show();
        }
        if (roleSlug === 'portfolio') {
            $('.selectportfolio').show();
        }
    }).trigger('change');

});

$(document).ready(function () {
    const domain = window.location.hostname; // example: mychurch.org
    const $name = $('#name');
    const $email = $('#email');

    $name.on('input', function () {
        const rawName = $(this).val().trim();

        // Stop auto-filling if user has manually edited email
        if ($email.data('manual')) return;

        if (rawName.length > 0) {
            const parts = rawName
                .toLowerCase()
                .replace(/[^a-z\s]/g, '') // remove special characters
                .split(/\s+/)             // split by spaces
                .filter(Boolean);

            let firstName = parts[0] || '';
            let lastName = parts.length > 1 ? parts[parts.length - 1] : '';

            // If middle names exist, ignore them for email
            const emailPrefix = [firstName, lastName].filter(Boolean).join('_');
            const email = `${emailPrefix}@${domain}`;

            $email.val(email);
        } else {
            $email.val('');
        }
    });

    // Detect manual change of email input
    $email.on('input', function () {
        $email.data('manual', true);
    });
});
</script>
@endsection
