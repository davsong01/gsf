@component('mail::message')

Dear Admin,
@if ($data['level'] == 'Moderator' || $data['level'] == 'Participant')
A participant has just registered for the GSF National Conference, Please find details below:
@endif

@if ($data['level'] == 'Alumni')
An Alumni has just registered for the GSF National Conference, Please find details below:
@endif

<strong>Name: </strong> {{ $data['name'] }} <br>
<strong>Email: </strong> {{ $data['email'] }} <br>
<strong>Phone: </strong> {{ $data['phone'] }} <br>
<strong>Conference ID: </strong>{{ $data['family_id'] }} <br>
<strong>Amount Paid: </strong> {!! currency_symbol() !!}{{ $data['amount'] }} <br>
<strong>Campus: </strong> {{ $data['chapter'] }} <br>


You can also login to the portal to view and manage registrations

@component('mail::button', ['url' => config('app.url') .'/account'])
Login to portal here<br><br>
@endcomponent



Thanks,<br>
{{ config('app.name') }}
@endcomponent
