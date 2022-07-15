<?php

namespace App\Http\Middleware;

use Closure;
use DB;
use Illuminate\Support\Facades\Redirect;
use Session;

class checkCouponNumber
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
        if (Session::has('customer_id')) {
            //get available coupon number
            $coupon_number = DB::table('customer_reward')
                ->select('coupon')
                ->where('customer_id', Session::get('customer_id'))
                ->first();
            //redirect to profile if coupon number is 0
            if ($coupon_number->coupon == 0) {
                return Redirect('users/'.Session::get('customer_username'));
            }
        }

        return $next($request);
    }
}
