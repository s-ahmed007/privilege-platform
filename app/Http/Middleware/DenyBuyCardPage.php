<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class DenyBuyCardPage
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
        if (session('customer_id')) {
            if (Session::has('restrict_access_buy_card_page') || session('user_type') == 2) {
                return Redirect('page-session-expired');
            }
        } else {
            return Redirect('login');
        }

        return $next($request);
    }
}
