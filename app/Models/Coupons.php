<?php

namespace App\Models;

use App\Repositories\CouponsRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use \App\Constants\Roles;

class Coupons extends Model
{
    use HasFactory;

    public static function getIP()
    {
        if (App::environment('local')) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }


    public function isValid($user_id, $ip)
    {
        if (Auth::guard('api')->user()->role_id == Roles::REPRESENTATIVE)
            return false;
        if ($this->multi) {
            $startDate = Carbon::parse($this->start_date);
            $endDate = Carbon::parse($this->end_date);
            $now = Carbon::now();
            if ($now->lessThan($startDate) || $now->greaterThan($endDate)) return false;
            return !CouponHistory::where('coupon_id', $this->id)->where(function ($q) use ($user_id, $ip) {
                $q->where('user_id', $user_id)->orWhere('ip', $ip);
            })->exists();
        }

        return $this->user_id ? false : true;
    }

    public function activate($order)
    {
        $user_id = $order->user_id;
        $ip = self::getIP();

        if ($this->isValid($user_id, $ip)) {
            if ($this->multi) {
                $couponHistory = new CouponHistory();
                $couponHistory->coupon_id = $this->id;
                $couponHistory->user_id = $user_id;
                $couponHistory->ip = $ip;
                $couponHistory->order_id = $order->id;
                $couponHistory->save();
                if ($this->isAttachedToRep()) {
                    $this->saveRepPayment($couponHistory);
                }
            } else {
                $this->user_id = $user_id;
                $this->save();
            }
        }

        return $this;
    }

    private function saveRepPayment($couponHistory)
    {
        $paidAmount = $couponHistory->orders->payments->amount;
        $compensationType = $this->repCoupons->compensation_type;
        $compensationAmount = $this->repCoupons->quantity;
        if ($compensationType == 'percentage') {
            $compensationAmount = round(($compensationAmount / 100) * $paidAmount);
        }
        $repPayment = new RepresentativePayments();
        $repPayment->coupon_history_id = $couponHistory->id;
        $repPayment->rep_id = $this->repCoupons->rep_id;
        $repPayment->fee = $compensationAmount;
        $repPayment->save();
    }

    public function couponHistories()
    {
        return $this->hasMany(CouponHistory::class, 'coupon_id');
    }

    public function repCoupons()
    {
        return $this->hasOne(RepCoupon::class, 'coupon_id');
    }

    public function isAttachedToRep()
    {
        return $this->repCoupons()->exists();
    }
}
