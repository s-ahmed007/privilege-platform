<?php

namespace App\Http\Controllers;

session_start();

use App\AllAmounts;
use App\CardDelivery;
use App\CardPrice;
use App\CardPromoCodes;
use App\CardPromoCodeUsage;
use App\CardSellerInfo;
use App\CustomerInfo;
use App\CustomerNotification;
use App\CustomerPoint;
use App\Donation;
use App\Events\refer_bonus;
use App\Http\Controllers\donate\donationController;
use App\Http\Controllers\Enum\CustomerType;
use App\Http\Controllers\Enum\DeliveryType;
use App\Http\Controllers\Enum\InfluencerPercentage;
use App\Http\Controllers\Enum\MembershipPriceType;
use App\Http\Controllers\Enum\notificationType;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\Enum\PointType;
use App\Http\Controllers\Enum\SellerCommissionType;
use App\Http\Controllers\Enum\ssl_validation_type;
use App\Http\Controllers\Enum\SSLPaymentType;
use App\Http\Controllers\Renew\apiController;
use App\Http\Controllers\Voucher\functionController as voucherFunctionController;
use App\InfluencerPayment;
use App\InfoAtBuyCard;
use App\SellerBalance;
use App\SslTransactionTable;
use Datetime;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\ViewErrorBag;
use Response;
use Session;
use SMTPValidateEmail\Validator as SmtpEmailValidator;
use View;

