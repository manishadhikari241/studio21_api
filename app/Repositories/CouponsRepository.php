<?php


namespace App\Repositories;


use App\Constants\ErrorCodes;
use App\Constants\Roles;
use App\Interfaces\CouponsInterface;
use App\Models\Coupons;
use App\Models\RepCoupon;
use App\Models\RepresentativePayments;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CouponsRepository extends MainRepository implements CouponsInterface
{
    public function all()
    {
        $coupons = Coupons::with(['couponHistories.users', 'couponHistories.orders', 'repCoupons.users'])->orderBy('id', 'desc')->get();
        $representatives = User::where('role_id', Roles::REPRESENTATIVE)->get();
        foreach ($coupons as $repPayments) {
            $userId = $repPayments->repCoupons ? $repPayments->repCoupons->users->id : null;
            $repPayments->rep_payments = $userId ? RepresentativePayments::where('rep_id', $userId)->with(['users', 'couponHistories.orders.payments', 'couponHistories.users'])->get() : [];
        }
        $data['coupons'] = $coupons;
        $data['representatives'] = $representatives;
        return $this->success('Coupons Fetched successfully', $data, ErrorCodes::SUCCESS);
    }

    public function store($request)
    {
        try {
            $coupon = new Coupons();
            $coupon->code = ($request->code && strlen($request->code) ? $request->code : Str::random(12));
            $coupon->quantity = $request->quantity;
            $coupon->multi = $request->multi;
            if ($request->multi) {
                $coupon->start_date = Carbon::parse($request->start_date);
                $coupon->end_date = Carbon::parse($request->end_date);
            }
            $coupon->discount_type = $request->discount_type;
            $coupon->save();
            return $this->success('Coupons created successfully', $coupon, ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

    }

    public function update($request, $id)
    {
        try {
            $coupon = Coupons::findOrFail($id);
            if ($coupon->multi == true) {
                $coupon->start_date = Carbon::parse($request->start_date);
                $coupon->end_date = Carbon::parse($request->end_date);
            }
            $coupon->save();
            return $this->success('Coupons updated successfully', $coupon, ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }


    public function delete($id)
    {
        try {
            $coupon = Coupons::findOrFail($id);
            if ($coupon->couponHistories->isnotEmpty()) {
                foreach ($coupon->couponHistories as $history) {
                    if ($history->repPayments->exists())
                        return $this->error('You cannot delete this coupon. Already used in representative');
                }
            }
            $coupon->delete();
            return $this->success('Coupons deleted successfully', [], ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function check($code)
    {
        $coupon = Coupons::where('code', $code)->first();
//        if (Auth::guard('api')->user()->role_id == Roles::REPRESENTATIVE)
//            return $this->error('Coupons not valid for representatives', ErrorCodes::NOT_FOUND);

        if (!$coupon || !$coupon->isValid(Auth::guard('api')->id(), Coupons::getIP()))
            return $this->error('Coupon not valid', ErrorCodes::NOT_FOUND);
        return $this->success('Coupons Available', ['amount' => $coupon->quantity, 'type' => $coupon->discount_type], ErrorCodes::SUCCESS);
    }

    public function attachRepresentative($request)
    {
        try {
            $user = User::findorfail($request->rep_id);
            $coupon = Coupons::findorfail($request->coupon_id);
            if (!$user->isRepresentative())
                return $this->error('The provided user is not a representative', ErrorCodes::NOT_FOUND);
            if (!$coupon->multi)
                return $this->error('The coupon must be a multi usage coupon', ErrorCodes::NOT_FOUND);
            $coupon = RepCoupon::updateorCreate(['coupon_id' => $request->coupon_id], [
                'rep_id' => $request->rep_id,
                'compensation_type' => $request->compensation_type,
                'quantity' => $request->quantity
            ]);
            return $this->success('Coupons Successfully saved', $coupon, ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function payRepresentative($request)
    {
        try {
            $payments = RepresentativePayments::findorfail($request->id);
            $payments->payment_status = $request->status;
            $payments->save();
            return $this->success('Representative Payment Status Saved', $payments, ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function allRepPayments()
    {
        try {
            $payments = RepresentativePayments::with(['users', 'couponHistories.coupons.repCoupons', 'couponHistories.orders.payments', 'couponHistories.orders.reservations.timeSlots', 'couponHistories.users'])->latest()->get();
            return $this->success('Representative Payment Status Saved', $payments, ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function repPaymentsByUser()
    {
        try {
            $userId = Auth::guard('api')->id();
            $payments = RepresentativePayments::where('rep_id', $userId)->with(['users', 'couponHistories.coupons.repCoupons', 'couponHistories.orders.payments', 'couponHistories.orders.reservations.timeSlots', 'couponHistories.users'])->get();
            return $this->success('Payments fetched', $payments, ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

    }

    public function inviteCoupon($request)
    {
        try {
            if (!$request->email)
                return $this->error('Please provide email field', ErrorCodes::VALIDATION_FAILED);

            $repCoupon = RepCoupon::where('id', $request->coupon)->where('rep_id', Auth::guard('api')->id())->first();
            if (!$repCoupon)
                return $this->error('Invitation denied', ErrorCodes::FORBIDDEN);

            RepCoupon::sendInviteCouponMail($repCoupon, $request->email);

            return $this->success('Successfully sent Invitation Email', [], ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
