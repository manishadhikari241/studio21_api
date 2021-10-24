<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    use HasFactory;

    public function billingAddresses()
    {
        return $this->belongsTo(BillingAddresses::class, 'billing_id');
    }
}
