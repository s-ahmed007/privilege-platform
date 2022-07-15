<?php

namespace App\Http\Controllers;

use App\AllAmounts;
use App\B2b2cInfo;
use App\BranchOffers;
use App\BranchUser;
use App\BranchUserNotification;
use App\CustomerAccount;
use App\CustomerInfo;
use App\CustomerNotification;
use App\CustomerRewardRedeem;
use App\Events\like_post;
use App\Events\offer_availed;
use App\Helpers\LengthAwarePaginator;
use App\Http\Controllers\Enum\Constants;
use App\Http\Controllers\Enum\GlobalTexts;
use App\Http\Controllers\Enum\LikerType;
use App\Http\Controllers\Enum\notificationType;
use App\Http\Controllers\Enum\PartnerBranchNotificationType;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\Enum\PostType;
use App\LikePost;
use App\PartnerBranch;
use App\PartnerInfo;
use App\Post;
use App\RoyaltyLogEvents;
use App\ScannerReward;
use App\TransactionTable;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class JsonControllerV3 extends Controller
{
    public function createTransactionRequest(Request $request)
    {
        $customerID = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $offer_id = $request->post('offer_id');
        $redeem_id = $request->post('redeem_id');
        $platform = $request->header('platform', null);
        //one transaction at one day
        $offer = BranchOffers::where('id', $offer_id)->first();
        $branch_transaction_count = TransactionTable::where('branch_id', $offer->branch_id)
            ->where('offer_id', $offer_id)
            ->where('customer_id', $customerID)
            ->where('posted_on', 'like', date('Y-m-d').'%')
            ->count();

        if ($offer->active == 0) {
            return response()->json(['message' => 'This offer is not valid.'], 401);
        } elseif ($offer->counter_limit != null) {
            $offer_count = TransactionTable::where('offer_id', $offer_id)->count();
            if ($offer->counter_limit <= $offer_count) {
                return response()->json(['message' => 'The offer has reached it\'s limit.'], 401);
            }
        } elseif ($offer->scan_limit != null) {
            $offer_count = TransactionTable::where('offer_id', $offer_id)->where('customer_id', $customerID)->count();
            if ($offer->scan_limit <= $offer_count) {
                return response()->json(['message' => 'You have reached the offer limit.'], 401);
            }
        }

        if (! $offer->selling_point && $branch_transaction_count >= Constants::branch_transaction_count) {
            return response()->json(['message' => GlobalTexts::offer_wise_transaction_error], 406);
        } else {
            $transaction = (new \App\Http\Controllers\TransactionRequest\functionController())
                ->createTransactionRequest($platform, $offer_id, $customerID, $redeem_id);
            if ($transaction != null) {
                (new \App\Http\Controllers\TransactionRequest\functionController())
                    ->createTransactionRequestNotification($offer_id, $customerID, $transaction->id, $redeem_id);
                (new \App\Http\Controllers\TransactionRequest\functionController())
                    ->updateMerchantNotificationCount($offer->branch_id, $transaction);
                (new \App\Http\Controllers\AdminNotification\functionController())
                    ->newTransactionRequestNotification($transaction);

                return response()->json($transaction, 200);
            } else {
                return response()->json(['message' => GlobalTexts::pending_transaction_error], 406);
            }
        }
    }

    public function getRoyaltyPoints(Request $request)
    {
        $customerID = JWTAuth::toUser(JWTAuth::getToken())->customer_id;

        return response()->json((new \App\Http\Controllers\Reward\functionController())->getReferPoints($customerID), 200);
    }

    public function getAndroidReport(Request $request)
    {
        $id = $request->post('id');
        $log = RoyaltyLogEvents::where('id', $id)->first();

        return response()->json($log, 200);
    }

    public function markAllNotification(Request $request)
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $result = (new functionController2())->markUserAllNotificationsAsRead($customer_id);

        return response()->json($result, 200);
    }

    public function createSearchStat(Request $request)
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $branch_id = $request->post('branch_id');
        $key = $request->post('key');

        return response()->json((new functionController2())->createSearchStats($customer_id, $branch_id, $key), 200);
    }

    public function checkBranchUserPin(Request $request)
    {
        $branch_id = $request->post('branch_id');
        $pin = $request->post('pin');

        $scanner = (new \App\Http\Controllers\TransactionRequest\v2\functionController())
            ->checkBranchUserPin($branch_id, $pin);
        if ($scanner) {
            return response()->json($scanner, 200);
        } else {
            return response()->json(['message' => GlobalTexts::scanner_pin_error], 404);
        }
    }

    public function confirmOfferTransaction(Request $request)
    {
        //get all values from user
        $customerID = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $offer_id = $request->post('offer_id');
        $redeem_id = $request->post('redeem_id');
        $guid = $request->post('guid');
        $branch_user_id = $request->post('branch_user_id');
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

        $user = BranchUser::where('id', $branch_user_id)->with('branchScanner.branch')->first();
        $branch_id = $offer->branch_id;

        if ((new \App\Http\Controllers\TransactionRequest\functionController())->alreadyRequested($offer_id, $customerID)) {
            return response()->json(['error' => GlobalTexts::already_requested_transaction], 201);
        }
        $customer = CustomerInfo::where('customer_id', $customerID)->first();
        $branch = PartnerBranch::where('id', $branch_id)->with('info.profileImage')->first();
        $prev_transaction = TransactionTable::where('GUID', $guid)->first();

        if ($prev_transaction) {
            //trigger 'setPusher' function to do live push notification for website
            event(new offer_availed($customerID));
            //(new pusherController)->liveDiscountNotification($customerID);
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
            //while ($previousPoint >= 2500) {
            //$previousPoint -= 2500;
            //$point_bonus_counter++;
            //}
            //one transaction at one day
            $branch_transaction_count = TransactionTable::where('branch_id', $branch_id)
                ->where('customer_id', $customerID)
                ->where('offer_id', $offer_id)
                ->where('posted_on', 'like', date('Y-m-d').'%')
                ->count();
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
                $offer_count = TransactionTable::where('offer_id', $offer_id)
                    ->where('customer_id', $customerID)->count();
                if ($offer->scan_limit <= $offer_count) {
                    return response()->json(['error' => 'This customer has reached the offer limit.'], 201);
                }
            }
            //        return Response::json($customer->firebase_token);
            try {
                \DB::beginTransaction();

                if ($redeem_id) {
                    CustomerRewardRedeem::where('id', $redeem_id)->update(['used' => 1]);
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
                (new \App\Http\Controllers\AdminNotification\functionController())
                    ->newTransactionNotification($transaction);

                if ($offer->selling_point) {
                    $merchant_notif_text = $transaction->customer->customer_full_name.' has availed a reward.';
                } else {
                    $merchant_notif_text = $transaction->customer->customer_full_name.' has availed an offer.';
                }

                $notification = new BranchUserNotification();
                $notification->branch_user_id = $user->id;
                $notification->customer_id = $customerID;
                $notification->notification_text = $merchant_notif_text;
                $notification->notification_type = PartnerBranchNotificationType::OFFER_AVAILED;
                $notification->source_id = $transaction->id;
                $notification->seen = 0; //not seen
                $notification->posted_on = date('Y-m-d H:i:s');
                $notification->save();

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
            (new jsonController)
                ->sendFirebaseDiscountNotification($message, $customer, $notif_type, $branch->id, $transaction->id);
            (new \App\Http\Controllers\TransactionRequest\functionController())
                ->onTransactionSendReferPoints($customerID);

            //this not actually for post like notification, only to append new notification to merchant account
            event(new like_post($branch_id));

            return response()->json($transaction, 200);
        }
    }

    public function homepageLink()
    {
        $link = \App\DynamicLink::all();

        return response()->json($link, 200);
    }
}
