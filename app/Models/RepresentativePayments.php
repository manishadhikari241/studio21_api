<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepresentativePayments extends Model
{
    use HasFactory;

    public function users()
    {
        return $this->belongsTo(User::class, 'rep_id');
    }

    public function couponHistories()
    {
        return $this->belongsTo(CouponHistory::class, 'coupon_history_id');
    }
}