class paymentController extends Controller
{
    //function to do payment with sslcommerze
    public function transaction(Request $request)
    {
        $this->validate($request, [
            'card_promo' => 'nullable|exists:card_promo,code',
        ]);
        $request->flashOnly('card_promo');

        //        $referrer = $request->get('refer') != null ? $request->get('refer') : '0';
        $card_promo = $request->get('card_promo') != null ? $request->get('card_promo') : '0';
        $card_promo_exists = CardPromoCodes::where('code', $card_promo)->first();
        if ($card_promo_exists) {
            $total_card_promo_used = CardPromoCodeUsage::where('promo_id', $card_promo_exists->id)->count();
        }
        //        $self_refer_code = DB::table('customer_info')
        //            ->select('referral_number')
        //            ->where('customer_id', Session::get('customer_id'))
        //            ->orWhere('customer_id', Session::get('cus_id_buy_card'))
        //            ->first();
        //user can't send his/her own refer code
        //        if ($referrer == $self_refer_code->referral_number) {
        //            return redirect()->back()->with('own_refer_code', 'You can not enter your own refer code');
        //        }
        //get customer info from customer id
        $customer_info = CustomerInfo::where('customer_id', Session::get('customer_id'))
            ->orWhere('customer_id', Session::get('cus_id_buy_card'))
            ->with('account')->first();
        $delivery_type = DeliveryType::home_delivery;

        //payable amount
        $month = $request->get('subscription');
        $taka = $request->get('subscription_price');
        $check_price = CardPrice::where([['platform', PlatformType::web], ['type', MembershipPriceType::buy]])->where('month', $month)->first();

        if ($taka != $check_price->price) {
            return redirect()->back();
        }
        //card promo code checking
        $today = date('Y-m-d');
        if ($card_promo_exists && $card_promo_exists->active == 1 && $card_promo_exists->expiry_date >= $today &&
            ($card_promo_exists->usage == 'unlimited' || $card_promo_exists->usage > $total_card_promo_used)) {
            if ($card_promo_exists->type == 1) {
                $taka = round($taka - $card_promo_exists->flat_rate);
            } elseif ($card_promo_exists->type == 2) {
                $promo_discount = round(($taka * $card_promo_exists->percentage) / 100);
                $taka -= $promo_discount;
            }
            $card_promo_id = $card_promo_exists->id;
        } else {
            $card_promo_id = 0;
        }

        A: //come back to regenerate tran id again if exists
        //generate random text for transaction id
        $random_text = '';
        $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
        $codeAlphabet .= '0123456789';
        $max = strlen($codeAlphabet); // edited
        for ($i = 0; $i < 15; $i++) {
            $random_text .= $codeAlphabet[random_int(0, $max - 1)];
        }

        $random_text = 'ROYALTYBD'.$random_text;
        $tran_id_exists = DB::table('info_at_buy_card')->where('tran_id', $random_text)->count();
        //regenerate tran id if already exists
        if ($tran_id_exists > 0) {
            goto A;
        }

        $info_at_buy_card = new InfoAtBuyCard([
            'customer_id' => $customer_info->customer_id,
            'tran_id' => $random_text,
            'customer_serial_id' => $customer_info->account->customer_serial_id,
            'customer_username' => $customer_info->account->customer_username,
            'password' => 'Asdf1234',
            'moderator_status' => 2,
            'customer_first_name' => 'first name',
            'customer_last_name' => 'last name',
            'customer_full_name' => $customer_info->customer_full_name,
            'customer_email' => $customer_info->customer_email,
            'customer_dob' => $customer_info->customer_dob,
            'customer_gender' => $customer_info->customer_gender,
            'customer_contact_number' => $customer_info->customer_contact_number,
            'customer_profile_image' => $customer_info->customer_profile_image,
            'customer_type' => 2,
            'month' => $month,
            'card_active' => 0,
            'card_activation_code' => 0,
            'firebase_token' => 0,
            'expiry_date' => '1971-03-26',
            'member_since' => $customer_info->member_since,
            'referral_number' => 0,
            'delivery_type' => $delivery_type,
            'card_promo_id' => $card_promo_id,
            'order_date' => date('Y-m-d H:i:s'),
            'paid_amount' => $taka,
            'platform' => PlatformType::web,
        ]);
        $info_at_buy_card->save();

        //insert info into ssl transaction table
        DB::table('ssl_transaction_table')->insert([
            'customer_id' => $customer_info->customer_id,
            'status' => ssl_validation_type::not_valid,
            'tran_date' => date('Y-m-d H:i:s'),
            'tran_id' => $random_text,
            'amount' => $taka,
            'platform' => PlatformType::web,

        ]);

        (new \App\Http\Controllers\AdminNotification\functionController())->buyCardAttemptNotification($info_at_buy_card);

        $post_data = [];
        $post_data['store_id'] = env('SSL_STORE_ID');
        $post_data['store_passwd'] = env('SSL_STORE_PASS');

        $post_data['total_amount'] = $taka;
        $post_data['currency'] = 'BDT';
        $post_data['tran_id'] = $random_text;
        $post_data['success_url'] = url('/payment_success?_token='.csrf_token());
        $post_data['fail_url'] = url('/payment_fail?_token='.csrf_token());
        $post_data['cancel_url'] = url('/payment_cancel?_token='.csrf_token());

        //==========================================================================================
        // CUSTOMER INFORMATION (mandatory)
        $post_data['cus_name'] = $customer_info->customer_full_name;
        $post_data['cus_email'] = $customer_info->customer_email;
        $post_data['cus_phone'] = $customer_info->customer_contact_number;

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
        // return view('/test_success', )
    }

