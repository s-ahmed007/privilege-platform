<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class BranchLoginCheck
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
        if (! Session::has('branch_user_username_v2')) {
            return redirect('/partner');
        }

        return $next($request);
    }
}
