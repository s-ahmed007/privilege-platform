<?php

namespace App\Http\Controllers\LoginRegister;

use App\AccountKitStats;
use App\BranchUser;
use App\CustomerAccount;
use App\CustomerInfo;
use App\Helpers\UpgradeUserToPremium;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\Constants;
use App\Http\Controllers\Enum\CredentialType;
use App\Http\Controllers\Enum\VerificationType;
use App\Http\Controllers\functionController2;
use App\Http\Controllers\jsonController;
use App\Http\Controllers\JsonControllerV2;
use App\ResetUser;
use App\Subscribers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Tymon\JWTAuth\Claims\Custom;

class functionController extends Controller
{
    public function getReferrerWithCode($code)
    {
        $info = CustomerInfo::where('referral_number', $code)->first();
        if (! $info) {
            return null;
        } else {
            return $info->customer_id;
        }
    }

    public function randomTextForLoginSession()
    {
        $random_text = '';
        $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
        $codeAlphabet .= '0123456789';
        $max = strlen($codeAlphabet); // edited
        for ($i = 0; $i < 20; $i++) {
            $random_text .= $codeAlphabet[random_int(0, $max - 1)];
        }
        $random_text = (new \App\Http\Controllers\functionController())->encrypt_decrypt('encrypt', $random_text);

        return $random_text;
    }

    public function getCustomer($phone)
    {
        $customer = CustomerInfo::where('customer_contact_number', $phone)
                                ->with('account')
                                ->with(['account', 'cardDelivery' => function ($query) {
                                    $query->orderBy('id', 'DESC')->first();
                                }])
                                ->first();

        return $customer;
    }

    public function setStatNumber($phone)
    {
        $stat = new AccountKitStats();
        $stat->phone_number = $phone;
        $stat->status = 0; // undefined state
        $stat->save();

        return $stat;
    }

    public function updateStatNumber($id, $status, $phone = null)
    {
        AccountKitStats::where('id', $id)
                       ->update(['status' => $status, 'final_phone_number' => $phone]);
        $stat = AccountKitStats::where('id', $id)->first();

        return $stat;
    }

    public function getPin($customerID)
    {
        $account = CustomerAccount::where('customer_id', $customerID)->first();

        return $account->pin;
    }

    public function setPin($customerID, $pin)
    {
        $encrypted_pin = (new \App\Http\Controllers\functionController)->encrypt_decrypt('encrypt', $pin);
        CustomerAccount::where('customer_id', $customerID)
                       ->update(['pin' => $encrypted_pin]);

        return $encrypted_pin;
    }

    public function generate_customer_id()
    {
        A:
        $customer_id_10 = mt_rand(1000000000, mt_getrandmax());
        //get serial number of last user
        $id = CustomerAccount::orderBy('customer_serial_id', 'DESC')->take(1)->select('customer_serial_id')->first();

        if (! empty($id)) {
            $id = $id->customer_serial_id;
        } else {
            $id = '000000';
        }
        //generate 6 digit ID for new user
        for ($i = $id + 1; $i <= 999999; $i++) {
            $customer_id_6 = sprintf('%06d', $i);
            break;
        }
        $customer_id = $customer_id_10.$customer_id_6;
        $user_exists = CustomerAccount::where('customer_id', $customer_id)->count();
        //regenerate customer id if already exists
        if ($user_exists > 0) {
            goto A;
        }

        return response()->json(['id' => $customer_id, 'serial' => $customer_id_6]);
    }

    public function generate_refer_code()
    {
        A: //come back to regenerate refer code again if exists
        $token = '';
        $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
        $codeAlphabet .= '0123456789';
        $max = strlen($codeAlphabet); // edited
        for ($i = 0; $i < 5; $i++) {
            $token .= $codeAlphabet[random_int(0, $max - 1)];
        }
        $refer_code = CustomerInfo::where('referral_number', $token)->count();
        //regenerate refer code if already exists
        if ($refer_code > 0) {
            goto A;
        }

        return $token;
    }

