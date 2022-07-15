<?php

namespace App\Http\Middleware;

use Closure;

class PreOrderCheck
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
        $cardType = $request->route()->parameters();
        if ($cardType['cardType'] != 'gold' && $cardType['cardType'] != 'platinum') {
            return redirect('pre-order/card-selection');
        }

        return $next($request);
    }
}
