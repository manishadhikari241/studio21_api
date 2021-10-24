<?php

namespace App\Constants;

class ErrorCodes {

    const UNKNOWN_ERROR = 4000;
    const INVALID_CREDENTIALS = 4001;
    const DUPLICATE_ENTRY = 4002;
    const VALIDATION_FAILED = 403;
    const NOT_FOUND = 404;
    const THROTTLED = 4005;
    const NOT_ENOUGH_QUOTA = 4006;
    CONST UNAUTHORIZED = 401;
    CONST TOO_MANY_REQUESTS = 4008;
    CONST UNVERIFIED = 4009;
    const PAYMENT_FAILED = 5001;
    const SUCCESS= 200;
    const FORBIDDEN= 403;

    /* Messages */

    public static $messages = [
        4000 => 'unknown error',
        4001 => 'invalid credentials',
        4002 => 'duplicate entry',
        4003 => 'validation failed',
        4004 => 'not found',
        4005 => 'throttled',
        4006 => 'not enough quota',
        4007 => 'unauthorized',
        4008 => 'too many requests',
        4009 => 'unverified',
        5001 => 'payment failed'
    ];

    public static function Message($code) {
        return self::$messages[$code];
    }
}