    //function to hash validate
    public function _ipn_hash_varify($store_passwd)
    {
        if (isset($_POST) && isset($_POST['verify_sign']) && isset($_POST['verify_key'])) {
            // NEW ARRAY DECLARED TO TAKE VALUE OF ALL POST

            $pre_define_key = explode(',', $_POST['verify_key']);

            $new_data = [];
            if (! empty($pre_define_key)) {
                foreach ($pre_define_key as $value) {
                    if (isset($_POST[$value])) {
                        $new_data[$value] = ($_POST[$value]);
                    }
                }
            }
            // ADD MD5 OF STORE PASSWORD
            $new_data['store_passwd'] = md5($store_passwd);

            // SORT THE KEY AS BEFORE
            ksort($new_data);

            $hash_string = '';
            foreach ($new_data as $key => $value) {
                $hash_string .= $key.'='.($value).'&';
            }
            $hash_string = rtrim($hash_string, '&');

            if (md5($hash_string) == $_POST['verify_sign']) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    //function to check payment & update database(IPN listener)
    public function paymentCheck()
    {
        $status = $_POST['status'];
        $tran_id = $_POST['tran_id'];
        $tran_date = $_POST['tran_date'];
        $val_id = $_POST['val_id'];
        $store_amount = empty($_POST['store_amount']) ? 0 : $_POST['store_amount'];
        $card_type = $_POST['card_type'];
        $card_no = $_POST['card_no'];
        $currency = $_POST['currency'];
        $bank_tran_id = $_POST['bank_tran_id'];
        $card_issuer = $_POST['card_issuer'];
        $card_brand = $_POST['card_brand'];
        $card_issuer_country = $_POST['card_issuer_country'];
        $card_issuer_country_code = $_POST['card_issuer_country_code'];
        $amount = $_POST['amount'];
        $currency_amount = $_POST['currency_amount'];
        $verify_sign = $_POST['verify_sign'];
        $verify_key = $_POST['verify_key'];
        $value_a = $_POST['value_a'];

        if ($status == 'VALID' || $status == 'VALIDATE') {
            $result = 'NO Result';
            $domain = url('');

            $ssl_store_id = env('SSL_STORE_ID');
            $ssl_store_pass = env('SSL_STORE_PASS');

            if ($this->_ipn_hash_varify($ssl_store_pass)) {
                $result = 'Hash validation success.';

                $val_id = urlencode($_POST['val_id']);
                $store_id = urlencode($ssl_store_id);
                $store_passwd = urlencode($ssl_store_pass);

                if (env('RBD_SERVER') == 'PRODUCTION') {
                    $requested_url = ("https://securepay.sslcommerz.com/validator/api/validationserverAPI.php?val_id=" . $val_id . "&store_id=" . $store_id . "&store_passwd=" . $store_passwd . "&v=1&format=json");//live url
                } else {
                    $requested_url = ("https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php?val_id=".$val_id."&store_id=".$store_id."&store_passwd=".$store_passwd."&v=1&format=json");//sandbox url
                }
                $handle = curl_init();
                curl_setopt($handle, CURLOPT_URL, $requested_url);
                curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false); // IF YOU RUN FROM LOCAL PC
                curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false); // IF YOU RUN FROM LOCAL PC

                $result = curl_exec($handle);

                $code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

                if ($code == 200 && ! (curl_errno($handle))) {
                    $result = json_decode($result, true);
                    $status = $result['status'];
                    $tran_date = $result['tran_date'];
                    $tran_id = $result['tran_id'];
                    $val_id = $result['val_id'];
                    $amount = $result['amount'];
                    $store_amount = $result['store_amount'];
                    $card_type = $result['card_type'];
                    $card_no = $result['card_no'];
                    $currency = $result['currency'];
                    $bank_tran_id = $result['bank_tran_id'];
                    $card_issuer = $result['card_issuer'];
                    $card_brand = $result['card_brand'];
                    $card_issuer_country = $result['card_issuer_country'];
                    $card_issuer_country_code = $result['card_issuer_country_code'];
                    $currency_amount = $result['currency_amount'];
                    $value_a = $result['value_a'];

                    $return_status = 'false';
                    if ($value_a == SSLPaymentType::DONATION) {
                        //update donation status
                        $donation = Donation::where('tran_id', $tran_id)->where('amount', $amount)->first();
                        $donation->status = ssl_validation_type::valid;
                        $donation->tran_date = $tran_date;
                        $donation->store_amount = $store_amount;
                        $donation->val_id = $val_id;
                        $donation->card_type = $card_type;
                        $donation->card_no = $card_no;
                        $donation->currency = $currency;
                        $donation->bank_tran_id = $bank_tran_id;
                        $donation->card_issuer = $card_issuer;
                        $donation->card_brand = $card_brand;
                        $donation->card_issuer_country = $card_issuer_country;
                        $donation->card_issuer_country_code = $card_issuer_country_code;
                        $donation->currency_amount = $currency_amount;
                        $donation->save();

                        (new \App\Http\Controllers\AdminNotification\functionController())->newDonationNotification($donation);

                        (new donationController())->sendDonationEmail($donation->email);

                        return $return_status;
                    } elseif ($value_a == SSLPaymentType::VOUCHER) {
                        (new voucherFunctionController())->saveVoucherPurchasedInfo($tran_id, $amount, $tran_date, $store_amount, $val_id, $card_type, $card_no, $currency, $bank_tran_id, $card_issuer, $card_brand, $card_issuer_country, $card_issuer_country_code, $currency_amount);

                        return $return_status;
                    }
                    //get temporarily saved info at payment time
                    $temporary_info = DB::table('info_at_buy_card')->where('tran_id', $tran_id)->first();

                    if ($temporary_info) {
                        if ($temporary_info->customer_first_name == 'RENEW') {
                            (new \App\Http\Controllers\Renew\functionController())->updateSSLRenew($amount, $tran_id, $tran_date,
                                $val_id, $store_amount, $card_type, $card_no, $currency, $bank_tran_id, $card_issuer, $card_brand,
                                $card_issuer_country, $card_issuer_country_code, $currency_amount, $temporary_info->customer_id);
                        } else {
                            try {
                                DB::beginTransaction(); //to do query rollback

                                DB::table('ssl_transaction_table')
                                    ->where('tran_id', $tran_id)
                                    ->where('amount', $amount)
                                    ->update([
                                        'status' => ssl_validation_type::valid,
                                        'tran_date' => date('Y-m-d H:i:s'),
                                        'val_id' => $val_id,
                                        'store_amount' => $store_amount,
                                        'card_type' => $card_type,
                                        'card_no' => $card_no,
                                        'currency' => $currency,
                                        'bank_tran_id' => $bank_tran_id,
                                        'card_issuer' => $card_issuer,
                                        'card_brand' => $card_brand,
                                        'card_issuer_country' => $card_issuer_country,
                                        'card_issuer_country_code' => $card_issuer_country_code,
                                        'currency_amount' => $currency_amount,
                                        'month' => $temporary_info->month,
                                    ]);

                                $customer_data = DB::table('customer_info as ci')
                                    ->join('user_type as ut', 'ut.id', '=', 'ci.customer_type')
                                    ->select('ci.*', 'ut.type')
                                    ->where('ci.customer_id', $temporary_info->customer_id)
                                    ->get();
                                $customer_data = json_decode(json_encode($customer_data), true);
                                $customer_data = $customer_data[0];
                                //check customers extra remaining days for the card
                                $curDate = date('Y-m-d');
                                $exp_date = $customer_data['expiry_date'];

                                $cur_date = new DateTime($curDate);
                                $expiry_date = new DateTime($exp_date);
                                $interval = date_diff($cur_date, $expiry_date);
                                $daysRemaining = $interval->format('%R%a');
                                //get date after months
                                $date = date_create(date('Y-m-d'));
                                $expiry_date = date_add($date, date_interval_create_from_date_string($temporary_info->month.' month'));
                                if ($daysRemaining > 0) {
                                    $expiry_date = date_add($date, date_interval_create_from_date_string($daysRemaining.' days'));
                                }
                                $expiry_date = $expiry_date->format('Y-m-d');
                                //update data in customer_info table
                                DB::table('customer_info')
                                    ->where('customer_id', $temporary_info->customer_id)
                                    ->update([
                                        'customer_type' => $temporary_info->customer_type,
                                        'month' => $temporary_info->month,
                                        'expiry_date' => $expiry_date,
                                        'card_active' => 2,
                                        'delivery_status' => 1,
                                        'approve_date' => date('Y-m-d H:i:s'),
                                    ]);
                                $ssl_id = DB::table('ssl_transaction_table')
                                    ->select('id')
                                    ->where('tran_id', $tran_id)
                                    ->first();
                                //save delivery address
                                $card_delivery = CardDelivery::where('ssl_id', $ssl_id->id)->first();
                                if (! $card_delivery) {
                                    DB::table('card_delivery')->insert([
                                        'customer_id' => $temporary_info->customer_id,
                                        'delivery_type' => $temporary_info->delivery_type,
                                        'shipping_address' => $temporary_info->shipping_address,
                                        'order_date' => $temporary_info->order_date,
                                        'paid_amount' => $temporary_info->paid_amount,
                                        'ssl_id' => $ssl_id->id,
                                    ]);
                                }
                                //save card promo usage data if exists
                                if ($temporary_info->card_promo_id != 0) {
                                    $ssl_id = DB::table('ssl_transaction_table')
                                        ->select('id')
                                        ->where('tran_id', $tran_id)
                                        ->first();
                                    CardPromoCodeUsage::insert([
                                        'customer_id' => $temporary_info->customer_id,
                                        'promo_id' => $temporary_info->card_promo_id,
                                        'ssl_id' => $ssl_id->id,
                                    ]);
                                    //update influencer payment info if this promo belongs to anyone
                                    (new functionController)->updateInfluencerPaymentInfo($temporary_info->card_promo_id, $amount);
                                }

                                //sales
                                $seller_info = CardSellerInfo::where('promo_ids', 'like', "%\"{$temporary_info->card_promo_id}\"%")->first();
                                if ($seller_info) {
                                    $seller_balance = SellerBalance::where('seller_id', $seller_info->id)->first();
                                    $commission = $seller_info->commission;
                                } else {
                                    $seller_balance = null;
                                    $commission = null;
                                }
                                $all_amount = AllAmounts::all();
                                $per_card_sell = $all_amount[11]['price'];
                                //make history
                                if ($seller_info) {
                                    (new functionController2())->addToCustomerHistory($temporary_info->customer_id, $seller_info->seller_account_id,
                                        CustomerType::card_holder, $ssl_id->id, $temporary_info->card_promo_id);
                                    if ($commission) {
                                        //get main price to calculate commission
                                        $main_price = CardPrice::where('platform', PlatformType::web)
                                            ->where('type', MembershipPriceType::buy)
                                            ->where('month', $temporary_info->month)
                                            ->first();
                                        //update seller balance
                                        $commission_received = (new JsonSalesController())->updateSellerBalance($main_price->price, $commission,
                                            $seller_balance, $per_card_sell, $temporary_info->month, false);
                                        //send sms to seller
                                        (new JsonSalesController())->sendSellerBalanceSMS($seller_info->id, $seller_info->account->phone,
                                            $main_price->price, $commission, $temporary_info->month, $temporary_info->customer_full_name);
                                        //save seller commission history
                                        (new JsonSalesController())->saveSellerCommissionHistory($seller_info->id, $ssl_id->id,
                                            $commission_received, SellerCommissionType::ONLINE_PAY);
                                    }
                                } else {
                                    (new functionController2())->addToCustomerHistory($temporary_info->customer_id, null,
                                        CustomerType::card_holder, $ssl_id->id, $temporary_info->card_promo_id);
                                }

                                DB::table('info_at_buy_card')->where('customer_id', $temporary_info->customer_id)->delete();

                                $validity = $temporary_info->month == 12 ? 'one year' : $temporary_info->month.' months';
                                (new adminController)->OnlinePaymentMail($temporary_info->customer_full_name, $temporary_info->customer_email,
                                    $temporary_info, $validity);
                                (new \App\Http\Controllers\AdminNotification\functionController())->membershipPurchaseNotification($temporary_info);

                                DB::commit(); //to do query rollback
                            } catch (\Exception $e) {
                                DB::rollBack(); //rollback all successfully executed queries
                            }
                        }
                    }
                } else {
                    DB::table('ssl_transaction_table')
                        ->where('tran_id', $tran_id)
                        ->where('amount', $amount)
                        ->update([
                            'status' => ssl_validation_type::not_valid,
                            'tran_date' => date('Y-m-d H:i:s'),
                            'val_id' => $val_id,
                            'store_amount' => $store_amount,
                            'card_type' => $card_type,
                            'card_no' => $card_no,
                            'currency' => $currency,
                            'bank_tran_id' => $bank_tran_id,
                            'card_issuer' => $card_issuer,
                            'card_brand' => $card_brand,
                            'card_issuer_country' => $card_issuer_country,
                            'card_issuer_country_code' => $card_issuer_country_code,
                            'currency_amount' => $currency_amount,
                        ]);
                }
            } else {
                $result = 'Hash validation failed.';
            }
        } else {
            DB::table('ssl_transaction_table')
                ->where('tran_id', $tran_id)
                ->where('amount', $amount)
                ->update([
                    'status' => ssl_validation_type::not_valid,
                    'tran_date' => date('Y-m-d H:i:s'),
                    'val_id' => $val_id,
                    'store_amount' => $store_amount,
                    'card_type' => $card_type,
                    'card_no' => $card_no,
                    'currency' => $currency,
                    'bank_tran_id' => $bank_tran_id,
                    'card_issuer' => $card_issuer,
                    'card_brand' => $card_brand,
                    'card_issuer_country' => $card_issuer_country,
                    'card_issuer_country_code' => $card_issuer_country_code,
                    'currency_amount' => $currency_amount,
                ]);
        }
    }

