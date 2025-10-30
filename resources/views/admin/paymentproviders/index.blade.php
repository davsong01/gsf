@extends('layouts.dashboard')
@section('title', 'Payment Providers')

@section('active')
<li class="breadcrumb-item">Payment Providers</li>
@endsection

@section('content')
<div class="content-body">
    <section id="basic-datatable">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title">Payment Providers</h4>
                        <a href="{{ route('paymentproviders.create') }}" class="btn btn-primary">Add New Provider</a>
                    </div>


                    <div class="card-content">
                        <div class="card-body card-dashboard">
                            <div class="table-responsive">
                                <table class="table zero-configuration">
                                    <thead>
                                        <tr>
                                            <th>S/N</th>
                                            <th>Provider</th>
                                            <th>Status</th>
                                            <th>Charge</th>
                                            <th>Customer Pays?</th>
                                            <th>Base URL</th>
                                            <th>Channels</th>
                                            <th>Created At</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                        @forelse($providers as $provider)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>

                                                <td>
                                                    <strong>{{ $provider->name }}</strong> <br>
                                                    <small class="text-muted">{{ $provider->slug }}</small>
                                                    @if($provider->logo)
                                                        <div>
                                                            <img src="{{ asset($provider->logo) }}" 
                                                                 alt="Logo" width="50" class="mt-1 rounded">
                                                        </div>
                                                    @endif
                                                </td>

                                                <td>
                                                    <span class="badge badge-{{ $provider->status === 'active' ? 'success' : 'secondary' }}">
                                                        {{ ucfirst($provider->status) }}
                                                    </span>
                                                </td>

                                                <td>
                                                    ₦{{ number_format($provider->provider_charge ?? 0, 2) }}
                                                </td>

                                                <td>
                                                    @if($provider->customer_pays_provider_charge)
                                                        <span class="badge badge-info">Yes</span>
                                                    @else
                                                        <span class="badge badge-light">No</span>
                                                    @endif
                                                </td>

                                                <td>{{ $provider->base_url ?? '—' }}</td>

                                                <td>
                                                    @php
                                                        $channels = is_array($provider->channels) 
                                                            ? $provider->channels 
                                                            : json_decode($provider->channels, true);
                                                    @endphp
                                                    @if(!empty($channels))
                                                        @foreach($channels as $ch)
                                                            <span class="badge badge-light-primary">{{ strtoupper($ch) }}</span>
                                                        @endforeach
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>

                                                <td>{{ $provider->created_at->format('d M, Y') }}</td>

                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('paymentproviders.edit', $provider->id) }}" 
                                                           class="btn btn-sm btn-outline-primary" 
                                                           data-toggle="tooltip" title="Edit">
                                                            <i class="bx bxs-edit"></i>
                                                        </a>
                                                        
                                                        <form method="POST" 
                                                              action="{{ route('paymentproviders.destroy', $provider->id) }}" 
                                                              onsubmit="return confirm('Are you sure you want to delete this provider?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="btn btn-sm btn-outline-danger" 
                                                                    data-toggle="tooltip" title="Delete">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-muted">
                                                    No payment providers found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>
@endsection
