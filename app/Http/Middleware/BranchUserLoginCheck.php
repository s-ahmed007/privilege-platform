<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class BranchUserLoginCheck
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
        if (! Session::has('branch_user_username')) {
            return redirect('/branch_user_logout');
        }

        return $next($request);
    }
}
