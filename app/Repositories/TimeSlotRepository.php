<?php


namespace App\Repositories;


use App\Constants\ErrorCodes;
use App\Interfaces\TimeSlotInterface;
use App\Models\TimeSlots;

class TimeSlotRepository extends MainRepository implements TimeSlotInterface
{
    public function all()
    {
        return TimeSlots::all();
    }

    public function getActiveSlots()
    {
        try {
            $timeSlots = TimeSlots::where('status', 1)->get();
            return $this->success('Active Slots Fetched', $timeSlots, ErrorCodes::SUCCESS);

        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function update($request, $id)
    {
        try {
            $slots = TimeSlots::findorfail($id);
            $slots->slot_name = $request->slot_name;
            $slots->price_text = $request->price_text;
            $slots->status = $request->status;
            $slots->save();
            return $this->success('Slots successfully updated', $slots);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }

    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    public function store($request)
    {
        try {
            $slots = TimeSlots::create([
                'price' => $request->price,
                'slot_name' => $request->slot_name,
                'from' => $request->from,
                'to' => $request->to,
            ]);
            return $this->success('Slots successfully added', $slots);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), $e->getCode());
        }
    }
}
