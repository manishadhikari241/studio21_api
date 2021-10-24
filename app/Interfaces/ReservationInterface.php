<?php


namespace App\Interfaces;


use App\Filters\ReservationFilter;

interface ReservationInterface
{
    public function store($request);

    public function all($filters);

    public function getCalendarData();

    public function getAvailableSlots($date);

    public function getActiveDateSlot();

    public function getById();

    public function cancelBooking($request);
}
