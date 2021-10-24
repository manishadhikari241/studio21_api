<?php


namespace App\Traits;


use App\Models\CustomClosure;
use App\Models\TimeSlots;
use App\Models\WeeklyClosure;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use function Composer\Autoload\includeFile;

trait Closures
{
    public function isInBulkClosure($date, $slotId, $type)
    {
        $class = '';
        switch ($type) {
            case 'custom':
                $class = CustomClosure::class;
                break;
            case 'weekly':
                $class = WeeklyClosure::class;
                break;
        }
        $customClosure = $class::first();
        if (!$customClosure) return false;
        if ($type == 'weekly') {
            $checkDate = Carbon::parse($date)->format('D');
            if ($checkDate != $customClosure->week_value)
                return false;
        }
        $closure = $class::whereDate('from', '<=', $date)->whereDate('to', '>=', $date)->where('time_slots_id', $slotId)->first();
        if ($closure) return true;
        $inDate = $class::whereDate('from', '<=', $date)->whereDate('to', '>=', $date)->first();
        if ($inDate) {
            $slot = $customClosure->timeSlots;
            $check = TimeSlots::where('status', 1)->where('id', $slotId)->where(function ($query) use ($slot) {
                $query->whereBetween('from', [$slot->from, $slot->to]);
                $query->orwhereBetween('to', [$slot->from, $slot->to]);
            })->first();
            if ($check) return true;
            $check2 = TimeSlots::where('status', 1)->where('id', $slotId)->first();
            foreach (range($check2->from, $check2->to) as $number) {
                if ($number == $slot->from || $number == $slot->to)
                    return true;
            }
        }

        return false;
    }

    public function getCustomClosureCalendarData()
    {
        $customClosure = CustomClosure::first();
        if ($customClosure) {
            $customClosure->custom = true;
            $customClosure->type = "closure";
            $customClosure->dateFrom = $customClosure->from;
            $customClosure->dateTo = $customClosure->to;
            $customClosure->from = $customClosure->timeSlots->from;
            $customClosure->to = $customClosure->timeSlots->to;
            $customClosure->price = $customClosure->timeSlots->price;
            $customClosure->slot_name = $customClosure->timeSlots->slot_name;
            unset($customClosure->timeSlots);
        }
        return $customClosure;
    }

    public function getWeeklyClosureCalendarData()
    {
        $weeklyClosure = WeeklyClosure::first();
        $weeklyClosureData = [];
        if ($weeklyClosure) {
            $period = CarbonPeriod::create($weeklyClosure->from, $weeklyClosure->to);
            foreach ($period as $key => $value) {
                $date = $value->format('Y-m-d');
                $day = $value->format('D');
                if ($day == $weeklyClosure->week_value) {
                    $data['custom'] = true;
                    $data['dateFrom'] = $date;
                    $data['dateTo'] = $date;
                    $data['from'] = $weeklyClosure->timeSlots->from;;
                    $data['to'] = $weeklyClosure->timeSlots->to;
                    $data['name'] = $weeklyClosure->name;
                    $data['details'] = $weeklyClosure->details;
                    $data['color'] = $weeklyClosure->color;
                    $data['price'] = $weeklyClosure->timeSlots->price;
                    $data['slot_name'] = $weeklyClosure->timeSlots->slot_name;
                    $data['date'] = "";
                    $data['reservationStatus'] = null;
                    $data['type'] = "closure";
                    $data['orderDetail'] = null;
                    array_push($weeklyClosureData, (object)$data);
                }
            }
        }
        return $weeklyClosureData;
    }


}
