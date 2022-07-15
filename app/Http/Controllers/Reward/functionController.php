<?php

namespace App\Http\Controllers\Reward;

use App\AllAmounts;
use App\BranchOffers;
use App\BranchRewardPayment;
use App\CustomerAccount;
use App\CustomerInfo;
use App\CustomerNotification;
use App\CustomerPoint;
use App\CustomerRewardRedeem;
use App\Events\like_review;
use App\Events\reward_notification;
use App\Helpers\LengthAwarePaginator;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\AdminScannerType;
use App\Http\Controllers\Enum\notificationType;
use App\Http\Controllers\Enum\PointType;
use App\Http\Controllers\Enum\ReviewType;
use App\Http\Controllers\Enum\RewardRequiredFieldsType;
use App\Http\Controllers\jsonController;
use App\PartnerBranch;
use App\TransactionTable;
use App\VoucherSslInfo;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class functionController extends Controller
{
    public function branchPayments($branch_id)
    {
        $rewards = ($this)->getSpecificPartnerReward($branch_id, true);
        $total_amount = 0;
        $total_paid = BranchRewardPayment::totalPaid($branch_id);
        $last_paid = BranchRewardPayment::where('branch_id', $branch_id)->latest()->first();
        if (! $last_paid) {
            $last_paid_date = 'N/A';
            $last_paid_amount = 'N/A';
        } else {
            $last_paid_date = date('d F, Y h:i A', strtotime($last_paid->created_at));
            $last_paid_amount = $last_paid->amount;
        }
        if (! $total_paid) {
            $total_paid = 0;
        }
        foreach ($rewards as $reward) {
            $total_amount = $total_amount + ($reward->actual_price * $reward->offer_use_count);
        }
        $total_due = $total_amount - $total_paid;

        $payment = [];
        $payment['due'] = $total_due;
        $payment['paid'] = $total_paid;
        $payment['last_paid'] = $last_paid_date;
        $payment['last_paid_amount'] = $last_paid_amount;

        return $payment;
    }

    public function getSpecificBranchPayment($branch_id)
    {
    }

    public function sendRewardNotification($customer_point, $review_type = null)
    {
        $text = '';

        $credit_text = $customer_point->point > 1 ? 'credits' : 'credit';

        if ($customer_point->point_type == PointType::review_point || $customer_point->point_type == PointType::rating_point) {
            if ($review_type == ReviewType::OFFER) {
                $partner_name = $customer_point->review->transaction->branch->info->partner_name;
                $partner_area = $customer_point->review->transaction->branch->partner_area;
            } else {
                $partner_name = $customer_point->review->dealPurchase->voucher->branch->info->partner_name;
                $partner_area = $customer_point->review->dealPurchase->voucher->branch->partner_area;
            }
        }

        if ($customer_point->point_type == PointType::review_point) {
            $text = 'You have earned '.$customer_point->point.' '.$credit_text.' for reviewing '
                .$partner_name.', '.$partner_area
                .'. Checkout your rewards and redeem them with your available credits.';
        } elseif ($customer_point->point_type == PointType::rating_point) {
            $text = 'You have earned '.$customer_point->point.' '.$credit_text.' for rating '
                .$partner_name.', '.$partner_area
                .'. Checkout your rewards and redeem them with your available credits.';
        } elseif ($customer_point->point_type == PointType::profile_completion_point) {
            $text = 'You have earned '.$customer_point->point.' '.$credit_text.' for completing profile.';
        }

        $customer_notification = new CustomerNotification([
            'user_id' => $customer_point->customer_id,
            'image_link' => 'https://royalty-bd.s3.ap-southeast-1.amazonaws.com/dynamic-images/rbd-offers/ROYALTY-COIN-01_1570021729.png',
            'notification_text' => $text,
            'notification_type' => notificationType::reward,
            'source_id' => $customer_point->id,
            'seen' => 0,
        ]);
        $customer_notification->save();

        $receiver = CustomerInfo::where('customer_id', $customer_point->customer_id)->first();

        event(new reward_notification($customer_point->customer_id));
        //make one event for pusher
        (new jsonController())->functionSendGlobalPushNotification($text, $receiver, notificationType::reward);
    }

    public function getTotalPoints($customer_id)
    {
        $royalty_points = $this->getRoyaltyPoints($customer_id);
        $transaction_points = TransactionTable::totalPoint($customer_id);
        $redeems = $this->getAllRedeemedRewards($customer_id);
        $point_used = 0;
        foreach ($redeems as $redeem) {
            $point_used = $point_used + ($redeem->quantity * $redeem->reward->selling_point);
        }
        if (! $royalty_points) {
            $royalty_points = 0;
        }
        if (! $transaction_points) {
            $transaction_points = 0;
        }
        $tran_points = $transaction_points - $point_used;

        return ['royalty_points' => $royalty_points, 'transaction_points' => $tran_points];
//        return $royalty_points + $transaction_points - $point_used;
    }

    public function getRoyaltyPoints($customer_id, $isLifeTime = false)
    {
        $royalty_points = CustomerPoint::royaltyPoint($customer_id);
        $transaction_points = TransactionTable::totalPoint($customer_id);
        $redeems = $this->getRedeemRewards($customer_id);
        $point_used = 0;
        foreach ($redeems as $redeem) {
            $point_used = $point_used + ($redeem->quantity * $redeem->reward->selling_point);
        }
        $point_used += VoucherSslInfo::where('customer_id', $customer_id)->where('status', 1)->sum('credit');

        if (! $royalty_points) {
            $royalty_points = 0;
        }

        if ($isLifeTime) {
            return $royalty_points + $transaction_points;
        }

        return ($royalty_points + $transaction_points) - $point_used < 0 ? 0 :
            ($royalty_points + $transaction_points) - $point_used;
    }

    public function getReferPoints($customer_id)
    {
        $points = CustomerPoint::referPoint($customer_id);

        if (! $points) {
            $points = 0;
        }

        return $points;
    }

    public function getRatingPoints($customer_id)
    {
        $points = CustomerPoint::ratingPoint($customer_id);

        if (! $points) {
            $points = 0;
        }

        return $points;
    }

    public function getReviewPoints($customer_id)
    {
        $points = CustomerPoint::reviewPoint($customer_id);

        if (! $points) {
            $points = 0;
        }

        return $points;
    }

    public function getProfileCompletePoints($customer_id)
    {
        $points = CustomerPoint::profileCompletePoint($customer_id);

        if (! $points) {
            $points = 0;
        }

        return $points;
    }

    public function getTransactionPoints($customer_id, $isLifeTime = false)
    {
        $transaction_points = TransactionTable::totalPoint($customer_id);
        $redeems = $this->getRedeemRewards($customer_id);
        $point_used = 0;
        foreach ($redeems as $redeem) {
            $point_used = $point_used + ($redeem->quantity * $redeem->reward->selling_point);
        }
        if (! $transaction_points) {
            $transaction_points = 0;
        }
        if ($isLifeTime) {
            return $transaction_points;
        } else {
            return $transaction_points - $point_used;
        }
    }

    public function getBranchPoint($customer_id, $branch_id, $isLifeTime = false)
    {
        if ($branch_id != AdminScannerType::royalty_branch_id) {
            $partner_id = PartnerBranch::where('id', $branch_id)->pluck('partner_account_id')->first();
            $ids = PartnerBranch::where('partner_account_id', $partner_id)->pluck('id');
            $points = TransactionTable::where('customer_id', $customer_id)->whereIn('branch_id', $ids)
                ->sum('transaction_point');
            dd($points);
        } else {
            $points = CustomerPoint::royaltyPoint($customer_id);
        }
        $redeems = DB::table('customer_reward_redeems as crr')
            ->join('branch_offers as bo', 'bo.id', '=', 'crr.offer_id')
            ->select('crr.id', 'crr.quantity', 'bo.selling_point')
            ->where('crr.customer_id', $customer_id)
            ->where('crr.deleted_at', '=', null)
            ->where('bo.branch_id', $branch_id)
            ->get();
        $point_used = 0;
        foreach ($redeems as $redeem) {
            $point_used = $point_used + ($redeem->quantity * $redeem->selling_point);
        }
        if (! $points) {
            $points = 0;
        }

        if ($isLifeTime) {
            $branch_point = $points;
        } else {
            $branch_point = $points - $point_used;
        }

        return $branch_point;
    }

    public function getDealRedeemedPoint($customer_id)
    {
        $points = CustomerPoint::dealRedeemedPoint($customer_id);

        if (! $points) {
            $points = 0;
        }

        return $points;
    }

    public function getDealRefundPoint($customer_id)
    {
        $points = CustomerPoint::dealRefundPoint($customer_id);

        if (! $points) {
            $points = 0;
        }

        return $points;
    }

    public function getPartnerRewards($customer_id, $search_key = null)
    {
        $search_key = $this->clean(Str::lower($search_key));
        $transacted_branch = $this->getTransactedBranches($customer_id);
        $non_transacted_branch = $this->getNonTransactedBranches($customer_id);
        $sorted_branches = array_merge($transacted_branch, $non_transacted_branch);

        foreach ($sorted_branches as $key => $branch) {
            if (count($branch->rewards) < 1) {
                unset($sorted_branches[$key]);
            }
            if ($branch->active != 1 || $branch->account->active != 1) {
                unset($sorted_branches[$key]);
            }
        }

        if ($search_key != null) {
            $sorted_branches = collect($sorted_branches);
            $sorted_branches = $sorted_branches->filter(function ($branch) use ($search_key) {
                return strstr($this->clean(Str::lower($branch->info->partner_name)), $search_key) ||
                    strstr($this->clean(Str::lower($branch->partner_area)), $search_key);
            });
        }

        return $sorted_branches;
    }

    public function clean($string)
    {
        $string = str_replace(' ', '', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    public function getSpecificPartnerReward($branch_id, $for_partner_end)
    {
        $date = date('d-m-Y');

        if ($branch_id == AdminScannerType::royalty_branch_id) {
            $rewards = BranchOffers::where('active', 1)
                ->where('deleted_at', '=', null)
                ->where('selling_point', '!=', null)
                ->with('branch.info')
                ->orderBy('id', 'DESC')
                ->get();
        } else {
            $rewards = BranchOffers::where('branch_id', $branch_id)
                ->where('active', 1)
                ->where('deleted_at', '=', null)
                ->where('selling_point', '!=', null)
                ->with('branch.info')
                ->orderBy('id', 'DESC')
                ->get();
        }

        $collect_rewards = collect();
        foreach ($rewards as $key => $reward) {
            if ($for_partner_end) {
                $offer_use_count = $reward->rewardAvailed->sum('quantity');
            } else {
                $offer_use_count = $reward->rewardRedeems->sum('quantity');
            }

            $reward_date = $reward->date_duration;
            $reward->weekdays = $reward->weekdays[0];
            $reward->offer_use_count = $offer_use_count;
            $reward->date_duration = $reward->date_duration[0];
            try {
                if (
                    new DateTime($reward_date[0]['from']) <= new DateTime($date)
                    && new DateTime($reward_date[0]['to']) >= new DateTime($date)
                ) {
                    $reward->expired = false;
                } else {
                    $reward->expired = true;
                }
                if ($for_partner_end) {
                    $collect_rewards->push($reward);
                } else {
                    if (! $reward->counter_limit) {
                        $collect_rewards->push($reward);
                    } else {
                        if ($reward->counter_limit > $offer_use_count) {
                            $collect_rewards->push($reward);
                        }
                    }
                }
            } catch (\Exception $e) {
            }
        }

        return $collect_rewards->sortBy('expired')->sortByDesc('priority');
    }

    public function getTransactedBranches($customer_id)
    {
        $transactions = TransactionTable::where('customer_id', $customer_id)
            ->with('branch.info.profileImage', 'branch.account')
            ->distinct('branch_id')->select('branch_id')->get();
        $transacted_branches = [];
        $i = 0;
        foreach ($transactions as $transaction) {
            $transacted_branches[$i] = $transaction->branch;
            $i++;
        }
        foreach ($transacted_branches as $branch) {
            $branch_point = TransactionTable::branchPoint($customer_id, $branch->id);
            $branch->branch_point = $branch_point;
            $branch->rewards = $this->getSpecificPartnerReward($branch->id, false);
        }
        $transacted_branches = collect($transacted_branches)->sortByDesc('branch_point')->all();

        return $transacted_branches;
    }

    public function getNonTransactedBranches($customer_id)
    {
        $transactions = TransactionTable::where('customer_id', $customer_id)
            ->with('branch.account')->distinct('branch_id')->select('branch_id')->get();
        $branches = PartnerBranch::with('info.profileImage')->get();
        foreach ($branches as $key1 => $branch) {
            if (count($transactions) > 0) {
                foreach ($transactions as $transaction) {
                    if ($transaction->branch->id == $branch->id) {
                        unset($branches[$key1]);
                        break;
                    } else {
                        $branch_point = TransactionTable::branchPoint($customer_id, $branch->id);
                        $branch->branch_point = $branch_point;
                        $branch->rewards = $this->getSpecificPartnerReward($branch->id, false);
                    }
                }
            } else {
                $branch_point = TransactionTable::branchPoint($customer_id, $branch->id);
                $branch->branch_point = $branch_point;
                $branch->rewards = $this->getSpecificPartnerReward($branch->id, false);
            }
        }

        $branches = $branches->sortBy('info.partner_name')->all();

        return $branches;
    }

    public function getRoyaltyRewards()
    {
        return $this->getSpecificPartnerReward(AdminScannerType::royalty_branch_id, false);
    }

    public function addRewardToProfile($rewards)
    {
        $branch_point = 0;
        $total_points_to_redeem = 0;
        foreach ($rewards as $reward) {
            $offer = BranchOffers::where('id', $reward['offer_id'])->first();
            $customer_redeem_count = TransactionTable::where('customer_id', $reward['customer_id'])->where('branch_id', $offer->branch_id)->count();
            if (isset($reward['scan_limit']) && $customer_redeem_count >= $reward['scan_limit']) {
                if ($reward['scan_limit'] > 1) {
                    $txt_time = 'times';
                } else {
                    $txt_time = 'time';
                }
                $data['error'] = true;
                $data['message'] = 'You can not redeem this reward more than '.$reward['scan_limit'].' '.$txt_time.'.';

                return $data;
            } elseif ($offer->counter_limit && $reward['quantity'] > $offer->counter_limit - $offer->rewardRedeems->sum('quantity')) {
                $data['error'] = true;
                $data['message'] = 'Sorry! No sufficient rewards to complete this request. Please try again.';

                return $data;
            }

            if ($reward['type'] == 'partner') {
                $branch_point = TransactionTable::branchPoint($reward['customer_id'], $offer->branch_id);
                $total_points_to_redeem += $offer->selling_point * $reward['quantity'];
            } else {
                $branch_point = $this->getRoyaltyPoints($reward['customer_id']);
                $total_points_to_redeem += $offer->selling_point * $reward['quantity'];
            }
        }
        if ($branch_point >= $total_points_to_redeem) {
            $saved_reward = [];
            $s_key = 0;
            for ($i = 0; $i < count($rewards); $i++) {
                $offer = BranchOffers::where('id', $rewards[$i]['offer_id'])->first();
                if ($offer) {
                    if (isset($rewards[$i]['required_fields'])) {
                        $reward = new CustomerRewardRedeem([
                            'offer_id' => $rewards[$i]['offer_id'],
                            'customer_id' => $rewards[$i]['customer_id'],
                            'quantity' => $rewards[$i]['quantity'],
                            'required_fields' => json_decode($rewards[$i]['required_fields']),
                            'used' => 0,
                        ]);
                        //if customer has new type of address or what so ever
                        $customer_address = collect(json_decode($rewards[$i]['required_fields']))->where('type', RewardRequiredFieldsType::del_add);
                        if (count(array_values($customer_address->toArray())) > 0) {
                            CustomerInfo::where('customer_id', $rewards[0]['customer_id'])
                                ->update(['customer_address' => array_values($customer_address->toArray())[0]->value]);
                        }
                    } else {
                        $reward = new CustomerRewardRedeem([
                            'offer_id' => $rewards[$i]['offer_id'],
                            'customer_id' => $rewards[$i]['customer_id'],
                            'quantity' => $rewards[$i]['quantity'],
                            'used' => 0,
                        ]);
                    }

                    $reward->save();
                    (new \App\Http\Controllers\AdminNotification\functionController())->rbdRewardRequestNotification($reward->id);

                    $saved_reward[$s_key] = $reward;
                    $s_key++;
                }
            }
        } else {
            return null;
        }

        return $saved_reward;
    }

    public function getExpStatusOfRedeemedReward($exp_date, $days)
    {
        //get expiry date
        $curDate = date('Y-m-d');
        $cur_date = new DateTime($curDate);
        $expiry_date = new DateTime($exp_date);
        $interval = date_diff($cur_date, $expiry_date);
        $daysRemaining = $interval->format('%R%a');

        if ($daysRemaining < $days && $daysRemaining >= '+0') { //10 days to expire
            return 1; //expiring
        } elseif ($daysRemaining <= '+0') { //expired
            return 2; //expired
        } else {
            return 0; //not used
        }
    }

    public function getAllRedeemedRewards($customer_id)
    {
        $redeems = CustomerRewardRedeem::where('customer_id', $customer_id)->where('deleted_at', '=', null)
            ->with('reward.branch.info')->orderBy('id', 'DESC')->get();
        foreach ($redeems as $key => $redeem) {
            $temp_reward = BranchOffers::where('id', $redeem->reward->id)->first();
            $redeem->reward->weekdays = $temp_reward->weekdays[0];
            $redeem->reward->date_duration = $temp_reward->date_duration[0];
            $redeem->reward->expiring = $this->getExpStatusOfRedeemedReward($temp_reward->date_duration[0]['to'], 3);
        }

        return $redeems;
    }

    public function getRedeemRewards($customer_id)
    {
        return CustomerRewardRedeem::where('customer_id', $customer_id)->where('deleted_at', '=', null)->with('reward')->get();
    }

    public function store_rating_review_point($customer_id, $point, $point_type, $source_id)
    {
        $customer_point = new CustomerPoint();
        $customer_point->customer_id = $customer_id;
        $customer_point->point = $point;
        $customer_point->point_type = $point_type;
        $customer_point->source_id = $source_id;
        $customer_point->save();

        return $customer_point;
    }

    public function pointHistoryFromTT($customer_id, $partner)
    {
        $transactions = TransactionTable::where('customer_id', $customer_id)->with('rewardRedeem.reward')->get();
        $result = [];
        if ($partner == 'partner') {
            foreach ($transactions as $key => $transaction) {
                if ($transaction->rewardRedeem['reward']['branch_id'] != AdminScannerType::royalty_branch_id) {
                    if ($transaction->transaction_point != 0 && $transaction->redeem_id == null) {
                        $result[$key]['activity'] = 'Offer availed';
                        $result[$key]['status'] = 'availed';
                        $result[$key]['point'] = $transaction->transaction_point;
                        $date = date('d F, Y', strtotime($transaction->posted_on));
                        $result[$key]['date'] = $date;
                    } elseif ($transaction->transaction_point == 0 && $transaction->redeem_id != null) {
                        $result[$key]['activity'] = 'Reward redeemed';
                        $result[$key]['status'] = 'used';
                        $result[$key]['point'] = $transaction->rewardRedeem['quantity'] * $transaction->rewardRedeem['reward']['selling_point'];
                        $date = date('d F, Y', strtotime($transaction->posted_on));
                        $result[$key]['date'] = $date;
                    }
                }
            }
        } elseif ($partner == 'royalty') {
            foreach ($transactions as $key => $transaction) {
                if ($transaction->rewardRedeem['reward']['branch_id'] == AdminScannerType::royalty_branch_id) {
                    $result[$key]['activity'] = 'Reward redeemed';
                    $result[$key]['status'] = 'used';
                    $result[$key]['point'] = $transaction->rewardRedeem['quantity'] * $transaction->rewardRedeem['reward']['selling_point'];
                    $date = date('d F, Y', strtotime($transaction->posted_on));
                    $result[$key]['date'] = $date;
                }
            }
        }

        return $result;
    }

    public function pointEarnHistory($customer_id)
    {
        $transactions = TransactionTable::where('customer_id', $customer_id)->with('rewardRedeem.reward', 'branch.info')->get();
        $result = [];
        foreach ($transactions as $key => $transaction) {
            if ($transaction->transaction_point != 0 && $transaction->redeem_id == null) {
                $result[$key]['activity'] = 'Transacted at '.$transaction->branch->info->partner_name.', '.$transaction->branch->partner_area;
                $result[$key]['status'] = 'availed';
                $result[$key]['point'] = $transaction->transaction_point;
                $result[$key]['icon'] = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/accounts/user-account/credit-history/transaction.png';
                $date = date('d F, Y', strtotime($transaction->posted_on));
                $time = date('h:i A', strtotime($transaction->posted_on));
                //$timestamp = date("d F, Y h:i A", strtotime($transaction->posted_on));
                $result[$key]['date'] = $date;
                $result[$key]['time'] = $time;
                $result[$key]['timestamp'] = $transaction->posted_on;
            }
        }

        return $result;
    }

    public function royaltyPointHistory($customer_id)
    {
        $customer_points = CustomerPoint::where('customer_id', $customer_id)->with('sourceCustomerInfo', 'review.partnerInfo')->get();
        $result = [];
        foreach ($customer_points as $key => $value) {
            if ($value->point_type == PointType::refer_point) {
                $result[$key]['activity'] = 'Referred '.$value->sourceCustomerInfo->customer_full_name;
                $result[$key]['status'] = 'availed';
                $result[$key]['point'] = $value->point;
                $result[$key]['icon'] = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/accounts/user-account/credit-history/refer.png';
            } elseif ($value->point_type == PointType::referred_by_point) {
                $result[$key]['activity'] = 'Referred by '.$value->sourceCustomerInfo->customer_full_name;
                $result[$key]['status'] = 'availed';
                $result[$key]['point'] = $value->point;
                $result[$key]['icon'] = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/accounts/user-account/credit-history/refer.png';
            } elseif ($value->review && $value->point_type == PointType::rating_point) {
                if ($value->review->transaction) {
                    $partner_area = $value->review->transaction->branch->partner_area;
                } else {
                    $partner_area = $value->review->dealPurchase->voucher->branch->partner_area;
                }
                $result[$key]['activity'] = 'Rated '.$value->review->partnerInfo->partner_name.', '.$partner_area;
                $result[$key]['status'] = 'availed';
                $result[$key]['point'] = $value->point;
                $result[$key]['icon'] = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/accounts/user-account/credit-history/rate.png';
            } elseif ($value->review && $value->point_type == PointType::review_point) {
                if ($value->review->transaction) {
                    $partner_area = $value->review->transaction->branch->partner_area;
                } else {
                    $partner_area = $value->review->dealPurchase->voucher->branch->partner_area;
                }
                $result[$key]['activity'] = 'Reviewed '.$value->review->partnerInfo->partner_name.', '.$partner_area;
                $result[$key]['status'] = 'availed';
                $result[$key]['point'] = $value->point;
                $result[$key]['icon'] = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/accounts/user-account/credit-history/review.png';
            } elseif ($value->point_type == PointType::profile_completion_point) {
                $result[$key]['activity'] = 'Completed profile';
                $result[$key]['status'] = 'availed';
                $result[$key]['point'] = $value->point;
                $result[$key]['icon'] = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/accounts/user-account/credit-history/gender.png';
            } elseif ($value->point_type == PointType::deal_refund_point) {
                $result[$key]['activity'] = 'Deal refund for "'.$value->dealRefund->purchaseDetails->voucher->heading.' of '.$value->dealRefund->purchaseDetails->voucher->branch->info->partner_name.', '.$value->dealRefund->purchaseDetails->voucher->branch->partner_area.'"';
                $result[$key]['status'] = 'availed';
                $result[$key]['point'] = $value->point;
                $result[$key]['icon'] = 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/home-page/category/dealswebicon.png';
            } elseif ($value->point_type == PointType::deal_redeem_point) {
                $result[$key]['activity'] = 'Deal redeemed at '.$value->dealPurchaseDetails->voucher->branch->info->partner_name.', '.$value->dealPurchaseDetails->voucher->branch->partner_area.'"';
                $result[$key]['status'] = 'availed';
                $result[$key]['point'] = $value->point;
                $result[$key]['icon'] = 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/home-page/category/dealswebicon.png';
            }
            if (isset($result[$key])) {
                $date = date('d F, Y', strtotime($value->created_at));
                $time = date('h:i A', strtotime($value->created_at));
                $result[$key]['date'] = $date;
                $result[$key]['time'] = $time;
                $result[$key]['timestamp'] = $value->created_at;
            }
        }

        return $result;
    }

    public function pointHistory($customer_id)
    {
//        $partner_history = $this->pointHistoryFromTT($customer_id, 'partner');
//
//        $rbd_used_history = $this->pointHistoryFromTT($customer_id, 'royalty');

        $rbd_point_earn_history = $this->royaltyPointHistory($customer_id);
        $transaction_point_earn_history = $this->pointEarnHistory($customer_id);
        $all_earn_history = array_merge($rbd_point_earn_history, $transaction_point_earn_history);
        $all_earn_history = collect($all_earn_history)->sortByDesc('timestamp');

        //pagination
        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        // Create a new Laravel collection from the array data
        $itemCollection = collect($all_earn_history);
        // Define how many items we want to be visible in each page
        $perPage = 20;
        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();
        // Create our paginator and pass it to the view
        $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);
        $paginatedItems->setPath('');
        $paginatedItems->setArrayName('history');

        return $paginatedItems;
    }

    public function collectAllPoints($customer_id, $life_time)
    {
        $points = [];
        $points['royalty_points'] = (new self())->getRoyaltyPoints($customer_id, $life_time);
        $points['refer_points'] = (new self())->getReferPoints($customer_id);
        $points['profile_complete_points'] = (new self())->getProfileCompletePoints($customer_id);
        $points['rating_points'] = (new self())->getRatingPoints($customer_id);
        $points['review_points'] = (new self())->getReviewPoints($customer_id);
        $points['transaction_points'] = (new self())->getTransactionPoints($customer_id, $life_time);
        $points['activity_points'] = 0;
        $points['deal_redeemed_points'] = (new self())->getDealRedeemedPoint($customer_id);
        $points['deal_refund_points'] = (new self())->getDealRefundPoint($customer_id);
        $points = collect($points);

        return $points;
    }

    public function profileCompletionPercentage($customer_id)
    {
        $percent = 70;
        $customer = CustomerInfo::where('customer_id', $customer_id)->first();
        if ($customer) {
            if ($customer->customer_gender) {
                $percent += 10;
            }
            if ($customer->customer_dob) {
                $percent += 10;
            }
            if ($customer->customer_profile_image != 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/registration/user.png') {
                $percent += 10;
            }
        } else {
            $percent = 0;
        }

        return $percent;
    }

    public function addProfileCompletionPoint($customer_id)
    {
        $percent = $this->profileCompletionPercentage($customer_id);
        $exist_point = CustomerPoint::where('customer_id', $customer_id)->where('point_type', PointType::profile_completion_point)->first();
        $customer_info = CustomerInfo::where('customer_id', $customer_id)->first();
        if (! $exist_point && $percent >= 100) {
            $customer_point = new CustomerPoint();
            $customer_point->customer_id = $customer_id;
            $customer_point->point = 10;
            $customer_point->point_type = PointType::profile_completion_point;
            $customer_point->source_id = $customer_info->id; //customer_info id
            $customer_point->save();

            $this->sendRewardNotification($customer_point);
        }

        return $percent;
    }

    public function inviteFriendsWithRefer($customer_id)
    {
        $customer = CustomerInfo::where('customer_id', $customer_id)->first();
        $refer_point = AllAmounts::where('type', 'refer_bonus')->first()->price;

        return 'Get amazing discounts and offers at your favourite places with Royalty. Use '.$customer->customer_full_name.'\'s referral code "'.
            $customer->referral_number.'" to earn '.$refer_point.' Royalty Credits.'."\n\n".'Download the app now: https://royaltybd.com/rbdapp';
    }
}
