@component('mail::message')
# Congrats!

{{$user}} is sending you a {{$discount}} discount voucher that you
can use when booking a studio space at Studio21.

ENJOY {{$discount}} OFF WITH THE FOLLOWING CODE:

{{$coupon_code}}

Simply enter the code when making your booking online on studio21.HK


Thanks,<br>
{{ config('app.name') }}

<p style="font-size: 12px">Legal disclaimer: This email has been sent to you by Studio21.hk as a Invitation
    from and authorised by {{$user}}. If you did not consent to receive this email or it
    is unwanted, you may send us a mail at shoot@studio21.hk to ask us to remove
    your information from our systems. We will not send you any further
    communication once you have done this. Your personal information and
    contact details will not be added to our mailing lists or used for any purpose.
</p>

@endcomponent

