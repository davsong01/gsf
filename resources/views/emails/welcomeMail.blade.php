@component('mail::message')
{!! $data['content'] !!}
<em>Regards,</em><br>
{{ config('app.name') }}
@endcomponent
