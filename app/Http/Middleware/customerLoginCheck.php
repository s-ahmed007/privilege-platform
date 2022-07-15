<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class customerLoginCheck
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
        $prev_url = url()->previous();
        if ($prev_url == url('/order-success')) {
            $request->session()->forget('restrict_access_buy_card_page');
        }
        if (! Session::has('customer_id')) {
            return redirect('/login');
        }

        return $next($request);
    }
}
