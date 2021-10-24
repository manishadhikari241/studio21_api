<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingAddresses extends Model
{
    protected $fillable=['user_id','first_name','last_name','company','address_1','address_2','city','country','post_code'];
    use HasFactory;
}
