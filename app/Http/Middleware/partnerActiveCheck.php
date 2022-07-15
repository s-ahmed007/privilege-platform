<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class partnerActiveCheck
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
        $partner_info = $request->route()->parameters();

        $status = DB::table('partner_account as pa')
            ->join('partner_branch as pb', 'pb.partner_account_id', '=', 'pa.partner_account_id')
            ->where('pb.id', $partner_info['branch'])
            ->select('pa.active')
            ->first();

        if ($status && $status->active == 1) {
            return $next($request);
        } else {
            return redirect('page-not-found');
        }
    }
}
