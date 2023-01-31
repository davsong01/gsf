@component('mail::message')

Dear {{ $data['name'] }},

Your registration for GSF National conference is successful.

Below are the details of your registration

<strong>Name: </strong> {{ $data['name'] }} <br>
<strong>Email: </strong> {{ $data['email'] }} <br>
<strong>Phone: </strong> {{ $data['phone'] }} <br>
<strong>Family ID: </strong>{{ $data['family_id'] }} <br>
<strong>Amount Paid: </strong> &#8358;{{ $data['amount'] }}

<strong>Allocation Detals:</strong> <br>
@if(isset($data['hostel']) && !empty($data['hostel']))
<strong>Allocated Hostel: </strong>{{ $data['hostel'] }} <br>
@endif
@if(isset($data['foodstand']) && !empty($data['foodstand']))
<strong>Allocated Foodstand: </strong>{{ $data['foodstand'] }} <br>
@endif

Kindly login to you dashboard with the following details to view your profile and print ID card:

<strong>Family ID: </strong> {{ $data['family_id'] }} <br>
<strong>Password: </strong> {{ $data['phone'] }}<br>

You can login and change your password for confidential reasons

@component('mail::button', ['url' => url('/') .'/account'])
Login here<br><br>
@endcomponent


Thanks,<br>
{{ config('app.name') }}
@endcomponent
