@extends('layouts.conference')

@section('title', 'Transaction '.$transaction->transid)
@section('active')
<li class="breadcrumb-item"><a href="{{ route('conference.transactions', $edition->id) }}">{{ $edition->conference_theme }} Transactions</a></li>
<li class="breadcrumb-item">{{ $transaction->transid }}</li>
@endsection

@section('content2')
<style>
    .transaction-detail { max-width:1600px; margin:0 auto; padding:24px 28px 40px; }
    .transaction-detail__header { display:flex; align-items:center; justify-content:space-between; gap:22px; margin-bottom:24px; padding:28px 30px; border:1px solid rgba(255,255,255,.1); border-radius:16px; color:#fff; background:linear-gradient(120deg,#10244a,#193e70); box-shadow:0 14px 34px rgba(16,36,74,.16); }
    .transaction-detail__header h2 { margin:6px 0; color:#fff; font-family:Georgia,serif; font-size:30px; font-weight:500; letter-spacing:-.025em; }
    .transaction-detail__header p { margin:0; color:rgba(255,255,255,.72); font-size:14px; }
    .transaction-detail__eyebrow { color:#e2c46f; font-size:10px; font-weight:850; letter-spacing:.16em; text-transform:uppercase; }
    .transaction-detail__actions { display:flex; flex-wrap:wrap; gap:9px; justify-content:flex-end; }
    .transaction-detail__actions .btn { min-height:39px; padding:9px 14px; border-radius:7px; font-size:13px; font-weight:700; }
    .transaction-detail__actions .btn-outline-secondary { border-color:rgba(255,255,255,.5); color:#fff; }
    .transaction-detail__actions .btn-outline-secondary:hover { color:#10244a; background:#fff; }
    .transaction-detail__grid { display:grid; grid-template-columns:minmax(0,1.25fr) minmax(320px,.75fr); gap:20px; align-items:start; }
    .transaction-detail__card { margin-bottom:20px; overflow:hidden; border:1px solid #e4e9f1; border-radius:13px; background:#fff; box-shadow:0 7px 22px rgba(22,39,75,.055); }
    .transaction-detail__card h3 { margin:0; padding:17px 21px; border-bottom:1px solid #edf0f5; color:#192b50; font-family:Georgia,serif; font-size:19px; font-weight:600; }
    .transaction-detail__body { padding:20px 21px; }
    .transaction-detail__rows { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:20px 24px; }
    .transaction-detail__item { min-width:0; }
    .transaction-detail__item span { display:block; margin-bottom:6px; color:#7a879b; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.07em; }
    .transaction-detail__item strong { color:#263653; font-size:15px; font-weight:650; overflow-wrap:anywhere; }
    .transaction-detail__item--wide { grid-column:1 / -1; }
    .transaction-detail__amount { display:flex; justify-content:space-between; gap:15px; padding:13px 0; border-bottom:1px solid #edf0f5; color:#66748c; font-size:14px; }
    .transaction-detail__amount strong { color:#263653; white-space:nowrap; }
    .transaction-detail__amount--total { padding-top:17px; border-bottom:0; color:#192b50; font-size:16px; font-weight:750; }
    .transaction-detail__amount--total strong { color:#1b5185; font-size:21px; }
    .transaction-detail__toolbar { display:flex; flex-wrap:wrap; gap:9px; }
    .transaction-detail__toolbar form { margin:0; }
    .transaction-detail pre { max-height:480px; margin:0; padding:15px; overflow:auto; border-radius:8px; color:#263653; background:#f5f7fa; font-size:12px; white-space:pre-wrap; overflow-wrap:anywhere; }
    .transaction-detail__empty { margin:0; color:#75829a; font-size:14px; }
    @media(max-width:850px) { .transaction-detail__grid { grid-template-columns:1fr; } }
    @media(max-width:600px) {
        .transaction-detail { padding:15px; }
        .transaction-detail__header { flex-direction:column; }
        .transaction-detail__header { align-items:flex-start; padding:22px; }
        .transaction-detail__header h2 { font-size:26px; }
        .transaction-detail__actions { justify-content:flex-start; }
        .transaction-detail__rows { grid-template-columns:1fr; }
        .transaction-detail__item--wide { grid-column:auto; }
    }
</style>

<div class="content-body transaction-detail">
    @include('includes.alerts')

    <header class="transaction-detail__header">
        <div>
            <h2>Transaction details</h2>
            <p>Reference {{ $transaction->transid }} · {{ $transaction->created_at?->format('d M Y, g:i a') }}</p>
        </div>
        <div class="transaction-detail__actions">
            <a class="btn btn-outline-secondary" href="{{ route('conference.transactions', $edition->id) }}">Back to transactions</a>
            <a class="btn btn-light" href="{{ route('conference.transactions.receipt', [$edition->id, $transaction->id]) }}">Download receipt</a>
            @if(auth()->user()->conference_role === 'superadmin' && $transaction->status !== 'Complete')
                <button class="btn btn-info" type="button" data-toggle="modal" data-target="#editTransactionModal">Update transaction</button>
            @endif
            @if(auth()->user()->conference_role === 'superadmin' && $transaction->status !== 'Complete')
                <form method="POST" action="{{ route('conference.transactions.resolve', [$edition->id, $transaction->id]) }}" onsubmit="return confirm('Resolve this transaction and complete its registration if the provider confirms payment?')">
                    @csrf
                    <button class="btn btn-success" type="submit">Resolve transaction</button>
                </form>
            @endif
        </div>
    </header>

    <div class="transaction-detail__grid">
        <div>
            <section class="transaction-detail__card">
                <h3>Registration</h3>
                <div class="transaction-detail__body transaction-detail__rows">
                    <div class="transaction-detail__item"><span>Participant</span><strong>{{ $transaction->name ?: '—' }}</strong></div>
                    <div class="transaction-detail__item"><span>Email</span><strong>{{ $transaction->email ?: '—' }}</strong></div>
                    <div class="transaction-detail__item"><span>Phone</span><strong>{{ $transaction->phone ?: '—' }}</strong></div>
                    <div class="transaction-detail__item"><span>Registration plan</span><strong>{{ $transaction->conferenceplan->title ?? '—' }}</strong></div>
                    <div class="transaction-detail__item"><span>Participant level</span><strong>{{ $transaction->level ?: '—' }}</strong></div>
                    <div class="transaction-detail__item"><span>Registration type</span><strong>{{ ucfirst($transaction->registration_user_type ?? '—') }}</strong></div>
                    <div class="transaction-detail__item"><span>Transaction status</span><strong><span class="badge badge-{{ $transaction->status === 'Complete' ? 'success' : 'warning' }}">{{ $transaction->status }}</span></strong></div>
                    <div class="transaction-detail__item"><span>Registration status</span><strong>{{ $transaction->registration_status ?: '—' }}</strong></div>
                    <div class="transaction-detail__item"><span>Payment location</span><strong>{{ $transaction->location ?: 'Online' }}</strong></div>
                    <div class="transaction-detail__item"><span>Conference edition</span><strong>{{ $edition->conference_theme }}</strong></div>
                    @if($transaction->remarks)
                        <div class="transaction-detail__item transaction-detail__item--wide"><span>Additional notes</span><strong>{{ $transaction->remarks }}</strong></div>
                    @endif
                </div>
            </section>

            <section class="transaction-detail__card">
                <h3>Registration details</h3>
                <div class="transaction-detail__body transaction-detail__rows">
                    @forelse($transaction->allocationFields as $field)
                        <div class="transaction-detail__item">
                            <span>{{ \Illuminate\Support\Str::headline($field->key) }}</span>
                            <strong>{{ $field->value ?: '—' }}</strong>
                        </div>
                    @empty
                        <p class="transaction-detail__empty">No additional registration fields were recorded.</p>
                    @endforelse
                </div>
            </section>

            <section class="transaction-detail__card">
                <h3>Provider response</h3>
                <div class="transaction-detail__body">
                    @if($transaction->api_response)
                        <pre id="provider-response-json">{{ json_encode($transaction->api_response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    @else
                        <pre id="provider-response-json" hidden></pre>
                        <p id="provider-response-empty" class="transaction-detail__empty">No provider response has been recorded.</p>
                    @endif
                </div>
            </section>
        </div>

        <aside>
            <section class="transaction-detail__card">
                <h3>Payment summary</h3>
                <div class="transaction-detail__body">
                    <div class="transaction-detail__item"><span>Provider</span><strong>{{ $transaction->paymentprovider->name ?? '—' }}</strong></div>
                    <div class="transaction-detail__item" style="margin-top:16px"><span>Provider reference</span><strong>{{ $transaction->provider_reference ?: 'Not available' }}</strong></div>
                    <div class="transaction-detail__amount" style="margin-top:14px"><span>Registration amount</span><strong>₦{{ number_format((float) $transaction->amount_paid, 2) }}</strong></div>
                    <div class="transaction-detail__amount"><span>Provider charge</span><strong>₦{{ number_format((float) $transaction->provider_charge, 2) }}</strong></div>
                    <div class="transaction-detail__amount transaction-detail__amount--total"><span>Total paid</span><strong>₦{{ number_format((float) ($transaction->total_amount ?? $transaction->amount_paid), 2) }}</strong></div>

                    @if($isOnlinePayment && auth()->user()->conference_role === 'superadmin')
                        <div class="transaction-detail__toolbar">
                            <form id="provider-requery-form" method="POST" action="{{ route('conference.transactions.requery-provider', [$edition->id, $transaction->id]) }}">
                                @csrf
                                <button id="provider-requery-button" class="btn btn-info" type="submit">Requery provider</button>
                            </form>
                        </div>
                        <small class="text-muted d-block mt-2">Queries the provider directly and displays its unmodified JSON response. Requery does not complete the registration.</small>
                    @elseif(strtolower($transaction->location ?? 'online') === 'on site')
                        <small class="text-muted d-block mt-2">Provider requery is unavailable for on-site payments.</small>
                    @endif
                </div>
            </section>

            @if($transaction->resolved_at)
                <section class="transaction-detail__card">
                    <h3>Resolution record</h3>
                    <div class="transaction-detail__body transaction-detail__rows">
                        <div class="transaction-detail__item"><span>Resolved by</span><strong>{{ $transaction->resolvedBy->fullname ?? 'System' }}</strong></div>
                        <div class="transaction-detail__item"><span>Resolved at</span><strong>{{ $transaction->resolved_at->format('d M Y, g:i a') }}</strong></div>
                    </div>
                </section>
            @endif
        </aside>
    </div>
</div>

@if(auth()->user()->conference_role === 'superadmin' && $transaction->status !== 'Complete')
    <div class="modal fade" id="editTransactionModal" tabindex="-1" role="dialog" aria-labelledby="editTransactionModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form method="POST" action="{{ route('conference.transactions.update', $transaction->id) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="editTransactionModalTitle">Update transaction</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3"><label for="edit-name">Name</label><input id="edit-name" class="form-control" type="text" name="name" value="{{ $transaction->name }}"></div>
                            <div class="col-md-6 mb-3"><label for="edit-email">Email</label><input id="edit-email" class="form-control" type="email" name="email" value="{{ $transaction->email }}"></div>
                            <div class="col-md-6 mb-3"><label for="edit-phone">Phone</label><input id="edit-phone" class="form-control" type="text" name="phone" value="{{ $transaction->phone }}"></div>
                            <div class="col-md-6 mb-3"><label for="edit-transid">Transaction ID</label><input id="edit-transid" class="form-control" type="text" name="transid" value="{{ $transaction->transid }}"></div>
                            <div class="col-md-6 mb-3"><label for="edit-amount-paid">Registration amount</label><input id="edit-amount-paid" class="form-control" type="number" step="0.01" name="amount_paid" value="{{ $transaction->amount_paid }}"></div>
                            <div class="col-md-6 mb-3"><label for="edit-total-amount">Total amount</label><input id="edit-total-amount" class="form-control" type="number" step="0.01" name="total_amount" value="{{ $transaction->total_amount }}"></div>
                            <div class="col-md-6 mb-3"><label for="edit-provider-charge">Provider charge</label><input id="edit-provider-charge" class="form-control" type="number" step="0.01" name="provider_charge" value="{{ $transaction->provider_charge }}"></div>
                            <div class="col-md-6 mb-3"><label for="edit-slot">Slots</label><input id="edit-slot" class="form-control" type="number" min="1" name="slot" value="{{ $transaction->slot }}"></div>
                            <div class="col-md-6 mb-3">
                                <label for="edit-registration-type">Registration user type</label>
                                <select id="edit-registration-type" name="registration_user_type" class="form-control">
                                    <option value="participant" @selected($transaction->registration_user_type === 'participant')>Participant</option>
                                    <option value="moderator" @selected($transaction->registration_user_type === 'moderator')>Moderator</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Save transaction</button>
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('provider-requery-form');
    if (!form) return;

    const button = document.getElementById('provider-requery-button');
    const output = document.getElementById('provider-response-json');
    const empty = document.getElementById('provider-response-empty');

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        button.disabled = true;
        button.textContent = 'Querying provider…';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value
                },
                body: new FormData(form)
            });
            const result = await response.json();

            output.hidden = false;
            output.textContent = JSON.stringify(
                result.provider_response ?? { message: result.message || 'The provider returned no response.' },
                null,
                2
            );
            if (empty) empty.hidden = true;
        } catch (error) {
            output.hidden = false;
            output.textContent = JSON.stringify({ message: 'The provider query could not be completed. Please try again.' }, null, 2);
            if (empty) empty.hidden = true;
        } finally {
            button.disabled = false;
            button.textContent = 'Requery provider';
        }
    });
});
</script>
@endpush
@endsection
