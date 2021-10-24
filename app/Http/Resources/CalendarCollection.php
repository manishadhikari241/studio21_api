<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CalendarCollection extends JsonResource
{

    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'customClosure'=> $this->custom ? $this->custom : false,
            'reservation_date' => $this->date,
            'name' => $this->name,
            'start' => ($this->custom ? $this->dateFrom : $this->date) . ' ' . $this->from . ':00:00',
            'end' => ($this->custom ? $this->dateTo : $this->date) . ' ' . $this->to . ':00:00',
            'price' => $this->price,
            'color' => $this->color,
            'slot_name' => $this->slot_name,
            'status' => $this->reservationStatus,
            'type' => $this->type,
            'details' => $this->details,
            'orderDetail' => $this->orderDetail,
        ];
    }
}
