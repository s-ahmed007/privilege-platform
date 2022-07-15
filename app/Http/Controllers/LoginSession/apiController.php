<?php

namespace App\Http\Controllers\LoginSession;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class apiController extends Controller
{
    public function createSession(Request $request)
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $platform = $request->post('platform') ?? 0;
        $physical_address = $request->post('physical_address') ?? 'Not found';
        $ip_address = $request->post('ip_address') ?? 'Not found';
        $status = $request->post('status');

        return response()->json((new functionController())->store($customer_id, $platform, $physical_address, $ip_address, $status), 200);
    }

    public function checkSession(Request $request)
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $platform = $request->post('platform') ?? 0;
        $physical_address = $request->post('physical_address') ?? 'Not found';
        $ip_address = $request->post('ip_address') ?? 'Not found';
        $version = $request->post('version');

        return response()->json((new functionController())->checkSession($customer_id, $platform, $physical_address, $ip_address, $version), 200);
    }
}
