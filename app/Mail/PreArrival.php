<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PreArrival extends Mailable
{
    use Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.reservations.preArrival')->with([
            'order_code' => $this->order->orders->order_code,
            'date' => $this->order->date,
            'time_slots' => $this->order->timeSlots[0]->slot_name,

        ])->subject('Dont forget, you have a studio booking tomorrow :)');
    }
}
