<?php

namespace App\Http\Controllers\ActivitySession;

use App\CustomerActivitySession;
use App\CustomerInfo;
use App\Http\Controllers\Controller;

class functionController extends Controller
{
    public function makeUsersEmailVerified()
    {

//        $merged_emails = array_merge($mail1, $mail2, $mail3,$mail4);
//        $unique_emails = array_unique($merged_emails);
//
//        foreach ($unique_emails as $email) {
//            CustomerInfo::where('customer_email', $email)
//                ->where('email_verified', 0)
//                ->update(['email_verified' => 1]);
//        }
//
//        echo count($unique_emails) . " email ids are verified";
    }

    public function saveSession($customer_id, $platform, $physical_address, $ip_address, $version = null)
    {
        $activity = new CustomerActivitySession();
        $activity->customer_id = $customer_id;
        $activity->platform = $platform;
        $activity->physical_address = $physical_address;
        $activity->ip_address = $ip_address;
        $activity->version = $version;
        $activity->save();

        return $activity;
    }
}
