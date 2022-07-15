<?php

namespace App\Http\Controllers\donate;

use App\Donation;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\DonationType;
use App\Http\Controllers\Enum\ssl_validation_type;
use App\Http\Controllers\Enum\SSLPaymentType;
use App\Http\Controllers\functionController2;
use Illuminate\Http\Request;

class donationController extends Controller
{
    public function index()
    {
        $title = 'Donation | royaltybd.com';
        $donations = Donation::where('status', 1)->orderBy('id', 'DESC')->get();

        return view('donation.index', compact('title', 'donations'));
    }

    public function saveDonation(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'phone' => 'required',
            'email' => 'required|email',
            'amount' => 'required|numeric',
        ]);
        $request->flashOnly(['name', 'phone', 'email', 'amount']);

        $name = $request->get('name');
        $phone = $request->get('phone');
        $email = $request->get('email');
        $amount = $request->get('amount');

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

        $random_text = 'DONATION'.$random_text;
        $tran_id_exists = Donation::where('tran_id', $random_text)->count();
        //regenerate tran id if already exists
        if ($tran_id_exists > 0) {
            goto A;
        }

        $donation = new Donation();
        $donation->name = $name;
        $donation->email = $email;
        $donation->phone = $phone;
        $donation->tran_id = $random_text;
        $donation->tran_date = date('Y-m-d H:i:s');
        $donation->amount = $amount;
        $donation->status = ssl_validation_type::not_valid;
        $donation->donation_type = DonationType::CORONA;
        $donation->save();

        $post_data = [];

        $post_data['store_id'] = env('SSL_STORE_ID');
        $post_data['store_passwd'] = env('SSL_STORE_PASS');

        $post_data['total_amount'] = $amount;
        $post_data['currency'] = 'BDT';
        $post_data['tran_id'] = $random_text;
        $post_data['success_url'] = url('/donation_success?_token='.csrf_token());
        $post_data['fail_url'] = url('/donation_fail?_token='.csrf_token());
        $post_data['cancel_url'] = url('/donation_cancel?_token='.csrf_token());

        //==========================================================================================
        // CUSTOMER INFORMATION (mandatory)
        $post_data['cus_name'] = $name;
        $post_data['cus_email'] = $email;
        $post_data['cus_phone'] = $phone;

        //SHIPMENT INFORMATION
        $post_data['ship_name'] = 'Store Test';
        $post_data['ship_add1 '] = 'Dhaka';
        $post_data['ship_add2'] = 'Dhaka';
        $post_data['ship_city'] = 'Dhaka';
        $post_data['ship_state'] = 'Dhaka';
        $post_data['ship_postcode'] = '1000';
        $post_data['ship_country'] = 'Bangladesh';

        // OPTIONAL PARAMETERS
        $post_data['value_a'] = SSLPaymentType::DONATION;
        // $post_data['value_b '] = $post_data['cus_email'];
        // $post_data['value_c'] = $post_data['cus_phone'];
        // $post_data['value_d'] = "ROYALTYBD";

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

    public function sendDonationEmail($email)
    {
        $subject = 'Thank you for your donation.';

        try {
            $mg = (new functionController2())->getMailGun();
            $mg->messages()->send('mail.royaltybd.com', [
                'from' => 'Royalty no-reply@royaltybd.com',
                'to' => $email,
                'subject' => $subject,
                'html' => view('emails.thankyou-donation')->render(),
            ]);
        } catch (\Exception $exception) {
            //
        }
    }
}
