<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeSlots extends Model
{
    use HasFactory;

    protected $fillable = ['price', 'slot_name', 'from', 'to', 'price_text'];

    public function reservations()
    {
        return $this->belongsToMany(Reservations::class, 'reservation_slot');
    }

    public static function checkAmount($amount, $slots)
    {
        $timeSlot = TimeSlots::whereIn('id', $slots)->get();
        $actualPrice = 0;
        foreach ($timeSlot as $slots) {
            $actualPrice += $slots->price;
        }
        return $actualPrice == $amount;
    }

    public function customClosures()
    {
        return $this->belongsToMany(CustomClosure::class, 'time_slots_id');
    }
}

