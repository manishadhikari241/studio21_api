<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomClosure extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'from', 'to', 'color', 'details','time_slots_id'];

    public function timeSlots(){
        return $this->belongsTo(TimeSlots::class,'time_slots_id');
    }

}
