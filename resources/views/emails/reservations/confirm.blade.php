@component('mail::message')
# BOOKING CONFIRMED

No. {{$orderCode}}

Date: {{$reservedDate}}

Time: {{$slots}}

Location:<br>
611, Kinetic Industrial Centre, <br>
7 Wang Kwong Rd, Kowloon Bay, HK <br>
+852 2174 1203


Thank you for choosing Studio21!
We hope you have a great experience.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