    public function register($name, $email, $phone, $pin, $platform, $referrer_id = null)
    {
        $username = (new JsonControllerV2())->createUserName($name, $email);
        $profile_image = 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/registration/user.png';
//        $encrypted_pin = (new \App\Http\Controllers\functionController)->encrypt_decrypt('encrypt', $pin);
        $encrypted_pin = null;
        $genCustomerID = $this->generate_customer_id();
        $main_customer_id = $genCustomerID->getData()->id;
        $refer_code = $this->generate_refer_code();
        $account = new CustomerAccount([
            'customer_id' => $main_customer_id,
            'customer_serial_id' => $genCustomerID->getData()->serial,
            'customer_username' => $username,
            'pin' => $encrypted_pin,
            'moderator_status' => 2, // profile active
            'platform' => $platform, // web/android/ios
        ]);
        $account->save();
        //save data in customer_info table
        $info = new CustomerInfo([
            'customer_id' => $main_customer_id,
            'customer_full_name' => $name,
            'customer_email' => $email,
            'customer_contact_number' => $phone,
            'customer_profile_image' => $profile_image,
            'customer_type' => 3, // general customer
            'month' => 0,
            'card_active' => 0, // card not active
            'card_activation_code' => 0,
            'expiry_date' => '1971-03-26',
            'member_since' => date('Y-m-d H:i:s'),
            'firebase_token' => 0,
            'referral_number' => $refer_code,
            'referrer_id' => $referrer_id,
        ]);
        $info->save();

        $subscribe = new Subscribers(['email' => $email]);
        $subscribe->save();
        if ($referrer_id) {
            $ref_info = CustomerInfo::where('customer_id', $referrer_id)->first();
            $ref_info->increment('reference_used', 1);
            $refer_value = (new \App\Http\Controllers\functionController)->referValue();
            if (Constants::refer_transaction_count > 1) {
                $offer_text = 'offers';
            } else {
                $offer_text = 'offer';
            }
            $refer_user_notification_text = 'You have used '.$ref_info->customer_full_name."'s referral code. Avail ".
                                            Constants::refer_transaction_count.' '.$offer_text.' to earn '.$refer_value.' Referral Credits to redeem rewards.';
            $referrar_notification_text = $info->customer_full_name.' has joined Royalty. You will earn '.$refer_value.' Referral Credits when your friend avails '.Constants::refer_transaction_count.' '.$offer_text.'.';
            (new \App\Http\Controllers\TransactionRequest\functionController())->sendReferNotification($info, $ref_info, $referrar_notification_text, $refer_user_notification_text);
        }
        (new \App\Http\Controllers\AdminNotification\functionController())->newUserRegistration($info, $platform);

        //making user premium
        (new UpgradeUserToPremium())->convertGuestToPremium($info->customer_id, $platform);//turned off paid membership

        return $this->getCustomer($phone);
    }

