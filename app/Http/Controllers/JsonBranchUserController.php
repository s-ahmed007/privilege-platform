<?php

namespace App\Http\Controllers;

use App\AllAmounts;
use App\AllCoupons;
use App\BonusRequest;
use App\BranchOffers;
use App\BranchScanner;
use App\BranchUser;
use App\BranchVoucher;
use App\CustomerInfo;
use App\CustomerNotification;
use App\CustomerRewardRedeem;
use App\Events\offer_availed;
use App\Helpers\LengthAwarePaginator;
use App\Http\Controllers\Enum\BranchUserRole;
use App\Http\Controllers\Enum\Constants;
use App\Http\Controllers\Enum\GlobalTexts;
use App\Http\Controllers\Enum\LikerType;
use App\Http\Controllers\Enum\notificationType;
use App\Http\Controllers\Enum\PartnerRequestType;
use App\Http\Controllers\Enum\PlatformType;
use App\LeaderboardPrizes;
use App\PartnerAccount;
use App\PartnerBranch;
use App\RbdCouponPayment;
use App\ScannerPrizeHistory;
use App\ScannerPrizes;
use App\ScannerReward;
use App\TransactionTable;
use App\VoucherPurchaseDetails;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JsonBranchUserController extends Controller
{
    public function authenticate(Request $request)
    {
        $pin_code = $request->post('pin');
        $long = $request->post('long');
        $lat = $request->post('lat');
        $phone = $request->post('phone');
        $distance = 0;
        if ($pin_code) {
            $user = BranchUser::where('phone', '=', $phone)->where('pin_code', '=', $pin_code)->first();
        } else {
            $user = BranchUser::where('phone', '=', $phone)->first();
        }
        if (! $user) {
            return response()->json(['error' => 'Invalid credential.'], 201);
        } else {
            $scanner = BranchScanner::where('branch_user_id', $user->id)->with('branch')->first();
        }

        //check the radius within 5km
        if ($long && $lat) {
            $distance = (new functionController)->calculateDistance($lat, $long, $scanner->branch->latitude, $scanner->branch->longitude, 'K');
        }
        if ($distance > 0.5) {
            return response()->json(['error' => 'Please be within the 0.5km radius of your shop in order to login.'], 201);
        } elseif ($this->remainingDays($scanner->branch) <= 0) {
            return response()->json(['error' => GlobalTexts::partner_account_expired_login_msg], 201);
        } elseif ($user->active == 0 || $scanner->branch->active == 0 || $scanner->branch->account->active == 0) {
            return response()->json(['error' => GlobalTexts::merchant_end_deactivated_msg], 201);
        } else {
            try {
                if (! $token = JWTAuth::fromUser($user)) {
                    return response()->json(['error' => 'invalid_credentials'], 400);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'could_not_create_token'], 500);
            }
        }

        $this->changeJWT($token, $user->id);

        return response()->json(compact('token'));
    }

    public function remainingDays($branch)
    {
        $curDate = date('Y-m-d');
        try {
            $cur_date = new DateTime($curDate);
            $expiry_date = new DateTime($branch->info->expiry_date);
        } catch (\Exception $e) {
            return 0;
        }

        $interval = date_diff($cur_date, $expiry_date);

        return $interval->format('%R%a');
    }

    public function changeJWT($token, $id)
    {
        BranchUser::where('id', $id)
            ->update(['jwt_token' => $token]);
    }

    public function manageJWTCredential(Request $request)
    {
        $id = $request->post('id');
        $token = $request->post('token');
        $user = BranchUser::where('id', $id)->first();
        if ($user && strcmp($user->jwt_token, $token) != 0) {
            JWTAuth::setToken($token)->invalidate(true);

            return response()->json(['error' => 'Unauthorized.'], 401);
        } else {
            return response()->json(compact('token'), 200);
        }
    }

    public function setFirebaseToken(Request $request)
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['error' => 'User does not exist.'], 201);
        } else {
            BranchUser::where('id', $login->id)
                ->update(
                    [
                        'f_token' => $request->post('f_token'),
                    ]
                );
            if ($login->role >= BranchUserRole::branchScanner) {
                $user = BranchUser::where('id', $login->id)->with('branchScanner.branch.info.profileImage', 'branchScanner.branch.info.discount', 'branchScanner.branch.info.account', 'branchScanner.scannerReward')->first();
            } else {
                $user = null;
            }
        }

        return response()->json(compact('user'));
    }

    //function to check user validity && user requests
    public function checkCustomer(Request $request)
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['error' => 'Branch User does not exist.'], 201);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                $user = BranchUser::where('id', $login->id)->with('branchScanner.branch.info.profileImage', 'branchScanner.branch.info.discount', 'branchScanner.scannerReward')->first();
                $branch_id = $user->branchScanner->branch->id;
            } else {
                $user = null;
                $branch_id = null;

                return response()->json(['error' => 'Something went wrong.'], 201);
            }

            $customer_id = $request->post('customer_id');
            $today = date('Y-m-d');
            //check if customer id exists or not
            $customer = CustomerInfo::where('customer_id', $customer_id)->with('type', 'account')->first();

            if (empty($customer)) {
                $data['error'] = 'Invalid Customer';

                return response()->json($data, 201);
            } else {
                $customer_requests = DB::table('bonus_request as brq')
                    ->join('all_coupons as acp', 'acp.id', '=', 'brq.coupon_id')
                    ->select('acp.reward_text', 'acp.coupon_type', 'brq.*')
                    ->where('brq.customer_id', $customer_id)
                    ->where('acp.branch_id', $branch_id)
                    ->where('brq.used', 0)
                    ->where('brq.expiry_date', '>=', $today)
                    ->get();
                $customer_requests = json_decode(json_encode($customer_requests), true);
                $customer->requests = $customer_requests;

                return response()->json($customer, 200);
            }
        }
    }

    public function pointPrizes()
    {
        $prizes = ScannerPrizes::all();

        return response()->json($prizes, 200);
    }

    public function removeFirebaseToken()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['error' => 'User does not exist.'], 201);
        } else {
            BranchUser::where('id', $login->id)
                ->update(
                    [
                        'f_token' => 0,
                    ]
                );
            if ($login->role >= BranchUserRole::branchScanner) {
                $user = BranchUser::where('id', $login->id)->with('branchScanner.branch.info.profileImage', 'branchScanner.branch.info.discount', 'branchScanner.scannerReward')->first();
            } else {
                $user = null;
            }
        }

        return response()->json(compact('user'));
    }

    public function getAllRewards()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['error' => 'User does not exist.'], 201);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                $user = BranchUser::where('id', $login->id)->with('branchScanner.branch')->first();
                $rewards = (new \App\Http\Controllers\Reward\functionController())->getSpecificPartnerReward($user->branchScanner->branch->id, true);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }

        return response()->json($this->makePagination($rewards, 'branch_rewards'), 200);
    }

    public function getPaymentInfo()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['error' => 'User does not exist.'], 201);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                $user = BranchUser::where('id', $login->id)->with('branchScanner.branch')->first();

                return response()->json((new \App\Http\Controllers\Reward\functionController())->branchPayments($user->branchScanner->branch->id), 200);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    public function makePagination($list, $name)
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // Create a new Laravel collection from the array data
        $itemCollection = collect($list);

        // Define how many items we want to be visible in each page
        $perPage = 10;

        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

        // Create our paginator and pass it to the view
        $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);
        $paginatedItems->setPath('');
        $paginatedItems->setArrayName($name);

        return $paginatedItems;
    }

    public function getAuthenticatedUser()
    {
        try {
            if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
                return response()->json(['user_not_found'], 404);
            } else {
                if ($login->role >= BranchUserRole::branchScanner) {
                    $user = BranchUser::where('id', $login->id)->with('branchScanner.branch.info.profileImage', 'branchScanner.branch.info.discount', 'branchScanner.branch.info.account', 'branchScanner.scannerReward')->first();
                } else {
                    $user = null;
                }
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        return response()->json(compact('user'));
    }

    //function to calculate customer bill
    public function calculateBill(Request $request)
    {
        $bill = $request->post('bill');
        $customerID = $request->post('customer_id');
        $coupon_type = $request->post('coupon_type');
        $request_code = $request->post('request_code');
        $discount = $request->post('discount');

        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                $user = BranchUser::where('id', $login->id)->with('branchScanner.branch')->first();

                if (empty($discount)) {
                    $discount_percentage = DB::table('customer_info as ci')
                        ->join('discount as dis', 'dis.user_type', '=', 'ci.customer_type')
                        ->select('discount_percentage')
                        ->where('ci.customer_id', $customerID)
                        ->where('dis.partner_account_id', $user->branchScanner->branch->partner_account_id)
                        ->get();
                    if (count($discount_percentage) > 0) {
                        $discount_percentage = json_decode(json_encode($discount_percentage), true);
                        $discount_percentage = $discount_percentage[0]['discount_percentage'];
                    } else {
                        $discount_percentage = -1;
                    }
                } else {
                    $discount_percentage = $discount;
                }

                if (! empty($request_code)) {
                    //get refer bonus amount from database
                    $refer_bonus_from_db = DB::table('all_amounts')->select('price')->where('type', 'refer_bonus')->first();
                    if ($coupon_type == 1) {
                        $payable_amount = $bill;
                        $discount = 0;
                    } elseif ($coupon_type == 2) {
                        $discount = ((($bill * $discount_percentage) / 100) + $refer_bonus_from_db->price);
                        $payable_amount = $bill - $discount;
                        $payable_amount < 0 ? $payable_amount = 0 : $payable_amount = $payable_amount;
                    } else {
                        $payable_amount = $bill;
                        $discount = 0;
                    }
                } else {
                    $discount = (($bill * $discount_percentage) / 100);
                    $payable_amount = $bill - $discount;
                }
                $transaction_details['requestCode'] = $request_code;
                $transaction_details['customerID'] = $customerID;
                $transaction_details['bill'] = round($bill);
                $transaction_details['discount'] = round($discount);
                $transaction_details['bill_amount'] = round($payable_amount);

                return response()->json($transaction_details);
            } else {
                return response()->json(['error' => 'You do not have the access'], 201);
            }
        }
    }

    public function confirmOfferTransaction(Request $request)
    {
        //get all values from confirm modal
        $customerID = $request->post('customer_id');
        $offer_id = $request->post('offer_id');
        $redeem_id = $request->post('redeem_id');
        $guid = $request->post('guid');
        $offer = BranchOffers::where('id', $offer_id)->with('customizedPoint')->first();
        $point = $offer->point;
        $date = date('d-m-Y');
        $week_Day = strtolower(date('D'));
        $time = date('H:i');
        $other_prices = AllAmounts::all();
        $daily_point_limit = $other_prices->where('type', 'daily_point_limit')->first()->price;
        if ($offer->selling_point) {
            $notif_text = 'You availed a reward at '.$offer->branch->info->partner_name.', '.$offer->branch->partner_area.'.';
            $notif_type = notificationType::reward;
        } else {
            $notif_text = 'You availed an offer at ';
            $notif_type = notificationType::transaction;
        }
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['error' => 'You do not have the access'], 201);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                $user = BranchUser::where('id', $login->id)->with('branchScanner.branch')->first();
                $branch_id = $user->branchScanner->branch->id;

                if ((new \App\Http\Controllers\TransactionRequest\functionController())
                    ->alreadyRequested($offer_id, $customerID)) {
                    return response()->json(['error' => GlobalTexts::already_requested_transaction], 201);
                }
                $customer = CustomerInfo::where('customer_id', $customerID)->first();
                $branch = PartnerBranch::where('id', $branch_id)->with('info.profileImage')->first();
                $prev_transaction = TransactionTable::where('GUID', $guid)->first();

                if ($prev_transaction) {
                    //trigger 'setPusher' function to do live push notification for website
                    event(new offer_availed($customerID));
                    //message to send as parameter
                    $message = $notif_text.$branch->info->partner_name.', '.$branch->partner_area.'. Please share your experience.';
                    //send notification to app
                    (new jsonController)->sendFirebaseDiscountNotification($message, $customer, 3, $branch->id, $prev_transaction->id);

                    return response()->json($prev_transaction, 200);
                } else {
                    if ($offer->customizedPoint) {
                        $date_valid = false;
                        $time_valid = false;
                        $week_valid = false;
                        $customize_point_date = $offer->customizedPoint->date_duration;
                        $customize_point_week = $offer->customizedPoint->weekdays;
                        $customize_point_times = $offer->customizedPoint->time_duration;

                        try {
                            if (
                                new DateTime($customize_point_date[0]['from']) <= new DateTime($date)
                                && new DateTime($customize_point_date[0]['to']) >= new DateTime($date)
                            ) {
                                $date_valid = true;
                            }
                        } catch (\Exception $e) {
                            $date_valid = false;
                        }
                        if (count($customize_point_times) > 0) {
                            foreach ($customize_point_times as $customize_point_time) {
                                try {
                                    if (
                                        new DateTime($customize_point_time['from']) <= new DateTime($time)
                                        && new DateTime($customize_point_time['to']) >= new DateTime($time)
                                    ) {
                                        $time_valid = true;
                                        break;
                                    }
                                } catch (\Exception $e) {
                                }
                            }
                        } else {
                            $time_valid = true;
                        }

                        if ($customize_point_week[0][$week_Day] == 1) {
                            $week_valid = true;
                        }

                        if ($date_valid && $time_valid && $week_valid) {
                            $point = $point * $offer->customizedPoint->point_multiplier;
                        } else {
                            $point = $point * 1;
                        }
                    } else {
                        $point = $point * 1;
                    }
                    $today_point = TransactionTable::where('customer_id', $customerID)->where('posted_on', 'like', date('Y-m-d').'%')->sum('transaction_point');

                    if ($notif_type == notificationType::transaction) {
                        if ($today_point >= $daily_point_limit) {
                            $new_point = 0;
                            $notif_text = 'You have reached your daily point limit of '.$daily_point_limit.' after availing an offer at ';
                        } else {
                            if (($point + $today_point) > $daily_point_limit) {
                                $new_point = $daily_point_limit - $today_point;
                                $notif_text = 'You have reached your daily point limit of '.$daily_point_limit.' after availing an offer at ';
                            } else {
                                $new_point = $point;
                                if ($new_point > 1) {
                                    $notif_text = 'You have earned '.$new_point.' credits from availing an offer at ';
                                } else {
                                    $notif_text = 'You have earned '.$new_point.' credit from availing an offer at ';
                                }
                            }
                        }
                    } else {
                        $new_point = 0;
                    }

                    //                while ($previousPoint >= 2500) {
                    //                    $previousPoint -= 2500;
                    //                    $point_bonus_counter++;
                    //                }

                    //one transaction at one day
                    $branch_transaction_count = TransactionTable::where('branch_id', $branch_id)->where('customer_id', $customerID)->where('offer_id', $offer_id)
                        ->where('posted_on', 'like', date('Y-m-d').'%')->count();
                    if (! $offer->selling_point && $branch_transaction_count >= Constants::branch_transaction_count) {
                        return response()->json(['error' => GlobalTexts::offer_wise_transaction_error], 201);
                    }
                    if ($offer->active == 0) {
                        return response()->json(['error' => 'This offer is not valid.'], 201);
                    } elseif ($offer->counter_limit != null) {
                        $offer_count = TransactionTable::where('offer_id', $offer_id)->count();
                        if ($offer->counter_limit <= $offer_count) {
                            return response()->json(['error' => 'The offer has reached it\'s limit.'], 201);
                        }
                    } elseif ($offer->scan_limit != null) {
                        $offer_count = TransactionTable::where('offer_id', $offer_id)->where('customer_id', $customerID)->count();
                        if ($offer->scan_limit <= $offer_count) {
                            return response()->json(['error' => 'This customer has reached the offer limit.'], 201);
                        }
                    }
                    //        return Response::json($customer->firebase_token);
                    try {
                        \DB::beginTransaction();

                        if ($redeem_id) {
                            CustomerRewardRedeem::where('id', $redeem_id)
                                ->update(['used' => 1]);
                        }
                        $transaction = new TransactionTable([
                            'branch_id' => $branch_id,
                            'customer_id' => $customerID,
                            'posted_on' => date('Y-m-d H:i:s'),
                            'transaction_point' => $new_point,
                            'branch_user_id' => $user->id,
                            'GUID' => $guid,
                            'redeem_id' => $redeem_id,
                            'offer_id' => $offer_id,
                            'platform' => PlatformType::android,
                        ]);
                        $transaction->save();

                        //scanner point system
                        if ($branch_transaction_count < 1) {
                            $scan_point = AllAmounts::all();
                            ScannerReward::where('scanner_id', $user->branchScanner->id)
                                ->increment('point', $scan_point[10]['price']);
                        }

                        $customer_notification = new CustomerNotification([
                            'user_id' => $customerID,
                            'image_link' => $branch->info->profileImage->partner_profile_image,
                            'notification_text' => $notif_text,
                            'notification_type' => $notif_type,
                            'source_id' => $transaction->id,
                            'seen' => 0,
                        ]);
                        $customer_notification->save();
                        (new \App\Http\Controllers\AdminNotification\functionController())->newTransactionNotification($transaction);

                        \DB::commit(); //to do query rollback
                    } catch (\Exception $e) {
                        \DB::rollBack(); //rollback all successfully executed queries
                        return response()->json(['error' => 'Please try again!'], 201);
                    }

                    //message to send as parameter
                    if ($notif_type == notificationType::transaction) {
                        $message = $notif_text.$branch->info->partner_name.', '.$branch->partner_area;
                    } else {
                        $message = $notif_text;
                    }
                    //send notification to app
                    (new jsonController)->sendFirebaseDiscountNotification($message, $customer, $notif_type, $branch->id, $transaction->id);
                    (new \App\Http\Controllers\TransactionRequest\functionController())->onTransactionSendReferPoints($customerID);

                    return response()->json($transaction, 200);
                }
            } else {
                return response()->json(['error' => 'You do not have the access'], 201);
            }
        }
    }

    public function confirmCouponTransaction(Request $request)
    {
        //get all values from confirm modal
        $customerID = $request->post('customer_id');
        $requestCode = $request->post('requestCode');

        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['error' => 'You do not have the access'], 201);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                $user = BranchUser::where('id', $login->id)->with('branchScanner.branch')->first();
                $branch_id = $user->branchScanner->branch->id;

                $customer = CustomerInfo::where('customer_id', $customerID)->first();
                $branch = PartnerBranch::where('id', $branch_id)->with('info.profileImage')->first();

                if ($requestCode) {
                    //get request id from bonus request table by request code
                    $req_id = BonusRequest::where('request_code', $requestCode)->first();
                    $coupon_id = $req_id->coupon_id;
                    $req_id = $req_id->req_id;
                    $notif_text = 'You availed a coupon at ';

                    //one transaction at one day
                    $branch_transaction_count = TransactionTable::where('branch_id', $branch_id)->where('customer_id', $customerID)->where('posted_on', 'like', date('Y-m-d').'%')->count();
                    if ($branch_transaction_count >= Constants::branch_transaction_count) {
                        return response()->json(['error' => GlobalTexts::offer_wise_transaction_error], 201);
                    }

                    //        return Response::json($customer->firebase_token);
                    try {
                        \DB::beginTransaction();

                        $transaction = new TransactionTable([
                            'branch_id' => $branch_id,
                            'customer_id' => $customerID,
                            'posted_on' => date('Y-m-d H:i:s'),
                            'req_id' => $req_id,
                            'transaction_point' => 0,
                            'branch_user_id' => $user->id,
                        ]);
                        $transaction->save();

                        //scanner point system
                        if ($branch_transaction_count < 1) {
                            $scan_point = AllAmounts::all();
                            ScannerReward::where('scanner_id', $user->branchScanner->id)
                                ->increment('point', $scan_point[10]['price']);
                        }
                        //customer
                        //make bonus request used with req_id
                        BonusRequest::where('customer_id', $customerID)
                            ->where('req_id', $req_id)
                            ->update(['used' => 1]);
                        $coupon_type = AllCoupons::where('id', $coupon_id)->first()->coupon_type;

                        if ($coupon_type == 2) {
                            $exists = RbdCouponPayment::where('branch_id', $branch_id)->first();
                            if ($exists != null) { //if exists then update
                                $total_payable_amount = $exists->total_amount + 250;
                                RbdCouponPayment::where('branch_id', $branch_id)
                                    ->update([
                                        'total_amount' => $total_payable_amount,
                                    ]);
                            } else { //if doesn't exist then insert
                                RbdCouponPayment::insert([
                                    'branch_id' => $branch_id,
                                    'total_amount' => 250,
                                    'paid_amount' => 0,
                                ]);
                            }
                        }

                        $customer_notification = new CustomerNotification([
                            'user_id' => $customerID,
                            'image_link' => $branch->info->profileImage->partner_profile_image,
                            'notification_text' => $notif_text,
                            'notification_type' => 3,
                            'source_id' => $transaction->id,
                            'seen' => 0,
                        ]);
                        $customer_notification->save();

                        //trigger 'setPusher' function to do live push notification for website
                        event(new offer_availed($customerID));
//                        (new pusherController)->liveDiscountNotification($customerID);
                        //message to send as parameter
                        $message = $notif_text.$branch->info->partner_name.'. Please share your experience.';
                        //send notification to app
                        (new jsonController)->sendFirebaseDiscountNotification($message, $customer, 3, $branch->id, $transaction->id);

                        \DB::commit(); //to do query rollback
                        return response()->json($transaction, 200);
                    } catch (\Exception $e) {
                        \DB::rollBack(); //rollback all successfully executed queries
                        return response()->json(['error' => 'Please try again!'], 201);
                    }
                } else {
                    return response()->json(['error' => 'Something went wrong'], 201);
                }
            } else {
                return response()->json(['error' => 'You do not have the access'], 201);
            }
        }
    }

    public function getBranchOffers(Request $request)
    {
        $date = date('d-m-Y');
        $week_Day = strtolower(date('D'));
        $time = date('H:i');
        $customer_id = $request->get('customer_id');
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['error' => 'Token Mismatched'], 201);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                $user = BranchUser::where('id', $login->id)->with('branchScanner.branch')->first();
                $branch_id = $user->branchScanner->branch->id;
                $branch_offers = [];
                $i = 0;
                $branch_col_offers = BranchOffers::where('branch_id', $branch_id)
                    ->where('active', 1)
                    ->where('selling_point', '=', null)
                    ->with('customizedPoint')
                    ->orderBy('priority', 'DESC')
                    ->get();
                foreach ($branch_col_offers as $col_offer) {
                    $branch_offers[$i++] = $col_offer;
                }
                $redeem_rewards = [];
                $i = 0;
                $_redeems = CustomerRewardRedeem::where('customer_id', $customer_id)->where('used', 0)->get();
                foreach ($_redeems as $key => $customer_redeem) {
                    $reward = BranchOffers::where('id', $customer_redeem->offer_id)->where('branch_id', $branch_id)->first();
                    if ($reward) {
                        $reward->redeem = $_redeems[$key];
                        $redeem_rewards[$i++] = $reward;
                    }
                }

                $branch_offers = array_merge($redeem_rewards, $branch_offers);
                $i = 0;
                foreach ($branch_offers as $branch_offer) {
                    $offer_use_count = TransactionTable::where('offer_id', $branch_offer['id'])->count();
                    $branch_offers[$i]['offer_use_count'] = $offer_use_count;
                    //check expiry
                    $offer_date = $branch_offer['date_duration'];

                    try {
                        if (
                            new DateTime($offer_date[0]['from']) <= new DateTime($date)
                            && new DateTime($offer_date[0]['to']) >= new DateTime($date)
                        ) {
                            $expiry_status = false;
                        } else {
                            $expiry_status = true;
                        }
                    } catch (\Exception $e) {
                        $expiry_status = 1;
                    }
                    $branch_offer['expired'] = $expiry_status;
                    $branch_offer['weekdays'] = $branch_offer['weekdays'][0];
                    $branch_offer['date_duration'] = $branch_offer['date_duration'][0];
                    //check expiry
                    //customize point time wise dynamic
                    if ($branch_offer['customized_point']) {
                        $date_valid = false;
                        $time_valid = false;
                        $week_valid = false;
                        $customize_point_date = $branch_offer['customized_point']['date_duration'];
                        $customize_point_week = $branch_offer['customized_point']['weekdays'];
                        $customize_point_times = $branch_offer['customized_point']['time_duration'];

                        try {
                            if (
                                new DateTime($customize_point_date[0]['from']) <= new DateTime($date)
                                && new DateTime($customize_point_date[0]['to']) >= new DateTime($date)
                            ) {
                                $date_valid = true;
                            }
                        } catch (\Exception $e) {
                        }
                        if (count($customize_point_times) > 0) {
                            foreach ($customize_point_times as $customize_point_time) {
                                try {
                                    if (
                                        new DateTime($customize_point_time['from']) <= new DateTime($time)
                                        && new DateTime($customize_point_time['to']) >= new DateTime($time)
                                    ) {
                                        $time_valid = true;
                                        break;
                                    }
                                } catch (\Exception $e) {
                                }
                            }
                        } else {
                            $time_valid = true;
                        }

                        if ($customize_point_week[0][$week_Day] == 1) {
                            $week_valid = true;
                        }

                        if (! $date_valid || ! $time_valid || ! $week_valid) {
                            $branch_offer['customized_point']['point_multiplier'] = 1;
                        }
                    }
                    $i++;
                    //customize point time wise dynamic
                }
                if ($branch_offers) {
                    $branch_offers = collect($branch_offers)->sortBy('expired')->all();

                    return response()->json($this->makePagination($branch_offers, 'branch_offers'), 200);
                } else {
                    $error = 'Partner does not exist.';

                    return response()->json(['error' => $error], 201);
                }
            } else {
                return response()->json(['error' => 'You do not have the access'], 201);
            }
        }
    }

    public function requestScannerPrize(Request $request)
    {
        $reward_details = $request->post('reward_details');
        $point = $request->post('point');
        $request_comment = $request->post('request_comment');

        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                $user = BranchUser::where('id', $login->id)->with('branchScanner')->first();
                try {
                    \DB::beginTransaction();
                    $scanner_prize = new ScannerPrizeHistory([
                        'text' => $reward_details,
                        'point' => $point,
                        'scanner_id' => $user->branchScanner->id,
                        'status' => 0,
                        'posted_on' => date('Y-m-d H:i:s'),
                        'request_comment' => $request_comment,
                    ]);
                    $scanner_prize->save();
                    $scanner_reward = ScannerReward::where('scanner_id', $user->branchScanner->id)->first();
                    $scanner_reward->decrement('point', $point);
                    $scanner_reward->increment('point_used', $point);
                    (new \App\Http\Controllers\AdminNotification\functionController())->newScannerRequestNotification($scanner_prize);

                    \DB::commit(); //to do query rollback
                } catch (\Exception $e) {
                    \DB::rollBack(); //rollback all successfully executed queries
                    return response()->json(['error' => 'Please try again!'], 201);
                }

                return response()->json($scanner_prize);
            } else {
                return response()->json(['error' => 'You do not have the access'], 201);
            }
        }
    }

    public function scannerPrizeRequestHistory()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                $user = BranchUser::where('id', $login->id)->with('branchScanner')->first();
                $scanner_prize_history = ScannerPrizeHistory::where('scanner_id', $user->branchScanner->id)
                    ->orderBy('posted_on', 'DESC')
                    ->get();

                return response()->json($this->makePagination($scanner_prize_history, 'scanner_prizes'));
            } else {
                return response()->json(['error' => 'You do not have the access'], 201);
            }
        }
    }

    public function getPartnerTransactionHistory()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                $user = BranchUser::where('id', $login->id)->with('branchScanner.branch')->first();
                $transactions = DB::table('transaction_table as tt')
                    ->leftJoin('customer_info as ci', 'ci.customer_id', '=', 'tt.customer_id')
                    ->leftJoin('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
                    ->leftJoin('branch_user as bu', 'bu.id', '=', 'tt.branch_user_id')
                    ->leftJoin('branch_scanner as bs', 'bu.id', '=', 'bs.branch_user_id')
                    ->leftJoin('customer_reward_redeems as crr', 'crr.id', '=', 'tt.redeem_id')
                    ->select(
                        'tt.customer_id',
                        'tt.amount_spent',
                        'tt.posted_on',
                        'tt.discount_amount',
                        'bs.first_name',
                        'bs.last_name',
                        'bs.full_name',
                        'ci.customer_full_name',
                        'ci.customer_profile_image',
                        'ca.customer_username',
                        'tt.offer_id',
                        'crr.quantity as redeem_quantity'
                    )
                    ->where('tt.branch_id', $user->branchScanner->branch->id)
                    ->where('tt.deleted_at', null)
                    ->orderBy('tt.posted_on', 'DESC')
                    ->get();
                $transactions = json_decode(json_encode($transactions), true);
                $i = 0;
                foreach ($transactions as $key => $transaction) {
                    if ($transaction['offer_id'] != null && $transaction['offer_id'] != 0) {
                        $offer = BranchOffers::where('id', $transaction['offer_id'])->first();
                        $transactions[$i]['offer'] = $offer;
                    } else {
                        $transactions[$i]['offer'] = null;
                    }
                    $i++;
                }

                $amount_sum = DB::table('transaction_table')
                    ->where('branch_id', $user->branchScanner->branch->id)
                    ->sum('amount_spent');
                $discount_sum = DB::table('transaction_table')
                    ->where('branch_id', $user->branchScanner->branch->id)
                    ->sum('discount_amount');

                return response()->json(['transactionHistory' => $this->makePagination($transactions, 'transactionHistory'),
                    'amount_sum' => $amount_sum, 'discount_sum' => $discount_sum, ]);
            //
            } else {
                return response()->json(['error' => 'You do not have the access'], 201);
            }
        }
    }

    public function sortPartnerTransactionHistory(Request $request)
    {
        $year = $request->post('year');
        $month = $request->post('month');

        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                $user = BranchUser::where('id', $login->id)->with('branchScanner.branch')->first();
                $transactions = DB::table('transaction_table as tt')
                    ->leftJoin('customer_info as ci', 'ci.customer_id', '=', 'tt.customer_id')
                    ->leftJoin('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
                    ->leftJoin('branch_user as bu', 'bu.id', '=', 'tt.branch_user_id')
                    ->leftJoin('branch_scanner as bs', 'bu.id', '=', 'bs.branch_user_id')
                    ->leftJoin('customer_reward_redeems as crr', 'crr.id', '=', 'tt.redeem_id')
                    ->select(
                        'tt.customer_id',
                        'tt.amount_spent',
                        'tt.posted_on',
                        'tt.discount_amount',
                        'bs.first_name',
                        'bs.last_name',
                        'bs.full_name',
                        'ci.customer_full_name',
                        'ci.customer_profile_image',
                        'ca.customer_username',
                        'tt.offer_id',
                        'crr.quantity as redeem_quantity'
                    )
                    ->where('tt.branch_id', $user->branchScanner->branch->id)
                    ->where('tt.deleted_at', null)
                    ->orderBy('tt.posted_on', 'DESC')
                    ->get();
                $transactions = json_decode(json_encode($transactions), true);
                $i = 0;
                foreach ($transactions as $transaction) {
                    if ($transaction['offer_id'] != null && $transaction['offer_id'] != 0) {
                        $offer = BranchOffers::where('id', $transaction['offer_id'])->first();
                        $transactions[$i]['offer'] = $offer;
                    } else {
                        $transactions[$i]['offer'] = null;
                    }
                    $i++;
                }
                foreach ($transactions as $key => $value) {
                    $ex = explode('-', $value['posted_on']);
                    //checking if DB=>"month,year" & selected=>"month,year" are same or not
                    if ($transactions[$key]['offer']->selling_point) {
                        unset($transactions[$key]);
                    }
                    if ($ex[0] != $year || $ex[1] != $month) {
                        //unset specific array index if not match
                        unset($transactions[$key]);
                    }
                }

                $transactions = array_values($transactions);
                //total spent amount of this customer
                $amount_sum = DB::table('transaction_table')->where('branch_id', $user->branchScanner->branch->id)->where('posted_on', 'like', $year.'-'.$month.'%')->sum('amount_spent');
                //total discount a customer got
                $discount_sum = DB::table('transaction_table')->where('branch_id', $user->branchScanner->branch->id)->where('posted_on', 'like', $year.'-'.$month.'%')->sum('discount_amount');

                return response()->json(['transactionHistory' => $this->makePagination($transactions, 'transactionHistory'),
                    'amount_sum' => $amount_sum, 'discount_sum' => $discount_sum, ]);
            //
            } else {
                return response()->json(['error' => 'You do not have the access'], 201);
            }
        }
    }

    public function getLeaderBoard()
    {
        $leaderBoard = [];
        $previous_day_leaderBoard = [];
        $accounts = PartnerAccount::where('active', 1)->with('branches.info.profileImage')->get();
        $i = 0;
        foreach ($accounts as $account) {
            if ($account->active == 1) {
                foreach ($account->branches as $branch) {
                    if ($branch->active == 1) {
                        $branches[$i] = $branch;
                        $i++;
                    }
                }
            }
        }
        $scan_point = AllAmounts::all();

        //till previous day
        $i = 0;
        foreach ($branches as $branch) {
            $previous_transaction_count = TransactionTable::where('branch_id', $branch->id)->where('posted_on', 'like', date('Y-m').'%')
                ->where('posted_on', 'not like', date('Y-m-d').'%')->count();
            $previous_day_leaderBoard[$i]['profile_image'] = $branch->info->profileImage->partner_profile_image;
            $previous_day_leaderBoard[$i]['partner_name'] = $branch->info->partner_name;
            $previous_day_leaderBoard[$i]['area'] = $branch->partner_area;
            $previous_day_leaderBoard[$i]['branch_id'] = $branch->id;
            $previous_day_leaderBoard[$i]['point'] = $previous_transaction_count * $scan_point[10]['price'];
            $i++;
        }
        $array_point = array_column($previous_day_leaderBoard, 'point');
        $array_name = array_column($previous_day_leaderBoard, 'partner_name');
        array_multisort($array_point, SORT_DESC, $array_name, SORT_ASC, $previous_day_leaderBoard);

        //till current day
        $i = 0;
        foreach ($branches as $branch) {
            $current_transaction_count = TransactionTable::where('branch_id', $branch->id)->where('posted_on', 'like', date('Y-m').'%')->count();
            $leaderBoard[$i]['profile_image'] = $branch->info->profileImage->partner_profile_image;
            $leaderBoard[$i]['partner_name'] = $branch->info->partner_name;
            $leaderBoard[$i]['area'] = $branch->partner_area;
            $leaderBoard[$i]['branch_id'] = $branch->id;
            $leaderBoard[$i]['point'] = $current_transaction_count * $scan_point[10]['price'];
            $leaderBoard[$i]['prev_date'] = Carbon::yesterday()->toDateString();
            for ($j = 0; $j < count($previous_day_leaderBoard); $j++) {
                if ($previous_day_leaderBoard[$j]['branch_id'] == $branch->id) {
                    $leaderBoard[$i]['prev_index'] = $j;
                    break;
                }
            }
            $i++;
        }
        $array_point = array_column($leaderBoard, 'point');
        $array_name = array_column($leaderBoard, 'partner_name');
        array_multisort($array_point, SORT_DESC, $array_name, SORT_ASC, $leaderBoard);

        //monthly leader board prize
        $leaderboard_monthly_prize = LeaderboardPrizes::where('month', Carbon::today()->month)->first();

        return response()->json(['leaderboard' => $this->makePagination($leaderBoard, 'leaderboard_response'), 'leaderboard_prize' => $leaderboard_monthly_prize]);
    }

    public function rbdCouponPayment()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                $user = BranchUser::where('id', $login->id)->with('branchScanner.branch')->first();
                $partner_payment_stats = RbdCouponPayment::where('branch_id', $user->branchScanner->branch->id)->first();

                return response()->json($partner_payment_stats);
            } else {
                return response()->json(['error' => 'You do not have the access'], 201);
            }
        }
    }

    public function getNotificationList()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                $list = (new \App\Http\Controllers\TransactionRequest\functionController())
                    ->getAllBranchUserNotification($login->id);

                return response()->json($this->makePagination($list, 'Notifications'), 200);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    public function getNotificationCount()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                $count = (new \App\Http\Controllers\TransactionRequest\functionController())
                    ->getUnseenMerchantNotificationCount($login->id);

                return response()->json($count, 200);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    public function setSeenNotification(Request $request)
    {
        $id = $request->post('id');
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                $notification = (new \App\Http\Controllers\TransactionRequest\functionController())
                    ->setSeenBranchUserNotification($id);

                return response()->json($notification, 200);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    public function transactionRequestUpdate(Request $request)
    {
        $id = $request->post('id');
        $status = $request->post('status');
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                $request = (new \App\Http\Controllers\TransactionRequest\functionController())
                    ->updateTransactionRequest($id, $status, $login->id, false, PlatformType::android);
                (new \App\Http\Controllers\TransactionRequest\functionController())
                    ->updateMerchantNotificationCount($login->branchScanner->branch_id, null, $id, $status);

                return response()->json($request, 200);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    public function getDashboardMetrics()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                return response()
                    ->json((new \App\Http\Controllers\TransactionRequest\v2\functionController())
                        ->getDashboardMetrics($login->branchScanner->branch_id), 200);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    public function getTopTransactor()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                return response()
                    ->json((new \App\Http\Controllers\TransactionRequest\v2\functionController())
                        ->getTopTransactors($login->branchScanner->branch_id, 5), 200);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    public function getPeakHour(Request $request)
    {
        $from = $request->post('from');
        $to = $request->post('to');
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                return response()
                    ->json((new \App\Http\Controllers\TransactionRequest\v2\functionController())
                        ->getPeakHour($login->branchScanner->branch_id, $from, $to, true), 200);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    public function addOfferRequest(Request $request)
    {
        $comment = $request->post('comment');
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                return response()
                    ->json((new \App\Http\Controllers\TransactionRequest\v2\functionController())
                        ->addPartnerRequest($login->id, $comment, PartnerRequestType::offer_request), 200);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    public function getReviews()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                return response()
                    ->json(($this->makePagination(
                        (new \App\Http\Controllers\Review\functionController())
                            ->getReviews($login->branchScanner->branch_id, null, LikerType::partner), 'reviews')), 200);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    public function getReview(Request $request)
    {
        $review_id = $request->post('review_id');
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                return response()
                    ->json(($this->makePagination(
                        (new \App\Http\Controllers\Review\functionController())
                            ->getReview($login->branchScanner->branch_id, $review_id), 'reviews')), 200);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    public function replyReview(Request $request)
    {
        $review_id = $request->post('review_id');
        $reply = $request->post('reply');
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                return response()
                    ->json((new \App\Http\Controllers\Review\functionController())
                        ->replyToReview($review_id, $reply), 200);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    public function editReplyReview(Request $request)
    {
        $reply_id = $request->post('reply_id');
        $reply = $request->post('reply');
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                return response()
                    ->json((new \App\Http\Controllers\Review\functionController())
                        ->editReviewReply($reply_id, $reply), 200);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    public function deleteReplyReview(Request $request)
    {
        $reply_id = $request->post('reply_id');
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                return response()
                    ->json((new \App\Http\Controllers\Review\functionController())
                        ->deleteReviewReply($reply_id), 200);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    public function likeReview(Request $request)
    {
        $review_id = $request->post('review_id');
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                return response()
                    ->json((new \App\Http\Controllers\Review\functionController())
                        ->likeReview($login->branchScanner->branch_id, LikerType::partner, $review_id), 200);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    public function unlikeReview(Request $request)
    {
        $like_id = $request->post('like_id');
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                (new \App\Http\Controllers\Review\functionController())->unlikeReview($like_id);

                return response()->json(['result' => 'Unliked']);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    public function getDeals()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                $branch_vouchers = (new \App\Http\Controllers\Voucher\functionController())->merchantDealList($login->branchScanner->branch_id);
                // dd($branch_vouchers);
                $branch_vouchers = $this->makePagination($branch_vouchers, 'vouchers');

                return response()->json($branch_vouchers, 200);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    public function getDealPaymentHistory()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                $payment_details = (new \App\Http\Controllers\Voucher\functionController())->dealPaymentHistory($login->branchScanner->branch_id);
                $payment_details = $this->makePagination($payment_details, 'payments');

                return response()->json($payment_details, 200);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    public function dealRedeemed(Request $request)
    {
        $sort = $request->post('sort');
        $year = $request->post('year');
        $month = $request->post('month');

        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['user_not_found'], 404);
        } else {
            if ($login->role >= BranchUserRole::branchScanner) {
                if ($sort == 'true') {
                    if ($year != null && $month != null) {
                        $sel_month = $year.'-'.$month;
                    } elseif ($year != null && $month == null) {
                        $sel_month = $year;
                    } else {
                        return response()->json(['error' => 'Please select value.'], 401);
                    }
                    $transactions = (new \App\Http\Controllers\Voucher\functionController())->branchDealPurchased($login->branchScanner->branch_id, true, $sel_month);
                } else {
                    $transactions = (new \App\Http\Controllers\Voucher\functionController())->branchDealPurchased($login->branchScanner->branch_id, false, null);
                }

                $transactions = $this->makePagination($transactions, 'deal_redeemed');

                return response()->json($transactions, 200);
            } else {
                return response()->json(['error' => 'You do not have the access.'], 401);
            }
        }
    }

    public function merchantVersionControl()
    {
        $data2 = [
            'version' => 21,
            'force_update' => true,
            'app_url' => 'https://play.google.com/store/apps/details?id=com.royaltybd.royaltybd_merchant&hl=en',
        ];

        return response()->json($data2);
    }
}
