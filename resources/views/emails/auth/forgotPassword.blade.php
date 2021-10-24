@component('mail::message')
# FORGOT YOUR PASSWORD?

That's okay, it happens!

Use the code below to reset your password.

{{$code}}

This password reset code will expire in 60 minutes.

If you did not request a password reset,

no further action is required.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
