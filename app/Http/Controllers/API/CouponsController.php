<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\CouponsRepository;
use Illuminate\Http\Request;

class CouponsController extends Controller
{
    protected $coupons;

    public function __construct(CouponsRepository $coupons)
    {
        $this->coupons = $coupons;
    }

    public function check(Request $request)
    {
        return $this->coupons->check($request->code);
    }

    public function repPaymentsByUser(Request $request)
    {
        return $this->coupons->repPaymentsByUser();
    }

    public function inviteCoupon(Request $request)
    {
        return $this->coupons->inviteCoupon($request);
    }
}
