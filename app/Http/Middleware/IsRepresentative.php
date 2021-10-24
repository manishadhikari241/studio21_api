<?php

namespace App\Http\Middleware;

use App\Constants\ErrorCodes;
use App\Traits\ResponseAPI;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsRepresentative
{
    use ResponseAPI;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('api')->check() && Auth::guard('api')->user()->isRepresentative()){
            return $next($request);
        }
        else return $this->error('Unauthorized',ErrorCodes::UNAUTHORIZED);    }
}