    public function matchCustomer($id, $type, $value)
    {
        $encrypted_value = (new \App\Http\Controllers\functionController)->encrypt_decrypt('encrypt', $value);
        if ($type == CredentialType::PWD) {
            $account = CustomerAccount::where('password', $encrypted_value)->where('customer_id', $id)->first();
            if ($account) {
                return true;
            } else {
                return false;
            }
        } elseif ($type == CredentialType::PIN) {
            $account = CustomerAccount::where('pin', $encrypted_value)->where('customer_id', $id)->first();
            if ($account) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function send_reset_pin_email($email)
    {
        //check if user with this email exists or not
        $user = DB::table('customer_info as ci')
                  ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
                  ->select('ci.customer_id', 'ci.customer_full_name')
                  ->where('ci.customer_email', $email)
                  ->first();

        if (count((array) $user) > 0) {
            $user_id = $user->customer_id;
            $name = $user->customer_full_name;
            //generate reset token
            $reset_token = sha1(mt_rand(1, 90000).$user_id);

            //generate reset table
            DB::table('reset_user')->insert(
                [
                    'customer_id' => $user_id,
                    'token' => $reset_token,
                ]
            );
            //send mail
            $to = $email;
            $subject = 'Forgot pin';
            $message_text = 'Hello '.$name.','.'<br><br>';
            $message_text .= 'To reset your PIN please click on the button below.'.'<br>'.'<br>';
            $message_text .= '<a style="color: #fff;background-color: #007bff;padding: 8px;border-radius: .25rem;text-decoration: none;border: 1px solid transparent;" 
                        href="'.url('/reset/'.$reset_token).'">Reset Pin</a>'.'<br>'.'<br>';
            $message_text .= 'Thank you'.'<br>'.'<br>';
            $message_text .= 'Royalty';

            //using zoho mail service
            $smtpAddress = 'smtp.zoho.com';
            $port = 465;
            $encryption = 'ssl';
            $yourEmail = 'support@royaltybd.com';
            $yourPassword = 'SUp963**';

            // Prepare transport
            $transport = new Swift_SmtpTransport($smtpAddress, $port, $encryption);
            $transport->setUsername($yourEmail);
            $transport->setPassword($yourPassword);
            $mailer = new Swift_Mailer($transport);

            $message = new Swift_Message($subject);
            $message->setFrom(['support@royaltybd.com' => 'Royalty']);
            $message->setTo([$to => $name]);
            // If you want plain text instead, remove the second paramter of setBody
            $message->setBody($message_text, 'text/html');

            if ($mailer->send($message)) {
                return 'We have sent you an E-mail with a link to reset your PIN. This may take a minute and also don’t forget to check the spam folder.';
            } else {
                return 'Internal Server Error';
            }
        } else {
            return 'Email does not exist!';
        }
    }

	public function send_reset_pin_sms($phone)
	{
		//check if user with this email exists or not
		$user = DB::table('customer_info as ci')
		          ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
		          ->select('ci.customer_id', 'ci.customer_full_name')
		          ->where('ci.customer_contact_number', $phone)
		          ->first();

		if (count((array) $user) > 0) {
			$user_id = $user->customer_id;
//            $name = $user->customer_full_name;

			//            //generate 6 digit code
			//            $code = "";
			//            $codeAlphabet = "0123456789";
			//            $max = strlen($codeAlphabet); // edited
			//            for ($i = 0; $i < 6; $i++) {
			//                $code .= $codeAlphabet[random_int(0, $max - 1)];
			//            }
			//
			//            $text_message = "Use ".$code." as your PIN reset.";
			//            return $text_message;
			//generate reset token
			$reset_token = sha1(mt_rand(1, 90000).$user_id);
			A:
			$reset_otp = (new \App\Http\Controllers\OTP\functionController())->getRandomPin();
			if (ResetUser::where('reset_otp', $reset_otp)->count() > 0) {
				goto A;
			}

			$reset_user = new ResetUser();
			$reset_user->customer_id = $user_id;
			$reset_user->token = $reset_token;
			$reset_user->sent_value = $phone;
			$reset_user->reset_otp = $reset_otp;
			$reset_user->save();

//            $long_url = url('/reset/' . $reset_token);
//            $short_url = (new functionController2)->urlShortener($long_url);

            $short_url = (new \App\Http\Controllers\functionController())->vgdShorten(url('/reset/'.$reset_token));

            $message_text = 'Use '.$reset_otp.' to reset your Royalty PIN. You may also click on the link to reset your PIN : ';
            $message_text .= $short_url['shortURL'];
            //            $message_text .= ' Thank you, ';
            //            $message_text .= 'Royalty';

            //send sms ssl
//            $user = 'Royaltybd';
//            $pass = '66A6Q13d';
//            $sid = 'RoyaltybdMasking';
//            $url = 'http://sms.sslwireless.com/pushapi/dynamic/server.php';
//            $param = "user=$user&pass=$pass&sms[0][0]= $phone &sms[0][1]=".urlencode($message_text)."&sms[0][2]=123456789&sid=$sid";

            $username = env('BOOMCAST_USERNAME');
            $password = env('BOOMCAST_PASSWORD');
            $url = 'http://api.boom-cast.com/boomcast/WebFramework/boomCastWebService/externalApiSendTextMessage.php';
            $param = "masking=NOMASK&userName=$username&password=$password&MsgType=TEXT&receiver=$phone&message=".$message_text;

            logDebug("{$url}?{$param}");
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

            $response = json_decode($response, true)[0];

            if ($response['success'] == 1) {
                return 'SMS sent successfully.';
            } else {
                return 'Failed.';
            }
//            $xmlob = simplexml_load_string($response) or die('Error: Cannot create object');
//            $MSISDNSTATUS = (string) $xmlob->SMSINFO->MSISDNSTATUS; //return receiver number
//            if ($MSISDNSTATUS == 'Invalid Mobile No') {
//                return 'Failed : Invalid Mobile No';
//            } else {
//                return 'SMS sent successfully.';
//            }
        } else {
            return 'Phone number does not exist!';
        }
    }

    public function checkOTP($phone, $otp)
    {
        return ResetUser::where('sent_value', $phone)
            ->select('customer_id', 'token')
            ->where('reset_otp', $otp)
            ->where('used', 0)
            ->where('verification_type', VerificationType::reset_password)
            ->first();
    }

    public function storeNewPin($customer_id, $encrypted_pin, $token)
    {
        try {
            DB::beginTransaction(); //to do query rollback

            //update customer account table with new password
            DB::table('customer_account')
                ->where('customer_id', $customer_id)
                ->update([
                    'pin' => $encrypted_pin,
                ]);

            //increment pas change value
            $exist = DB::table('pass_changed')->where('customer_id', $customer_id)->first();
            if ($exist) {
                DB::table('pass_changed')
                    ->where('customer_id', $customer_id)
                    ->increment('pass_change', 1);
            } else {
                DB::table('pass_changed')->insert([
                    'customer_id' => $customer_id,
                    'pass_change' => 1,
                ]);
            }

            ResetUser::where('token', $token)->update(
                [
                    'used' => 1,
                ]
            );

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
        return true;
    }

    public function trim_branch_user_pin()
    {
        $users = BranchUser::all();

        foreach ($users as $key => $user) {
            if ($user->pin_code != null && strlen($users[0]->pin_code) == 6) {
                $pin = substr($user->pin_code, 0, 4);
                BranchUser::where('id', $user->id)->update(['pin_code' => $pin]);
            }
        }

        return BranchUser::all();
    }

    public function testApi(Request $request)
    {
        $result = $this->send_reset_pin_sms(1846472405);

        return response()->json(['result' => $result]);
    }
}
