@component('mail::message')

Dear Admin,

A new donation has just been made for the conference.

Please find details below:


<strong>Name: </strong> {{ $data['name'] }} <br>
<strong>Email: </strong> {{ $data['email'] }} <br>
<strong>Phone: </strong> {{ $data['phone'] }} <br>
<strong>Amount Paid: </strong> {!! currency_symbol() !!}{{ $data['amount'] }} <br>
<strong>Payment Mode: </strong> {{ $data['payment_type'] }} <br>
<strong>Transaction ID: </strong> {{ $data['transid'] }} <br>


Thanks,<br>
{{ config('app.name') }}
@endcomponent
