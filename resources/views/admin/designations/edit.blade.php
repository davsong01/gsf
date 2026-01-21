@extends('layouts.dashboard')

@php
    $isEdit = isset($designation);
@endphp

@section('title', $isEdit ? 'Edit Designation' : 'Create Designation')

@section('item')
<li class="breadcrumb-item">
    <a href="{{ route('designation.index') }}">designation</a>
</li>
@endsection

@section('active')
<li class="breadcrumb-item">
    {{ $isEdit ? 'Update Designation' : 'Create Designation' }}
</li>
@endsection

@section('content')
<div class="content-body">
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">

                            <form
                                action="{{ $isEdit ? route('designation.update', $designation->id) : route('designation.store') }}"
                                method="POST"
                            >
                                @csrf
                                @if($isEdit)
                                    @method('PATCH')
                                @endif

                                <div class="row">

                                    {{-- Name --}}
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="name">Name</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="name"
                                                name="name"
                                                value="{{ old('name', $designation->name ?? '') }}"
                                                placeholder="Enter name"
                                                required
                                            >
                                        </fieldset>
                                    </div>

                                    {{-- Order --}}
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="order">Order</label>
                                            <input
                                                type="number"
                                                min="1"
                                                class="form-control"
                                                id="order"
                                                name="order"
                                                value="{{ old('order', $designation->order ?? '') }}"
                                                placeholder="Enter Order"
                                            >
                                        </fieldset>
                                    </div>

                                    {{-- Type --}}
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="type">Type</label>
                                            <select class="form-control" name="type" id="type" required>
                                                <option value="">-- Select Type --</option>
                                                <option value="nec" {{ old('type', $designation->type ?? '') === 'nec' ? 'selected' : '' }}>NEC</option>
                                                <option value="chapter-exco" {{ old('type', $designation->type ?? '') === 'chapter-exco' ? 'selected' : '' }}>Chapter Executive</option>
                                            </select>
                                        </fieldset>
                                    </div>
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="zone_id">Zone</label>
                                            <select class="form-control" name="zone_id" id="zone_id" required>
                                                <option value="">-- Select Zone --</option>
                                                @foreach ($zones as $zone)
                                                    <option value="{{ $zone->id }}" {{ old('zone_id', $designation->zone_id ?? '') == $zone->id ? 'selected' : '' }}>{{ $zone->name }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>

                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="field_id">Field</label>
                                            <select class="form-control" name="field_id" id="field_id" required>
                                                <option value="">-- Select Field --</option>
                                                @foreach ($fields as $field)
                                                    <option value="{{ $field->id }}" {{ old('field_id', $designation->field_id ?? '') == $field->id ? 'selected' : '' }}>{{ $field->name }}</option>
                                                @endforeach
                                            </select>
                                        </fieldset>
                                    </div>
                                    {{-- Status --}}
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" name="status" id="status" required>
                                                <option value="">-- Select Status --</option>
                                                <option value="active" {{ old('status', $designation->status ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('status', $designation->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </fieldset>
                                    </div>

                                    {{-- Submit --}}
                                    <div class="col-md-12 col-sm-12">
                                        <button class="btn btn-primary w-100" type="submit">
                                            {{ $isEdit ? 'Update Designation' : 'Create Designation' }}
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
