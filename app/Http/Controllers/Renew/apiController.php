<?php

namespace App\Http\Controllers\Renew;

use App\CustomerAccount;
use App\CustomerHistory;
use App\CustomerInfo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\CustomerType;
use App\Http\Controllers\Enum\DeliveryType;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\Enum\ssl_validation_type;
use App\Http\Controllers\JsonControllerV2;
use App\Http\Controllers\LoginRegister\functionController;
use App\SslTransactionTable;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class apiController extends Controller
{
    public function fixExpiry()
    {
        $all_customers = CustomerInfo::with('latestSSLTransaction')
            ->where('customer_type', 2)
            ->where('expiry_date', '<=', '2020-01-31')
            ->get();
        $all_customers = collect($all_customers)
            ->where('latestSSLTransaction.tran_date', '>', '2019-01-31')
            ->where('latestSSLTransaction.tran_date', '<', '2019-12-19');
        foreach ($all_customers as $customer) {
            if ($customer->latestSSLTransaction) {
                $date = date_create($customer->latestSSLTransaction->tran_date);
                $customer->expiry_date = date_add($date, date_interval_create_from_date_string($customer->month.' month'));
                $customer->save();
            }
        }

        return 'success';
    }

    public function renew4ExpiredUser()
    {
        $users = ['1809218876001157', '1546709315001127', '1148553931000529', '1112747960000536'];
        $text_message = 'Hello there! To get 2020 off to a great start, we have extended your membership to January 31st 2020. Save more, live better with Royalty!';
        foreach ($users as $user) {
            $info = CustomerInfo::where('customer_id', $user)->first();
            $info->expiry_date = '2020-01-31';
            $info->save();
            $this->sendSms($info->customer_contact_number, $text_message);
        }
        echo 'Completed';
    }

    public function sendSms($phone, $text_message)
    {
        //send password via SMS
        //SMS ssl
//        $user = 'Royaltybd';
//        $pass = '66A6Q13d';
//        $sid = 'RoyaltybdMasking';
//        $url = 'http://sms.sslwireless.com/pushapi/dynamic/server.php';
//        $param = "user=$user&pass=$pass&sms[0][0]= $phone &sms[0][1]=".urlencode($text_message)."&sms[0][2]=123456789&sid=$sid";

        $username = env('BOOMCAST_USERNAME');
        $password = env('BOOMCAST_PASSWORD');
        $url = 'http://api.boom-cast.com/boomcast/WebFramework/boomCastWebService/externalApiSendTextMessage.php';
        $param = "masking=NOMASK&userName=$username&password=$password&MsgType=TEXT&receiver=$phone&message=".$text_message;

        $crl = curl_init();
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_HEADER, 0);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($crl, CURLOPT_POST, 1);
        curl_setopt($crl, CURLOPT_POSTFIELDS, $param);
        $response = curl_exec($crl);
        curl_close($crl);
        //code successfully sent
//        $message = 1;
        $response = json_decode($response, true)[0];
        return Response::json($response['success']);
    }

    public function makeVirtualTrialUser(Request $request)
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $promo_id = $request->post('promo_id');
        $month = $request->post('month');
        $platform = $request->header('platform', null);
        if ($promo_id) {
            return $this->createVirtualTrialUser($platform, $month, $customer_id, $promo_id);
        } else {
            return $this->createVirtualTrialUser($platform, $month, $customer_id);
        }
    }

    public function createVirtualTrialUser($platform, $month, $customer_id, $promo_id = 0, $seller_id = null)
    {
        if (! $month) {
            $month = 3;
        }
        $amount = 0;
        $tran_id = (new JsonControllerV2())->getSSLTransactionId();
        $delivery_type = DeliveryType::virtual_card;
        $tran_date = date('Y-m-d H:i:s');

        $customer = CustomerAccount::where('customer_id', $customer_id)->first();
        $renewInfo = (new \App\Http\Controllers\Renew\functionController())->insertInfoRenew($customer, $tran_id, $month, $delivery_type, $promo_id, $amount, $platform, false);
        if (! $renewInfo) {
            return Response::json(['message' => 'Something went wrong in renewing.'], 400);
        }
        $renewSSL = (new \App\Http\Controllers\Renew\functionController())->insertSSLRenew($customer_id, $tran_id, $amount, $platform);
        if (! $renewSSL) {
            return Response::json(['message' => 'Something went wrong in renewing.'], 400);
        }

        $info = (new \App\Http\Controllers\Renew\functionController())->updateSSLRenew($amount, $tran_id, $tran_date, '', '0.00',
            'CASH', '', 'BDT',
            '', '', '', 'BD',
            '', '0.00', $customer_id, $seller_id);

        if (! $info) {
            return Response::json(['message' => 'Something went wrong with the payment.'], 403);
        } else {
            $customer = CustomerInfo::where('customer_id', $customer_id)->first();
            $msg = 'Your FREE trial has been activated and it will expire on '.date('M d, Y', strtotime($customer->expiry_date)).'.';

            return Response::json(['result' => $msg],
                200);
        }
    }

    public function insertSSLInfo(Request $request)
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $month = $request->post('month');
        $amount = $request->post('amount');
        $tran_id = (new JsonControllerV2())->getSSLTransactionId();
        $delivery_type = $request->post('delivery_type');
        $promo_id = $request->post('promo_id');
        $platform = $request->header('platform', null);

        $customer = CustomerAccount::where('customer_id', $customer_id)->first();
        $renewInfo = (new \App\Http\Controllers\Renew\functionController())
            ->insertInfoRenew($customer, $tran_id, $month, $delivery_type, $promo_id, $amount, $platform, false);
        if (! $renewInfo) {
            return Response::json(['message' => 'Something went wrong in renewing.'], 400);
        }
        $renewSSL = (new \App\Http\Controllers\Renew\functionController())
            ->insertSSLRenew($customer_id, $tran_id, $amount, PlatformType::android);
        if (! $renewSSL) {
            return Response::json(['message' => 'Something went wrong in renewing.'], 400);
        }

        return Response::json(['result' => $tran_id], 200);
    }

    public function renewSuccess(Request $request)
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $tran_id = $request->post('tran_id');
        $amount = $request->post('amount');
        $tran_date = $request->post('tran_date');
        $val_id = $request->post('val_id');
        $store_amount = $request->post('store_amount');
        $card_type = $request->post('card_type');
        $currency = $request->post('currency');
        $card_no = $request->post('card_no');
        $bank_tran_id = $request->post('bank_tran_id');
        $card_issuer = $request->post('card_issuer');
        $card_brand = $request->post('card_brand');
        $card_issuer_country = $request->post('card_issuer_country');
        $card_issuer_country_code = $request->post('card_issuer_country_code');
        $currency_amount = $request->post('currency_amount');

        $info = (new \App\Http\Controllers\Renew\functionController())
            ->updateSSLRenew(
                $amount,
                $tran_id,
                $tran_date,
                $val_id,
                $store_amount,
                $card_type,
                $card_no,
                $currency,
                $bank_tran_id,
                $card_issuer,
                $card_brand,
                $card_issuer_country,
                $card_issuer_country_code,
                $currency_amount,
                $customer_id
            );

        if (! $info) {
            return Response::json(['message' => 'Something went wrong with the payment.'], 403);
        } else {
            $customer = CustomerInfo::where('customer_id', $info->customer_id)->first();
            $month_txt = $customer->month > 1 ? ' months' : ' month';
            $msg = 'Congratulations! Your payment for '.$customer->month.$month_txt.
                ' membership has been successful and it will expire on '.
                date('M d, Y', strtotime($customer->expiry_date)).'.';

            return Response::json(['result' => $msg], 200);
        }
    }
}
