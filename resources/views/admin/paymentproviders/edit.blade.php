@extends('layouts.dashboard')
@section('title', 'Edit Payment Provider')
@section('item')
<li class="breadcrumb-item"> <a href="{{ route('paymentproviders.index') }}">Payment Providers</a></li>
@endsection
@section('active')
<li class="breadcrumb-item">Edit Payment Provider</li>
@endsection

@section('content')
<div class="content-body">
    <section id="basic-input">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        @include('includes.alerts')
                    </div>

                    <div class="card-content">
                        <div class="card-body">
                            <form 
                                action="{{ isset($paymentprovider->id) ? route('paymentproviders.update', $paymentprovider->id) : route('paymentproviders.store') }}" 
                                method="POST" 
                                enctype="multipart/form-data"
                            >
                                @csrf
                                @if(isset($paymentprovider->id))
                                    @method('PATCH')
                                @endif

                                <div class="row">
                                    {{-- Left column --}}
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label for="name">Provider Name</label>
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                id="name" 
                                                name="name"
                                                value="{{ old('name', $paymentprovider->name ?? '') }}" 
                                                placeholder="Enter provider name" 
                                                required
                                            >
                                            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="slug">Engine</label>
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                id="slug" 
                                                name="slug"
                                                value="{{ old('slug', $paymentprovider->slug ?? '') }}" 
                                                placeholder="e.g. paystack" 
                                                required
                                            >
                                            @error('slug') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control" name="status" id="status" required>
                                                <option value="inactive" {{ old('status', $paymentprovider->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                <option value="active" {{ old('status', $paymentprovider->status ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                                            </select>
                                            @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="description">Description (optional)</label>
                                            <textarea 
                                                class="form-control" 
                                                id="description" 
                                                name="description" 
                                                rows="4"
                                                placeholder="Enter description">{{ old('description', $paymentprovider->description ?? '') }}</textarea>
                                            @error('description') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="base_url">Base URL (optional)</label>
                                            <input 
                                                type="url" 
                                                class="form-control" 
                                                id="base_url" 
                                                name="base_url"
                                                value="{{ old('base_url', $paymentprovider->base_url ?? '') }}" 
                                                placeholder="https://api.provider.com/">
                                            @error('base_url') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="provider_charge">Provider Charge (optional)</label>
                                            <input 
                                                type="number" 
                                                step="0.01" 
                                                min="0" 
                                                class="form-control" 
                                                id="provider_charge" 
                                                name="provider_charge"
                                                value="{{ old('provider_charge', $paymentprovider->provider_charge ?? '') }}" 
                                                placeholder="e.g. 50.00">
                                            @error('provider_charge') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>
                                        <fieldset class="form-group">
                                            <label for="logo">Upload Logo (optional)</label>
                                            <input type="file" accept="image/*" class="form-control" name="logo" id="logo">
                                            @if (!empty($paymentprovider->logo))
                                                <small class="d-block mt-1">
                                                    Current: 
                                                    <a href="{{ asset($paymentprovider->logo) }}" target="_blank">
                                                        <img src="{{ asset($paymentprovider->logo) }}" alt="Logo" style="width:40px; height:auto;">
                                                    </a>
                                                </small>
                                            @endif
                                            @error('logo') <small class="text-danger d-block">{{ $message }}</small> @enderror
                                        </fieldset>
                                    </div>

                                    {{-- Right column --}}
                                    <div class="col-md-6 col-sm-12">
                                        <fieldset class="form-group">
                                            <label>Channels</label>
                                            <div class="d-flex flex-wrap">
                                                @php
                                                    $savedChannels = old('channels', $paymentprovider->channels ?? []);
                                                    $options = [
                                                        'card' => 'Card',
                                                        'bank' => 'Bank',
                                                        'ussd' => 'USSD',
                                                        'qr' => 'QR',
                                                        'mobile_money' => 'Mobile Money',
                                                        'bank_transfer' => 'Bank Transfer',
                                                        'account_transfer' => 'Account Transfer',
                                                        'apple_pay' => 'Apple Pay',
                                                    ];
                                                @endphp

                                                @foreach($options as $val => $label)
                                                    <div class="form-check mr-3 mb-2">
                                                        <input 
                                                            class="form-check-input" 
                                                            type="checkbox" 
                                                            name="channels[]" 
                                                            id="{{ $val }}" 
                                                            value="{{ $val }}"
                                                            {{ in_array($val, (array)$savedChannels) ? 'checked' : '' }}
                                                        >
                                                        <label class="form-check-label" for="{{ $val }}">{{ $label }}</label>
                                                    </div>
                                                @endforeach
                                            </div>
                                            @error('channels') <small class="text-danger d-block">{{ $message }}</small> @enderror
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="api_key">API Key</label>
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                id="api_key" 
                                                name="api_key"
                                                value="{{ old('api_key', $paymentprovider->api_key ?? '') }}" 
                                                placeholder="API key">
                                            @error('api_key') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="secret_key">Secret Key</label>
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                id="secret_key" 
                                                name="secret_key"
                                                value="{{ old('secret_key', $paymentprovider->secret_key ?? '') }}" 
                                                placeholder="Secret key">
                                            @error('secret_key') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="public_key">Public Key</label>
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                id="public_key" 
                                                name="public_key"
                                                value="{{ old('public_key', $paymentprovider->public_key ?? '') }}" 
                                                placeholder="Public key">
                                            @error('public_key') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="customer_pays_provider_charge">Customer Pays Provider Charge</label>
                                            <select class="form-control" name="customer_pays_provider_charge" id="customer_pays_provider_charge" required>
                                                <option value="1" {{ old('customer_pays_provider_charge', $paymentprovider->customer_pays_provider_charge ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                                                <option value="0" {{ old('customer_pays_provider_charge', $paymentprovider->customer_pays_provider_charge ?? 0) == 0 ? 'selected' : '' }}>No</option>
                                            </select>
                                            @error('customer_pays_provider_charge') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="enable_sub_account">Enable Sub Account</label>
                                            <select class="form-control" name="enable_sub_account" id="enable_sub_account" required>
                                                <option value="1" {{ old('enable_sub_account', $paymentprovider->enable_sub_account ?? 0) == 1 ? 'selected' : '' }}>Yes</option>
                                                <option value="0" {{ old('enable_sub_account', $paymentprovider->enable_sub_account ?? 0) == 0 ? 'selected' : '' }}>No</option>
                                            </select>
                                            @error('enable_sub_account') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="sub_account_code">Sub Account Code</label>
                                            <input 
                                                type="text" 
                                                class="form-control" 
                                                id="sub_account_code" 
                                                name="sub_account_code"
                                                value="{{ old('sub_account_code', $paymentprovider->sub_account_code ?? '') }}" 
                                                placeholder="Enter sub account code">
                                            @error('sub_account_code') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>

                                        <fieldset class="form-group">
                                            <label for="sub_account_fee_percentage">Sub Account Fee Percentage (%)</label>
                                            <input 
                                                type="number" 
                                                step="0.01" 
                                                class="form-control" 
                                                id="sub_account_fee_percentage" 
                                                name="sub_account_fee_percentage"
                                                value="{{ old('sub_account_fee_percentage', $paymentprovider->sub_account_fee_percentage ?? 0) }}" 
                                                placeholder="Enter fee percentage (e.g. 80)">
                                            @error('sub_account_fee_percentage') <small class="text-danger">{{ $message }}</small> @enderror
                                        </fieldset>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <button class="btn btn-primary w-100" type="submit">
                                            {{ isset($paymentprovider->id) ? 'Update' : 'Create' }}
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
