<?php

namespace App\Http\Controllers;

use App\CustomerInfo;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\LoginRegister\functionController as loginFunctionController;
use App\Http\Controllers\LoginRegister\webController;
use App\Http\Controllers\OTP\functionController as otpFunctionController;
use App\Subscribers;
use Datetime;
use DB;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ViewErrorBag;
use Mockery\Exception;
use PhpParser\Node\Stmt\Goto_;
use Response;
use Session;
use View;

class RegistrationController extends Controller
{
    //function to register a new customer - NEW VERSION
    public function registration(Request $request)
    {
        $this->validate($request, [
            'phone_number' => 'required',
            'signup_email' => 'required|email|unique:customer_info,customer_email',
            'full_name' => 'required',
        ]);
        $request->flashOnly(['phone_number', 'signup_email', 'full_name']);

        $email = $request->get('signup_email');
        $name = $request->get('full_name');
        $phone = (new webController())->createPhoneFromUserInput($request->get('phone_number'));
        $pin = null;
        $refer_code = $request->get('refer_code');
        if ($refer_code) {
            $referrer_id = CustomerInfo::where('referral_number', $refer_code)->first()->customer_id;
        } else {
            $referrer_id = null;
        }

        DB::beginTransaction(); //to do query rollback

        try {
            $customer = (new loginFunctionController)->register(
                $name,
                $email,
                $phone,
                $pin,
                PlatformType::web,
                $referrer_id
            );
            session(['customer_id' => $customer->customer_id]);
            session(['customer_username' => $customer->account->customer_username]);
            session(['reg_email' => $email]);
            $customer_name = explode(' ', $customer->customer_full_name);
            //set all info in session
            session(['customer_half_name' => $customer_name[0]]);
            //get all notifications of this customer
            $allNotifications = (new functionController)->allNotifications($customer->customer_id);
            session(['customerAllNotifications' => $allNotifications]);

            (new otpFunctionController())->sendMailVerification($email, $email, $customer->customer_id);//turned off paid membership

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack();

            return view('errors.404');
        }
        return Redirect('users/'.$customer->account->customer_username);

//        return Redirect('registration/verify_email')->with('reg_email', $email);
//        return Redirect('select-card');
    }

    //function to verify email view page
    public function verifyEmailView()
    {
        $reg_email = session('reg_email');

        return view('verify_email', compact('reg_email'));
    }

    //function to verify email
    public function verifyEmail(Request $request)
    {
        $reg_email = $request->get('reg_email');
        $email = $request->get('verifying_email');
        $code = $request->get('verifying_code');

        if ($email == null && $code == null) {
            return \redirect()->back()->with('error', 'Please fill up any field');
        }

        if ($email) {
            $user = CustomerInfo::where('customer_id', session('customer_id'))->first();
            if ($user->customer_email != $email) {
                $send_email = (new otpFunctionController())->sendMailVerification($user->customer_email, $email, session('customer_id'));
                if ($send_email->status() == 201) {
                    return \redirect()->back()->with('error', $send_email->getData()->result);
                } else {
                    session(['reg_email' => $email]);

                    return \redirect()->back()->with('result', $send_email->getData()->result);
                }
            } else {
                return \redirect()->back()->with('error', 'You entered same email.');
            }
        }
        if (session('reg_email')) {
            $reg_email = session('reg_email');
        }

        $verify = (new otpFunctionController())->verifyEmailOTP($reg_email, $code);
        if ($verify->status() == 201) {
            return \redirect()->back()->with('error', $verify->getData()->result);
        } else {
            if (session('reg_email')) {
                Session::forget('reg_email');
            }

            return Redirect('users/'.session('customer_username'))->with('reg_succeeded', 'Registration successful.');
//            return Redirect('select-card');
        }
    }

