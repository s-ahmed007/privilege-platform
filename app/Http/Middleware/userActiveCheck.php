<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class userActiveCheck
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
        $user_info = $request->route()->parameters();

        $status = DB::table('customer_account')
            ->where('customer_username', $user_info['username'])
            ->select('moderator_status')
            ->first();

        if ($status->moderator_status == 2) {
            return $next($request);
        } elseif ($status->moderator_status == 1) {
            return redirect('page-not-found');
        }
    }
}
