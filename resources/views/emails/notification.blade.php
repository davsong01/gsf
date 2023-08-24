@component('mail::message')

@if($data['type'] == 'zone' || $data['type'] == 'field' || $data['type'] == 'secretariat')
Dear {{ $data['addressee'] }},

You have a new GSF report from <strong>{{ $data['chapter'] }}</strong> to attend to.

Report Date is <strong>{{ $data['date'] }}</strong>

Please login to your dashboard to approve report

@component('mail::button', ['url' => config('app.url') . 'stakeholderdashboard'])
    Login here
@endcomponent

@endif

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

@if($data['type'] == 'birthdaynotification')
Dear Admin, <br>
{{ $data['name'] }} has birthday today, <br>
{{ $data['name'] }} is the {{ $data['portfolio'] }} <br><br>
Kindly design birthday flyer.
@endif

@if($data['type'] == 'twodaysnecbirthdaynotification')
Hello, <br>
{{ $data['name'] }} has birthday in two days time - <strong>{{$data['bday']}}</strong>, <br>
{{ $data['name'] }} is the {{ $data['portfolio'] }} <br><br>
Kindly design birthday flyer.
@endif

@if($data['type'] == 'threedaysnecbirthdaynotification')
Hello, <br>
{{ $data['name'] }} has birthday in three days time - <strong>{{$data['bday']}}</strong>, <br>
{{ $data['name'] }} is the {{ $data['portfolio'] }} <br><br>
Kindly design birthday flyer.
@endif

@if($data['type'] == 'onedaynecbirthdaynotification')
Hello, <br>
{{ $data['name'] }} has birthday tomorrow - <strong>{{$data['bday']}}</strong>, <br>
{{ $data['name'] }} is the {{ $data['portfolio'] }} <br><br>
Kindly design birthday flyer.
@endif

@if($data['type'] == 'nationalRejection')
Dear {{ $data['addressee'] }},

<strong>{{ $data['chapter'] }}'s </strong> report for <strong>{{ $data['date'] }}</strong> has been rejected.
<hr>
<strong> Below is the comment sent: </strong> <br>
{!! $data['comment'] !!}
<hr>
You can also login to your dashboard, click the blue info icon next to the report rejected to view the National Gen Sec's comment; Then click the pencil icon to the left of your dashboard to edit and resend report
@component('mail::button', ['url' => config('app.url') . 'stakeholderdashboard'])
    Login here
@endcomponent

@endif

@if($data['type'] == 'fieldRejection')
Dear {{ $data['addressee'] }},

<strong>{{ $data['chapter'] }}'s </strong> report for <strong>{{ $data['date'] }}</strong> has been rejected.
<hr>
<strong> Below is the comment sent: </strong><br>
{!! $data['comment'] !!}
<hr>
You can also login to your dashboard, click the blue info icon next to the report rejected to view the Field Pastor's comment; Then click the pencil icon to the left of your dashboard to edit and resend report

@component('mail::button', ['url' => config('app.url') . 'stakeholderdashboard'])
    Login here
@endcomponent

@endif
@if($data['type'] == 'zonalRejection')
Dear {{ $data['addressee'] }},

<strong>{{ $data['chapter'] }}'s </strong> report for <strong>{{ $data['date'] }}</strong> has been rejected.
<hr>
<strong> Below is the comment sent: </strong><br>
{!! $data['comment'] !!}
<hr>
You can also login to your dashboard, click the blue info icon next to the report rejected to view the Zonal Pastor's comment; Then click the pencil icon to the left of your dashboard to edit and resend report

@component('mail::button', ['url' => config('app.url') . 'stakeholderdashboard'])
    Login here
@endcomponent

@endif

@if($data['type'] == 'resend')
Dear {{ $data['addressee'] }},

<strong>{{ $data['chapter'] }}'s </strong> has resent report for <strong>{{ $data['date'] }}</strong>.
<hr>

Please login to approve!
@component('mail::button', ['url' => config('app.url') . 'stakeholderdashboard'])
    Login here
@endcomponent

@endif

@if($data['type'] == 'pop')
Dear {{ $data['addressee'] }},

<strong>{{ $data['chapter'] }}'s </strong> has sent payment report of <strong> &#8358;{{ $data['amount'] }} </strong>for <strong>{{ $data['date'] }}</strong>.
<hr>

Please login to download report!
@component('mail::button', ['url' => config('app.url') . 'stakeholderdashboard'])
    Login here
@endcomponent

@endif


@if($data['type'] == 'email')
Dear {{ $data['name'] }},

{!! $data['content'] !!}
@endif

@if($data['type'] == 'emailReport')
Dear {{ $data['name'] }},
{{ $data['count'] }} emails have been sent, below is a copy of the mail: 

{!! $data['content'] !!}
@endif

@if($data['type'] == 'contactCampus')
Dear {{ $data['name'] }},

{!! $data['content'] !!}
@endif

@if($data['type'] == 0)
Dear {{ $data['name'] }},

{!! $data['content'] !!}
@endif

<em>Regards,</em><br>
{{ config('app.name') }}
@endcomponent
