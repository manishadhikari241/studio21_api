<?php

namespace App\Http\Controllers\API\CMS;

use App\Http\Controllers\Controller;
use App\Http\Requests\CMS\TimeSlotRequest;
use App\Http\Requests\CMS\TimeSlotUpdateRequest;
use App\Interfaces\TimeSlotInterface;
use Illuminate\Http\Request;

class TimeSlotController extends Controller
{
    protected $timeSlotInterface;

    public function __construct(TimeSlotInterface $timeSlotInterface)
    {
        $this->timeSlotInterface = $timeSlotInterface;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->timeSlotInterface->all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(TimeSlotRequest $request)
    {
        return $this->timeSlotInterface->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(TimeSlotUpdateRequest $request, $id)
    {
        return $this->timeSlotInterface->update($request, $id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getActiveSlots(){
        return $this->timeSlotInterface->getActiveSlots();
    }
}
