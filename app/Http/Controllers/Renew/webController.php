<?php

namespace App\Http\Controllers\Renew;

use App\AllAmounts;
use App\CardDelivery;
use App\CardPrice;
use App\CardPromoCodes;
use App\CardPromoCodeUsage;
use App\CustomerAccount;
use App\CustomerInfo;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\DeliveryType;
use App\Http\Controllers\Enum\MembershipPriceType;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\functionController2;
use App\Http\Controllers\JsonControllerV2;
use App\InfoAtBuyCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class webController extends Controller
{
    public function sslConfig($customer, $price, $tran_id)
    {
        $post_data = [];

        $post_data['store_id'] = env('SSL_STORE_ID');
        $post_data['store_passwd'] = env('SSL_STORE_PASS');

        $post_data['total_amount'] = $price;
        $post_data['currency'] = 'BDT';
        $post_data['tran_id'] = $tran_id;
        $post_data['success_url'] = url('/payment_success?_token='.csrf_token());
        $post_data['fail_url'] = url('/renew_fail?_token='.csrf_token());
        $post_data['cancel_url'] = url('/renew_cancel?_token='.csrf_token());

        //==========================================================================================
        // CUSTOMER INFORMATION (mandatory)
        $post_data['cus_name'] = $customer->info->customer_full_name;
        $post_data['cus_email'] = $customer->info->customer_email;
        $post_data['cus_phone'] = $customer->info->customer_contact_number;
        //SHIPMENT INFORMATION
        $post_data['ship_name'] = 'Store Test';
        $post_data['ship_add1 '] = 'Dhaka';
        $post_data['ship_add2'] = 'Dhaka';
        $post_data['ship_city'] = 'Dhaka';
        $post_data['ship_state'] = 'Dhaka';
        $post_data['ship_postcode'] = '1000';
        $post_data['ship_country'] = 'Bangladesh';

        // OPTIONAL PARAMETERS
        $post_data['value_a'] = $post_data['cus_name'];
        $post_data['value_b '] = $post_data['cus_email'];
        $post_data['value_c'] = $post_data['cus_phone'];
        $post_data['value_d'] = 'ROYALTYBD';

        // REQUEST SEND TO SSLCOMMERZ
        if (env('RBD_SERVER') == 'PRODUCTION') {
            $direct_api_url = 'https://securepay.sslcommerz.com/gwprocess/v3/api.php'; //live url
        } else {
            $direct_api_url = 'https://sandbox.sslcommerz.com/gwprocess/v3/api.php'; //sandbox url
        }

        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $direct_api_url);
        curl_setopt($handle, CURLOPT_TIMEOUT, 30);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($handle, CURLOPT_POST, 1);
        curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false); // KEEP IT FALSE IF YOU RUN FROM LOCAL PC

        $content = curl_exec($handle);

        $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        if ($code == 200 && ! (curl_errno($handle))) {
            curl_close($handle);
            $sslcommerzResponse = $content;
        } else {
            curl_close($handle);
            echo 'FAILED TO CONNECT WITH SSLCOMMERZ API';
            exit;
        }

        // PARSE THE JSON RESPONSE
        $sslcz = json_decode($sslcommerzResponse, true);

        if (isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL'] != '') {
            // THERE ARE MANY WAYS TO REDIRECT - Javascript, Meta Tag or Php Header Redirect or Other
            // echo "<script>window.location.href = '". $sslcz['GatewayPageURL'] ."';</script>";
            echo "<meta http-equiv='refresh' content='0;url=".$sslcz['GatewayPageURL']."'>";
            // header("Location: ". $sslcz['GatewayPageURL']);
            exit;
        } else {
            echo 'JSON Data parsing error!';
        }

        if (isset($sslcz['status']) && $sslcz['status'] == 'SUCCESS') {
            // VISA GATEWAY
            if (isset($sslcz['gw']['visa']) && $sslcz['gw']['visa'] != '') {
                echo '<h3>VISA</h3>';
                $sslcz_visa = explode(',', $sslcz['gw']['visa']);
                foreach ($sslcz_visa as $gw_value) {
                    if ($gw_value == 'dbbl_visa') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."dbbl_visa'>DBBL VISA</a><br />";
                    }
                    if ($gw_value == 'brac_visa') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."brac_visa'>BRAC VISA</a><br />";
                    }
                    if ($gw_value == 'city_visa') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."city_visa'>CITY VISA</a><br />";
                    }
                    if ($gw_value == 'ebl_visa') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."ebl_visa'>EBL VISA</a><br />";
                    }
                    if ($gw_value == 'visacard') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."visacard'>VISA</a><br />";
                    }
                }
            } // END OF VISA

            // MASTER GATEWAY
            if (isset($sslcz['gw']['master']) && $sslcz['gw']['master'] != '') {
                echo '<h3>MASTER</h3>';
                $sslcz_visa = explode(',', $sslcz['gw']['master']);
                foreach ($sslcz_visa as $gw_value) {
                    if ($gw_value == 'dbbl_master') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."dbbl_master'>DBBL MASTER</a><br />";
                    }
                    if ($gw_value == 'brac_master') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."brac_master'>BRAC MASTER</a><br />";
                    }
                    if ($gw_value == 'city_master') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."city_master'>CITY MASTER</a><br />";
                    }
                    if ($gw_value == 'ebl_master') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."ebl_master'>EBL MASTER</a><br />";
                    }
                    if ($gw_value == 'mastercard') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."mastercard'>MASTER</a><br />";
                    }
                }
            } // END OF MASTER

            // AMEX GATEWAY
            if (isset($sslcz['gw']['amex']) && $sslcz['gw']['amex'] != '') {
                echo '<h3>AMEX</h3>';
                $sslcz_visa = explode(',', $sslcz['gw']['amex']);
                foreach ($sslcz_visa as $gw_value) {
                    if ($gw_value == 'city_amex') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."city_amex'>AMEX</a><br />";
                    }
                }
            } // END OF AMEX

            // OTHER CARDS GATEWAY
            if (isset($sslcz['gw']['othercards']) && $sslcz['gw']['othercards'] != '') {
                echo '<h3>OTHER CARDS</h3>';
                $sslcz_visa = explode(',', $sslcz['gw']['othercards']);
                foreach ($sslcz_visa as $gw_value) {
                    if ($gw_value == 'dbbl_nexus') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."dbbl_nexus'>NEXUS</a><br />";
                    }

                    if ($gw_value == 'qcash') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."qcash'>QCASH</a><br />";
                    }

                    if ($gw_value == 'fastcash') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."fastcash'>FASTCASH</a><br />";
                    }
                }
            } // END OF OTHER CARDS

            // INTERNET BANKING GATEWAY
            if (isset($sslcz['gw']['internetbanking']) && $sslcz['gw']['internetbanking'] != '') {
                echo '<h3>INTERNET BANKING</h3>';
                $sslcz_visa = explode(',', $sslcz['gw']['internetbanking']);
                foreach ($sslcz_visa as $gw_value) {
                    if ($gw_value == 'city') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."city'>CITYTOUCH</a><br />";
                    }

                    if ($gw_value == 'bankasia') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."bankasia'>BANK ASIA</a><br />";
                    }

                    if ($gw_value == 'ibbl') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."ibbl'>IBBL</a><br />";
                    }

                    if ($gw_value == 'mtbl') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."mtbl'>MTBL</a><br />";
                    }
                }
            } // END OF INTERNET BANKING

            // MOBILE BANKING GATEWAY
            if (isset($sslcz['gw']['mobilebanking']) && $sslcz['gw']['mobilebanking'] != '') {
                echo '<h3>MOBILE BANKING</h3>';
                $sslcz_visa = explode(',', $sslcz['gw']['mobilebanking']);
                foreach ($sslcz_visa as $gw_value) {
                    if ($gw_value == 'dbblmobilebanking') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."dbblmobilebanking'>DBBL MOBILE BANKING</a><br />";
                    }

                    if ($gw_value == 'bkash') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."bkash'>Bkash</a><br />";
                    }

                    if ($gw_value == 'abbank') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."abbank'>AB Direct</a><br />";
                    }

                    if ($gw_value == 'ibbl') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."ibbl'>IBBL</a><br />";
                    }

                    if ($gw_value == 'mycash') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."mycash'>MYCASH</a><br />";
                    }

                    if ($gw_value == 'ific') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."ific'>IFIC</a><br />";
                    }

                    if ($gw_value == 'ific') {
                        echo "<a href='".$sslcz['redirectGatewayURL']."ific'>IFIC</a><br />";
                    }
                }
            } // END OF MOBILE BANKING
        } else {
            echo 'Invalid Credential!';
        }
    }

    public function checkCardPrice($month, $delivery_type, $promo_code)
    {
        $months = CardPrice::where([['platform', PlatformType::web], ['type', MembershipPriceType::renew]])->pluck('month');

        if (in_array($month, $months->toArray())) {
            $price = CardPrice::where([['platform', PlatformType::web], ['type', MembershipPriceType::renew], ['month', $month]])->first()->price;

            if ($promo_code) {
                $today = date('Y-m-d');
                $code_exists = CardPromoCodes::where('code', $promo_code)->first();
                $count = CardPromoCodeUsage::where('id', $code_exists->id)->count();
                if (! $code_exists) {
                    return false;
                } elseif ($code_exists->active != 1) {
                    return false;
                } elseif ($code_exists->expiry_date < $today) {
                    return false;
                } elseif ($code_exists->usage != 'unlimited' && $code_exists->usage <= $count) {
                    return false;
                } else {
                    if ($code_exists->type == 1) {
                        $final_price = $price - $code_exists->flat_rate;
                        $result['price'] = $final_price;
                        $result['promo_id'] = $code_exists->id;

                        return $result;
                    } elseif ($code_exists->type == 2) {
                        $final_price = $price - round(($price * $code_exists->percentage) / 100);
                        $result['price'] = $final_price;
                        $result['promo_id'] = $code_exists->id;

                        return $result;
                    } else {
                        return false;
                    }
                }
            } else {
                $result['price'] = $price;
                $result['promo_id'] = null;

                return $result;
            }
        } else {
            return false;
        }
    }

    public function renewView()
    {
        $customer_data = CustomerInfo::where('customer_id', session('customer_id'))->first();
        $card_delivery = CardDelivery::where('customer_id', $customer_data->customer_id)->orderBy('id', 'DESC')->first();
        $amounts = AllAmounts::all();
        $cards = CardPrice::where([['platform', PlatformType::web], ['type', MembershipPriceType::renew]])->orderBy('price', 'ASC')->get();
        $exp_status = (new functionController2)->getExpStatusOfCustomer($customer_data->expiry_date);

        return view('renew.index', compact('amounts', 'cards', 'customer_data', 'card_delivery', 'exp_status'));
    }

    public function confirmRenew(Request $request)
    {
        $card_duration = explode('-', $request->get('card_duration'));
        $month = $card_duration[0];
        $delivery_type = $request->get('delivery_type');
        $shipping_address = $request->get('delivery_address');
        $promo_code = $request->get('card_promo');
        $result = $this->checkCardPrice($month, $delivery_type, $promo_code);
        $tran_id = (new JsonControllerV2())->getSSLTransactionId();
//        $delivery_type = $delivery_type == 11 ? 1 : 12;
        $customer = CustomerAccount::where('customer_id', session('customer_id'))->first();
        $renewInfo = (new functionController())->insertInfoRenew($customer, $tran_id, $month, $delivery_type,
            $result['promo_id'], $result['price'], PlatformType::web, false);
        if (! $renewInfo) {
            return redirect()->back()->with('error', 'Something went wrong in renewing.');
        }
        $renewSSL = (new functionController())->insertSSLRenew($customer->customer_id, $tran_id, $result['price'], PlatformType::web);
        if (! $renewSSL) {
            return redirect()->back()->with('error', 'Something went wrong in renewing.');
        }

        $this->sslConfig($customer, $result['price'], $tran_id);
    }

    public function makeTrialUser($promo_id = 0)
    {
        $customer_id = session('customer_id');
        $month = CardPrice::where([['platform', PlatformType::web], ['type', MembershipPriceType::buy], ['price', 0]])->first()->month;
        $amount = 0;
        $tran_id = (new JsonControllerV2())->getSSLTransactionId();
        $delivery_type = DeliveryType::virtual_card;
        $tran_date = date('Y-m-d H:i:s');

        $customer = CustomerAccount::where('customer_id', $customer_id)->first();
        $renewInfo = (new functionController())->insertInfoRenew($customer, $tran_id, $month, $delivery_type, $promo_id, $amount, PlatformType::web, false);
        if (! $renewInfo) {
            return redirect()->back()->with('try_again', 'Something went wrong in renewing.');
        }
        $renewSSL = (new functionController())->insertSSLRenew($customer_id, $tran_id, $amount, PlatformType::web);
        if (! $renewSSL) {
            return redirect()->back()->with('try_again', 'Something went wrong in renewing.');
        }

        $info = (new functionController())->updateSSLRenew($amount, $tran_id, $tran_date, '', '0.00', 'CASH', '', 'BDT',
            '', '', '', 'BD', '', '0.00', $customer_id);

        if (! $info) {
            return redirect()->back()->with('try_again', 'Something went wrong in renewing.');
        } else {
            InfoAtBuyCard::where('customer_id', $customer_id)->delete();
            $txt = $month > 1 ? ' months' : ' month';

            return redirect('users/'.session('customer_username'))->with('free_trial_success', 'You have successfully activated your '.$month.$txt.' free trial. Thank you for choosing Royalty.');
        }
    }
}
