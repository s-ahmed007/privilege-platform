<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class partnerAdminLoginCheck
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
        if (Session::has('partner_id') && ! Session::has('partner_admin')) {
            return redirect('/partners/'.Session::get('partner_username'));
        } elseif (! Session::has('partner_id') && ! Session::has('partner_admin')) {
            return redirect('/login-page');
        }

        return $next($request);
    }
}
