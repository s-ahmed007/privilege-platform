<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Enum\BranchUserRole;
use Closure;
use Illuminate\Support\Facades\Session;

class BranchOwnerLoginCheck
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
        if (Session::has('branch_user_username_v2') && Session::get('branch_user_role') > BranchUserRole::branchScanner) {
            //allow user
        } else {
            return redirect('/partner');
        }

        return $next($request);
    }
}
