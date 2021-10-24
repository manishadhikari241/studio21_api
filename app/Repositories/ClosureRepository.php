<?php


namespace App\Repositories;


use App\Constants\ErrorCodes;
use App\Interfaces\ClosureInterface;
use App\Models\CustomClosure;
use App\Models\Reservations;
use App\Models\TimeSlots;
use App\Models\WeeklyClosure;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Storage;

class ClosureRepository extends ReservationRepository implements ClosureInterface
{
    public function all($request = "")
    {
        try {
            $closures = Reservations::where('type', 'closure')->with('timeSlots')->latest()->get();
            $data['singleClosure'] = $closures;
            $data['customClosure'] = $this->getCustomClosure();
            $data['weeklyClosure'] = $this->getWeeklyClosure();
            return $this->success('Closures Fetched.', $data, ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function store($request)
    {
        try {
            if (!$this->canMakeReservation($request->date, $request->time_slots))
                return $this->error('Already Reserved at this time Slot. Please try again.', ErrorCodes::NOT_FOUND);
            $reserve = Reservations::create([
                'name' => $request->name,
                'date' => $request->date,
                'color' => ($request->color && $request->color != "") ? $request->color : 'red',
                'details' => $request->details,
                'status' => "pending",
                "type" => 'closure'
            ]);
            $reserve->timeSlots()->attach($request->time_slots);
            return $this->success('You have successfully closed on following dates.', $reserve, ErrorCodes::SUCCESS);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $closure = Reservations::findorfail($id);
            if ($closure->type != 'closure')
                return $this->error('The item provided is not a closure');
            $closure->timeSlots()->detach();
            $closure->delete();
            return $this->success('Closure has been successfully removed', [], ErrorCodes::SUCCESS);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

//Custom

    public function getCustomClosure()
    {
        return CustomClosure::with('timeSlots')->first();
    }

    public function customClosure($request)
    {
        try {
            if ($this->canCustomClosure($request->from, $request->to, $request->time_slots)) {
                $customClosure = CustomClosure::updateOrCreate(['id' => 1], [
                    'from' => $request->from,
                    'to' => $request->to,
                    'name' => $request->name,
                    'color' => ($request->color && $request->color != "") ? $request->color : 'red',
                    'details' => $request->details,
                    'time_slots_id' => $request->time_slots
                ]);
                return $this->success('You have successfully closed on following dates.', $customClosure, ErrorCodes::SUCCESS);
            } else return $this->error('Already Reserved at this time Slot. Please try again.', ErrorCodes::NOT_FOUND);


        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function canCustomClosure($from, $to, $timeSlots)
    {

        $period = CarbonPeriod::create($from, $to);
        foreach ($period as $key => $value) {
            $date = $value->format('Y-m-d');
            if (!$this->isSLotAvailable($date, $timeSlots, $closure = false))
                return false;
        }
        return true;
    }

    public function resetCustomClosure()
    {
        try {
            CustomClosure::truncate();
            return $this->success('Custom Closure has been successfully reset', [], ErrorCodes::SUCCESS);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

// Weekly
    public function getWeeklyClosure()
    {
        return WeeklyClosure::with('timeSlots')->first();
    }

    public function weeklyClosure($request)
    {
        try {
            if ($this->canWeeklyClosure($request->from, $request->to, $request->time_slots, $request->week_value)) {
                $customClosure = WeeklyClosure::updateOrCreate(['id' => 1], [
                    'from' => $request->from,
                    'to' => $request->to,
                    'name' => $request->name,
                    'week_day' => $request->week_day,
                    'week_value' => $request->week_value,
                    'color' => ($request->color && $request->color != "") ? $request->color : 'red',
                    'details' => $request->details,
                    'time_slots_id' => $request->time_slots
                ]);
                return $this->success('You have successfully closed on following dates.', $customClosure, ErrorCodes::SUCCESS);
            } else return $this->error('Already Reserved at this time Slot. Please try again.', ErrorCodes::NOT_FOUND);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    private function canWeeklyClosure($from, $to, $timeSlots, $weekDay)
    {

        $period = CarbonPeriod::create($from, $to);
        foreach ($period as $key => $value) {
            $day = $value->format('D');
            if ($day == $weekDay) {
                $date = $value->format('Y-m-d');
                if (!$this->isSLotAvailable($date, $timeSlots, $closure = false))
                    return false;
            }
        }
        return true;
    }

    public function resetWeeklyClosure()
    {
        try {
            WeeklyClosure::truncate();
            return $this->success('Weekly Closure has been successfully reset', [], ErrorCodes::SUCCESS);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


}
