<?php

namespace App\Models;

use App\Mail\InviteCoupon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

class RepCoupon extends Model
{
    protected $fillable = ['rep_id', 'coupon_id', 'compensation_type', 'quantity'];
    use HasFactory;

    public function users()
    {
        return $this->belongsTo(User::class, 'rep_id');
    }

    public function coupons()
    {
        return $this->belongsTo(Coupons::class, 'coupon_id');
    }

    public static function sendInviteCouponMail($repCoupon, $email)
    {
//        return new InviteCoupon($repCoupon);
        Mail::to($email)->send(new InviteCoupon($repCoupon));
    }
}