    //function to show registration success page
    public function registrationSucceed()
    {
        if (Session::has('cus_id_buy_card')) {
            $customer_id = Session::get('cus_id_buy_card');
            $show_username = 1;
        } else {
            $customer_id = '';
            $show_username = 0;
        }
        //get username
        $username = DB::table('customer_account')->where('customer_id', $customer_id)->first();
        //redirect
        return view('customerReg.registration-success', compact('username', 'show_username'));
    }

    //function to check FB Id already exists or not
    public function checkFbId(Request $request)
    {
        $requested_fb_id = $request->input('requested_fb_id');
        $fb_id_exists = DB::table('social_id')->where('customer_social_id', $requested_fb_id)->where('customer_social_type', 'facebook')->count();

        if ($fb_id_exists > 0) {
            return Response::json(1);
        } else {
            return Response::json(0);
        }
    }

    //function to check Google Id already exists or not
    public function checkGoogleId(Request $request)
    {
        $requested_google_id = $request->input('requested_google_id');
        $google_id_exists = DB::table('social_id')->where('customer_social_id', $requested_google_id)->where('customer_social_type', 'google')->count();

        if ($google_id_exists > 0) {
            return Response::json(0);
        } else {
            return Response::json(1);
        }
    }

    //function to store data by fb sign up
    public function fbRegistration(Request $request)
    {
        $first_name = $request->input('first');
        $last_name = $request->input('last');
        $email = $request->input('email') != '' ? $request->input('email') : '';
        $phone = $request->input('phone');
        $fb_id = $request->input('fb_id');
        //get previous values
        $fb_id_exists = DB::table('social_id')->where('customer_social_id', $fb_id)->where('customer_social_type', 'facebook')->count();
        $email_exists = DB::table('customer_info')->where('customer_email', $email)->count();
        $subscribed_email_exists = DB::table('subscribers')->where('email', $email)->count();
        //check, if there's already an account with this fb id
        if ($fb_id_exists > 0) {
            return Response::json(0);
        } elseif ($email_exists > 0) {
            return Response::json(1);
        } else {
            //generate 10 digit number for customer id
            $customer_id = mt_rand(1000000000, mt_getrandmax());
            //$customer_id = random_int(1111111111, 9999999999);//generates 10 digit random number
            $customer_id_10 = $customer_id;
            //get previous serial number of previous user
            $id = DB::table('customer_account')
                ->select('customer_serial_id')
                ->take(1)
                ->orderBy('customer_serial_id', 'DESC')
                ->first();
            $id = json_decode(json_encode($id), true);
            //generate 6 digits id for new user
            for ($i = $id['customer_serial_id'] + 1; $i <= 100; $i++) {
                $customer_id_6 = sprintf('%06d', $i);
                break;
            }
            //generate 5 chars for username
            $username = '';
            $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
            $codeAlphabet .= '0123456789';
            $max = strlen($codeAlphabet); // edited
            for ($i = 0; $i < 5; $i++) {
                $username .= $codeAlphabet[random_int(0, $max - 1)];
            }
            //save data in customer_account table
            DB::table('customer_account')->insert([
                'customer_id' => $customer_id_10.$customer_id_6,
                'customer_serial_id' => $customer_id_6,
                'customer_username' => $first_name.'.'.$last_name.'.'.$username,
                'password' => '',
                'moderator_status' => 2,
            ]);

            //generate referral number
            $token = '';
            $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
            $codeAlphabet .= '0123456789';
            $max = strlen($codeAlphabet); // edited
            for ($i = 0; $i < 5; $i++) {
                $token .= $codeAlphabet[random_int(0, $max - 1)];
            }
            //save data in customer_info table
            DB::table('customer_info')->insert([
                'customer_id' => $customer_id_10.$customer_id_6,
                'customer_first_name' => $first_name,
                'customer_last_name' => $last_name,
                'customer_full_name' => $first_name.' '.$last_name,
                'customer_email' => $email,
                //'customer_dob' => '',
                'customer_contact_number' => $phone,
                'customer_address' => '',
                //'customer_profile_image' => '',
                'customer_type' => 3,
                'month' => 0,
                'expiry_date' => '1971-03-26',
                'member_since' => date('Y-m-d'),
                'referral_number' => $token,
            ]);
            if ($subscribed_email_exists == 0) {
                //save email in subscribers table
                DB::table('subscribers')->insert([
                    'email' => $email,
                ]);
            }

            //store social sign up id and type in social_id table
            DB::table('social_id')->insert([
                'customer_id' => $customer_id_10.$customer_id_6,
                'customer_social_id' => $fb_id,
                'customer_social_type' => 'facebook',
            ]);

            return Response::json(2);
        }
    }

