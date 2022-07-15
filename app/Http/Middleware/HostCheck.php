<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Redirect;

class HostCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $host = $_SERVER['SERVER_NAME'];
        if ($host != 'royaltybd.club' && $host != '206.189.94.16') {
            //redirect to .club if someone tries to access .com or else
            return Redirect::to('http://royaltybd.club/');
        }

        return $next($request);
    }
}