    //function to store member info from spot purchase
    public function spotPurchaseFromUser(Request $request)
    {
        $amount = $request->post('price');
        $customer_id = $request->post('customer');
        $month = $request->post('month');
        $promo_id = $request->post('promo');
        $seller_id = $request->post('seller');
        $purchased = (new JsonSalesController())->storeSpotPurchaseFromUser($promo_id, $amount, $customer_id, $month, $seller_id, PlatformType::web);
        if ($purchased) {
            return \redirect('spot_purchase_success');
        } else {
            dd('please try again');
        }
    }

    public function spotPurchaseFromUserSuccess()
    {
        //check if renewing
        $customer = CustomerInfo::where('customer_id', session('customer_id'))->first();
        //update exp date & status
        $exp_status = (new functionController2)->getExpStatusOfCustomer($customer->expiry_date);
        session(['expiry_date' => $customer->expiry_date]);
        session(['expiry_status' => $exp_status]);

        //get username
        $username = DB::table('customer_account')->where('customer_id', $customer->customer_id)->first();
        $message = 'Please pay the amount to sales agent.';
        //redirect
        return view('payment-success', compact('username', 'message'));
    }

    public function orderSuccess()
    {
        if (Session::has('customer_id')) {
            $customer_id = Session::get('customer_id');
            $show_username = 0;
        } elseif (Session::has('cus_id_buy_card')) {
            $customer_id = Session::get('cus_id_buy_card');
            $show_username = 1;
        } else {
            $customer_id = '';
            $show_username = 0;
        }
        //get username
        $username = DB::table('customer_account')->where('customer_id', $customer_id)->first();
        //session to access buy card page with back button pressed
        session(['restrict_access_buy_card_page' => true]);
        //redirect
        return view('order-succeeded', compact('username', 'show_username'));
    }

