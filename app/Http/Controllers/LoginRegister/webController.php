<?php

namespace App\Http\Controllers\LoginRegister;

use App\BranchOffers;
use App\CardPrice;
use App\CustomerAccount;
use App\CustomerHistory;
use App\CustomerInfo;
use App\CustomerLoginSession;
use App\Helpers\LengthAwarePaginator;
use App\Http\Controllers\ActivitySession\functionController as activityFunctionController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\Constants;
use App\Http\Controllers\Enum\LoginStatus;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\Enum\RewardRequiredFieldsType;
use App\Http\Controllers\Enum\VerificationType;
use App\Http\Controllers\functionController;
use App\Http\Controllers\functionController2;
use App\Http\Controllers\LoginRegister\functionController as loginFunctionController;
use App\Http\Controllers\LoginSession\functionController as loginSessionFunctionController;
use App\Http\Controllers\OTP\functionController as otpFunctionController;
use App\Http\Controllers\Reward\functionController as rewardFunctionController;
use App\Http\Controllers\Voucher\functionController as voucherFunctionController;
use App\PartnerBranch;
use App\ResetUser;
use App\Subscribers;
use App\TransactionTable;
use Carbon\Carbon;
use DateTime;
use function GuzzleHttp\Promise\all;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;

class webController extends Controller
{
    public function createPhoneFromUserInput($phone)
    {
        if (substr($phone, 0, 1) === '0') {
            $phone = '+88'.$phone;
        } else {
            $phone = '+880'.$phone;
        }
        return $phone;
    }

