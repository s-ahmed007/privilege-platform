<?php

namespace App\Http\Controllers\TransactionRequest\v2;

use App\AllAmounts;
use App\BranchOffers;
use App\BranchUser;
use App\CustomerInfo;
use App\CustomerNotification;
use App\CustomerRewardRedeem;
use App\Events\offer_availed;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\BranchUserRole;
use App\Http\Controllers\Enum\Constants;
use App\Http\Controllers\Enum\GlobalTexts;
use App\Http\Controllers\Enum\notificationType;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\jsonController;
use App\Http\Controllers\pusherController;
use App\PartnerBranch;
use App\ScannerReward;
use App\TransactionTable;
use DateTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class webCheckoutController extends Controller
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Check user.
     *
     * @param customerID
     * @return JsonResponse
     */
    public function checkUser()
    {
        $customer_id = $this->request->input('customer_id');
        $today = date('Y-m-d');
        //check if customer id exists or not
        $customer = CustomerInfo::where([['customer_id', $customer_id], ['customer_type', 2]])->with('type')->first();

        if (empty($customer)) {
            $data['error'] = 'Not a Cardholder';

            return response()->json($data);
        } elseif ($customer->expiry_date < $today) {
            $data['error'] = 'The customer\'s membership has expired. Please ask to Renew.';

            return response()->json($data);
        } elseif ($customer->account->isSuspended == 1 || $customer->account->moderator_status != 2) {
            $data['error'] = 'This customer account is suspended';

            return response()->json($data);
        } else {
            $date = date('d-m-Y');
            $week_Day = strtolower(date('D'));
            $time = date('H:i');
            $branch_offers = [];
            $i = 0;
            $branch_col_offers = BranchOffers::where('branch_id', session('branch_id'))
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
                $reward = BranchOffers::where('id', $customer_redeem->offer_id)
                    ->where('branch_id', session('branch_id'))
                    ->where('active', 1)
                    ->first();
                if ($reward) {
                    $reward->redeem = $_redeems[$key];
                    $redeem_rewards[$i++] = $reward;
                }
            }
            $branch_offers = array_merge($redeem_rewards, $branch_offers);
            if (empty($branch_offers)) {
                $data['error'] = 'No offer found';

                return response()->json($data);
            } else {
                $i = 0;
                foreach ($branch_offers as $branch_offer) {
                    $offer_use_count = TransactionTable::where('offer_id', $branch_offer->id)->count();
                    $branch_offers[$i]['offer_use_count'] = $offer_use_count;
                    //check expiry
                    $offer_date = $branch_offer->date_duration;
                    $branch_offers[$i]['offer_to'] = date('F d, Y', strtotime($offer_date[0]['to']));
                    try {
                        if (new DateTime($offer_date[0]['from']) <= new DateTime($date)
                            && new DateTime($offer_date[0]['to']) >= new DateTime($date)
                        ) {
                            $expiry_status = false;
                        } else {
                            $expiry_status = true;
                        }
                    } catch (\Exception $e) {
                        $expiry_status = 1;
                    }
                    $branch_offer->expired = $expiry_status;
                    $branch_offer->weekdays = $branch_offer->weekdays[0];
                    $branch_offer->date_duration = $branch_offer->date_duration[0];
                    //check expiry
                    //customize point time wise dynamic
                    if ($branch_offer->customizedPoint) {
                        $date_valid = false;
                        $time_valid = false;
                        $week_valid = false;
                        $customize_point_date = $branch_offer->customizedPoint->date_duration;
                        $customize_point_week = $branch_offer->customizedPoint->weekdays;
                        $customize_point_times = $branch_offer->customizedPoint->time_duration;

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
                            $branch_offer->customizedPoint->point_multiplier = 1;
                        }
                    }
                    $i++;
                    //customize point time wise dynamic
                }
                usort($branch_offers, function ($left, $right) {
                    return $left['expired'] - $right['expired'];
                });
                $data['offers'] = $branch_offers;
                $data['customer'] = $customer;

                return response()->json($data);
            }
        }
    }

    public function confirmOfferTransaction()
    {
        $customerID = $this->request->post('user_id');
        $offer_id = $this->request->post('offer_id');
        $redeem_id = $this->request->post('redeem_id');
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
            $notif_type = notificationType::transaction;
        }

        if ((new \App\Http\Controllers\TransactionRequest\functionController())->alreadyRequested($offer_id, $customerID)) {
            return response()->json(['error' => GlobalTexts::already_requested_transaction], 401);
        }
        if (session('branch_user_role') >= BranchUserRole::branchScanner) {
            $user = BranchUser::where('id', session('branch_user_id'))->with('branchScanner.branch')->first();
            $branch_id = $user->branchScanner->branch->id;
//            $double_transaction = (new functionController())->timeDiffOf10Min($branch_id, $customerID);

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

            //one transaction at one day
            $branch_transaction_count = TransactionTable::where('branch_id', $branch_id)->where('customer_id', $customerID)->where('offer_id', $offer_id)
                ->where('posted_on', 'like', date('Y-m-d').'%')->count();
            if (! $offer->selling_point && $branch_transaction_count >= Constants::branch_transaction_count) {
                return response()->json(['error' => GlobalTexts::offer_wise_transaction_error], 401);
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
            $uuid = 'web-'.(new \App\Http\Controllers\TransactionRequest\functionController())->gen_uuid();
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
                    'redeem_id' => $redeem_id,
                    'offer_id' => $offer_id,
                    'GUID' => $uuid,
                    'platform' => PlatformType::web,
                ]);
                $transaction->save();

                //scanner point system
                if ($branch_transaction_count < 1) {
                    ScannerReward::where('scanner_id', $user->branchScanner->id)
                        ->increment('point', $other_prices[10]['price']);
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

            //event to do live push notification for website
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
        } else {
            return response()->json(['error' => 'You do not have the access'], 201);
        }
    }
}
