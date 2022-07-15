<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class user_registration
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Session::has('registration_url') || Session::has('registration_error')) {
            Session::forget('registration_url');
            Session::forget('registration_error');
        } else {
            return redirect('phone-verification');
        }

        return $next($request);
    }
}
