<?php

namespace App\Models;

use App\Mail\PreArrival;
use App\Mail\ReservationCancel;
use App\Mail\ReservationConfirm;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;
use Stripe;

class Reservations extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'date', 'color', 'details', 'status', 'order_id', 'type'];

    public function orders()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function timeSlots()
    {
        return $this->belongsToMany(TimeSlots::class, 'reservation_slot');
    }

    public static function changeStatus()
    {
        $reservation = Reservations::where('date', Carbon::today())->where('type', 'reservation')->where('status', 'pending')->with('timeSlots')->get();
        $currentTime = explode(':', Carbon::now()->toTimeString())[0];
        foreach ($reservation as $reserve) {
            foreach ($reserve->timeSlots as $slot) {
                if ($currentTime > explode(':', $slot->to)[0]) {
                    $update = Reservations::find($reserve->id);
                    $update->color = 'green';
                    $update->status = "finished";
                    $update->save();
                }

            }
        }
    }

    public static function chargeDueAmount()
    {
        $chargeDate = Carbon::now()->addDays(3)->format('Y-m-d');
        $dues = Reservations::whereHas('orders.payments', function ($query) {
            $query->where('payments.status', 'DUE');
        })->whereDate('reservations.date', '<=', $chargeDate)->get();
        if ($dues->isnotEmpty()) {
            foreach ($dues as $chargeDue) {
                $chargeId = $chargeDue->orders->payments->transaction_id;
                $stripe = new Stripe\StripeClient(env('STRIPE_SECRET'));
                $stripe->charges->capture($chargeId,
                    []
                );
                $chargeDue->orders->payments->status = "PAID";
                $chargeDue->orders->payments->save();
            }

        }

        return $dues;
    }

    public static function sendPreArrivalEmail()
    {
        $preDate = Carbon::now()->addDays(1)->format('Y-m-d');
        $preArrivals = Reservations::whereHas('orders.payments', function ($query) {
            $query->where('payments.status', 'PAID');
        })->whereDate('reservations.date', '=', $preDate)->with(['orders.users', 'timeSlots'])->get();

        foreach ($preArrivals as $arrival) {
//            return new PreArrival($arrival);
            Mail::to($arrival->orders->users->email)->bcc(env('MAIL_USERNAME'))->send(new PreArrival($arrival));
        }
        return true;
    }

    public static function sendConfirmEmail($orderCode, $reservedDate, $slots, $email)
    {
        Mail::to($email)->bcc(env('MAIL_USERNAME'))->send(new ReservationConfirm($orderCode, $reservedDate, $slots));
    }

    public static function sendCancelEmail($orderCode, $reservedDate, $slots, $email)
    {
        Mail::to($email)->bcc(env('MAIL_USERNAME'))->send(new ReservationCancel($orderCode, $reservedDate, $slots));
    }

}