    //function for insert all data after payment is cleared
    public function paymentSucceed()
    {
        if (Session::has('customer_id')) {
            $customer_id = Session::get('customer_id');
            $show_username = 0;
        } elseif (Session::has('cus_id_buy_card')) {
            $customer_id = Session::get('cus_id_buy_card');
            $show_username = 1;
        } else {
            $customer_id = '';
            $show_username = 0;
        }
        //check if renewing
        $customer = CardDelivery::where('customer_id', session('customer_id'))
            ->with('info.account')
            ->first();
        if ($customer && $customer->delivery_type == 12) {
            $username = $customer->info->account->customer_username;

            //update exp date & status
            $exp_status = (new functionController2)->getExpStatusOfCustomer($customer->info->expiry_date);
            session(['expiry_date' => $customer->info->expiry_date]);
            session(['expiry_status' => $exp_status]);

            return view('renew.success', compact('username'));
        }
        //get username
        $username = DB::table('customer_account')->where('customer_id', $customer_id)->first();
        $title = 'Order-Success';
        $message = 'We have accepted your payment. Please check your email for a confirmation message.';
        //redirect
        return view('payment-success', compact('username', 'show_username', 'title', 'message'));
    }

    //function to show payment failure page
    public function paymentFail(Request $request)
    {
        $tran_id = $_POST['tran_id'];
        DB::table('info_at_buy_card')->where('tran_id', $tran_id)->delete();
        //get username
        $username = DB::table('customer_account')->where('customer_id', Session::get('customer_id'))->first();

        return view('payment_fail', compact('username'));
    }

    public function paymentCancel(Request $request)
    {
        $tran_id = $_POST['tran_id'];
        DB::table('info_at_buy_card')->where('tran_id', $tran_id)->delete();
        //get username
        $username = DB::table('customer_account')->where('customer_id', Session::get('customer_id'))->first();

        return view('payment_cancel', compact('username'));
    }
}//controller ends
