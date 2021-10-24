<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'billing_id', 'order_code'];

    public function reservations()
    {
        return $this->hasOne(Reservations::class, 'order_id');
    }

    public function payments()
    {
        return $this->hasOne(Payments::class, 'order_id');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function couponHistories()
    {
        return $this->hasOne(CouponHistory::class, 'order_id');
    }
}
