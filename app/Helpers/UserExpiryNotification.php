<?php

namespace App\Helpers;

use App\Http\Controllers\adminController;
use App\Http\Controllers\Enum\Constants;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UserExpiryNotification
{
    public function sendNotification()
    {
        if (strpos(php_sapi_name(), 'cli') !== false) {
            //nothing
        } else {
            if (env('RBD_SERVER') == 'PRODUCTION') {//aborting if someone hits from live server
                return redirect('page-not-found');
            }
        }
//        $title = 'Renew your membership';
//
//        $customers_10 = DB::select("select firebase_token, (expiry_date - CURDATE()) as dcount
//                            from customer_info
//                            where firebase_token != '0'
//                            having dcount = 10");
//        $customers_5 = DB::select("select firebase_token, (expiry_date - CURDATE()) as dcount
//                            from customer_info
//                            where firebase_token != '0'
//                            having dcount = 5");
//        $customers_0 = DB::select("select firebase_token, (expiry_date - CURDATE()) as dcount
//                            from customer_info
//                            where firebase_token != '0'
//                            having dcount = 0");
//        $f_result1 = array_chunk($customers_10, Constants::notification_chunk);
//        $f_result2 = array_chunk($customers_5, Constants::notification_chunk);
//        $f_result3 = array_chunk($customers_0, Constants::notification_chunk);
//
//        foreach ($f_result1 as $customers){
//            $f_tokens = Arr::pluck($customers, 'firebase_token');
//            $message = "Your membership is about to expire in 10 days. Renew now and stay connected.";
//            (new adminController())->sendCustomerWisePushNotification($title, $message, $f_tokens);
//        }
//        foreach ($f_result2 as $customers){
//            $f_tokens = Arr::pluck($customers, 'firebase_token');
//            $message = "Your membership is about to expire in 5 days. Renew now and stay connected.";
//            (new adminController())->sendCustomerWisePushNotification($title, $message, $f_tokens);
//        }
//        foreach ($f_result3 as $customers){
//            $f_tokens = Arr::pluck($customers, 'firebase_token');
//            $message = "Your membership expired. Renew now and stay connected.";
//            (new adminController())->sendCustomerWisePushNotification($title, $message, $f_tokens);
//        }

        $title = 'Renew your membership';
        $customers_10 = DB::select('select customer_email, firebase_token, DATEDIFF(expiry_date, CURDATE()) as dcount
                            from customer_info
                            having dcount = 10');
//        $customers_3 = DB::select("select customer_email, firebase_token, DATEDIFF(expiry_date, CURDATE()) as dcount
//                            from customer_info
//                            having dcount = 3");
        $customers_0 = DB::select('select customer_email, firebase_token, DATEDIFF(expiry_date, CURDATE()) as dcount
                            from customer_info
                            having dcount = 0');
        $customers_after_15 = DB::select('select customer_email, firebase_token, DATEDIFF(CURDATE(), expiry_date) as dcount
                            from customer_info
                            having dcount = 15');

        $f_result1 = array_chunk($customers_10, Constants::notification_chunk);
//        $f_result2 = array_chunk($customers_3, Constants::notification_chunk);
        $f_result3 = array_chunk($customers_0, Constants::notification_chunk);
        $f_result4 = array_chunk($customers_after_15, Constants::notification_chunk);

        foreach ($f_result1 as $customers) {
            $emails = Arr::pluck($customers, 'customer_email');
            (new adminController())->userExpiryMail($emails, 'expiring');
        }
//        foreach ($f_result2 as $customers){
//            $emails = Arr::pluck($customers, 'customer_email');
//            (new adminController())->userExpiryMail($emails);
//        }
        foreach ($f_result3 as $customers) {
            $emails = Arr::pluck($customers, 'customer_email');
            (new adminController())->userExpiryMail($emails, 'expiry');
        }
        foreach ($f_result4 as $customers) {
            $emails = Arr::pluck($customers, 'customer_email');
            (new adminController())->userExpiryMail($emails, 'expired');
        }

        $customers_10 = collect($customers_10)->where('firebase_token', '!=', '');
//        $customers_3 = collect($customers_3)->where('firebase_token', '!=', '');
        $customers_0 = collect($customers_0)->where('firebase_token', '!=', '');
        $customers_after_15 = collect($customers_after_15)->where('firebase_token', '!=', '');

        $f_result1 = array_chunk(json_decode(json_encode($customers_10), true), Constants::notification_chunk);
//        $f_result2 = array_chunk(json_decode(json_encode($customers_3), true), Constants::notification_chunk);
        $f_result3 = array_chunk(json_decode(json_encode($customers_0), true), Constants::notification_chunk);
        $f_result4 = array_chunk(json_decode(json_encode($customers_after_15), true), Constants::notification_chunk);

        foreach ($f_result1 as $customers) {
            $f_tokens = Arr::pluck($customers, 'firebase_token');
            $message = 'Your membership is about to expire in 10 days. Renew now and stay connected.';
            (new adminController())->sendCustomerWisePushNotification($title, $message, $f_tokens);
        }
//        foreach ($f_result2 as $customers){
//            $f_tokens = Arr::pluck($customers, 'firebase_token');
//            $message = "Your membership is about to expire in 3 days. Renew now and stay connected.";
//            (new adminController())->sendCustomerWisePushNotification($title, $message, $f_tokens);
//        }
        foreach ($f_result3 as $customers) {
            $f_tokens = Arr::pluck($customers, 'firebase_token');
            $message = 'Your membership has expired. Renew now and stay connected.';
            (new adminController())->sendCustomerWisePushNotification($title, $message, $f_tokens);
        }
        foreach ($f_result4 as $customers) {
            $f_tokens = Arr::pluck($customers, 'firebase_token');
            $message = 'Your membership has expired. Renew now and stay connected.';
            (new adminController())->sendCustomerWisePushNotification($title, $message, $f_tokens);
        }

        if (strpos(php_sapi_name(), 'cli') !== false) {
            //nothing
        } else {
            return 'Success';
        }
    }
}