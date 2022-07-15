<?php

namespace App\Http\Controllers\Review;

use App\AllAmounts;
use App\BranchUserNotification;
use App\CustomerInfo;
use App\CustomerNotification;
use App\Events\append_notification;
use App\Events\like_review;
use App\Events\review_reply_notification;
use App\Http\Controllers\adminController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\BranchUserRole;
use App\Http\Controllers\Enum\LikerType;
use App\Http\Controllers\Enum\notificationType;
use App\Http\Controllers\Enum\PartnerBranchNotificationType;
use App\Http\Controllers\Enum\PointType;
use App\Http\Controllers\Enum\ReviewType;
use App\Http\Controllers\Firebase\Merchant\SetupController;
use App\Http\Controllers\jsonController;
use App\Http\Controllers\Reward\functionController as rewardFunctionController;
use App\LikesReview;
use App\PartnerBranch;
use App\Rating;
use App\Review;
use App\ReviewComment;
use App\TransactionTable;
use App\VoucherHistory;
use App\VoucherPurchaseDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class functionController extends Controller
{
    public function getReviews($branch_id, $customer_id, $type)
    {
        //offer reviews
        $transactions = TransactionTable::where('branch_id', $branch_id)
            ->where('review_id', '!=', null)
            ->orderBy('id', 'DESC')
            ->with('review.customerInfo', 'review.partnerInfo.profileImage', 'review.likes', 'review.comments')
            ->get();
        $transactions = collect($transactions)->where('review', '!=', null);
        $transactions = collect($transactions)->sortByDesc('review.id');
        if ($type == LikerType::partner) {
            $customer_id = $branch_id;
        }
        $tran_reviews = $transactions->map(function ($transaction) use ($customer_id, $type) {
            $like = $transaction->review->likes
                ->where('liker_id', $customer_id)
                ->where('liker_type', $type)
                ->first();
            if ($like) {
                $liked = 1;
                $source_id = $like->id;
            } else {
                $liked = 0;
                $source_id = 0;
            }
            $transaction->review->previous_like = $liked;
            $transaction->review->previous_like_id = $source_id;
            $transaction->review->customerInfo->total_review = Review::where('customer_id', $transaction->customer_id)->count();

            return $transaction->review;
        });

        //deal reviews
        $deal_purchased = VoucherHistory::where('branch_id', $branch_id)->with('voucherDetails.review', 'voucherDetails.review.customerInfo', 'voucherDetails.review.partnerInfo.profileImage', 'voucherDetails.review.likes', 'voucherDetails.review.comments')->get();

        $deal_reviews = collect();
        foreach ($deal_purchased as $key => $row) {
            foreach ($row->voucherDetails as $key => $value) {
                if ($value->review != null) {
                    $like = LikesReview::where('liker_id', $customer_id)->where('liker_type', $type)->first();
                    if ($like) {
                        $liked = 1;
                        $source_id = $like->id;
                    } else {
                        $liked = 0;
                        $source_id = 0;
                    }
                    $value->review->previous_like = $liked;
                    $value->review->previous_like_id = $source_id;
                    $value->review->customerInfo->total_review = Review::where('customer_id', $row->customer_id)->count();
                    $deal_reviews->push($value->review);
                }
            }
        }
        $all_reviews = $tran_reviews->merge($deal_reviews);
        $all_reviews = $all_reviews->sortByDesc('posted_on');

        return $all_reviews;
    }

    public function getReview($branch_id, $review_id)
    {
        $transactions = TransactionTable::where('branch_id', $branch_id)
            ->where('review_id', $review_id)
            ->with('review.customerInfo', 'review.partnerInfo.profileImage', 'review.likes', 'review.comments')
            ->first();

        $transactions = collect($transactions)->where('review', '!=', null);
        if (count($transactions) > 0) {
            $review = $transactions->pluck('review');
        } else {
            $review = Review::where('id', $review_id)->with('customerInfo', 'partnerInfo.profileImage', 'likes', 'comments')->first();
        }
        $like = $review->likes->where('liker_id', $branch_id)->where('liker_type', LikerType::partner)->first();
        if ($like) {
            $liked = 1;
            $source_id = $like->id;
        } else {
            $liked = 0;
            $source_id = 0;
        }
        $review->previous_like = $liked;
        $review->previous_like_id = $source_id;
        $result = [];
        array_push($result, $review);

        return $result;
    }

    public function unlikeReview($like_id)
    {
        $like = LikesReview::find($like_id);
        $like->delete();
        $this->updatePusher($like);
    }

    public function updatePusher($like)
    {
        //for pusher
        //total likes of this review
        $likes_of_a_review = DB::table('likes_review')->where('review_id', $like->review_id)->count();
        $review = Review::find($like->review_id);
        //total likes of the customer of this review
        $likes_of_a_user = (new \App\Http\Controllers\functionController)->likeNumber($review->customer_id);
        $data['review_id'] = $like->review_id;
        $data['customer_id'] = $review->customer_id;
        $data['source_id'] = $like->id;
        $data['liker_id'] = $like->liker_id;
        $data['total_likes_of_a_review'] = $likes_of_a_review;
        $data['total_likes_of_a_user'] = $likes_of_a_user;

        event(new append_notification($data));
        //for pusher end
    }

    public function likeReview($liker_id, $liker_type, $review_id)
    {
        if ($liker_type == LikerType::partner) {
            $partner = PartnerBranch::where('id', $liker_id)->with('info.profileImage')->first();
            $likes_review_count = LikesReview::where('review_id', $review_id)
                ->where('liker_id', $liker_id)->where('liker_type', LikerType::partner)->first();
            $image = $partner->info->profileImage->partner_profile_image;
            $name = $partner->info->partner_name.', '.$partner->partner_area;
        } else {
            $customer = CustomerInfo::where('customer_id', $liker_id)->first();
            $likes_review_count = LikesReview::where('review_id', $review_id)
                ->where('liker_id', $liker_id)->where('liker_type', LikerType::customer)->first();
            $image = $customer->customer_profile_image;
            $name = $customer->customer_full_name;
        }

        if (! $likes_review_count) {
            $like = new LikesReview([
                'review_id' => $review_id,
                'liker_id' => $liker_id,
                'liker_type' => $liker_type,
            ]);
            $like->save();
            $this->sendLikeReviewNotification($review_id, $image, $like, $name);

            return $like;
        } else {
            return $likes_review_count;
        }
    }

    public function sendLikeReviewNotification($review_id, $image, $like, $name)
    {
        $review = Review::where('id', $review_id)->with('customer')->first();
        $notification_text = 'liked your review.';
        $notification = new CustomerNotification();
        $notification->user_id = $review->customer_id;
        $notification->image_link = $image;
        $notification->notification_text = $notification_text;
        $notification->notification_type = notificationType::like_review;
        $notification->source_id = $like->id;
        $notification->seen = 0;
        $notification->save();
        $message = $name.' '.$notification_text;
        //for pusher
        //total likes of this review
        $likes_of_a_review = DB::table('likes_review')->where('review_id', $review_id)->count();
        //total likes of the customer of this review
        $likes_of_a_user = (new \App\Http\Controllers\functionController)->likeNumber($review->customer_id);
        $data['review_id'] = $review_id;
        $data['customer_id'] = $review->customer_id;
        $data['liker_id'] = $like->liker_id;
        $data['source_id'] = $like->id;
        $data['total_likes_of_a_review'] = $likes_of_a_review;
        $data['total_likes_of_a_user'] = $likes_of_a_user;
        event(new like_review($data));
        //for pusher end
        (new jsonController())->functionSendGlobalPushNotification($message, $review->customer, notificationType::like_review);
    }

    public function getRatings($branch_id)
    {
        $tran_reviews = $this->getOfferReviews($branch_id);
        $deal_reviews = $this->getDealReviews($branch_id);
        $all_reviews = $tran_reviews->merge($deal_reviews);

        $rating = [];
        $rating_counter = [];
        for ($i = 1; $i <= 5; $i++) {
            //total specific star partner got
            $totalStars = $all_reviews->where('rating', $i)->count();
            //percentage of the specific star
            if (count($all_reviews) > 0) {
                $starPercentage = $totalStars / count($all_reviews) * 100;
                $rating[$i.'_star'] = $starPercentage;
            } else {
                $rating[$i.'_star'] = 0;
            }
            $rating_counter[$i.'_star'] = $totalStars;
        }
        $rating_counter['total'] = count($all_reviews);

        $rating['average_rating'] = $this->getAverageBranchRating($branch_id);
        $rating['rating_counter'] = $rating_counter;

        return $rating;
    }

    public function getAverageBranchRating($branch_id)
    {
        $tran_reviews = $this->getOfferReviews($branch_id);
        $deal_reviews = $this->getDealReviews($branch_id);
        $all_reviews = $tran_reviews->merge($deal_reviews);

        if (count($all_reviews) > 0) {
            $average = collect($all_reviews)->sum('rating');
            $average = $average / count($all_reviews);
            $average = round($average, 2);
        } else {
            $average = 0;
        }

        return $average;
    }

    public function getOfferReviews($branch_id)
    {
        $transactions = TransactionTable::where('branch_id', $branch_id)
            ->where('review_id', '!=', null)
            ->with('review')
            ->get();
        $reviews = collect($transactions)->where('review', '!=', null)->pluck('review');

        return $reviews;
    }

    public function getDealReviews($branch_id)
    {
        $deal_purchased = VoucherHistory::where('branch_id', $branch_id)->with('voucherDetails.review')->get();
        $reviews = collect();
        foreach ($deal_purchased as $key => $row) {
            foreach ($row->voucherDetails as $key => $value) {
                if ($value->review != null) {
                    $reviews->push($value->review);
                }
            }
        }

        return $reviews;
    }

    public function setReviewPostNotification($transaction_id, $customer, $review_type)
    {
        if ($review_type == ReviewType::OFFER) {
            $transaction = TransactionTable::where('id', $transaction_id)->first();
            $branch_id = $transaction->branch_id;
        } else {
            $transaction = VoucherPurchaseDetails::where('id', $transaction_id)->first();
            $branch_id = $transaction->voucher->branch_id;
        }
        $branch = PartnerBranch::where('id', $branch_id)->with('branchScanner.branchUser')->first();
        $review = $transaction->review;

        if (($review->heading == 'n/a' && $review->comment == 'n/a')) {
            $notification_text = $customer->customer_full_name.' has rated '.$review->rating.' star to your profile.';
        } else {
            $notification_text = $customer->customer_full_name.' has posted a review on your profile.';
        }
        foreach ($branch->branchScanner as $branch_scanner) {
            if ($branch_scanner->branchUser->role == BranchUserRole::branchOwner) {
                $notification = new BranchUserNotification();
                $notification->branch_user_id = $branch_scanner->branch_user_id;
                $notification->customer_id = $customer->customer_id;
                $notification->notification_text = $notification_text;
                $notification->notification_type = PartnerBranchNotificationType::REVIEW_POST;
                $notification->source_id = $review->id;
                $notification->seen = 0; //not seen
                $notification->posted_on = date('Y-m-d H:i:s'); //not seen
                $notification->save();
                (new SetupController())->sendMerchantGlobalMessage($notification_text, $branch_scanner->branchUser->f_token);
            }
        }
    }

    public function replyToReview($review_id, $reply)
    {
        $comment = new ReviewComment();
        $comment->review_id = $review_id;
        $comment->comment = $reply;
        $comment->comment_type = 'partner';
        $comment->save();

        return ReviewComment::find($comment->id);
    }

    public function acceptReviewReplyModeration($comment_id)
    {
        $comment = ReviewComment::find($comment_id);
        $comment->moderation_status = 1;
        $comment->save();
        if ($comment->review->transaction) {
            $branch = $comment->review->transaction->branch;
        } else {
            $branch = $comment->review->dealPurchase->voucher->branch;
        }
        $this->sendReplyNotification($comment->review, $branch);
    }

    public function rejectReviewReplyModeration($comment_id)
    {
        $comment = ReviewComment::find($comment_id);
        $comment->delete();
    }

    public function editReviewReply($reply_id, $new_reply)
    {
        $reply = ReviewComment::find($reply_id);
        $reply->comment = $new_reply;
        $reply->save();

        return $reply;
    }

    public function deleteReviewReply($reply_id)
    {
        $reply = ReviewComment::find($reply_id);
        $reply->delete();

        return 'Success';
    }

    public function sendReplyNotification($review, $branch)
    {
        $notification_text = $branch->info->partner_name.', '.$branch->partner_area.' replied to your review.';
        $notification = new CustomerNotification();
        $notification->user_id = $review->customer_id;
        $notification->image_link = $branch->info->profileImage->partner_profile_image;
        $notification->notification_text = $notification_text;
        $notification->notification_type = notificationType::reply_review;
        $notification->source_id = $review->id;
        $notification->seen = 0;
        $notification->save();

        $customer = DB::table('customer_info')->where('customer_id', $review->customer_id)->first();
        event(new review_reply_notification($review->customer_id));
        //send notification to app
        (new jsonController)->functionSendGlobalPushNotification($notification_text, $customer);
    }

    public function saveReview($partner_account_id, $customer_id, $star, $heading, $platform, $comment, $transaction_id, $pending, $review_type)
    {
        if (($heading && $comment) == null) {
            $heading = 'n/a';
            $comment = 'n/a';
        }

        if ($review_type == ReviewType::OFFER) {//offer review
            $transaction = TransactionTable::find($transaction_id);
            if ($transaction->review_id) {
                $prev_review = $transaction->deleteReviewWithTran;
                $prev_review->previous_like = 0;
                $prev_review->previous_like_id = 0;

                return $prev_review;
            }
        } else {//deal review
            $transaction = VoucherPurchaseDetails::find($transaction_id);
            if ($transaction->review_id) {
                $prev_review = $transaction->deleteReviewWithTran;
                $prev_review->previous_like = 0;
                $prev_review->previous_like_id = 0;

                return $prev_review;
            }
        }

        $new_review = new Review([
            'partner_account_id' => $partner_account_id,
            'customer_id' => $customer_id,
            'rating' => $star,
            'heading' => $heading,
            'platform' => $platform,
            'body' => $comment,
        ]);
        $new_review->save();

        $transaction->review_id = $new_review->id;
        $transaction->save();

        $review_response = Review::where('id', $new_review->id)
            ->with('comments', 'likes', 'customer', 'partnerInfo.profileImage')
            ->first();
        $review_response->previous_like = 0;
        $review_response->previous_like_id = 0;

        if ($pending) {
            (new \App\Http\Controllers\AdminNotification\functionController())->reviewUnderModerationNotification($review_response, $review_type);
        }

        return $review_response;
    }

    public function acceptReviewModeration($review_id, $from_admin)
    {
        try {
            \DB::beginTransaction();

            $review = Review::find($review_id);
            $review->moderation_status = 1;
            $review->save();
            $customer = CustomerInfo::Where('customer_id', $review->customer_id)->first();
            $transaction = TransactionTable::where('review_id', $review->id)->first();

            if ($transaction) {
                $partner_name = $transaction->branch->info->partner_name;
                $partner_area = $transaction->branch->partner_area;
                $review_type = ReviewType::OFFER;
            } else {
                $transaction = VoucherPurchaseDetails::where('review_id', $review->id)->first();
                $partner_name = $transaction->voucher->branch->info->partner_name;
                $partner_area = $transaction->voucher->branch->partner_area;
                $review_type = ReviewType::DEAL;
            }
            if (($review->heading && $review->body) == null || ($review->heading == 'n/a' && $review->body == 'n/a')) {
                $point = AllAmounts::where('type', 'rating')->first()->price;
                $point_type = PointType::rating_point;
            } else {
                $point = AllAmounts::where('type', 'review')->first()->price;
                $point_type = PointType::review_point;
            }

            $customer_point = (new rewardFunctionController())->store_rating_review_point($review->customer_id, $point, $point_type, $review->id);
            //total reviews partner got
            $totalReview = Review::where('partner_account_id', $review->partner_account_id)->count();
            // average rating
            if ($totalReview > 0) {
                $average = Review::where('partner_account_id', $review->partner_account_id)->sum('rating');
                $average = $average / $totalReview;
                $average = round($average, 2);
                //update average rating in partner info
                Rating::where('partner_account_id', $review->partner_account_id)
                ->update(['average_rating' => $average]);
            }

            for ($i = 1; $i <= 5; $i++) {
                //total specific star partner got
                $totalStars = Review::select('rating')
                ->where('rating', $i)
                ->where('partner_account_id', $review->partner_account_id)
                ->count();
                //percentage of the specific star
                $starPercentage = $totalStars / $totalReview * 100;
                Rating::where('partner_account_id', $review->partner_account_id)
                ->update([
                    $i.'_star' => $starPercentage,
                ]);
            }
            (new \App\Http\Controllers\AdminNotification\functionController())->reviewNotification($review);
            (new \App\Http\Controllers\Reward\functionController())->sendRewardNotification($customer_point, $review_type);
            $this->setReviewPostNotification($transaction->id, $customer, $review_type);
            if ($from_admin) {
                $accept_message = 'Your review at '.$partner_name.', '.$partner_area.' has been published';
                $customer_f_reg_key[0] = $customer->firebase_token;
                (new adminController())->sendCustomerWisePushNotification('Royalty', $accept_message, $customer_f_reg_key);
            }
            $review_response = Review::where('id', $review->id)
            ->with('comments', 'likes', 'customer', 'partnerInfo.profileImage')
            ->first();
            $review_response->previous_like = 0;
            $review_response->previous_like_id = 0;

            \DB::commit();
        } catch (\Exception $e) {
            \DB::rollBack();

            return null;
        }

        return $review_response;
    }

    public function rejectReviewModeration($review_id)
    {
        $review = Review::find($review_id);
        $review->delete();
    }
}
