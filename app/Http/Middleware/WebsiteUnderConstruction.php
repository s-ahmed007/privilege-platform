<?php

namespace App\Http\Middleware;

use Closure;

class WebsiteUnderConstruction
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
        return redirect('maintenancebreak');

        return $next($request);
    }
}
