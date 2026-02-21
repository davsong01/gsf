@extends('layouts.dashboard')

@php
    $isEdit = isset($stakeholdersetting);
@endphp

@section('title', $isEdit ? 'Edit Setting' : 'Create Setting')

@section('item')
<li class="breadcrumb-item">
    <a href="{{ route('stakeholdersetting.index') }}">Settings</a>
</li>
@endsection

@section('active')
<li class="breadcrumb-item">
    {{ $isEdit ? 'Update Setting' : 'Create Setting' }}
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
                                action="{{ $isEdit ? route('stakeholdersetting.update', $stakeholdersetting->id) : route('stakeholdersetting.store') }}"
                                method="POST"
                            >
                                @csrf
                                @if($isEdit)
                                    @method('PATCH')
                                @endif

                                <div class="row">

                                    {{-- Key --}}
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="key">Key</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="key"
                                                name="key"
                                                value="{{ old('key', $stakeholdersetting->key ?? '') }}"
                                                placeholder="Enter setting key"
                                                required
                                            >
                                        </fieldset>
                                    </div>

                                    {{-- Value --}}
                                    <div class="col-md-12 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="value">Value</label>
                                            <input
                                                type="text"
                                                class="form-control"
                                                id="value"
                                                name="value"
                                                value="{{ old('value', $stakeholdersetting->value ?? '') }}"
                                                placeholder="Enter setting value"
                                            >
                                        </fieldset>
                                    </div>

                                    {{-- Submit --}}
                                    <div class="col-md-12 col-sm-12">
                                        <button class="btn btn-primary w-100" type="submit">
                                            {{ $isEdit ? 'Update Setting' : 'Create Setting' }}
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
