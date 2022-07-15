<?php

namespace App\Http\Controllers\TransactionRequest;

use App\AccountKitStats;
use App\AllAmounts;
use App\BranchOffers;
use App\BranchScanner;
use App\BranchUser;
use App\BranchUserNotification;
use App\CustomerInfo;
use App\CustomerNotification;
use App\CustomerPoint;
use App\CustomerRewardRedeem;
use App\CustomerTransactionRequest;
use App\Events\merchant_notification;
use App\Events\offer_availed;
use App\Events\refer_bonus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\AdminScannerType;
use App\Http\Controllers\Enum\BranchUserRole;
use App\Http\Controllers\Enum\Constants;
use App\Http\Controllers\Enum\GlobalTexts;
use App\Http\Controllers\Enum\notificationType;
use App\Http\Controllers\Enum\PartnerBranchNotificationType;
use App\Http\Controllers\Enum\PointType;
use App\Http\Controllers\Enum\TransactionRequestStatus;
use App\Http\Controllers\Firebase\Merchant\SetupController;
use App\Http\Controllers\functionController2;
use App\Http\Controllers\JsonBranchUserController;
use App\Http\Controllers\jsonController;
use App\Http\Controllers\pusherController;
use App\PartnerBranch;
use App\ScannerReward;
use App\TransactionTable;
use DateTime;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class functionController extends Controller
{
    public function onTransactionSendReferPoints($customer_id)
    {
        $info = CustomerInfo::where('customer_id', $customer_id)->with('branchTransactions', 'customerReferrer')->first();
        if ($info && $info->customerReferrer) {
            if (count($info->branchTransactions) == Constants::refer_transaction_count) {
                $this->serveReferPoints($info);
            } elseif (count($info->branchTransactions) < Constants::refer_transaction_count) {
                $refer_value = (new \App\Http\Controllers\functionController)->referValue();
                $transaction_away = Constants::refer_transaction_count - count($info->branchTransactions);
                if ($transaction_away > 1) {
                    $transaction_text = 'transactions';
                } else {
                    $transaction_text = 'transaction';
                }
                $refer_user_notification_text = 'You are '.($transaction_away).' more '.$transaction_text.' away from '.$refer_value.' Referral Credits.';
                (new self())->sendRemainingReferNotification($info, $refer_user_notification_text);
            }
        }
    }

    public function serveReferPoints($customer)
    {
        //customer and referrer follows customer info model
        //change
        $refer_value = (new \App\Http\Controllers\functionController)->referValue();

        $referrar_point = new CustomerPoint([
            'customer_id' => $customer->referrer_id,
            'point' => $refer_value,
            'point_type' => PointType::refer_point,
            'source_id' => $customer->customer_id,
        ]);
        $referrar_point->save();

        $customer_point = new CustomerPoint([
            'customer_id' => $customer->customer_id,
            'point' => $refer_value,
            'point_type' => PointType::referred_by_point,
            'source_id' => $customer->referrer_id,
        ]);
        $customer_point->save();

        $ref_info = CustomerInfo::where('customer_id', $customer->referrer_id)->first();
        $refer_user_notification_text = 'Congratulations! You have earned '.$refer_value.' Referral Credits.';
        $referrar_notification_text = 'Congratulations! '.$customer->customer_full_name.' has availed their '.
            $this->getNumberWithSuffix(Constants::refer_transaction_count).' offer. You have earned '.$refer_value.' Referral Credits.';
        $this->sendReferNotification($customer, $ref_info, $referrar_notification_text, $refer_user_notification_text);
    }

    public function getNumberWithSuffix($num)
    {
        if (! in_array(($num % 100), [11, 12, 13])) {
            switch ($num % 10) {
                // Handle 1st, 2nd, 3rd
                case 1:
                    return $num.'st';
                case 2:
                    return $num.'nd';
                case 3:
                    return $num.'rd';
            }
        }

        return $num.'th';
    }

    public function sendRemainingReferNotification($customer_info, $refer_user_notification_text)
    {
        CustomerNotification::insert([
            'user_id' => $customer_info->customer_id,
            'image_link' => 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/notification/refer.png',
            'notification_text' => $refer_user_notification_text,
            'notification_type' => 10,
            'source_id' => $customer_info->referrer_id,
            'seen' => 0,
        ]);

        //trigger 'livePushNotification' to send live push notification to refer user
        event(new refer_bonus($customer_info->customer_id));

        //send notification to app for refer user
        (new jsonController)->functionSendGlobalPushNotification($refer_user_notification_text, $customer_info, notificationType::refer);
    }

    public function sendReferNotification($customer_info, $ref_info, $referrer_notification_text, $refer_user_notification_text)
    {
        CustomerNotification::insert([
            'user_id' => $customer_info->referrer_id,
            'image_link' => $customer_info->customer_profile_image,
            'notification_text' => $referrer_notification_text,
            'notification_type' => 10,
            'source_id' => $customer_info->customer_id,
            'seen' => 0,
        ]);

        CustomerNotification::insert([
            'user_id' => $customer_info->customer_id,
            'image_link' => 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/notification/refer.png',
            'notification_text' => $refer_user_notification_text,
            'notification_type' => 10,
            'source_id' => $customer_info->referrer_id,
            'seen' => 0,
        ]);

        //send push notification
        //trigger 'livePushNotification' to send live push notification to referrar
        event(new refer_bonus($customer_info->referrer_id));
        //trigger 'livePushNotification' to send live push notification to refer user
        event(new refer_bonus($customer_info->customer_id));

        //send notification to app for referrar
        (new jsonController)->functionSendGlobalPushNotification($referrer_notification_text, $ref_info, notificationType::refer);
        //send notification to app for refer user
        (new jsonController)->functionSendGlobalPushNotification($refer_user_notification_text, $customer_info, notificationType::refer);
    }

    public function createTransactionRequest($platform, $offer_id, $customer_id, $redeem_id = null)
    {
        if ($this->alreadyRequested($offer_id, $customer_id)) {
            return null;
        } else {
            $transaction = new CustomerTransactionRequest();
            $transaction->offer_id = $offer_id;
            $transaction->customer_id = $customer_id;
            $transaction->status = TransactionRequestStatus::PENDING; // pending state
            $transaction->posted_on = date('Y-m-d H:i:s');
            $transaction->redeem_id = $redeem_id;
            $transaction->platform = $platform;
            $transaction->save();

            return $transaction;
        }
    }

    public function alreadyRequested($offer_id, $customer_id)
    {
        $today = date('Y-m-d');
        $transaction_count = CustomerTransactionRequest::where('offer_id', $offer_id)->where('status', TransactionRequestStatus::PENDING)
            ->where('customer_id', $customer_id)
            ->where('posted_on', 'like', $today.'%')
            ->count();
        if ($transaction_count > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function createTransactionRequestNotification($offer_id, $customer_id, $request_id, $redeem_id = null)
    {
        $offer = BranchOffers::where('id', $offer_id)->with('branchScanner.branchUser')->first();
        $customer = CustomerInfo::where('customer_id', $customer_id)->first();
        if ($redeem_id) {
            $notification_text = $customer->customer_full_name.' has requested for a reward ('.
                $offer->offer_description.') .';
        } else {
            $notification_text = $customer->customer_full_name.' has requested for transaction ('.
                $offer->offer_description.') .';
        }
        $branch_scanners = $offer->branchScanner;
        foreach ($branch_scanners as $branch_scanner) {
            $notification = new BranchUserNotification();
            $notification->branch_user_id = $branch_scanner->branch_user_id;
            $notification->customer_id = $customer_id;
            $notification->notification_text = $notification_text;
            $notification->notification_type = PartnerBranchNotificationType::TRANSACTION_REQUEST;
            $notification->source_id = $request_id;
            $notification->seen = 0; //not seen
            $notification->posted_on = date('Y-m-d H:i:s'); //not seen
            $notification->save();
            (new SetupController())->sendMerchantGlobalMessage(
                $notification_text,
                $branch_scanner->branchUser->f_token
            );
        }

        return $branch_scanners;
    }

    //get only scan notification of branch
    public function getAllBranchUserTransactionNotification($branch_user_id)
    {
        $five_days_before = date('Y-m-d', strtotime('-6 days'));
        $notifications = BranchUserNotification::where('branch_user_id', $branch_user_id)
            ->where('notification_type', PartnerBranchNotificationType::TRANSACTION_REQUEST)
            ->where('posted_on', '>', $five_days_before)
            ->orderBy('id', 'DESC')
            ->get();
        foreach ($notifications as $notification) {
            $customer = CustomerInfo::where('customer_id', $notification->customer_id)->first();
            $notification->image = $customer->customer_profile_image;
            $notification->customer_name = $customer->customer_full_name;
            if ($notification->notification_type == PartnerBranchNotificationType::TRANSACTION_REQUEST) {
                $request = CustomerTransactionRequest::where('id', $notification->source_id)->with('offer')->first();
                $request->offer->date_duration = $request->offer->date_duration[0];
                $request->offer->weekdays = $request->offer->weekdays[0];
                $notification->request = $request;
            }
        }

        return $notifications;
    }

    public function getAllBranchUserNotification($branch_user_id, $status = 0)
    {
        if ($branch_user_id == 0) {
            $notifications = BranchUserNotification::where('notification_type', PartnerBranchNotificationType::TRANSACTION_REQUEST)
                ->with('branchUser.branch.info', 'customerInfo.customerHistory', 'transactionRequest.offer',
                    'transactionRequest.transaction', 'transactionRequest.branchScanner')->get();
            $notifications = $notifications->unique('source_id');
            $notifications = $notifications->sortByDesc('id');
            $notifications = collect($notifications)->where('transactionRequest.status', $status);
            $notifications = (new functionController2())->getPaginatedData($notifications, 10);
        } else {
            $five_days_before = date('Y-m-d', strtotime('-6 days'));
            $notifications = BranchUserNotification::where('branch_user_id', $branch_user_id)
                ->where('posted_on', '>', $five_days_before)
                ->orderBy('id', 'DESC')
                ->get();
            foreach ($notifications as $notification) {
                $customer = CustomerInfo::where('customer_id', $notification->customer_id)->first();
                $notification->image = $customer->customer_profile_image;
                $notification->customer_name = $customer->customer_full_name;
                if ($notification->notification_type == PartnerBranchNotificationType::TRANSACTION_REQUEST) {
                    $request = CustomerTransactionRequest::where('id', $notification->source_id)->with('offer', 'redeem')->first();
                    $request->offer->date_duration = $request->offer->date_duration[0];
                    $request->offer->weekdays = $request->offer->weekdays[0];
                    $notification->request = $request;
                } else {
                    $notification->request = null;
                }
            }
        }

        return $notifications;
    }

    public function getUnseenMerchantNotificationCount($branch_user_id)
    {
        $five_days_before = date('Y-m-d', strtotime('-6 days'));
        $notifications = BranchUserNotification::where('branch_user_id', $branch_user_id)
            ->where('seen', 0)
            ->where('posted_on', '>', $five_days_before)
            ->count();

        return $notifications;
    }

    //used for web merchant panel
    public function merchantNotificationCount($branch_id)
    {
        $scanner_ids = BranchScanner::where('branch_id', $branch_id)->pluck('branch_user_id');
        $five_days_before = date('Y-m-d', strtotime('-6 days'));
        $notifications = BranchUserNotification::where('branch_user_id', $scanner_ids[0])
            ->select('source_id')
            ->where('seen', 0)
            ->where('posted_on', '>', $five_days_before)
            ->groupBy('source_id')
            ->get();

        return count($notifications);
    }

    public function setSeenBranchUserNotification($id)
    {
        BranchUserNotification::where('id', $id)
            ->update(['seen' => 1]);
        $notification = BranchUserNotification::where('id', $id)->first();

        return $notification;
    }

    public function updateTransactionRequest($id, $status, $branch_user_id, $rbd_admin = false, $platform = null)
    {
        $updated_by = $rbd_admin == true ? AdminScannerType::accept_tran_req : $branch_user_id;
        CustomerTransactionRequest::where('id', $id)->update([
            'status' => $status,
            'updated_by' => $updated_by,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $request = CustomerTransactionRequest::where('id', $id)->with('offer')->first();
        $request->offer->date_duration = $request->offer->date_duration[0];
        $request->offer->weekdays = $request->offer->weekdays[0];

        if ($status == TransactionRequestStatus::ACCEPTED) {
            $this->confirmOfferTransaction($request->customer_id, $request->offer_id, $branch_user_id, $id, $rbd_admin, $platform);
        }

        return $request;
    }

    public function confirmOfferTransaction($customerID, $offer_id, $branch_user_id, $request_id, $rbd_admin, $platform)
    {
        //get all values from confirm modal
        $tran_request = CustomerTransactionRequest::where('id', $request_id)->first();
        $offer = BranchOffers::where('id', $offer_id)->with('customizedPoint')->first();
        $point = $offer->point;
        $date = date('d-m-Y');
        $week_Day = strtolower(date('D'));
        $time = date('H:i');
        $other_prices = AllAmounts::all();
        $daily_point_limit = $other_prices->where('type', 'daily_point_limit')->first()->price;
        if ($offer->selling_point) {
            $notif_text = 'You availed a reward at '.$offer->branch->info->partner_name.', '.
                $offer->branch->partner_area.'.';
            $notif_type = notificationType::reward;
        } else {
            $notif_type = notificationType::transaction;
        }

        $user = BranchUser::where('id', $branch_user_id)->with('branchScanner.branch')->first();
        $branch_id = $user->branchScanner->branch->id;

        $customer = CustomerInfo::where('customer_id', $customerID)->first();
        $branch = PartnerBranch::where('id', $branch_id)->with('info.profileImage')->first();

        if ($offer->customizedPoint) {
            $date_valid = false;
            $time_valid = false;
            $week_valid = false;
            $customize_point_date = $offer->customizedPoint->date_duration;
            $customize_point_week = $offer->customizedPoint->weekdays;
            $customize_point_times = $offer->customizedPoint->time_duration;

            try {
                if (new DateTime($customize_point_date[0]['from']) <= new DateTime($date)
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
                        if (new DateTime($customize_point_time['from']) <= new DateTime($time)
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
        $today_point = TransactionTable::where('customer_id', $customerID)
            ->where('posted_on', 'like', date('Y-m-d').'%')->sum('transaction_point');
        if ($notif_type == notificationType::transaction) {
            if ($today_point >= $daily_point_limit) {
                $new_point = 0;
                $notif_text = 'You have reached your daily point limit of '.$daily_point_limit.
                    ' after availing an offer at ';
            } else {
                if (($point + $today_point) > $daily_point_limit) {
                    $new_point = $daily_point_limit - $today_point;
                    $notif_text = 'You have reached your daily point limit of '.$daily_point_limit.
                        ' after availing an offer at ';
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

        //one transaction at one day
        $branch_transaction_count = TransactionTable::where('branch_id', $branch_id)
            ->where('customer_id', $customerID)->where('offer_id', $offer_id)
            ->where('posted_on', 'like', date('Y-m-d').'%')->count();
        if (! $offer->selling_point && $branch_transaction_count >= Constants::branch_transaction_count) {
            return response()->json(['error' => GlobalTexts::offer_wise_transaction_error], 401);
        }
        $prev_request = TransactionTable::where([['customer_id', $customerID], ['transaction_request_id', $request_id]])
            ->count();

        if ($prev_request > 0) {
            return response()->json(['error' => 'This request has already been updated.'], 401);
        }
        if ($offer->active == 0) {
            return response()->json(['error' => 'This offer is not valid.'], 401);
        } elseif ($offer->counter_limit != null) {
            $offer_count = TransactionTable::where('offer_id', $offer_id)->count();
            if ($offer->counter_limit <= $offer_count) {
                return response()->json(['error' => 'The offer has reached it\'s limit.'], 401);
            }
        } elseif ($offer->scan_limit != null) {
            $offer_count = TransactionTable::where('offer_id', $offer_id)->where('customer_id', $customerID)->count();
            if ($offer->scan_limit <= $offer_count) {
                return response()->json(['error' => 'This customer has reached the offer limit.'], 401);
            }
        }
        //        return Response::json($customer->firebase_token);
        if ($rbd_admin) {
            $branch_user_id = AdminScannerType::accept_tran_req;
        }
        try {
            \DB::beginTransaction();
            if ($tran_request->redeem_id) {
                CustomerRewardRedeem::where('id', $tran_request->redeem_id)->update(['used' => 1]);
            }
            $transaction = new TransactionTable([
                'branch_id' => $branch_id,
                'customer_id' => $customerID,
                'posted_on' => date('Y-m-d H:i:s'),
                'transaction_point' => $new_point,
                'branch_user_id' => $branch_user_id,
                'offer_id' => $offer_id,
                'redeem_id' => $tran_request->redeem_id,
                'transaction_request_id' => $request_id,
                'platform' => $platform,
            ]);
            $transaction->save();

            //scanner point system
            if ($branch_transaction_count < 1) {
                if (! $rbd_admin) {
                    $scan_point = $other_prices->where('type', 'per_card_scan')->first()->price;
                    ScannerReward::where('scanner_id', $user->branchScanner->id)->increment('point', $scan_point);
                }
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

            \DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            \DB::rollBack(); //rollback all successfully executed queries
            return response()->json(['error' => 'Please try again!'], 409);
        }

        //function to do live push notification for website
        //message to send as parameter
        if ($notif_type == notificationType::transaction) {
            $message = $notif_text.$branch->info->partner_name.', '.$branch->partner_area;
        } else {
            $message = $notif_text;
        }
        //send notification to app
        (new jsonController)
            ->sendFirebaseDiscountNotification($message, $customer, $notif_type, $branch->id, $transaction->id);
        (new self())->onTransactionSendReferPoints($customerID);

        return $transaction;
    }

    public function gen_uuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            // 16 bits for "time_hi_and_version",
            // four most significant bits holds version number 4
            mt_rand(0, 0x0fff) | 0x4000,

            // 16 bits, 8 bits for "clk_seq_hi_res",
            // 8 bits for "clk_seq_low",
            // two most significant bits holds zero and one for variant DCE1.1
            mt_rand(0, 0x3fff) | 0x8000,

            // 48 bits for "node"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public function updateMerchantNotificationCount($branch_id, $request = null, $requestID = null, $status = null)
    {
        $tran_notifications = null;
        $tran_notifications_count = 0;
        if ($request) {
            $offer = BranchOffers::where('id', $request->offer_id)->with('branchScanner')->first();
            $branch_scanners = $offer->branchScanner->first();
            $allNotifications = (new \App\Http\Controllers\TransactionRequest\v2\webController())
                ->getAllNotifications($branch_scanners->branch_user_id);
            $five_days_before = date('Y-m-d', strtotime('-6 days'));
            $tran_notifications = $allNotifications->where('notification_type', PartnerBranchNotificationType::TRANSACTION_REQUEST)
                ->where('posted_on', '>', $five_days_before);
            $tran_notifications_count = $tran_notifications->where('request.status', 0)->count();
//            $tran_notifications = array_values(json_decode(json_encode($tran_notifications), true));
        }
        $notification_count = $this->merchantNotificationCount($branch_id);
        $data['notification_count'] = $notification_count;
        $data['branch_id'] = $branch_id;
        $data['request_id'] = $requestID;
        $data['request'] = $tran_notifications_count;
        $data['status'] = $status;

        event(new merchant_notification($data));
    }

//    public function timeDiffOf10Min($branch_id, $customer_id)
//    {
//        $now = new DateTime(date("Y-m-d h:i:s"));
//        $last_transaction = TransactionTable::where([['branch_id', $branch_id], ['customer_id', $customer_id]])
//                            ->orderBy('id', 'DESC')->first();
//        $then = new DateTime($last_transaction->posted_on);
//        $interval = $now->diff($then);
//        $minute = $interval->format('%i');
//        return array($minute, $now, $then);
//        dd($last_transaction->toArray());
//    }
}