    public function checkPhone(Request $request)
    {
        $user_phone = $request->input('phone');
        $phone = $this->createPhoneFromUserInput($user_phone);

        $customer = (new loginFunctionController)->getCustomer($phone);
        if ($customer == null) {
            $data['customer'] = 'invalid';
            $data['phone'] = $phone;
            $verify = (new otpFunctionController())
                ->sendPhoneVerification($phone, VerificationType::phone_verification);
            if ($verify->status() == 201) {
                $data['otp_sent'] = false;
                $data['message'] = $verify->getData()->result;
            } else {
                $data['otp_sent'] = true;
                $data['message'] = $verify->getData()->result;
            }
        } elseif ($customer['account']['isSuspended'] == 1) {
            return response()->json('Your account is suspended. Please contact our customer support at
             support@royaltybd.com or call us at +880-963-862-0202.', 404);
        } elseif ($customer['account']['moderator_status'] == 1) {
            return response()->json('Your account has been deactivated as your card maybe lost/damaged. Please
             contact our customer support at support@royaltybd.com or call us at +880-963-862-0202.', 404);
        } else {
            $verify = (new otpFunctionController())
                ->sendPhoneVerification($phone, VerificationType::phone_verification);
            if ($verify->status() == 201) {
                $data['otp_sent'] = false;
            } else {
                $data['otp_sent'] = true;
            }
            $data['message'] = $verify->getData()->result;

//            if ($customer['account']['pin'] == null) {
//                $verify = (new otpFunctionController())
//                    ->sendPhoneVerification($phone, VerificationType::phone_verification);
//                if ($verify->status() == 201) {
//                    $data['otp_sent'] = false;
//                } else {
//                    $data['otp_sent'] = true;
//                }
//                $data['pin'] = 0;
//                $data['message'] = $verify->getData()->result;
//            } else {
//                $data['pin'] = 1;
//                $data['otp_sent'] = false;
//                $data['message'] = 'Invalid';
//            }
            $data['customer'] = $phone;
        }

        return response()->json($data, 200);
    }

    public function setStatNumber(Request $request)
    {
        $phone = '+880'.$request->input('phone');
        $stat = (new loginFunctionController)->setStatNumber($phone);

        return response()->json($stat);
    }

    public function checkCodePhone(Request $request)
    {
        $code = $request->input('code');
        $phone = $request->input('phone');
        $phone = $this->createPhoneFromUserInput($phone);

        $verification_type = $request->input('type');
        //$user_type = $this->request->input('user_type');

	    if (strlen($phone) === 10 && strpos($phone, '1') === 1) {
	        $phone = '+880' . $phone;
	    }

        $verify = (new otpFunctionController())->verifyPhoneOTP($phone, $code, $verification_type);
        if ($verify->status() == 201) {
            return \response()->json(['status'=> false, 'message'=>$verify->getData()->result]);
        } else {
            return \response()->json(['status'=> true, 'message'=>$verify->getData()->result]);
        }
    }

    public function updateStatNumber(Request $request)
    {
        $id = $request->input('id');
        $status = $request->input('status');
        $phone = $request->input('phone');

        $stat = (new loginFunctionController)->updateStatNumber($id, $status, $phone);

        return response()->json($stat);
    }

    public function checkPinPass(Request $request)
    {
        $phone = $request->input('customer_phone');
        $customer_id = CustomerInfo::where('customer_contact_number', $phone)->first()->customer_id;
        $pinPass = $request->input('pinPass');
        $type = $request->input('type');
        $physical_address = $request->input('physical_address');

        $login = (new loginFunctionController)->matchCustomer($customer_id, $type, $pinPass);
        $username = CustomerAccount::where('customer_id', $customer_id)->select('customer_username')->first();

        if ($login) {
            if ($physical_address == 'null' || $physical_address == '') {
                $physical_address = (new loginFunctionController())->randomTextForLoginSession();
            }
            (new loginSessionFunctionController())->saveSession($customer_id, PlatformType::web,
                $physical_address, $_SERVER['REMOTE_ADDR'], LoginStatus::logged_in);

            session(['customer_id' => $customer_id]);
            session(['customer_username' => $username->customer_username]);
            $result['status'] = 1;
            $result['text'] = url('users/'.$username->customer_username);
            $result['physical_address'] = $physical_address;
            $result['phone_number'] = CustomerInfo::where('customer_id', $customer_id)
                ->first()->customer_contact_number;

            $physical_address = (new loginFunctionController())->randomTextForLoginSession();
            (new activityFunctionController())->saveSession($customer_id, PlatformType::web,
                $physical_address, $_SERVER['REMOTE_ADDR']);
        } else {
            $key = $type == 1 ? 'Password' : 'PIN';
            $result['status'] = 0;
            $result['text'] = 'Incorrect '.$key;
            $result['physical_address'] = $physical_address;
        }

        return response()->json($result);
    }

    public function signUpView()
    {
        return view('signup');
    }

    public function resetPin(Request $request)
    {
        $this->validate($request, [
            'phone' => 'required',
        ]);
        if (!is_numeric($request->get('phone'))) {
            return redirect()->back()->with('phone not exist', 'Please provide a valid phone number');
        }
        $phone = $this->createPhoneFromUserInput($request->get('phone'));
        if ($x = (new functionController2())->isResetSMSSent($phone)) {
            logDebug('reset pin already sent for ' . $phone);
            $current = Carbon::now();
            $dt = $x->created_at;
            $diff = $dt->diffInMinutes($current);
            $result = 'You have already requested for pin reset. You can request for another one in next '.(Constants::resend_time - $diff).' minutes.';

            return redirect()->back()->with('phone not exist', $result);
        } else {
            logDebug('sending reset pin ' . $phone);
            $result = (new loginFunctionController)->send_reset_pin_sms($phone);
        }

        if ($result == 'Phone number does not exist!') {
            return redirect()->back()->with('phone not exist', 'Phone number does not exist!');
        } elseif ($result == 'SMS sent successfully.') {
            return redirect('reset_pin/check-otp?phone='.$request->get('phone'))->with([
                'password sent' => 'We have sent you an SMS with your reset PIN code. This may take a minute. If you still haven’t received any SMS, please re-enter your phone number and we will send you another SMS.',
            ]);
        }
    }

    public function resetOTPCheck(Request $request)
    {
        $this->validate($request, [
            'reset_otp' => 'required|numeric'
        ]);
        $phone = $this->createPhoneFromUserInput($request->otp_check_phone);
        $exists = (new loginFunctionController())->checkOTP($phone, $request->reset_otp);
        if ($exists) {
            return redirect('reset/'.$exists->token);
        } else {
            return redirect()->back()->with('did_not_match', 'Code did not match. Please try again.');
        }
    }
    public function directLogin(Request $request)
    {
        $phone = $this->createPhoneFromUserInput($request->post('phone'));

        $user = CustomerInfo::where('customer_contact_number', $phone)->with('account')->first();
        session(['customer_id' => $user->customer_id]);
        session(['customer_username' => $user->account->customer_username]);
        $result = url('users/'.$user->account->customer_username);

        return \response()->json($result);
    }

    public function userCommonData($username)
    {
        //Login check when one user replace his username with another user in the search bar
        if (Session::get('customer_username') != $username) {
            return redirect('users/'.Session::get('customer_username'));
        }

        //get info of user from id
        $customer_data = DB::table('customer_info as ci')
            ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
            ->leftjoin('card_delivery', function ($join) {
                $join->on('card_delivery.customer_id', '=', 'ci.customer_id')
                    ->on('card_delivery.id', '=', DB::raw('(SELECT max(id) from card_delivery WHERE card_delivery.customer_id = ci.customer_id)'));
            })
            ->leftJoin('pass_changed as pc', 'pc.customer_id', '=', 'ci.customer_id')
            ->select('ci.*', 'ci.customer_id as customerID', 'ca.customer_username', 'ca.pin', 'card_delivery.*', 'pc.pass_change')
            ->where('ca.customer_username', $username)
            ->first();

        $info_at_buy_card = DB::table('info_at_buy_card')
            ->where('customer_username', $customer_data->customer_username)
            ->where('delivery_type', 4)
            ->first();
        if ($customer_data->customer_type != 3) {
            $exp_status = (new functionController2)->getExpStatusOfCustomer($customer_data->expiry_date);
            $customer_data->exp_status = $exp_status;
            $customer_history = CustomerHistory::where('customer_id', $customer_data->customerID)->orderBy('id', 'DESC')->first();
            $customer_data->customer_status = $customer_history->type;
        } else {
            $trial = CardPrice::where('platform', PlatformType::web)->where('price', 0)->first();
            $customer_data->can_get_trial = $trial == null ? false : true;
            $customer_data->exp_status = null;
            $customer_data->customer_status = null;
        }

        $customer_name = explode(' ', $customer_data->customer_full_name);
        $total_branch_count = PartnerBranch::where('active', 1)->count();
        //set all info in session
        session(['customer_half_name'      => $customer_name[0]]);
        session(['customer_full_name'      => $customer_data->customer_full_name]);
        session(['customer_email'          => $customer_data->customer_email]);
        session(['customer_email_verified' => $customer_data->email_verified]);
        session(['customer_profile_image'  => $customer_data->customer_profile_image]);
        session(['referral_number'         => $customer_data->referral_number]);
        session(['expiry_date'             => $customer_data->expiry_date]);
        session(['expiry_status'           => $customer_data->exp_status]);
        session(['user_type'               => $customer_data->customer_type]);
        session(['sms_app_link'            => 1]);
        session(['isPinSet'                => $customer_data->pin == null ? false : true]);
        session(['total_branch_count'      => $total_branch_count]);
        //get influencer data
        $promo_used = (new functionController)->influencersPromoUsed($customer_data->customerID);
        //get all notifications of this customer
        $allNotifications = (new functionController)->allNotifications($customer_data->customerID);
        session(['customerAllNotifications' => $allNotifications]);

        return ['customer_data' => $customer_data, 'temp_info' => $info_at_buy_card, 'promo_used' => $promo_used];
    }

    public function userNewsFeed($username)
    {
        $data = $this->userCommonData($username);
        $customer_data = $data['customer_data'];
        $info_at_buy_card = $data['temp_info'];
        $promo_used = $data['promo_used'];

        //get news feed of this customer
        $newsFeed = (new functionController)->newsFeed(session('customer_id'));

        return view('useracc/acchomenews', compact(
            'customer_data',
            'info_at_buy_card',
            'promo_used',
            'newsFeed'
        ));
    }

    public function userInfo($username)
    {
        $data = $this->userCommonData($username);
        $customer_data = $data['customer_data'];
        $info_at_buy_card = $data['temp_info'];
        $promo_used = $data['promo_used'];
        //email id from subscribers table
        $subscriber = Subscribers::where('email', $customer_data->customer_email)->first();
        $customer_data->subscribed = $subscriber ? $subscriber->email : null;
        $customer_data->total_points = (new rewardFunctionController())->getRoyaltyPoints($customer_data->customerID);
        $customer_data->refer_credits = (new rewardFunctionController())->getReferPoints($customer_data->customerID);
        $customer_data->profile_completed = (new rewardFunctionController())->profileCompletionPercentage($customer_data->customerID);

        return view('useracc/accinfo', compact(
            'customer_data',
            'info_at_buy_card',
            'promo_used'
        ));
    }

    public function userStatistics($username)
    {
        $data = $this->userCommonData($username);
        $customer_data = $data['customer_data'];
        $info_at_buy_card = $data['temp_info'];
        $promo_used = $data['promo_used'];
        //total number of card used
        $card_used = TransactionTable::where('customer_id', session('customer_id'))->count();
        //total partner number customer visited
        $partner_number = (new functionController)->totalPartnersCustomerVisited(session('customer_id'));
        //get total review number of the customer
        $review_number = DB::table('review')->where('customer_id', session('customer_id'))->count();
        //get transaction history of this customer
        $transactionHistory = (new functionController)->customerTransaction(session('customer_id'));
        //get all visited partners of this customer
        $visited_partners = (new functionController)->visitedPartners(session('customer_id'));

        return view('useracc/accstat', compact(
            'customer_data',
            'info_at_buy_card',
            'promo_used',
            'card_used',
            'partner_number',
            'review_number',
            'transactionHistory',
            'visited_partners'
        ));
    }

    public function userRewards($username)
    {
        $data = $this->userCommonData($username);
        $customer_data = $data['customer_data'];
        $info_at_buy_card = $data['temp_info'];
        $promo_used = $data['promo_used'];
        $all_points = (new rewardFunctionController())->collectAllPoints($customer_data->customerID, false);
        $rewards = (new rewardFunctionController())->getRoyaltyRewards();
        $rewards = (new functionController2())->getPaginatedData($rewards, 10);
        $redeemedRewards = (new rewardFunctionController())->getAllRedeemedRewards($customer_data->customerID);

        return view('useracc/accreward', compact(
            'customer_data',
            'info_at_buy_card',
            'promo_used',
            'rewards',
            'redeemedRewards',
            'all_points'
        ));
    }

    public function userReviews($username)
    {
        $data = $this->userCommonData($username);
        $customer_data = $data['customer_data'];
        $info_at_buy_card = $data['temp_info'];
        $promo_used = $data['promo_used'];

        //get all reviews of this customer
        $reviews = (new functionController)->customerAllReviews(session('customer_id'));

        return view('useracc/accreview', compact(
            'customer_data',
            'info_at_buy_card',
            'promo_used',
            'reviews'
        ));
    }

    public function userOffers($username)
    {
        $data = $this->userCommonData($username);
        $customer_data = $data['customer_data'];
        $info_at_buy_card = $data['temp_info'];
        $promo_used = $data['promo_used'];

        //get transaction history of this customer
        $transactionHistory = (new functionController)->customerTransaction(session('customer_id'));

        return view('useracc/accofferavailed', compact(
            'customer_data',
            'info_at_buy_card',
            'promo_used',
            'transactionHistory'
        ));
    }

    public function userDeals($username)
    {
        $data = $this->userCommonData($username);
        $customer_data = $data['customer_data'];
        $info_at_buy_card = $data['temp_info'];
        $promo_used = $data['promo_used'];

        //get user deals
        $purchased_vouchers = (new voucherFunctionController())->purchasedVouchers($customer_data->customerID);

        return view('useracc.deals.accdeals', compact(
            'customer_data',
            'info_at_buy_card',
            'promo_used',
            'purchased_vouchers'
        ));
    }

    public function dealDetails($username, $id)
    {
        $data = $this->userCommonData($username);
        $customer_data = $data['customer_data'];
        $info_at_buy_card = $data['temp_info'];
        $promo_used = $data['promo_used'];

        $voucher_details = (new voucherFunctionController())->singlePurchaseDetails($id);

        return view('useracc.deals.dealdetails', compact(
            'customer_data',
            'info_at_buy_card',
            'promo_used',
            'voucher_details'
        ));
    }

    //all rewards including royalty & partners
    public function specificReward($username, $id)
    {
        $data = $this->userCommonData($username);
        $customer_data = $data['customer_data'];
        $info_at_buy_card = $data['temp_info'];
        $promo_used = $data['promo_used'];
        $all_points = (new rewardFunctionController())->collectAllPoints($customer_data->customerID, false);

        $reward = BranchOffers::find($id);
//        $branch_points = (new rewardFunctionController())
//            ->getBranchPoint($customer_data->customerID, $reward->branch_id, true);

        //$offer_use_count = TransactionTable::where('offer_id', $reward->id)->count();
        $req_phone = $req_email = $req_del_add = $req_others = null;
        if ($reward->required_fields && count($reward->required_fields) > 0) {
            for ($i = 0; $i < count($reward->required_fields); $i++) {
                if ($reward->required_fields[$i]['type'] == RewardRequiredFieldsType::phone) {
                    $req_phone = $reward->required_fields[$i]['text'];
                } elseif ($reward->required_fields[$i]['type'] == RewardRequiredFieldsType::email) {
                    $req_email = $reward->required_fields[$i]['text'];
                } elseif ($reward->required_fields[$i]['type'] == RewardRequiredFieldsType::del_add) {
                    $req_del_add = $reward->required_fields[$i]['text'];
                } elseif ($reward->required_fields[$i]['type'] == RewardRequiredFieldsType::others) {
                    $req_others = $reward->required_fields[$i]['text'];
                }
            }
        }

        return view('useracc.accrewards.specific_reward', compact(
            'customer_data',
            'info_at_buy_card',
            'promo_used',
            'reward',
            'req_phone',
            'req_email',
            'req_del_add',
            'req_others',
            'all_points'
        ));
    }

    public function userCreditHistory($username)
    {
        $data = $this->userCommonData($username);
        $customer_data = $data['customer_data'];
        $info_at_buy_card = $data['temp_info'];
        $promo_used = $data['promo_used'];

        $rbd_point_earn_history = (new rewardFunctionController())->royaltyPointHistory($customer_data->customerID);
        $transaction_point_earn_history = (new rewardFunctionController())->pointEarnHistory($customer_data->customerID);
        $all_earn_history = array_merge($rbd_point_earn_history, $transaction_point_earn_history);
        $all_earn_history = collect($all_earn_history)->sortByDesc('timestamp');
        $all_points = (new rewardFunctionController())->collectAllPoints($customer_data->customerID, true);

        return view('useracc.accrewards.credit_history', compact(
            'customer_data',
            'info_at_buy_card',
            'promo_used',
            'all_earn_history',
            'all_points'
        ));
    }

    //check refer code at registration
    public function checkRegReferCode(Request $request)
    {
        $code = $request->input('refer_code');
        $refer_exists = DB::table('customer_info')->where('referral_number', $code)->count();

        if ($refer_exists == 1) {
            return response()->json(true);
        } else {
            return response()->json(false);
        }
    }

    public function customerLogout(Request $request)
    {
        $customer_id = \session('customer_id');
        $address = CustomerLoginSession::where('customer_id', $customer_id)->orderBy('id', 'DESC')->first();
        if ($address) {
            $physical_address = $address->physical_address;
        } else {
            $physical_address = (new loginFunctionController())->randomTextForLoginSession();
        }
        (new loginSessionFunctionController())->saveSession($customer_id, PlatformType::web,
            $physical_address, $_SERVER['REMOTE_ADDR'], LoginStatus::logged_out);
        $request->session()->flush();
        Auth::logout();

        return redirect('/login');
    }
}
