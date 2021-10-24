<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponHistory extends Model
{
    use HasFactory;

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function repPayments()
    {
        return $this->hasOne(RepresentativePayments::class, 'coupon_history_id');
    }

    public function coupons()
    {
        return $this->belongsTo(Coupons::class, 'coupon_id');
    }
}

