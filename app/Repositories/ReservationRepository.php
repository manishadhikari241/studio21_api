<?php


namespace App\Repositories;


use App\Constants\ErrorCodes;
use App\Http\Resources\CalendarCollection;
use App\Interfaces\ReservationInterface;
use App\Models\Configuration;
use App\Models\Coupons;
use App\Models\Orders;
use App\Models\Payments;
use App\Models\Reservations;
use App\Models\TimeSlots;
use App\Models\User;
use App\Traits\Closures;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Stripe;


class ReservationRepository extends MainRepository implements ReservationInterface
{
    use Closures;

    protected $currentTime;
    protected $maxDate;
    protected $currentDate;

    public function __construct()
    {
        $this->currentDate = Carbon::now()->format('Y-m-d');
        $this->currentTime = explode(':', Carbon::now()->toTimeString())[0];
        $this->maxDate = Configuration::firstOrCreate(['configuration_key' => 'max_date'], ['configuration_value' => Carbon::now()->addDays(20)->format('Y-m-d')])->configuration_value;
    }

    public function all($filters)
    {
        $reservations = Reservations::with(['orders.payments.billingAddresses', 'orders.users', 'orders.couponHistories.coupons', 'timeSlots'])->where('type', 'reservation')->filter($filters);
        return $this->success('Data Fetched', $reservations, ErrorCodes::SUCCESS);
    }

    public function getById()
    {
        $user = User::where('id', Auth::guard('api')->id())->with(['orders' => function ($query) {
            $query->orderBy('id', "desc");
            $query->with('reservations.timeSlots');
        }, 'orders.payments.billingAddresses'])->first();
        return $this->success('Data Fetched', $user->orders, ErrorCodes::SUCCESS);
    }

    public function getActiveDateSlot()
    {
        $increment = 0;
        $availableSlots = TimeSlots::where('status', 1)->get();
        $available = false;
        $activeDate = '';
        $activeSLotIds = [];
        $reservedDate = $this->reservedDates($this->maxDate);
        do {
            $addDate = Carbon::today()->addDays($increment)->format('Y-m-d');
            $sunday = Carbon::parse($addDate)->format('D');
            if ($sunday == 'Sun') {
                $addDate = Carbon::today()->addDays($increment + 1)->format('Y-m-d');
            }
            foreach ($availableSlots as $slots) {
                if ($this->isSLotAvailable($addDate, $slots->id)) {
                    $activeDate = $addDate;
                    array_push($activeSLotIds, $slots->id);
                    $available = true;
                    break;
                } else {
                    $available = false;
                }
            }
            if (!$available) $increment++;
        } while ($available == false);

        $data = ['activeDate' => $activeDate, 'activeSlot' => TimeSlots::whereIn('id', $activeSLotIds)->get(), 'max_date' => $this->maxDate, 'reservedDate' => $reservedDate];

        return $this->success('Latest Available Date', (object)$data, ErrorCodes::SUCCESS);
    }

    private function reservedDates($maxDate)
    {
        $loopCount = Carbon::today()->diff($maxDate)->days;
        $reservedDates = [];
        $availableSlots = TimeSlots::where('status', 1)->get();
        $totalSlots = count($availableSlots);
        $blockedSlot = 0;
        for ($i = 0; $i <= $loopCount; $i++) {

            $date = Carbon::today()->addDays($i)->format('Y-m-d');
            foreach ($availableSlots as $slots) {
                if (!$this->isSLotAvailable($date, $slots->id)) {
                    $blockedSlot += 1;
                }
            }
            if ($blockedSlot == $totalSlots) {
                array_push($reservedDates, $date);
            }
            $blockedSlot = 0;
        }
        return $reservedDates;

    }

    public function getAvailableSlots($date)
    {
        $availableSlots = TimeSlots::all();
        $availableOnly = [];
        foreach ($availableSlots as $slots) {
            if ($this->isSLotAvailable($date, $slots->id))
                array_push($availableOnly, $slots);
//            $isDisabled = '$isDisabled';
//            $slots->$isDisabled = !$this->isSLotAvailable($date, $slots->id);
        }
        return $availableOnly;
    }

