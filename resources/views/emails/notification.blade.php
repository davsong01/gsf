@component('mail::message')
@if($data['type'] == 'payout_notification')
Dear Admin,

{{ $data['content'] }}<br><br>
Username : {{ $data['username'] }}<br>
Amount Requested : {!! $setting->default_currency !!}{{ $data['amount'] }}

Please login to your dashboard to process payment

@component('mail::button', ['url' => config('app.url')])
    Login here
@endcomponent
@endif

@if($data['type'] == 'payment_made')
Dear {{ $data['name'] }},

{!! $data['content'] !!}<br><br>
Amount Paid : {!! $setting->default_currency !!}{{ $data['amount'] }}<br>
Current Amount in Wallet: {!! $setting->default_currency !!}{{ $data['wallet'] }},

@endif

@if($data['type'] == 'post_approval')
Dear {{ $data['name'] }},

{!! $data['content'] !!}<br><br>

Please find details below:
           
Date Approved : {{ $data['date_approved'] }}<br>
Value of your Post : {!! $setting->default_currency !!}{{ $data['amount'] }}<br>
Current Amount in Wallet: {!! $setting->default_currency !!}{{ $data['wallet'] }}<br><br>

@endif

@if($data['type'] == 'post_unapproval')
Dear {{ $data['name'] }},

{!! $data['content'] !!}<br><br>

Please find details below:
           
Current Amount in Wallet: {!! $setting->default_currency !!}{{ $data['wallet'] }}<br><br>

@endif

Thanks,<br>
{{ config('app.name') }}
@endcomponent
