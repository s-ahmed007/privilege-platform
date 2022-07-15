<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Enum\AdminRole;
use Closure;
use Illuminate\Support\Facades\Session;

class RbdSuperAdminLoginCheck
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
        if (Session::get('admin') != AdminRole::superadmin) {
            return redirect('/adminLogout');
        }

        return $next($request);
    }
}