    public function isSLotAvailable($date, $timeSlotId, $closure = true)
    {
        //checks if slot already passed away
        $checkSlot = TimeSlots::where('status', 1)->where('id', $timeSlotId)->first();
        if (!$checkSlot || ($this->currentTime >= explode(':', $checkSlot->from)[0] - 1 && $date == Carbon::today()->format('Y-m-d'))) {
            return false;
        }
        if ($closure && ($this->isInBulkClosure($date, $timeSlotId, 'custom') || $this->isInBulkClosure($date, $timeSlotId, 'weekly')))
            return false;

        //takes single SlotId and date and returns if slot is available on the specific date
        $reservedSlot = TimeSlots::where('status', 1)->whereHas('reservations', function ($query) use ($date) {
            $query->where('date', $date);
            $query->where('status', 'pending');
        })->get();
        if ($reservedSlot->isEmpty()) return true;
        else {
            foreach ($reservedSlot as $reserved) {
                $slots = TimeSlots::where('status', 1)->where('id', $timeSlotId)
                    ->where(function ($query) use ($reserved) {
                        $query->whereBetween('from', [explode(':', $reserved->from)[0], explode(':', $reserved->to)[0]]);
                        $query->orwhereBetween('to', [explode(':', $reserved->from)[0], explode(':', $reserved->to)[0]]);
                    })
                    ->first();
                if ($slots) return false;
                $onlySlots = TimeSlots::where('status', 1)->where('id', $timeSlotId)->first();
                $from = explode(':', $reserved->from)[0];
                $to = explode(':', $reserved->to)[0];
                foreach (range($onlySlots->from, $onlySlots->to) as $number) {
                    if ($number == $from || $number == $to)
                        return false;
                }
            }
        }
        return true;
    }

