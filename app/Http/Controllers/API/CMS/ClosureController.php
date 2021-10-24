<?php

namespace App\Http\Controllers\API\CMS;

use App\Http\Controllers\Controller;
use App\Http\Requests\CMS\ClosureRequest;
use App\Http\Requests\CMS\CustomClosureRequest;
use App\Http\Requests\CMS\WeeklyClosureRequest;
use App\Interfaces\ClosureInterface;
use Illuminate\Http\Request;

class ClosureController extends Controller
{
    protected $closureInterface;

    public function __construct(ClosureInterface $closureInterface)
    {
        $this->closureInterface = $closureInterface;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->closureInterface->all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClosureRequest $request)
    {
        return $this->closureInterface->store($request);
    }

    public function customClosure(CustomClosureRequest $request)
    {
        return $this->closureInterface->customClosure($request);
    }

    public function weeklyClosure(WeeklyClosureRequest $request)
    {
        return $this->closureInterface->weeklyClosure($request);
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
    public function update(Request $request, int $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        return $this->closureInterface->delete($id);
    }

    public function resetCustomClosure()
    {
        return $this->closureInterface->resetCustomClosure();

    }

    public function resetWeeklyClosure()
    {
        return $this->closureInterface->resetWeeklyClosure();

    }

}
