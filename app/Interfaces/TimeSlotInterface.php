<?php


namespace App\Interfaces;


interface TimeSlotInterface
{

    public function all();

    public function store($request);

    public function delete($id);

    public function update($request,$id);

    public function getActiveSlots();

}
