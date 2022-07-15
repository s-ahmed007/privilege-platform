<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class B2b2cAdminLoginCheck
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
        if (! Session::has('client-admin')) {
            return redirect('/client/adminDashboard');
        }

        return $next($request);
    }
}
