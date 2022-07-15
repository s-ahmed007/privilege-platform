<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Socialite;

class SocialAuthController extends Controller
{
    public function callback(Request $request)
    {
        $code = $request->get('code');

        $access_details = 'https://graph.facebook.com/oauth/access_token?client_id=149014475722955&redirect_uri=https://royaltybd.com/_oauth/facebook&client_secret=f0e3ad6c8cb552ef8b9cc35ba5280e90&code='.$code;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $access_details);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        $access_details = curl_exec($ch);
        $access_details = json_decode($access_details);

        $user_details = 'https://graph.facebook.com/me?fields=name,email&access_token='.$access_details->access_token;
        curl_setopt($ch, CURLOPT_URL, $user_details);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        $user_details = curl_exec($ch);
        $user_details = json_decode($user_details);
        dd($user_details);
    }
}
