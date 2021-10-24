<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationRequest;
use App\Interfaces\ReservationInterface;
use App\Models\Payments;
use App\Models\Reservations;
use App\Repositories\ReservationRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    protected $reservationInterface;

    public function __construct(ReservationInterface $reservationInterface)
    {
        $this->reservationInterface = $reservationInterface;
    }

    public function getActiveDateSlot()
    {
        return $this->reservationInterface->getActiveDateSlot();
    }

    public function getAvailableSlots(Request $request)
    {
        return $this->reservationInterface->getAvailableSlots($request->date);
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->reservationInterface->all();
    }

    public function getById(Request $request)
    {
        return $this->reservationInterface->getById();
    }

    public function cancelBooking(Request $request)
    {
        return $this->reservationInterface->cancelBooking($request);

    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ReservationRequest $request)
    {
        return $this->reservationInterface->store($request);

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
    public function update(Request $request, $id)
    {
        //
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
}
