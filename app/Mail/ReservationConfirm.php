<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationConfirm extends Mailable
{
    use Queueable, SerializesModels;

    protected $orderCode;
    protected $reservedDate;
    protected $slots;

    /**
     * Create a new message instance.
     *
     * @param $orderCode
     * @param $reservedDate
     * @param $slots
     */
    public function __construct($orderCode, $reservedDate, $slots)
    {
        $this->orderCode = $orderCode;
        $this->reservedDate = $reservedDate;
        $this->slots = $slots;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.reservations.confirm')->with([
            'orderCode' => $this->orderCode,
            'reservedDate' => $this->reservedDate,
            'slots' => $this->slots
        ])->subject('Your studio booking has been confirmed!');
    }
}
