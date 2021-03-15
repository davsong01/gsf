@component('mail::message')

Dear {{ $data['name'] }},

Your registration for GSF National conference is successful.

Below are the details of your registration

<strong>Name: </strong> {{ $data['name'] }} <br>
<strong>Email: </strong> {{ $data['email'] }} <br>
<strong>Phone: </strong> {{ $data['phone'] }} <br>
<strong>Conference ID: </strong>{{ $data['conference_number'] }} <br>
<strong>Amount Paid: </strong> &#8358;{{ $data['amount'] }} <br>


To complete your registration and have access to hostel space, food stand, I.D. card and more, kindly login to your personalized portal and fill in your details.

Login Details are:

<strong>Conference I.D: </strong> {{ $data['conference_number'] }} <br>
<strong>Password: </strong> {{ $data['phone'] }}<br>

You can login and change your password for confidential reasons

@component('mail::button', ['url' => config('app.url') .'account'])
Login to portal here<br><br>
@endcomponent


Thanks,<br>
{{ config('app.name') }}
@endcomponent
