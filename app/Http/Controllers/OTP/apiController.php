<?php

namespace App\Http\Controllers\OTP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Tymon\JWTAuth\Facades\JWTAuth;

class apiController extends Controller
{
    public function sendMailVerification(Request $request)
    {
        $prev_mail = $request->post('prev_email');
        $email = $request->post('email');
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;

        return (new functionController())->sendMailVerification($prev_mail, $email, $customer_id);
    }

    public function sendPhoneVerification(Request $request)
    {
        $phone = $request->post('phone');
        $verification_type = $request->post('verification_type');

        return (new functionController())->sendPhoneVerification($phone, $verification_type);
    }

    public function verifyMailOTP(Request $request)
    {
        $email = $request->post('email');
        $pin = $request->post('pin');

        return (new functionController())->verifyEmailOTP($email, $pin);
    }

    public function verifyPhoneOTP(Request $request)
    {
        $phone = $request->post('phone');
        $pin = $request->post('pin');
        $verification_type = $request->post('verification_type');

        return (new functionController())->verifyPhoneOTP($phone, $pin, $verification_type);
    }
}
