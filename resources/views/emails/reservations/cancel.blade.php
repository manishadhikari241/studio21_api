@component('mail::message')
# BOOKING CANCELLED

No. {{$orderCode}}

Date: {{$reservedDate}}

Time: {{$slots}}

Location:<br>
611, Kinetic Industrial Centre, <br>
7 Wang Kwong Rd, Kowloon Bay, HK <br>
+852 2174 1203


We’re sorry to see you go!<br>
Your booking has been cancelled for free.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
