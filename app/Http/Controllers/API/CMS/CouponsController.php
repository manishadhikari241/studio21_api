<?php

namespace App\Http\Controllers\API\CMS;

use App\Http\Controllers\Controller;
use App\Http\Requests\CMS\AttachCouponRepRequest;
use App\Http\Requests\CMS\CouponsRequest;
use App\Http\Requests\CMS\UpdateCouponsRequest;
use App\Interfaces\CouponsInterface;
use Illuminate\Http\Request;

class CouponsController extends Controller
{
    protected $coupons;

    public function __construct(CouponsInterface $coupons)
    {
        $this->coupons = $coupons;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->coupons->all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(CouponsRequest $request)
    {
        return $this->coupons->store($request);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateCouponsRequest $request, $id)
    {
        return $this->coupons->update($request, $id);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->coupons->delete($id);
    }

    public function attachRepresentative(AttachCouponRepRequest $request)
    {
        return $this->coupons->attachRepresentative($request);
    }

    public function payRepresentative(Request $request)
    {
        return $this->coupons->payRepresentative($request);
    }

    public function allRepPayments(Request $request)
    {
        return $this->coupons->allRepPayments();
    }
}
