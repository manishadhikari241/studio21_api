<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyClosure extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'from', 'to', 'week_value', 'week_day', 'color', 'details', 'time_slots_id'];


    public function timeSlots()
    {
        return $this->belongsTo(TimeSlots::class, 'time_slots_id');
    }

}
