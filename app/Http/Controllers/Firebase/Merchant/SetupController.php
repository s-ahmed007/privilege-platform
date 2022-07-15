<?php

namespace App\Http\Controllers\Firebase\Merchant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SetupController extends Controller
{
    public function getFirebaseMerchantAPIkey()
    {
        return 'AAAAwHwFm_k:APA91bF5dEVHp9wyDx0ASZAxYnPmHHx3SYJFh7yf3QTSHsqC62fj4H9wmt0HmmfHXdRiEmZ3ZQvf2biOa7rfn290Aie4DR81D0Wjyb2zOFvaXe-woCJDvGDp3SVjVzPXMaRoMXG_Hd6Y';
    }

    // function makes curl request to firebase servers
    private function sendMerchantPushNotification($fields)
    {

        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = [
            'Authorization: key='.$this->getFirebaseMerchantAPIkey(),
            'Content-Type: application/json',
        ];
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === false) {
            die('Curl failed: '.curl_error($ch));
        }

        // Close connection
        curl_close($ch);

        return $result;
    }

    // sending push message to single user by firebase reg id
    public function sendMerchantFirebaseMessage($to, $message)
    {
        $fields = [
            'to' => $to,
            'data' => $message,
        ];

        return $this->sendMerchantPushNotification($fields);
    }

    public function sendMerchantGlobalMessage($message, $firebaseRegId)
    {
        // notification title
        $res = [];
        $res['data']['title'] = 'Royalty';
        $res['data']['is_background'] = false;
        $res['data']['message'] = $message;
        //$res['data']['image'] = 'N/A';
        //$res['data']['payload'] = 'N/A';
        $res['data']['timestamp'] = date('Y-m-d G:i:s');

        $this->sendMerchantFirebaseMessage($firebaseRegId, $res);
    }
}
