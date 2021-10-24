<?php

namespace App\Http\Controllers\API\CMS;

use App\Filters\ReservationFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReservationRequest;
use App\Interfaces\ReservationInterface;
use App\Repositories\ReservationRepository;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    protected $reservationInterface;

    public function __construct(ReservationInterface $reservationInterface)
    {
        $this->reservationInterface = $reservationInterface;
    }

    public function getCalendarData()
    {
        return $this->reservationInterface->getCalendarData();

    }

    /**
     * Display a listing of the resource.
     *
     * @param ReservationFilter $filters
     * @return \Illuminate\Http\Response
     */
    public function index(ReservationFilter $filters)
    {
        return $this->reservationInterface->all($filters);
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
