<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class InviteCoupon extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $couponCode;
    protected $discount;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($repCoupon)
    {
        $this->user = $repCoupon->users->first_name . ' ' . $repCoupon->users->last_name;
        $this->couponCode = $repCoupon->coupons->code;
        $this->discount = $repCoupon->coupons->discount_type == 'value' ? 'HK$' . $repCoupon->coupons->quantity : $repCoupon->coupons->quantity . '%';
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.coupons.invite')->with([
            'coupon_code' => $this->couponCode,
            'user' => $this->user,
            'discount' => $this->discount

        ])->subject('You have been invited to our Studio!');

    }
}