    //function to store data by fb sign up
    public function googleRegistration(Request $request)
    {
        $first_name = $request->input('first');
        $last_name = $request->input('last');
        $email = $request->input('email');
        $phone = $request->input('phone');
        $google_id = $request->input('google_id');
        //get previous values
        $google_id_exists = DB::table('social_id')->where('customer_social_id', $google_id)->where('customer_social_type', 'google')->count();
        $email_exists = DB::table('customer_info')->where('customer_email', $email)->count();
        $subscribed_email_exists = DB::table('subscribers')->where('email', $email)->count();
        /*check, if there's already an account with this fb id*/
        if ($google_id_exists > 0) {
            return Response::json(0);
        } elseif ($email_exists > 0) {
            return Response::json(1);
        } else {
            //generate 10 digit number for customer id
            $customer_id = mt_rand(1000000000, mt_getrandmax());
            //$customer_id = random_int(1111111111, 9999999999);//generates 10 digit random number
            $customer_id_10 = $customer_id;
            //get previous serial number of previous user
            $id = DB::table('customer_account')
                ->select('customer_serial_id')
                ->take(1)
                ->orderBy('customer_serial_id', 'DESC')
                ->first();
            $id = json_decode(json_encode($id), true);
            //generate 6 digits id for new user
            for ($i = $id['customer_serial_id'] + 1; $i <= 100; $i++) {
                $customer_id_6 = sprintf('%06d', $i);
                break;
            }
            //generate 5 chars for username
            $username = '';
            $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
            $codeAlphabet .= '0123456789';
            $max = strlen($codeAlphabet); // edited
            for ($i = 0; $i < 5; $i++) {
                $username .= $codeAlphabet[random_int(0, $max - 1)];
            }
            //save data in customer_account table
            DB::table('customer_account')->insert([
                'customer_id' => $customer_id_10.$customer_id_6,
                'customer_serial_id' => $customer_id_6,
                'customer_username' => $first_name.'.'.$last_name.'.'.$username,
                'password' => '',
                'moderator_status' => 2,
            ]);

            //generate referral number
            $token = '';
            $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
            $codeAlphabet .= '0123456789';
            $max = strlen($codeAlphabet); // edited
            for ($i = 0; $i < 5; $i++) {
                $token .= $codeAlphabet[random_int(0, $max - 1)];
            }
            //save data in customer_info table
            DB::table('customer_info')->insert([
                'customer_id' => $customer_id_10.$customer_id_6,
                'customer_first_name' => $first_name,
                'customer_last_name' => $last_name,
                'customer_full_name' => $first_name.' '.$last_name,
                'customer_email' => $email,
                //'customer_dob' => '',
                'customer_contact_number' => $phone,
                'customer_address' => '',
                //'customer_profile_image' => '',
                'customer_type' => 3,
                'month' => 0,
                'expiry_date' => '1971-03-26',
                'member_since' => date('Y-m-d'),
                'referral_number' => $token,
            ]);
            if ($subscribed_email_exists == 0) {
                //save email in subscribers table
                DB::table('subscribers')->insert([
                    'email' => $email,
                ]);
            }
            //store social sign up id and type in social_id table
            DB::table('social_id')->insert([
                'customer_id' => $customer_id_10.$customer_id_6,
                'customer_social_id' => $google_id,
                'customer_social_type' => 'google',
            ]);

            return Response::json(2);
        }
    }
}
