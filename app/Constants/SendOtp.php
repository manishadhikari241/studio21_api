<?php


namespace App\Constants;


use App\Mail\ForgotPassword;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Nexmo\Laravel\Facade\Nexmo;
use PharIo\Manifest\Email;

class SendOtp
{
    public static function sendOtp($phone)
    {
        $code = rand(1111, 9999);
        Nexmo::message()->send([
            'to' => $phone,
            'from' => env('APP_NAME'),
            'text' => "Studio21 Verification code:" . ' ' . $code . '. ' . 'Do not share this code with anyone.'
        ]);
        return $code;
    }

    public static function sendOtpEmail($email)
    {
        $code = rand(1111, 9999);
        Mail::to($email)->send(new ForgotPassword($code));
        return $code;
    }
}
