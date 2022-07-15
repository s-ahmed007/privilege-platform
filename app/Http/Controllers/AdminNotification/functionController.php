<?php

namespace App\Http\Controllers\AdminNotification;

use App\AdminActivityNotification;
use App\BranchOffers;
use App\BranchScanner;
use App\CardSellerInfo;
use App\CustomerInfo;
use App\CustomerRewardRedeem;
use App\Events\admin_notification;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\AdminNotificationType;
use App\Http\Controllers\Enum\AdminScannerType;
use App\Http\Controllers\Enum\PartnerRequestType;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\Enum\ReviewType;
use App\ScannerPrizeHistory;
use Illuminate\Http\Request;

class functionController extends Controller
{
//    public function __destruct()
//    {
//        $notifications = AdminActivityNotification::where('seen', 0)->orderBy('id', 'DESC')->get();
//        if(count($notifications) > 0){
//            $recent_notification = $notifications->first();
//            $data['notification'] = $recent_notification;
//            $data['notification_count'] = count($notifications);
//            $data['formatted_date'] = date("M d, Y h:i A ", strtotime($recent_notification->created_at));
//            event(new admin_notification($data));
//        }
//    }

    public function throwNotification($notificationId)
    {
        // $notifications = AdminActivityNotification::where('seen', 0)->orderBy('id', 'DESC')->get();
        // $recent_notification = $notifications->first();
        $recent_notification = AdminActivityNotification::where('id', $notificationId)->first();
        $notificationCount = AdminActivityNotification::where('seen', 0)->count();
        $data['notification'] = $recent_notification;
        $data['notification_count'] = $notificationCount;
        $data['formatted_date'] = date('M d, Y h:i A ', strtotime($recent_notification->created_at));
        event(new admin_notification($data));
    }

