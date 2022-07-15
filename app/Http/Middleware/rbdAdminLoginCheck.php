<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class rbdAdminLoginCheck
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
        if (! Session::has('admin')) {
            return redirect('/adminDashboard');
        }

        return $next($request);
    }
}
