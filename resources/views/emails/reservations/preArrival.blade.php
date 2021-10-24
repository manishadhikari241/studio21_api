@component('mail::message')
# One Day to go

No. {{$order_code}}

Date: {{$date}}

Time: {{$time_slots}}

Location:
611, Kinetic Industrial Centre, 7 Wang
Kwong Rd, Kowloon Bay, HK
+852 2174 1203

Thanks,<br>
{{ config('app.name') }}
@endcomponent