    public function getCalendarData()
    {
        try {
            $calendar = [];
            $reservation = Reservations::where('status', '!=', 'cancelled')->with(['orders.payments.billingAddresses', 'orders.users', 'orders.couponHistories.coupons', 'timeSlots'])->get();
            $customClosure = $this->getCustomClosureCalendarData();
            $weeklyClosure = $this->getWeeklyClosureCalendarData();
            if ($customClosure)
                array_push($calendar, $customClosure);
            if ($weeklyClosure) {
                foreach ($weeklyClosure as $closure)
                    array_push($calendar, $closure);
            }
//            return $weeklyClosure;
            foreach ($reservation as $reserve) {
                foreach ($reserve->timeSlots as $calendarData) {
                    $calendarData->date = $reserve->date;
                    $calendarData->name = $reserve->name;
                    $calendarData->color = $reserve->color;
                    $calendarData->type = $reserve->type;
                    $calendarData->details = $reserve->details;
                    $calendarData->reservationStatus = $reserve->status;
                    $calendarData->orderDetail = $reserve->orders;
                    array_push($calendar, $calendarData);
                }
            }
//            return collect($calendar);
            $calendarDetail = CalendarCollection::collection($calendar);
            return $this->success('Data Fetched Successfully', $calendarDetail, ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function store($request)
    {
        try {
            if (!TimeSlots::checkAmount($request->amount, $request->time_slots))
                return $this->error('Amount Does not Match', ErrorCodes::FORBIDDEN);
            if (!$this->canMakeReservation($request->date, $request->time_slots))
                return $this->error('Already Reserved at this time Slot. Please try again.', ErrorCodes::NOT_FOUND);
            $amount = $request->amount;
            $coupon = null;
            $user = User::find(Auth::guard('api')->id());
            if ($user->isRepresentative()) {
                $amount -= $user->representativeDiscountHistories->last() ? $user->representativeDiscountHistories->last()->discount_percent / 100 * $request->amount : 0;
            } else if ($request->coupon) {
                $tmpCoupon = Coupons::where('code', $request->coupon)->first();
                if ($tmpCoupon && $tmpCoupon->isValid(Auth::guard('api')->id(), Coupons::getIP())) {
                    if ($tmpCoupon->discount_type == 'percentage') {
                        $amount -= $tmpCoupon->quantity / 100 * $request->amount;
                    } else $amount -= $tmpCoupon->quantity;
                    if ($amount <= 0)
                        return $this->error('Amount Does not Match', ErrorCodes::FORBIDDEN);
                    $coupon = $tmpCoupon;
                } else
                    return $this->error('Coupon not valid', ErrorCodes::FORBIDDEN);
            }
            $charge = $this->stripeCharge($request->stripeToken, $amount, $request->billing_id, $request->date);
            if ($charge->status == "succeeded") {
                $order = Orders::create([
                    'user_id' => Auth::guard('api')->id(),
                    'order_code' => Str::random(12)
                ]);
                $payment = Payments::where('transaction_id', $charge->id)->first();
                $payment->order_id = $order->id;
                $payment->save();
                $reserve = Reservations::create([
                    'name' => $request->name,
                    'date' => $request->date,
                    'color' => $request->color,
                    'details' => $request->details,
                    'order_id' => $order->id,
                    'status' => "pending"
                ]);
                if ($coupon) {
                    $coupon->activate($order);
                }
                $reserve->timeSlots()->attach($request->time_slots);
                $reserveData = Reservations::where('id', $reserve->id)->with(['orders', 'timeSlots'])->first();
                Reservations::sendConfirmEmail($order->order_code, $reserve->date, $reserve->timeSlots[0]->slot_name, $order->users->email);
                return $this->success('You have successfully reserved the studio', $reserveData, ErrorCodes::SUCCESS);

            } else return $this->error('Your Payment Failed', ErrorCodes::UNAUTHORIZED);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

    }

    private function canBookSlots($timeSlots, $date)
    {
        $slots = TimeSlots::where('status', 1)->whereIn('id', $timeSlots)->get();
        foreach ($slots as $slot) {
            if (!$this->isSLotAvailable($date, $slot->id))
                return false;
            $slot->from = explode(':', $slot->from)[0];
            $slot->to = explode(':', $slot->to)[0];
        }
        $count = count($slots);
        if ($count > 1) {
            for ($i = 0; $i < $count; $i++) {
                for ($j = $i + 1; $j < $count; $j++) {
                    $firstLoop = range($slots[$i]->from, $slots[$i]->to);
                    $secondLoop = range($slots[$j]->from, $slots[$j]->to);
                    if (array_intersect($firstLoop, $secondLoop))
                        return false;
                }
            }
            return true;
        } else return true;
    }

    private function isDateAvailable($date)
    {
        $date = Carbon::parse($date);
//        $date->format('D') == 'Sun';
        if ($date->gt($this->maxDate) || $date->lt($this->currentDate)) {
            return false;
        } else return true;
    }

    protected function canMakeReservation($date, array $timeSlots)
    {
        if (!$this->isDateAvailable($date) || !$this->canBookSlots($timeSlots, $date))
            return false;
        //takes date and array of slot Id to check multiple slot
        $slots = TimeSlots::where('status', 1)->whereIn('id', $timeSlots)->get();
        $reservation = Reservations::where('status', 'pending')->whereDate('date', $date)->whereHas('timeSlots', function ($query) use ($timeSlots, $slots) {
            $query->where('status', 1);
//            $query->whereIn('time_slots.id', $timeSlots);
            foreach ($slots as $time) {
                $query->whereBetween('from', [explode(':', $time->from)[0], explode(':', $time->to)[0]]);
                $query->orWhereBetween('to', [explode(':', $time->from)[0], explode(':', $time->to)[0]]);
            }
        })->with('timeSlots')->get();
        return $reservation->isnotEmpty() ? false : true;
    }

    private function stripeCharge($stripeToken, $amount, $billingId, $date)
    {
        try {
            if ($this->canCancel($date)) {
                $stripe = new Stripe\StripeClient(env('STRIPE_SECRET'));
                $charge = $stripe->charges->create([
                    'amount' => $amount * 100,
                    'currency' => 'hkd',
                    'capture' => false,
                    'source' => $stripeToken
                ]);
            } else {
                Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                $charge = Stripe\Charge::create([
                    "amount" => $amount * 100,
                    "currency" => "hkd",
                    "source" => $stripeToken,
                ]);
            }
//            return $charge;
            $payment = new Payments();
            $payment->user_id = Auth::guard('api')->id();
            $payment->billing_id = $billingId ? $billingId : null;
            $payment->amount = $amount;
            $payment->transaction_id = $charge->id;
            if ($charge->status == "succeeded") {
                if ($charge->captured == true) {
                    $payment->status = "PAID";
                } else $payment->status = "DUE";
            } else $payment->status = "NOTCHARGED";
            $payment->save();
            return $charge;
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ErrorCodes::FORBIDDEN);
        }
    }

    private function canCancel($date)
    {
        $bookingDate = new \Carbon\Carbon($date);
        $todaysDate = Carbon::today();
        $days = $bookingDate->diff($todaysDate)->days;
        if ($days > 3)
            return true;
        else return false;
    }

    public function cancelBooking($request)
    {
        try {
            $order = Orders::where('user_id', Auth::guard('api')->id())->where('id', $request->id)->firstOrFail();
            if ($this->canCancel(Carbon::parse($order->reservations->date)->format('Y-m-d'))) {
                $stripe = new \Stripe\StripeClient(
                    env('STRIPE_SECRET')
                );
                $stripe->refunds->create([
                    'charge' => $order->payments->transaction_id,
                ]);
                $order->payments->status = "NOTCHARGED";
                $order->payments->save();
                if ($order->couponHistories && $order->couponHistories->repPayments) {
                    $order->couponHistories->repPayments->fee = 0;
                    $order->couponHistories->repPayments->save();
                }
            }
            $order->reservations->status = "cancelled";
            $order->reservations->color = "red";
            $order->reservations->save();
            $order->save();
            Reservations::sendCancelEmail($order->order_code, $order->reservations->date, $order->reservations->timeSlots[0]->slot_name, $order->users->email);
            return $this->success('Reservation Successfully Cancelled', $order, ErrorCodes::SUCCESS);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), ErrorCodes::FORBIDDEN);
        }
    }


}
