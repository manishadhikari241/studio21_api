<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe;

class StripePaymentController extends Controller
{
    public function stripePost(Request $request)
    {
        dd($request->all());
        try {
            Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            $charge= Stripe\Charge::create([
                "amount" => 100 * 100,
                "currency" => "hkd",
                "source" => $request->stripeToken,
                "description" => "Test payment from studio21.hk."
            ]);

            return $this->success('Payment Successful', $charge, 200);
        }catch (\Exception $e){
            return $this->error($e->getMessage());
        }

    }
}
