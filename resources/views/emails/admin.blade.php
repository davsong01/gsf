@component('mail::message')

Dear Admin,
@if ($data['level'] == 'Moderator' || $data['level'] == 'Participant')
A participant has just registered for the GSF Conference, Please find details below:
@endif

@if ($data['level'] == 'Alumni')
An Alumni has just registered for the GSF Conference, Please find details below:
@endif

<strong>Name: </strong> {{ $data['name'] }} <br>
<strong>Email: </strong> {{ $data['email'] }} <br>
<strong>Phone: </strong> {{ $data['phone'] }} <br>
<strong>Conference ID: </strong>{{ $data['conference_number'] }} <br>
<strong>Amount Paid: </strong> &#8358;{{ $data['amount'] }} <br>
<strong>Chapter: </strong> {{ $data['chapter'] }} <br>


You can also login to the portal to view and manage registrations

@component('mail::button', ['url' => config('app.url') .'/myaccount'])
Login to portal here<br><br>
@endcomponent



Thanks,<br>
{{ config('app.name') }}
@endcomponent