    public function membershipPurchaseNotification($temp_info)
    {
        $notification = new AdminActivityNotification();
        $notification->text = $temp_info->customer_full_name.' has purchased '.$temp_info->month.' months membership.';
        $notification->source = $temp_info->customer_id;
        $notification->type = AdminNotificationType::membership_purchased;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function trialActivateNotification($temp_info, $customer_history)
    {
        if ($customer_history->seller_id == null && $customer_history->admin_id == null) {
            $text = $temp_info->customer_full_name.' has activated free trial.';
        } elseif ($customer_history->seller_id) {
            $seller = CardSellerInfo::where('id', $customer_history->seller_id)->first();
            $text = $seller->first_name.' '.$seller->last_name.' has activated free trial of '.$temp_info->customer_full_name;
        } else {
            $text = 'Admin has activated free trial of '.$temp_info->customer_full_name;
        }
        $notification = new AdminActivityNotification();
        $notification->text = $text;
        $notification->source = $temp_info->customer_id;
        $notification->type = AdminNotificationType::activated_free_trial;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function renewNotification($temp_info, $isUpgrade, $admin_id = null)
    {
        $text = $isUpgrade == true ? 'upgraded ' : 'renewed ';
        $month = $temp_info->month > 1 ? ' months ' : ' month ';
        $notification = new AdminActivityNotification();
        if ($admin_id) {
            $notification->text = $temp_info->customer_full_name.' is upgraded to '.$temp_info->month.$month.'premium membership by admin.';
        } else {
            $notification->text = $temp_info->customer_full_name.' has '.$text.$temp_info->month.$month.'membership.';
        }
        $notification->source = $temp_info->customer_id;
        $notification->type = AdminNotificationType::renew_membership;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function buyCardAttemptNotification($temp_info)
    {
        $notification = new AdminActivityNotification();
        $notification->text = $temp_info->customer_full_name.' has attempted to buy '.$temp_info->month.' months membership.';
        $notification->source = $temp_info->customer_id;
        $notification->type = AdminNotificationType::buy_card_attempt;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function renewAttemptNotification($temp_info, $isUpgrade)
    {
        $text = $isUpgrade == true ? ' upgrade ' : ' renew ';
        $month = $temp_info->month > 1 ? ' months ' : ' month ';
        $notification = new AdminActivityNotification();
        $notification->text = $temp_info->customer_full_name.' has attempted to'.$text.$temp_info->month.$month.'membership.';
        $notification->source = $temp_info->customer_id;
        $notification->type = AdminNotificationType::renew_attempt;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function newPartnerAddNotification($partner_info)
    {
        $notification = new AdminActivityNotification();
        $notification->text = $partner_info->partner_name.' has been added to Royalty.';
        $notification->source = $partner_info->partner_account_id;
        $notification->type = AdminNotificationType::new_partner_added;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function newBranchAddNotification($partner_branch)//partner_branch->info
    {
        $notification = new AdminActivityNotification();
        $notification->text = 'A new branch - '.$partner_branch->partner_area.' has been added under '.$partner_branch->info->partner_name.'.';
        $notification->source = $partner_branch->id;
        $notification->type = AdminNotificationType::new_branch_added;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function newOfferAddNotification($offer)//offer->branch->info
    {
        $notification = new AdminActivityNotification();
        $notification->text = 'A new offer- '.$offer->offer_description.' has been added to '.$offer->branch->info->partner_name.
            ', '.$offer->branch->partner_area.'.';
        $notification->source = $offer->id;
        $notification->type = AdminNotificationType::new_offer_added;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function newAreaAddNotification($area)//area->division
    {
        $notification = new AdminActivityNotification();
        $notification->text = 'New area - '.$area->area_name.' has been added under '.$area->division->name.'.';
        $notification->source = $area->id;
        $notification->type = AdminNotificationType::new_area_added;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function partnerStatusChangeNotification($partner)//partner_account->info
    {
        $status = $partner->active == 1 ? 'activated' : 'deactivated';
        $notification = new AdminActivityNotification();
        $notification->text = $partner->info->partner_name.' has been '.$status.'.';
        $notification->source = $partner->partner_account_id;
        $notification->type = AdminNotificationType::partner_status_changed;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function partnerBranchStatusChangeNotification($branch)//partner_account->info
    {
        $status = $branch->active == 1 ? 'activated' : 'deactivated';
        $notification = new AdminActivityNotification();
        $notification->text = $branch->info->partner_name.', '.$branch->partner_area.' branch has been '.$status.'.';
        $notification->source = $branch->id;
        $notification->type = AdminNotificationType::branch_status_changed;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function transactionRequestRejectNotification($request)//transaction_request
    {
        $offer = BranchOffers::where('id', $request->offer_id)->with('branch.info')->first();
        if ($request->updated_by == AdminScannerType::accept_tran_req) {
            $text = 'Admin has rejected a transaction request of '.$request->customerInfo->customer_full_name.' at '
                .$offer->branch->info->partner_name.', '.$offer->branch->partner_area.'.';
        } else {
            $branch_scanner = BranchScanner::where('branch_user_id', $request->updated_by)->first();
            $text = $branch_scanner->full_name.' has rejected a transaction request of '.$request->customerInfo->customer_full_name.' at '.
                $offer->branch->info->partner_name.', '.$offer->branch->partner_area.'.';
        }
        $notification = new AdminActivityNotification();
        $notification->text = $text;
        $notification->source = $request->id;
        $notification->type = AdminNotificationType::transaction_request_reject;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function newTransactionNotification($transaction)//transaction->user & transaction->branch->info
    {
        if ($transaction->branch_user_id == AdminScannerType::accept_tran_req) {
            $scanner = 'Admin';
        } else {
            $branch_user = BranchScanner::where('branch_user_id', $transaction->branch_user_id)->first();
            $scanner = $branch_user->full_name;
        }
        if ($transaction->offer_id) {
            $offer = BranchOffers::find($transaction->offer_id);
            $txt = $offer->selling_point != null ? ' has availed a reward at ' : ' has made a transaction at ';
        }
        $notification = new AdminActivityNotification();
        $notification->text = $transaction->customer->customer_full_name.$txt.$transaction->branch->info->partner_name.', '.
            $transaction->branch->partner_area.' by '.$scanner.'.';
        $notification->source = $transaction->id;
        $notification->type = AdminNotificationType::new_transaction;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function newTransactionRequestNotification($request)//transaction->user & transaction->branch->info
    {
        if ($request->redeem_id) {
            $text = ' has requested a reward (';
        } else {
            $text = ' has requested a transaction (';
        }
        $notification = new AdminActivityNotification();
        $notification->text = $request->customerInfo->customer_full_name.$text.$request->offer->offer_description.')'.
            ' at '.$request->offer->branch->info->partner_name.', '.$request->offer->branch->partner_address;
        $notification->source = $request->id;
        $notification->type = AdminNotificationType::new_transaction_request;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function manualTransactionNotification($transaction)//transaction->user & transaction->branch->info
    {
        $notification = new AdminActivityNotification();
        $notification->text = $transaction->customer->customer_full_name.' was manually transacted '.$transaction->transaction_point.' points'.
            ' at '.$transaction->offer->branch->info->partner_name.' - '.$transaction->offer->branch->partner_area.' ('.
            $transaction->offer->offer_description.')'.'.';
        $notification->source = $transaction->id;
        $notification->type = AdminNotificationType::manual_transaction;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function newScannerRequestNotification($request)//scanner_prize_history->branch_scanner->branch->partner_info
    {
        $notification = new AdminActivityNotification();
        $notification->text = $request->branchScanner->full_name.', '.$request->branchScanner->branch->info->partner_name.' has requested '.
            $request->text.'.';
        $notification->source = $request->id;
        $notification->type = AdminNotificationType::new_scanner_request;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function salesAppCardSoldNotification($assigned_card)//assigned_card->seller_account->info
    {
        $notification = new AdminActivityNotification();
        $notification->text = $assigned_card->seller->info->first_name.' '.$assigned_card->seller->info->last_name.' sold a membership of '.
            $assigned_card->month.' months.';
        $notification->source = $assigned_card->id;
        $notification->type = AdminNotificationType::card_sold_from_sales_app;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function reviewNotification($review)//review->user & review->partner
    {
        $notification = new AdminActivityNotification();
        if ($review->heading != 'n/a' && $review->body != 'n/a') {
            $notification->text = $review->customer->customer_full_name.' has rated '.$review->partnerInfo->partner_name
                .', '.$review->transaction->branch->partner_area.' ('.$review->rating.') reviewed '.
                '['.$review->heading.']'.'['.$review->body.']'.'.';
        } else {
            $notification->text = $review->customer->customer_full_name.' has rated '.$review->partnerInfo->partner_name
                .', '.$review->transaction->branch->partner_area.' ('.$review->rating.')'.'.';
        }
        $notification->source = $review->id;
        $notification->type = AdminNotificationType::user_review;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function newRbdRewardAddNotification($reward)//offer
    {
        $notification = new AdminActivityNotification();
        $notification->text = 'A new Royalty reward '.$reward->offer_description.' has been added.';
        $notification->source = $reward->id;
        $notification->type = AdminNotificationType::rbd_reward_added;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function newPartnerRequestNotification($partner)//partner_join_form
    {
        $notification = new AdminActivityNotification();
        $notification->text = $partner->business_name.' has requested to join as a partner.';
        $notification->source = $partner->id;
        $notification->type = AdminNotificationType::new_partner_request;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function userWishNotification($wish)//wish->customer
    {
        $user = CustomerInfo::where('customer_id', $wish->customer_id)->first();
        $notification = new AdminActivityNotification();
        $notification->text = $user->customer_full_name.' wished - '.$wish->comment.'.';
        $notification->source = $wish->id;
        $notification->type = AdminNotificationType::user_wish;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function influencerRequestNotification($influencer)//influencer_request
    {
        $notification = new AdminActivityNotification();
        $notification->text = $influencer->full_name.' requested to join as an influencer.';
        $notification->source = $influencer->id;
        $notification->type = AdminNotificationType::new_influencer_request;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function newContactNotification($contact)//influencer_request
    {
        $notification = new AdminActivityNotification();
        $notification->text = 'You have a new contact from '.$contact->name.'.';
        $notification->source = $contact->id;
        $notification->type = AdminNotificationType::new_contact;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function rbdRewardRequestNotification($reward_redeem_id)//request->user_info & request->offer
    {
        $request = CustomerRewardRedeem::find($reward_redeem_id);
        $points = $request->reward->selling_point * $request->quantity;
        $notification = new AdminActivityNotification();
        $notification->text = $request->customer->customer_full_name.' has redeemed '.$request->quantity.' '.
            $request->reward->offer_description.
            ' with '.$points.'.';
        $notification->source = $request->id;
        $notification->type = AdminNotificationType::rbd_reward_request;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function newPartnerRewardAddNotification($reward)//offer->branch->info
    {
        $notification = new AdminActivityNotification();
        $notification->text = 'A new reward '.$reward->offer_description.' has been added to '.$reward->branch->info->partner_name.'.';
        $notification->source = $reward->id;
        $notification->type = AdminNotificationType::partner_reward_added;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function newUserRegistration($info, $platform)//customer_info
    {
        if ($platform == PlatformType::rbd_admin) {
            $msg = 'Admin opened an account for '.$info->customer_full_name;
        } else {
            $msg = $info->customer_full_name.' has signed up.';
        }
        $notification = new AdminActivityNotification();
        $notification->text = $msg;
        $notification->source = $info->customer_id;
        $notification->type = AdminNotificationType::new_user_added;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function partnerAddedNewPost($post)
    {
        $notification = new AdminActivityNotification();
        $notification->text = $post->partnerBranch->info->partner_name.' has added a new post for the news feed and it is under admin moderation now.';
        $notification->source = $post->id;
        $notification->type = AdminNotificationType::partner_post_added;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function partnerEditedPost($post)
    {
        $notification = new AdminActivityNotification();
        $notification->text = $post->partnerBranch->info->partner_name.' has edited their post for the news feed and it is under admin moderation now.';
        $notification->source = $post->id;
        $notification->type = AdminNotificationType::edit_partner_post;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function partnerDeletedPost($post)
    {
        $notification = new AdminActivityNotification();
        $notification->text = $post->partnerBranch->info->partner_name.' has deleted their post from the news feed.';
        $notification->source = $post->id;
        $notification->type = AdminNotificationType::delete_partner_post;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function partnerOfferRequest($request)
    {
        if ($request->partner_request_type == PartnerRequestType::offer_request) {
            $txt = 'an offer.';
        } else {
            $txt = 'a reward.';
        }
        $notification = new AdminActivityNotification();
        $notification->text = $request->branchUser->branchScanner->full_name.' from '
            .$request->branchUser->branchScanner->branch->info->partner_name.', '.$request->branchUser->branchScanner->branch->partner_area
            .' has requested for '.$txt;
        $notification->source = $request->id;
        $notification->type = AdminNotificationType::partner_offer_request;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function adminAcceptedScannerPrizeRequest($history_id)
    {
        $data = ScannerPrizeHistory::with('branchScanner.branchUser', 'branchScanner.branch.info')->where('id', $history_id)->first();
        $notification = new AdminActivityNotification();
        $notification->text = ' Admin has accepted a reward request of "'.$data->branchScanner->full_name
            .', '.$data->branchScanner->branch->info->partner_name.' ('.$data->branchScanner->branch->partner_area.')"';
        $notification->source = $history_id;
        $notification->type = AdminNotificationType::scanner_prize_req_accept;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function reviewUnderModerationNotification($review, $type)
    {
        if ($type == ReviewType::OFFER) {
            $partner_name = $review->transaction->branch->info->partner_name;
            $partner_area = $review->transaction->branch->partner_area;
        } else {
            $partner_name = $review->dealPurchase->voucher->branch->info->partner_name;
            $partner_area = $review->dealPurchase->voucher->branch->partner_area;
        }
        $msg = 'There is a new review from '.$review->customer->customer_full_name.' at '.$partner_name.', '.$partner_area.' under moderation.';

        $notification = new AdminActivityNotification();
        $notification->text = $msg;
        $notification->source = $review->id;
        $notification->type = AdminNotificationType::review_under_moderation;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function partnerExpiryNotification($partner_id, $msg)
    {
        $notification = new AdminActivityNotification();
        $notification->text = $msg;
        $notification->source = $partner_id;
        $notification->type = AdminNotificationType::partner_expiry_notification;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function newDonationNotification($donation)
    {
        $msg = $donation->name.' has made a donation of '.intval($donation->amount).'tk.';
        $notification = new AdminActivityNotification();
        $notification->text = $msg;
        $notification->source = $donation->id;
        $notification->type = AdminNotificationType::new_donation_added;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function newVoucherAddNotification($voucher)
    {
        $msg = 'A new deal- '.$voucher->heading.' has been added to '.$voucher->branch->info->partner_name.', '.$voucher->branch->partner_area.'.';
        $notification = new AdminActivityNotification();
        $notification->text = $msg;
        $notification->source = $voucher->id;
        $notification->type = AdminNotificationType::new_voucher_added;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function newVoucherPurchaseNotification($ssl_info, $txt)
    {
        $customer = CustomerInfo::where('customer_id', $ssl_info->customer_id)->first();
        $msg = $customer->customer_full_name.' has purchased deals '.$txt;
        $notification = new AdminActivityNotification();
        $notification->text = $msg;
        $notification->source = $ssl_info->id;
        $notification->type = AdminNotificationType::new_voucher_purchased;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function newVoucherRefundRequestNotification($refund, $msg)
    {
        $notification = new AdminActivityNotification();
        $notification->text = $msg;
        $notification->source = $refund->id;
        $notification->type = AdminNotificationType::new_voucher_refund_request;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function voucherRefundAcceptNotification($refund_id, $msg)
    {
        $notification = new AdminActivityNotification();
        $notification->text = $msg;
        $notification->source = $refund_id;
        $notification->type = AdminNotificationType::voucher_refund_request_accept;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }

    public function reviewReplyNotification($msg, $reply_id)
    {
        $notification = new AdminActivityNotification();
        $notification->text = $msg;
        $notification->source = $reply_id;
        $notification->type = AdminNotificationType::review_reply_notification;
        if ($notification->save()) {
            try {
                $this->throwNotification($notification->id);
            } catch (\Exception $ex) {
                \Bugsnag::notifyException($ex);
            }
        }

        return $notification;
    }
}
