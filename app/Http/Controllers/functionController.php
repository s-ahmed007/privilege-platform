<?php

namespace App\Http\Controllers;

use App\AllAmounts;
use App\AssignedCard;
use App\B2b2cInfo;
use App\B2b2cUser;
use App\BranchOffers;
use App\BranchUser;
use App\CardPromoCodes;
use App\CardPromoCodeUsage;
use App\Categories;
use App\CustomerAccount;
use App\CustomerHistory;
use App\CustomerInfo;
use App\CustomerNotification;
use App\CustomerPoint;
use App\Helpers\LengthAwarePaginator;
use App\Http\Controllers\admin\AnalyticsController;
use App\Http\Controllers\Enum\CustomerType;
use App\Http\Controllers\Enum\DeliveryType;
use App\Http\Controllers\Enum\InfluencerPercentage;
use App\Http\Controllers\Enum\LikerType;
use App\Http\Controllers\Enum\NewsFeedType;
use App\Http\Controllers\Enum\notificationType;
use App\Http\Controllers\Enum\PointType;
use App\Http\Controllers\Enum\PostType;
use App\Http\Controllers\sksort;
use App\InfluencerPayment;
use App\LikePost;
use App\LikesReview;
use App\PartnerAccount;
use App\PartnerBranch;
use App\PartnerInfo;
use App\PartnerNotification;
use App\PartnersInHotspot;
use App\Post;
use App\RbdStatistics;
use App\Review;
use App\ScannerReward;
use App\transaction_table;
use App\TransactionTable;
use Auth;
use CodeItNow\BarcodeBundle\Utils\BarcodeGenerator;
use Datetime;
use DB;
use File;
use http\Env\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Khill\Lavacharts\Lavacharts;
use Mail;
use Milon\Barcode\DNS1D;
use Response;
use Session;
use SimpleSoftwareIO\QrCode\BaconQrCodeGenerator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use View;

class functionController extends Controller
{
    //function for get all unseen notifications of partner
    public function partnerUnseenNotifications($partner_id)
    {
        $notifications = DB::table('partner_notification')
            ->where('partner_account_id', $partner_id)
            ->where('seen', 0)
            ->orderBy('id', 'DESC')
            ->get();
        $notifications = json_decode(json_encode($notifications), true);
        //GET customers info of notification table
        for ($i = 0; $i < count($notifications); $i++) {
            if ($notifications[$i]['notification_type'] == '2') { //partner gets review notification
                $notify_partner = DB::table('customer_account as ca')
                    ->join('customer_info as ci', 'ci.customer_id', '=', 'ca.customer_id')
                    ->join('review as rev', 'rev.customer_id', '=', 'ci.customer_id')
                    ->select('ca.customer_username', 'ci.customer_first_name', 'ci.customer_last_name')
                    ->where('rev.id', $notifications[$i]['source_id'])
                    ->get();
                $notify_partner = json_decode(json_encode($notify_partner), true);
                $notify_partner = $notify_partner[0];
                $notifications[$i]['customer_name'] = $notify_partner['customer_first_name'].' '.$notify_partner['customer_last_name'];
            } elseif ($notifications[$i]['notification_type'] == '4') { //partner gets follow notification
                $notify_partner = DB::table('customer_account as ca')
                    ->join('customer_info as ci', 'ci.customer_id', '=', 'ca.customer_id')
                    ->join('follow_partner as fp', 'fp.follower', '=', 'ci.customer_id')
                    ->select('ca.customer_username', 'ci.customer_first_name', 'ci.customer_last_name', 'ci.customer_profile_image')
                    ->where('fp.id', $notifications[$i]['source_id'])
                    ->get();
                $notify_partner = json_decode(json_encode($notify_partner), true);
                $notify_partner = $notify_partner[0];
                $notifications[$i]['customer_username'] = $notify_partner['customer_username'];
                $notifications[$i]['customer_name'] = $notify_partner['customer_first_name'].' '.$notify_partner['customer_last_name'];
            } else { //partner gets post like notification (notification type => 7)
                $notify_partner = DB::table('customer_account as ca')
                    ->join('customer_info as ci', 'ci.customer_id', '=', 'ca.customer_id')
                    ->join('likes_post as lp', 'lp.liker_id', '=', 'ci.customer_id')
                    ->select('ca.customer_username', 'ci.customer_first_name', 'ci.customer_last_name')
                    ->where('lp.id', $notifications[$i]['source_id'])
                    ->get();
                $notify_partner = json_decode(json_encode($notify_partner), true);

                $notify_partner = $notify_partner[0];
                $notifications[$i]['customer_name'] = $notify_partner['customer_first_name'].' '.$notify_partner['customer_last_name'];
            }
        }

        return $notifications;
    }

    //function for get all seen notifications of partner
    public function partnerSeenNotifications($partner_id)
    {
        $notifications = DB::table('partner_notification')
            ->where('partner_account_id', $partner_id)
            ->where('seen', 1)
            ->orderBy('id', 'DESC')
            ->get();
        $notifications = json_decode(json_encode($notifications), true);

        //GET customers info of notification table
        for ($i = 0; $i < count($notifications); $i++) {
            if ($notifications[$i]['notification_type'] == '2') { //partner gets review notification
                $notify_partner = DB::table('customer_account as ca')
                    ->join('customer_info as ci', 'ci.customer_id', '=', 'ca.customer_id')
                    ->join('review as rev', 'rev.customer_id', '=', 'ci.customer_id')
                    ->select('ca.customer_username', 'ci.customer_first_name', 'ci.customer_last_name', 'ci.customer_profile_image')
                    ->where('rev.id', $notifications[$i]['source_id'])
                    ->get();
                $notify_partner = json_decode(json_encode($notify_partner), true);
                $notify_partner = $notify_partner[0];
                $notifications[$i]['customer_name'] = $notify_partner['customer_first_name'].' '.$notify_partner['customer_last_name'];
                $notifications[$i]['customer_profile_image'] = $notify_partner['customer_profile_image'];
            } elseif ($notifications[$i]['notification_type'] == '4') { //partner gets follow notification
                $notify_partner = DB::table('customer_account as ca')
                    ->join('customer_info as ci', 'ci.customer_id', '=', 'ca.customer_id')
                    ->join('follow_partner as fp', 'fp.follower', '=', 'ci.customer_id')
                    ->select('ca.customer_username', 'ci.customer_first_name', 'ci.customer_last_name', 'ci.customer_profile_image')
                    ->where('fp.id', $notifications[$i]['source_id'])
                    ->get();
                $notify_partner = json_decode(json_encode($notify_partner), true);
                $notify_partner = $notify_partner[0];
                $notifications[$i]['customer_username'] = $notify_partner['customer_username'];
                $notifications[$i]['customer_name'] = $notify_partner['customer_first_name'].' '.$notify_partner['customer_last_name'];
            } else { //partner gets post like notification
                $notify_partner = DB::table('customer_account as ca')
                    ->join('customer_info as ci', 'ci.customer_id', '=', 'ca.customer_id')
                    ->join('likes_post as lp', 'lp.liker_id', '=', 'ci.customer_id')
                    ->select('ca.customer_username', 'ci.customer_first_name', 'ci.customer_last_name')
                    ->where('lp.id', $notifications[$i]['source_id'])
                    ->get();
                $notify_partner = json_decode(json_encode($notify_partner), true);
                $notify_partner = $notify_partner[0];
                $notifications[$i]['customer_name'] = $notify_partner['customer_first_name'].' '.$notify_partner['customer_last_name'];
            }
        }

        return $notifications;
    }

    //function to get all reviews of specific customer for customer profile
    public function customerAllReviews($customer_id)
    {
        $reviews = DB::table('customer_info as ci')
            ->join('review as rev', 'rev.customer_id', '=', 'ci.customer_id')
            ->join('partner_profile_images as ppi', 'rev.partner_account_id', '=', 'ppi.partner_account_id')
            ->join('partner_info as pi', 'pi.partner_account_id', '=', 'ppi.partner_account_id')
            ->select(
                'ci.customer_profile_image',
                'ci.customer_id',
                'ci.customer_full_name',
                'rev.*',
                'ppi.partner_profile_image',
                'pi.partner_name',
                'pi.partner_account_id'
            )
            ->where('rev.customer_id', $customer_id)
            ->where('rev.deleted_at', null)
            ->where('rev.moderation_status', 1)
            ->orderBy('rev.posted_on', 'DESC')
            ->get();
        $reviews = json_decode(json_encode($reviews), true);
        $i = 0;
        foreach ($reviews as $review) {
            $main_branch = (new self)->mainBranchOfPartner($review['partner_account_id']);
            $reviews[$i]['main_branch_id'] = $main_branch[0]->id ?? null;

            $comments = DB::table('review_comment')
                ->where('review_id', $review['id'])
                ->where('deleted_at', null)
                ->get();
            $comments = json_decode(json_encode($comments), true);
            $reviews[$i]['comments'] = $comments;
            //total likes of a specific review
            $likes_of_a_review = DB::table('likes_review')
                ->where('review_id', $review['id'])
                ->count();
            $reviews[$i]['total_likes_of_a_review'] = $likes_of_a_review;
            $i++;
        }
        //previous like
        $id_array = [];
        foreach ($reviews as $review) {
            array_push($id_array, $review['id']);
        }

        $previousLike = DB::table('likes_review')
            ->select('id as like_review_id', 'review_id')
            ->whereIn('review_id', $id_array)
            ->where('liker_id', Session::get('customer_id'))
            ->get();
        $previousLike = json_decode(json_encode($previousLike), true);

        $liked_ids = [];
        if ($previousLike > 0) {
            foreach ($previousLike as $like) {
                array_push($liked_ids, $like['review_id']);
            }
            $count = count($reviews);
            for ($i = 0; $i < $count; $i++) {
                if (in_array($reviews[$i]['id'], $liked_ids)) {
                    $reviews[$i]['liked'] = 1;
                    $key = array_search($reviews[$i]['id'], array_column($previousLike, 'review_id'));
                    $reviews[$i]['source_id'] = $previousLike[$key]['like_review_id'];
                } else {
                    $reviews[$i]['liked'] = 0;
                    $reviews[$i]['source_id'] = 0;
                }
            }
        }

        return $reviews;
    }

    public function partnerAllReviews($partnerID)
    {
        //get all reviews from partner id
        $reviews = DB::table('customer_info as ci')
            ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
            ->join('review as rev', 'rev.customer_id', '=', 'ci.customer_id')
            ->join('partner_profile_images as ppi', 'rev.partner_account_id', '=', 'ppi.partner_account_id')
            ->join('partner_info as pi', 'pi.partner_account_id', '=', 'ppi.partner_account_id')
            ->select(
                'ci.customer_profile_image',
                'ci.customer_id',
                'ci.customer_full_name',
                'ca.customer_username',
                'rev.*',
                'ppi.partner_profile_image',
                'pi.partner_name',
                'pi.partner_account_id'
            )
            ->where('rev.partner_account_id', $partnerID)
            ->where('rev.deleted_at', null)
            ->orderBy('rev.posted_on', 'DESC')
            ->get();
        $reviews = json_decode(json_encode($reviews), true);
        $i = 0;
        foreach ($reviews as $review) {
            $comments = DB::table('review_comment')
                ->select('comment', 'comment_type', 'posted_on')
                ->where('review_id', $review['id'])
                ->get();
            $comments = json_decode(json_encode($comments), true);
            $reviews[$i]['comments'] = $comments;
            //total likes of a specific review
            $likes_of_a_review = DB::table('likes_review')
                ->where('review_id', $review['id'])
                ->count();
            $reviews[$i]['total_likes_of_a_review'] = $likes_of_a_review;
            $i++;
        }
        //previous like
        $id_array = [];
        foreach ($reviews as $review) {
            array_push($id_array, $review['id']);
        }

        $previousLike = DB::table('likes_review')
            ->select('id as like_review_id', 'review_id')
            ->whereIn('review_id', $id_array)
            ->where('liker_id', Session::get('partner_id'))
            ->where('liker_type', LikerType::partner)
            ->get();
        $previousLike = json_decode(json_encode($previousLike), true);
        $liked_ids = [];

        foreach ($previousLike as $like) {
            array_push($liked_ids, $like['review_id']);
        }
        $count = count($reviews);
        for ($i = 0; $i < $count; $i++) {
            if (in_array($reviews[$i]['id'], $liked_ids)) {
                $reviews[$i]['liked'] = 1;
                $key = array_search($reviews[$i]['id'], array_column($previousLike, 'review_id'));
                $reviews[$i]['source_id'] = $previousLike[$key]['like_review_id'];
            } else {
                $reviews[$i]['liked'] = 0;
                $reviews[$i]['source_id'] = 0;
            }
        }

        return $reviews;
    }

    //function to get visited partners of a customer
    public function visitedPartners($customer_id)
    {
        $visited_partners = DB::select("select branch_id,
       count(branch_id) as total_visit, partner_name, partner_area, pi.partner_account_id, partner_profile_image
        from transaction_table
         join partner_branch on branch_id = partner_branch.id
         join partner_info pi on partner_branch.partner_account_id = pi.partner_account_id
         join partner_profile_images ppi on partner_branch.partner_account_id = ppi.partner_account_id
        where customer_id = $customer_id and deleted_at is null
        group by branch_id, partner_name, pi.partner_account_id, partner_profile_image, partner_area");

        return $visited_partners;

//        $partner_id = [];
//        $visited = [];
//        $i = 0;
//        $transactions = TransactionTable::where('customer_id', $customer_id)
//            ->where('deleted_at', null)->with('branch')->orderBy('posted_on', "DESC")->get();
//        foreach ($transactions as $transaction) {
//            if (!in_array($transaction->branch->partner_account_id, $partner_id)) {
//                $partner_id[$i] = $transaction->branch->partner_account_id;
//                $visited[$i] = TransactionTable::where('customer_id', $customer_id)
//                    ->where('deleted_at', null)->where('branch_id', $transaction->branch->id)->count();
//                $i++;
//            }
//        }
//
//        $visited_partners = [];
//        $i = 0;
//        foreach ($partner_id as $key => $partner) {
//            $visitedPartners = DB::table('partner_info as pi')
//                ->join('partner_branch as pb', 'pb.partner_account_id', '=', 'pi.partner_account_id')
//                ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
//                ->select('pi.partner_account_id', 'pi.partner_name', 'ppi.partner_profile_image', 'pb.id as main_branch_id')
//                ->where('pi.partner_account_id', $partner)
//                ->where('pb.main_branch', 1)
//                ->first();
//            $visitedPartners = json_decode(json_encode($visitedPartners), true);
//            $visitedPartners['total_visit'] = $visited[$key];
//            array_push($visited_partners, $visitedPartners);
//            $i++;
//        }
//
//        return $visited_partners;
    }

    //function to get following list of a customer
    public function customerFollowingList($customerID)
    {
        //following list of partner
        $following = DB::table('follow_partner')
            ->where('follower', $customerID)
            ->get();
        $following = json_decode(json_encode($following), true);

        $following_list = [];
        foreach ($following as $value) {
            $follow_list = DB::table('partner_info as pi')
                ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                ->select('pi.partner_account_id', 'pi.partner_name', 'pi.partner_category', 'ppi.partner_profile_image')
                ->where('pi.partner_account_id', $value['following'])
                ->first();
            $follow_list = json_decode(json_encode($follow_list), true);
            array_push($following_list, $follow_list);
        }
        $result['partner'] = $following_list;
        //following list of customer
        $following = DB::table('follow_customer')->where([['follower', $customerID], ['follow_request', 1]])->get();
        $following = json_decode(json_encode($following), true);

        $following_list = [];
        foreach ($following as $value) {
            $follow_list = DB::table('customer_info as ci')
                ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
                ->join('user_type as ut', 'ut.id', '=', 'ci.customer_type')
                ->select(
                    'ci.customer_id',
                    'ci.customer_first_name',
                    'ci.customer_last_name',
                    'ci.customer_profile_image',
                    'ci.customer_type',
                    'ca.customer_username',
                    'ut.type'
                )
                ->where('ci.customer_id', $value['following'])
                ->first();
            $follow_list = json_decode(json_encode($follow_list), true);
            array_push($following_list, $follow_list);
        }
        $result['customer'] = $following_list;

        return $result;
    }

    //function to get follower list of a customer in public profile
    public function followerListOfCustomer($customerID)
    {
        //follower list of customer
        $followers = DB::table('follow_customer')
            ->where([['following', $customerID], ['follow_request', 1]])
            ->get();
        $followers = json_decode(json_encode($followers), true);
        //initiate an array
        $follower_list = [];
        foreach ($followers as $value) {
            $followerInfo = DB::table('customer_info as ci')
                ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
                ->join('user_type as ut', 'ut.id', '=', 'ci.customer_type')
                ->select(
                    'ci.customer_id',
                    'ci.customer_first_name',
                    'ci.customer_last_name',
                    'ci.customer_profile_image',
                    'ci.customer_type',
                    'ca.customer_username'
                )
                ->where('ci.customer_id', $value['follower'])
                ->first();
            $followerInfo = json_decode(json_encode($followerInfo), true);
            //            //check if this user already following his follower or not
            //            if(Session::has('customer_id')) {
            //                $alreadyFollowing = DB::table('follow_customer')
            //                    ->select('follow_request')
            //                    ->where([['follower', $customerID], ['following', $value['follower']]])
            //                    ->get();
            //                $alreadyFollowing = json_decode(json_encode($alreadyFollowing), true);
            //                if($alreadyFollowing){
            //                    $followerInfo['following'] = $alreadyFollowing[0]['follow_request'];
            //                }
            //            }
            array_push($follower_list, $followerInfo);
        }
        $result['follower'] = $follower_list;
        if (Session::has('customer_id')) {
            $following = DB::table('follow_customer')
                ->select('follow_request')
                ->where('follower', Session::get('customer_id'))
                ->where('following', $customerID)
                ->get();
            $following = json_decode(json_encode($following), true);
            if ($following) {
                $result['following'] = $following[0]['follow_request'];
            }
        }

        return $result;
    }

    //function to get follower list of a customer in user account
    public function userFollowerList($customerID)
    {
        //follower list of customer
        $followers = DB::table('follow_customer')
            ->where([['following', $customerID], ['follow_request', 1]])
            ->get();
        $followers = json_decode(json_encode($followers), true);
        //        initiate an array
        $follower_list = [];
        foreach ($followers as $value) {
            $followerInfo = DB::table('customer_info as ci')
                ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
                ->select('ci.customer_id', 'ci.customer_first_name', 'ci.customer_last_name', 'ci.customer_profile_image', 'ci.customer_type', 'customer_username')
                ->where('ci.customer_id', $value['follower'])
                ->first();
            $followerInfo = json_decode(json_encode($followerInfo), true);
            //check if this user already following his follower or not
            if (Session::has('customer_id')) {
                $alreadyFollowing = DB::table('follow_customer')
                    ->select('follow_request')
                    ->where([['follower', $customerID], ['following', $value['follower']]])
                    ->get();
                $alreadyFollowing = json_decode(json_encode($alreadyFollowing), true);
                if ($alreadyFollowing) {
                    $followerInfo['following'] = $alreadyFollowing[0]['follow_request'];
                }
            }
            array_push($follower_list, $followerInfo);
        }

        return $follower_list;
    }

    //function to get follower list of a partner
    public function followerListOfPartner($partnerID)
    {
        //follower list of customer
        $follower_ids = DB::table('follow_partner')
            ->where('following', $partnerID)
            ->get();
        $follower_ids = json_decode(json_encode($follower_ids), true);

        $followers_list = [];
        $i = 0;
        foreach ($follower_ids as $value) {
            $follower_info = DB::table('customer_account as ca')
                ->join('customer_info as ci', 'ca.customer_id', '=', 'ci.customer_id')
                ->select(
                    'ca.customer_id',
                    'ca.customer_username',
                    'ci.customer_first_name',
                    'ci.customer_last_name',
                    'ci.customer_profile_image',
                    'ci.customer_type'
                )
                ->where('ca.customer_id', $value['follower'])
                ->first();
            $follower_info = json_decode(json_encode($follower_info), true);
            array_push($followers_list, $follower_info);
            $followers_list[$i]['following_since'] = $value['posted_on'];
            $i++;
        }

        return $followers_list;
    }

    //function to get all info of every partner
    public function partnerInfo($category)
    {
        $info = DB::table('partner_info as pi')
            ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
            ->join('discount as dis', 'dis.partner_account_id', '=', 'pi.partner_account_id')
            ->select(
                'pi.partner_name',
                'pi.partner_area',
                'pi.partner_division',
                'ppi.partner_profile_image',
                'ppi.partner_thumb_image',
                'dis.discount_percentage'
            )
            ->where('dis.user_type', 2)
            ->where('pi.partner_category', $category)
            ->get();
        $info = json_decode(json_encode($info), true);

        return $info;
    }

    //function to get partners info for discount filtering
    public function partnerInfoForDiscountFilter($category, $division, $area)
    {
        //if division is selected
        if ($division != 'Division' && $area == 'Area') {
            $info = DB::table('partner_info as pi')
                ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
                ->join('discount as dis', 'dis.partner_account_id', '=', 'pi.partner_account_id')
                ->select(
                    'pi.partner_account_id',
                    'pi.partner_name',
                    'pi.partner_area',
                    'pi.partner_category',
                    'pi.partner_division',
                    'ppi.partner_profile_image',
                    'ppi.partner_thumb_image',
                    'dis.discount_percentage',
                    'rat.average_rating'
                )
                ->where('dis.user_type', 2)
                ->where('pi.partner_category', $category)
                ->where('pi.partner_division', $division)
                ->get();
            $info = json_decode(json_encode($info), true);
        } elseif ($division == 'Division' && $area != 'Area') { //if area is selected
            $info = DB::table('partner_info as pi')
                ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
                ->join('discount as dis', 'dis.partner_account_id', '=', 'pi.partner_account_id')
                ->select(
                    'pi.partner_account_id',
                    'pi.partner_name',
                    'pi.partner_area',
                    'pi.partner_category',
                    'pi.partner_division',
                    'ppi.partner_profile_image',
                    'ppi.partner_thumb_image',
                    'dis.discount_percentage',
                    'rat.average_rating'
                )
                ->where('dis.user_type', 2)
                ->where('pi.partner_category', $category)
                ->where('pi.partner_area', $area)
                ->get();
            $info = json_decode(json_encode($info), true);
        } elseif ($division != 'Division' && $area != 'Area') { //if division & area both are selected
            $info = DB::table('partner_info as pi')
                ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
                ->join('discount as dis', 'dis.partner_account_id', '=', 'pi.partner_account_id')
                ->select(
                    'pi.partner_account_id',
                    'pi.partner_name',
                    'pi.partner_area',
                    'pi.partner_category',
                    'pi.partner_division',
                    'ppi.partner_profile_image',
                    'ppi.partner_thumb_image',
                    'dis.discount_percentage',
                    'rat.average_rating'
                )
                ->where('dis.user_type', 2)
                ->where('pi.partner_category', $category)
                ->where('pi.partner_area', $area)
                ->where('pi.partner_division', $division)
                ->get();
            $info = json_decode(json_encode($info), true);
        } else { //if only discount is selected
            $info = DB::table('partner_info as pi')
                ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
                ->join('discount as dis', 'dis.partner_account_id', '=', 'pi.partner_account_id')
                ->select(
                    'pi.partner_account_id',
                    'pi.partner_name',
                    'pi.partner_area',
                    'pi.partner_category',
                    'pi.partner_division',
                    'ppi.partner_profile_image',
                    'ppi.partner_thumb_image',
                    'dis.discount_percentage',
                    'rat.average_rating'
                )
                ->where('dis.user_type', 2)
                ->where('pi.partner_category', $category)
                ->get();
            $info = json_decode(json_encode($info), true);
        }
        $i = 0;
        foreach ($info as $data) {
            //get 1 gallery image randomly of this partner
            $gallery_image = DB::table('partner_gallery_images')
                ->select('partner_gallery_image')
                ->where('partner_account_id', $data['partner_account_id'])
                ->inRandomOrder()
                ->first();
            $gallery_image = json_decode(json_encode($gallery_image), true);
            $info[$i]['gallery_image'] = $gallery_image['partner_gallery_image'];
            //get total review number of this partner
            $reviews = DB::table('review')
                ->where('partner_account_id', $data['partner_account_id'])
                ->count();
            $info[$i]['total_reviews'] = $reviews;
            $i++;
        }

        return $info;
    }

    //function to get partners info for division filtering
    public function partnerInfoForDivisionFilter($category, $division)
    {
        $cat = Categories::where('type', $category)->first();

        $info = PartnerAccount::with([
            'info' => function ($query) use ($cat) {
                $query->where('partner_category', '=', $cat->id);
            },
            'branches' => function ($query) use ($division) {
                $query->where('partner_division', '=', $division);
            },
            'rating', 'profileImage', 'galleryImages', 'reviews', 'discount',
        ])->get();

        return $info;
    }

    //function to get partners info for area filtering
    public function partnerInfoForAreaFilter($category, $area)
    {
        $info = DB::table('partner_info as pi')
            ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
            ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
            ->join('discount as dis', 'dis.partner_account_id', '=', 'pi.partner_account_id')
            ->select(
                'pi.partner_account_id',
                'pi.partner_name',
                'pi.partner_area',
                'pi.partner_category',
                'pi.partner_division',
                'ppi.partner_profile_image',
                'ppi.partner_thumb_image',
                'dis.discount_percentage',
                'rat.average_rating'
            )
            ->where('dis.user_type', 2)
            ->where('pi.partner_category', $category)
            ->where('pi.partner_area', $area)
            ->get();
        $info = json_decode(json_encode($info), true);
        $i = 0;
        foreach ($info as $data) {
            //get 1 gallery image randomly of this partner
            $gallery_image = DB::table('partner_gallery_images')
                ->select('partner_gallery_image')
                ->where('partner_account_id', $data['partner_account_id'])
                ->inRandomOrder()
                ->first();
            $gallery_image = json_decode(json_encode($gallery_image), true);
            $info[$i]['gallery_image'] = $gallery_image['partner_gallery_image'];
            //get total review number of this partner
            $reviews = DB::table('review')
                ->where('partner_account_id', $data['partner_account_id'])
                ->count();
            $info[$i]['total_reviews'] = $reviews;
            $i++;
        }

        return $info;
    }

    //function to get partners info for attribute filtering
    public function partnerInfoForAttributeFilter($category, $division, $area)
    {
        //if division is selected
        if ($division != 'Division' && $area == 'Area') {
            $info = DB::table('partner_info as pi')
                ->join('partner_facilities as pf', 'pf.partner_account_id', '=', 'pi.partner_account_id')
                ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
                ->join('discount as dis', 'dis.partner_account_id', '=', 'pi.partner_account_id')
                ->select(
                    'pi.partner_account_id',
                    'pi.partner_name',
                    'pi.partner_area',
                    'pi.partner_category',
                    'pi.partner_division',
                    'ppi.partner_profile_image',
                    'ppi.partner_thumb_image',
                    'dis.discount_percentage',
                    'rat.average_rating',
                    'pf.*'
                )
                ->where('dis.user_type', 2)
                ->where('pi.partner_category', $category)
                ->where('pi.partner_division', $division)
                ->get();
            $info = json_decode(json_encode($info), true);
        } elseif ($division == 'Division' && $area != 'Area') { //if area is selected
            $info = DB::table('partner_info as pi')
                ->join('partner_facilities as pf', 'pf.partner_account_id', '=', 'pi.partner_account_id')
                ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
                ->join('discount as dis', 'dis.partner_account_id', '=', 'pi.partner_account_id')
                ->select(
                    'pi.partner_account_id',
                    'pi.partner_name',
                    'pi.partner_area',
                    'pi.partner_category',
                    'pi.partner_division',
                    'ppi.partner_profile_image',
                    'ppi.partner_thumb_image',
                    'dis.discount_percentage',
                    'rat.average_rating',
                    'pf.*'
                )
                ->where('dis.user_type', 2)
                ->where('pi.partner_category', $category)
                ->where('pi.partner_area', $area)
                ->get();
            $info = json_decode(json_encode($info), true);
        } elseif ($division != 'Division' && $area != 'Area') { //if division & area both are selected
            $info = DB::table('partner_info as pi')
                ->join('partner_facilities as pf', 'pf.partner_account_id', '=', 'pi.partner_account_id')
                ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
                ->join('discount as dis', 'dis.partner_account_id', '=', 'pi.partner_account_id')
                ->select(
                    'pi.partner_account_id',
                    'pi.partner_name',
                    'pi.partner_area',
                    'pi.partner_category',
                    'pi.partner_division',
                    'ppi.partner_profile_image',
                    'ppi.partner_thumb_image',
                    'dis.discount_percentage',
                    'rat.average_rating',
                    'pf.*'
                )
                ->where('dis.user_type', 2)
                ->where('pi.partner_category', $category)
                ->where('pi.partner_area', $area)
                ->where('pi.partner_division', $division)
                ->get();
            $info = json_decode(json_encode($info), true);
        } else { //if only attribute is selected
            $info = DB::table('partner_info as pi')
                ->join('partner_facilities as pf', 'pf.partner_account_id', '=', 'pi.partner_account_id')
                ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
                ->join('discount as dis', 'dis.partner_account_id', '=', 'pi.partner_account_id')
                ->select(
                    'pi.partner_account_id',
                    'pi.partner_name',
                    'pi.partner_area',
                    'pi.partner_category',
                    'pi.partner_division',
                    'ppi.partner_profile_image',
                    'ppi.partner_thumb_image',
                    'dis.discount_percentage',
                    'rat.average_rating',
                    'pf.*'
                )
                ->where('dis.user_type', 2)
                ->where('pi.partner_category', $category)
                ->get();
            $info = json_decode(json_encode($info), true);
        }
        $i = 0;
        foreach ($info as $data) {
            //get 1 gallery image randomly of this partner
            $gallery_image = DB::table('partner_gallery_images')
                ->select('partner_gallery_image')
                ->where('partner_account_id', $data['partner_account_id'])
                ->inRandomOrder()
                ->first();
            $gallery_image = json_decode(json_encode($gallery_image), true);
            $info[$i]['gallery_image'] = $gallery_image['partner_gallery_image'];
            //get total review number of this partner
            $reviews = DB::table('review')
                ->where('partner_account_id', $data['partner_account_id'])
                ->count();
            $info[$i]['total_reviews'] = $reviews;
            $i++;
        }

        return $info;
    }

    //function to get all info of every partner for subcategory 1 filter
    public function partnerInfoForSubcategory1Filter($category, $division, $area)
    {
        //if division is selected
        if ($division != 'Division' && $area == 'Area') {
            $info = DB::table('partner_info as pi')
                ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
                ->join('discount as dis', 'dis.partner_account_id', '=', 'pi.partner_account_id')
                ->join('part_cat_rel as pcr', 'pcr.partner_id', '=', 'pi.partner_account_id')
                ->join('category_relation as cr', 'cr.id', '=', 'pcr.cat_rel_id')
                ->join('sub_cat_1 as sc1', 'sc1.id', '=', 'cr.sub_cat_1_id')
                ->select(
                    'pi.partner_account_id',
                    'pi.partner_name',
                    'pi.partner_area',
                    'pi.partner_category',
                    'pi.partner_division',
                    'ppi.partner_profile_image',
                    'ppi.partner_thumb_image',
                    'dis.discount_percentage',
                    'rat.average_rating',
                    'sc1.cat_name as sub_cat_1_name'
                )
                ->where('dis.user_type', 2)
                ->where('pi.partner_category', $category)
                ->where('pi.partner_division', $division)
                ->get();
            $info = json_decode(json_encode($info), true);
        } elseif ($division == 'Division' && $area != 'Area') { //if area is selected
            $info = DB::table('partner_info as pi')
                ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
                ->join('discount as dis', 'dis.partner_account_id', '=', 'pi.partner_account_id')
                ->join('part_cat_rel as pcr', 'pcr.partner_id', '=', 'pi.partner_account_id')
                ->join('category_relation as cr', 'cr.id', '=', 'pcr.cat_rel_id')
                ->join('sub_cat_1 as sc1', 'sc1.id', '=', 'cr.sub_cat_1_id')
                ->select(
                    'pi.partner_account_id',
                    'pi.partner_name',
                    'pi.partner_area',
                    'pi.partner_category',
                    'pi.partner_division',
                    'ppi.partner_profile_image',
                    'ppi.partner_thumb_image',
                    'dis.discount_percentage',
                    'rat.average_rating',
                    'sc1.cat_name as sub_cat_1_name'
                )
                ->where('dis.user_type', 2)
                ->where('pi.partner_category', $category)
                ->where('pi.partner_area', $area)
                ->get();
            $info = json_decode(json_encode($info), true);
        } elseif ($division != 'Division' && $area != 'Area') { //if division & area both are selected
            $info = DB::table('partner_info as pi')
                ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
                ->join('discount as dis', 'dis.partner_account_id', '=', 'pi.partner_account_id')
                ->join('part_cat_rel as pcr', 'pcr.partner_id', '=', 'pi.partner_account_id')
                ->join('category_relation as cr', 'cr.id', '=', 'pcr.cat_rel_id')
                ->join('sub_cat_1 as sc1', 'sc1.id', '=', 'cr.sub_cat_1_id')
                ->select(
                    'pi.partner_account_id',
                    'pi.partner_name',
                    'pi.partner_area',
                    'pi.partner_category',
                    'pi.partner_division',
                    'ppi.partner_profile_image',
                    'ppi.partner_thumb_image',
                    'dis.discount_percentage',
                    'rat.average_rating',
                    'sc1.cat_name as sub_cat_1_name'
                )
                ->where('dis.user_type', 2)
                ->where('pi.partner_category', $category)
                ->where('pi.partner_area', $area)
                ->where('pi.partner_division', $division)
                ->get();
            $info = json_decode(json_encode($info), true);
        } else { //if only subcategory 1 is selected
            $info = DB::table('partner_info as pi')
                ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
                ->join('discount as dis', 'dis.partner_account_id', '=', 'pi.partner_account_id')
                ->join('part_cat_rel as pcr', 'pcr.partner_id', '=', 'pi.partner_account_id')
                ->join('category_relation as cr', 'cr.id', '=', 'pcr.cat_rel_id')
                ->join('sub_cat_1 as sc1', 'sc1.id', '=', 'cr.sub_cat_1_id')
                ->select(
                    'pi.partner_account_id',
                    'pi.partner_name',
                    'pi.partner_area',
                    'pi.partner_category',
                    'pi.partner_division',
                    'ppi.partner_profile_image',
                    'ppi.partner_thumb_image',
                    'dis.discount_percentage',
                    'rat.average_rating',
                    'sc1.cat_name as sub_cat_1_name'
                )
                ->where('dis.user_type', 2)
                ->where('pi.partner_category', $category)
                ->get();
            $info = json_decode(json_encode($info), true);
        }
        if (count($info) > 0) {
            $i = 0;
            foreach ($info as $value) {
                //get 1 gallery image randomly of this partner
                $gallery_image = DB::table('partner_gallery_images')
                    ->select('partner_gallery_image')
                    ->where('partner_account_id', $value['partner_account_id'])
                    ->inRandomOrder()
                    ->first();
                $gallery_image = json_decode(json_encode($gallery_image), true);
                $info[$i]['gallery_image'] = $gallery_image['partner_gallery_image'];
                //get total review number of this partner
                $reviews = DB::table('review')
                    ->where('partner_account_id', $value['partner_account_id'])
                    ->count();
                $info[$i]['total_reviews'] = $reviews;
                $i++;
            }
        }

        return $info;
    }

    //function to get all info of every partner for subcategory 2 filter
    public function partnerInfoForSubcategory2Filter($sub_cat_1_id, $category, $division, $area)
    {
        //if division is selected
        if ($division != 'Division' && $area == 'Area') {
            $info = DB::table('partner_info as pi')
                ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
                ->join('discount as dis', 'dis.partner_account_id', '=', 'pi.partner_account_id')
                ->select(
                    'pi.partner_account_id',
                    'pi.partner_name',
                    'pi.partner_area',
                    'pi.partner_category',
                    'pi.partner_division',
                    'ppi.partner_profile_image',
                    'ppi.partner_thumb_image',
                    'dis.discount_percentage',
                    'rat.average_rating'
                )
                ->where('dis.user_type', 2)
                ->where('pi.partner_category', $category)
                ->where('pi.partner_division', $division)
                ->get();
            $info = json_decode(json_encode($info), true);
        } elseif ($division == 'Division' && $area != 'Area') { //if area is selected
            $info = DB::table('partner_info as pi')
                ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
                ->join('discount as dis', 'dis.partner_account_id', '=', 'pi.partner_account_id')
                ->select(
                    'pi.partner_account_id',
                    'pi.partner_name',
                    'pi.partner_area',
                    'pi.partner_category',
                    'pi.partner_division',
                    'ppi.partner_profile_image',
                    'ppi.partner_thumb_image',
                    'dis.discount_percentage',
                    'rat.average_rating'
                )
                ->where('dis.user_type', 2)
                ->where('pi.partner_category', $category)
                ->where('pi.partner_area', $area)
                ->get();
            $info = json_decode(json_encode($info), true);
        } elseif ($division != 'Division' && $area != 'Area') { //if division & area both are selected
            $info = DB::table('partner_info as pi')
                ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
                ->join('discount as dis', 'dis.partner_account_id', '=', 'pi.partner_account_id')
                ->select(
                    'pi.partner_account_id',
                    'pi.partner_name',
                    'pi.partner_area',
                    'pi.partner_category',
                    'pi.partner_division',
                    'ppi.partner_profile_image',
                    'ppi.partner_thumb_image',
                    'dis.discount_percentage',
                    'rat.average_rating'
                )
                ->where('dis.user_type', 2)
                ->where('pi.partner_category', $category)
                ->where('pi.partner_area', $area)
                ->where('pi.partner_division', $division)
                ->get();
            $info = json_decode(json_encode($info), true);
        } else { //if only attribute is selected
            $info = DB::table('partner_info as pi')
                ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
                ->join('discount as dis', 'dis.partner_account_id', '=', 'pi.partner_account_id')
                ->select(
                    'pi.partner_account_id',
                    'pi.partner_name',
                    'pi.partner_area',
                    'pi.partner_category',
                    'pi.partner_division',
                    'ppi.partner_profile_image',
                    'ppi.partner_thumb_image',
                    'dis.discount_percentage',
                    'rat.average_rating'
                )
                ->where('dis.user_type', 2)
                ->where('pi.partner_category', $category)
                ->get();
            $info = json_decode(json_encode($info), true);
        }
        if (count($info) > 0) {
            $i = 0;
            foreach ($info as $value) {
                //get 1 gallery image randomly of this partner
                $gallery_image = DB::table('partner_gallery_images')
                    ->select('partner_gallery_image')
                    ->where('partner_account_id', $value['partner_account_id'])
                    ->inRandomOrder()
                    ->first();
                $gallery_image = json_decode(json_encode($gallery_image), true);
                $info[$i]['gallery_image'] = $gallery_image['partner_gallery_image'];
                //get total review number of this partner
                $reviews = DB::table('review')
                    ->where('partner_account_id', $value['partner_account_id'])
                    ->count();
                $info[$i]['total_reviews'] = $reviews;

                $subcategories = DB::table('partner_info as pi')
                    ->join('part_cat_rel as pcr', 'pcr.partner_id', '=', 'pi.partner_account_id')
                    ->join('category_relation as cr', 'cr.id', '=', 'pcr.cat_rel_id')
                    ->join('sub_cat_2 as sc2', 'sc2.id', '=', 'cr.sub_cat_2_id')
                    ->select('sc2.cat_name as sub_cat_2_name')
                    ->where('pcr.partner_id', $value['partner_account_id'])
                    ->where('cr.sub_cat_1_id', $sub_cat_1_id)
                    ->get();
                $subcategories = json_decode(json_encode($subcategories), true);
                if (! empty($subcategories)) {
                    $j = 0;
                    foreach ($subcategories as $key => $value1) {
                        $info[$i][$value1['sub_cat_2_name']] = 1;
                        $j++;
                    }
                }
                $i++;
            }
        }

        return $info;
    }

    //function to get users & partners statistics for rbd admin
    public function usersPartnersNumber()
    {
//        $main_users = CustomerInfo::with('latestSSLTransaction')
//            ->where('customer_type', 2)
//            ->where('expiry_date', '>', date('Y-m-d'))
//            ->get();
//        $card_user = collect($main_users)->where('latestSSLTransaction.cardDelivery.delivery_type', '!=', DeliveryType::virtual_card);
//        $trial_user = collect($main_users)->where('latestSSLTransaction.cardDelivery.delivery_type', DeliveryType::virtual_card);

        $allUsers['allCustomers'] = CustomerInfo::count();
//        $allUsers['guest_user'] = CustomerInfo::where('customer_type', 3)->count();
//        $allUsers['card_user'] = $card_user->count();
//        $allUsers['trial_user'] = $trial_user->count();
        //total partners
        $partnersNumber = PartnerAccount::where('active', 1)->count();
        $branchNumber = DB::table('partner_account as pa')
            ->join('partner_branch as pb', 'pb.partner_account_id', '=', 'pa.partner_account_id')
            ->where('pa.active', 1)
            ->where('pb.active', 1)
            ->count();

        $active_user = TransactionTable::distinct('customer_id')->select('customer_id')->get();

        $current_active_user = DB::select('select tt.customer_id
                                                from transaction_table tt
                                                join customer_info ci on ci.customer_id=tt.customer_id
                                                where ci.expiry_date > CURDATE()
                                                group by customer_id');

        $inactive_user = DB::table('customer_info as ci')
            ->select('ci.*')
            ->where('ci.customer_type', '!=', 3)
            ->whereNotIn('ci.customer_id', $active_user)->count();

        $active_offers = BranchOffers::where('active', 1)->where('selling_point', null)->count();
        $all_reviews = Review::count();

//        $expiring_customers = DB::select('select DATEDIFF(expiry_date, CURDATE()) as dcount
//                            from customer_info
//                            having dcount <= 10 and dcount > 0');

        $allUsers['allPartners'] = $partnersNumber;
        $allUsers['allBranches'] = $branchNumber;
        $allUsers['inactive_user'] = $inactive_user;
        $allUsers['active_user'] = count($current_active_user);
//        $allUsers['expiring_user'] = count($expiring_customers);
//        $allUsers['expired_user'] = CustomerInfo::where('expiry_date', '<=', date('Y-m-d'))->where('customer_type', '!=', 3)->count();
        $allUsers['total_transaction'] = TransactionTable::count();
        $allUsers['total_offers'] = $active_offers;
        $allUsers['total_reviews'] = $all_reviews;
        $allUsers['verified_email'] = (new AnalyticsController())->getVerifiedEmailPercentage();
        $allUsers['completed_profile'] = (new AnalyticsController())->getCompletedProfilePercentage();

        return $allUsers;
    }

    //function to get customer's age
    public function getAge($dob, $condate)
    {
        $birthdate = new DateTime(date('Y-m-d', strtotime(implode('-', array_reverse(explode('/', $dob))))));
        $today = new DateTime(date('Y-m-d', strtotime(implode('-', array_reverse(explode('/', $condate))))));
        $age = $birthdate->diff($today)->y;

        return $age;
    }

    //function to sort rbd statistics
    public function sortRbdPartnerVisitStatistics($year, $month, $partner_id)
    {
        $partner_visits = [];
        if ($year != null && $month == null && $partner_id == null) { //only year
            $partner_visits = DB::select("select count(partner_id) as total, partner_id, pi.partner_name
                    from rbd_statistics 
                            join partner_info as pi on partner_id = pi.partner_account_id
                    where visited_on like '$year%'
                    group by partner_id, pi.partner_name
                    order by total desc
                    limit 5");
        } elseif ($year != null && $month != null && $partner_id == null) { //year & month
            $partner_visits = DB::select("select count(partner_id) as total, partner_id, pi.partner_name
                    from rbd_statistics 
                            join partner_info as pi on partner_id = pi.partner_account_id
                    where visited_on like '$year-$month%'
                    group by partner_id, pi.partner_name
                    order by total desc
                    limit 5");
        } elseif ($year != null && $month == null && $partner_id != null) { //year & partner
            $partner_visits = DB::select("select count(partner_id) as total, partner_id, pi.partner_name
                    from rbd_statistics 
                            join partner_info as pi on partner_id = pi.partner_account_id
                    where visited_on like '$year%' and partner_id='$partner_id'
                    group by partner_id, pi.partner_name
                    order by total desc
                    limit 5");
        } elseif ($year != null && $month != null && $partner_id != null) { //year, month & partner
            $partner_visits = DB::select("select count(partner_id) as total, partner_id, pi.partner_name
                    from rbd_statistics 
                            join partner_info as pi on partner_id = pi.partner_account_id
                    where visited_on like '$year-$month%' and partner_id='$partner_id'
                    group by partner_id, pi.partner_name
                    order by total desc
                    limit 5");
        }
        $visits = [];
        foreach ($partner_visits as $key => $visit) {
            $visits[$key]['partner'] = $visit->partner_name;
            $visits[$key]['total'] = $visit->total;
            $app_visit = RbdStatistics::where([['partner_id', $visit->partner_id],
                ['visited_on', 'like', $year.'-'.$month.'%'], ['browser_data', 'like', 'Android Application%'], ])->count();
            $ios_app_visit = RbdStatistics::where([['partner_id', $visit->partner_id],
                ['visited_on', 'like', $year.'-'.$month.'%'], ['browser_data', 'like', 'iOS Application%'], ])->count();
            $visits[$key]['android_app'] = $app_visit;
            $visits[$key]['ios_app'] = $ios_app_visit;
            $visits[$key]['web'] = $visit->total - $app_visit;
        }

        return $visits;
    }

    //function to sort rbd statistics
    public function sortRbdPartnerTransactionStatistics($year, $month, $partner_branch_id)
    {
        $transactions = 0;
        if ($year != null && $month == null && $partner_branch_id == null) { //only year
            $transactions = DB::table('transaction_table as tt')
                ->join('customer_info as ci', 'ci.customer_id', '=', 'tt.customer_id')
                ->select('tt.branch_id', 'ci.customer_type')
                ->where('ci.customer_type', 2)
                ->where('tt.posted_on', 'like', $year.'%')
                ->count();
        } elseif ($year != null && $month != null && $partner_branch_id == null) { //year & month
            $transactions = DB::table('transaction_table as tt')
                ->join('customer_info as ci', 'ci.customer_id', '=', 'tt.customer_id')
                ->select('tt.branch_id', 'ci.customer_type')
                ->where('ci.customer_type', 2)
                ->where('tt.posted_on', 'like', $year.'-'.$month.'%')
                ->count();
        } elseif ($year != null && $month == null && $partner_branch_id != null) { //year & partner
            $transactions = DB::table('transaction_table as tt')
                ->join('customer_info as ci', 'ci.customer_id', '=', 'tt.customer_id')
                ->select('tt.branch_id', 'ci.customer_type')
                ->where('ci.customer_type', 2)
                ->where('tt.posted_on', 'like', $year.'%')
                ->where('tt.branch_id', $partner_branch_id)
                ->count();
        } elseif ($year != null && $month != null && $partner_branch_id != null) { //year, month & partner
            $transactions = DB::table('transaction_table as tt')
                ->join('customer_info as ci', 'ci.customer_id', '=', 'tt.customer_id')
                ->select('tt.branch_id', 'ci.customer_type')
                ->where('ci.customer_type', 2)
                ->where('tt.posted_on', 'like', $year.'-'.$month.'%')
                ->where('tt.branch_id', $partner_branch_id)
                ->count();
        } elseif ($year == null && $month == null && $partner_branch_id != null) { //partner_branch
            $transactions = DB::table('transaction_table as tt')
                ->join('customer_info as ci', 'ci.customer_id', '=', 'tt.customer_id')
                ->select('tt.branch_id', 'ci.customer_type')
                ->where('ci.customer_type', 2)
                ->where('tt.branch_id', $partner_branch_id)
                ->count();
        }

        return $transactions;
    }

    //function to sort registered users in rbd statistics
    public function sortRbdRegUserStatistics($year, $month)
    {
        $card_user = DB::table('customer_history as ch')
            ->join('ssl_transaction_table as stt', 'stt.id', '=', 'ch.ssl_id')
            ->select('ch.customer_id')
            ->where('ch.type', CustomerType::card_holder)
            ->where('stt.tran_date', 'like', $year.'-'.$month.'%')
            ->groupBy('customer_id')
            ->pluck('customer_id');
        $trial_user = DB::table('customer_history as ch')
            ->join('ssl_transaction_table as stt', 'stt.id', '=', 'ch.ssl_id')
            ->select('ch.customer_id')
            ->where('ch.type', CustomerType::trial_user)
            ->whereNotIn('ch.customer_id', $card_user)
            ->where('stt.tran_date', 'like', $year.'-'.$month.'%')
            ->get()
            ->groupBy('customer_id')
            ->count();

        $allCustomers = CustomerInfo::count();
        $guest_user = CustomerInfo::where('customer_type', 3)->where('member_since', 'like', $year.'-'.$month.'%')->count();

        return ['guest_user' => $guest_user, 'trial_user' => $trial_user, 'card_user' => count($card_user), 'allCustomers' => $allCustomers];
    }

    //function to sort users gender/age/area statistics
    public function sortRbdUserAreaStatistics($area, $format = null)
    {
        $rbdAnalytics = new Lavacharts();
        $data = $rbdAnalytics->DataTable();

        $users = DB::table('customer_info')
            ->where('customer_dob', '!=', null)
            ->where('customer_gender', '!=', null)
            ->where('customer_address', '!=', null)
            ->where('customer_address', 'like', $area.'%')
            ->get();
        $users = json_decode(json_encode($users), true);

        //get customer id by group
        $i = 0;
        $filtered_users = [];
        foreach ($users as $user) {
            //get dob & gender of each customer
            $info = DB::table('customer_info')
                ->select('customer_dob', 'customer_gender')
                ->where('customer_id', $user['customer_id'])
                ->get();
            $info = json_decode(json_encode($info), true);
            $info = $info[0];

            $age = $this->getAge($info['customer_dob'], date('Y-m-d'));
            $filtered_users[$user['customer_id']][$i]['age'] = $age;
            $filtered_users[$user['customer_id']][$i]['gender'] = $info['customer_gender'];
            $i++;
        }

        //get visiting info of male & female according to age
        //initialize some variables
        $male10 = $female10 = $male20 = $female20 = $male30 = $female30 = $male40 = $female40 = $male50 = $female50 = $male50plus = $female50plus = 0;
        $result['0-10']['male'] = $result['0-10']['female']
            = $result['10-20']['male'] = $result['10-20']['female']
            = $result['20-30']['male'] = $result['20-30']['female']
            = $result['30-40']['male'] = $result['30-40']['female']
            = $result['40-50']['male'] = $result['40-50']['female']
            = $result['50+']['male'] = $result['50+']['female'] = 0;

        foreach ($filtered_users as $key => $value) {
            foreach ($value as $user) {
                if (0 <= $user['age'] && $user['age'] <= 10) {
                    $user['gender'] == 'male' ? $male10 += 1 : $female10 += 1;
                } elseif (10 < $user['age'] && $user['age'] <= 20) {
                    $user['gender'] == 'male' ? $male20 += 1 : $female20 += 1;
                } elseif (20 < $user['age'] && $user['age'] <= 30) {
                    $user['gender'] == 'male' ? $male30 += 1 : $female30 += 1;
                } elseif (30 < $user['age'] && $user['age'] <= 40) {
                    $user['gender'] == 'male' ? $male40 += 1 : $female40 += 1;
                } elseif (40 < $user['age'] && $user['age'] <= 50) {
                    $user['gender'] == 'male' ? $male50 += 1 : $female50 += 1;
                } else {
                    $user['gender'] == 'male' ? $male50plus += 1 : $female50plus += 1;
                }
            }
        }

        //set male & female percentage according to age
        if ($male10 != 0) {
            $result['0-10']['male'] = $male10;
        }
        if ($female10 != 0) {
            $result['0-10']['female'] = $female10;
        }

        if ($male20 != 0) {
            $result['10-20']['male'] = $male20;
        }
        if ($female20 != 0) {
            $result['10-20']['female'] = $female20;
        }

        if ($male30 != 0) {
            $result['20-30']['male'] = $male30;
        }
        if ($female30 != 0) {
            $result['20-30']['female'] = $female30;
        }

        if ($male40 != 0) {
            $result['30-40']['male'] = $male40;
        }
        if ($female40 != 0) {
            $result['30-40']['female'] = $female40;
        }

        if ($male50 != 0) {
            $result['40-50']['male'] = $male50;
        }
        if ($female50 != 0) {
            $result['40-50']['female'] = $female50;
        }

        if ($male50plus != 0) {
            $result['50+']['male'] = $male50plus;
        }
        if ($female50plus != 0) {
            $result['50+']['female'] = $female50plus;
        }

        $data->addStringColumn('Age')
            ->addNumberColumn('Male')
            ->addNumberColumn('Female')
            ->addRow(['0-10', $result['0-10']['male'], $result['0-10']['female']])
            ->addRow(['10-20', $result['10-20']['male'], $result['10-20']['female']])
            ->addRow(['21-30', $result['20-30']['male'], $result['20-30']['female']])
            ->addRow(['31-40', $result['30-40']['male'], $result['30-40']['female']])
            ->addRow(['41-50', $result['40-50']['male'], $result['40-50']['female']])
            ->addRow(['50+', $result['50+']['male'], $result['50+']['female']]);

        $rbdAnalytics->ColumnChart('UserStats', $data, [
            'title' => 'Age, gender & area statistics (Gender ratio against Age)',
            'titleTextStyle' => [
                'fontSize' => 14,
            ],
            'legend' => [
                'position' => 'out',
            ],
        ]);

        //check for json response format
        if ($format == 'json') {
            return $data->toJson();
        }
        $rbdAnalytics->ColumnChart('UserStats', $data);

        return $rbdAnalytics;
    }

    //function to get statistics of partner from transaction table
    public function analyticsOfPartner($partnerId)
    {
        //create a lavaChart object
        $lava = new Lavacharts();
        $data = $lava->DataTable();
        //get all transactions of this partner
        $transactions = DB::table('transaction_table as tt')
            ->join('partner_branch as pb', 'pb.id', '=', 'tt.branch_id')
            ->where('pb.partner_account_id', $partnerId)
            ->get();
        $transactions = json_decode(json_encode($transactions), true);

        $MONTHS['January'] = $MONTHS['February'] = $MONTHS['March'] = $MONTHS['April'] = $MONTHS['May'] = $MONTHS['June'] =
        $MONTHS['July'] = $MONTHS['August'] = $MONTHS['September'] = $MONTHS['October'] = $MONTHS['November'] = $MONTHS['December'] = 0;
        if ($transactions) {
            foreach ($transactions as $transaction) {
                $time = strtotime($transaction['posted_on']);
                $month = date('F', $time);
                $MONTHS[$month] = $MONTHS[$month] + 1;
            }
            $MONTHS = json_decode(json_encode($MONTHS), true);
        }

        $data->addStringColumn('Month')
            ->addNumberColumn('Transactions')
            ->addRow(['Jan', $MONTHS['January']])
            ->addRow(['Feb', $MONTHS['February']])
            ->addRow(['Mar', $MONTHS['March']])
            ->addRow(['Apr', $MONTHS['April']])
            ->addRow(['May', $MONTHS['May']])
            ->addRow(['Jun', $MONTHS['June']])
            ->addRow(['Jul', $MONTHS['July']])
            ->addRow(['Aug', $MONTHS['August']])
            ->addRow(['Sept', $MONTHS['September']])
            ->addRow(['Oct', $MONTHS['October']])
            ->addRow(['Nov', $MONTHS['November']])
            ->addRow(['Dec', $MONTHS['December']]);

        $lava->AreaChart('Transaction', $data, [
            'title' => 'Monthly transaction analysis of '.date('Y').' \n(Number of customers against Months)',
            'legend' => [
                'position' => 'out',
            ],
        ]);
        $totalTransactions = count($transactions);
        $lava->AreaChart('Transactions', $data);
        self::partnerPieChart($lava, $transactions);
        self::partnerSalesStatistics($lava, $partnerId);
        self::partnerAgeAndGenderStatistics($lava, $partnerId, $totalTransactions);

        return $lava;
    }

    //function to get data for gender statistics
    public function partnerPieChart($lava, $transactions)
    {
        $data = $lava->DataTable();
        //initialize gender counter
        $m = $f = 0;
        foreach ($transactions as $transaction) {
            $customer_info = DB::table('customer_info')
                ->select('customer_gender')
                ->where('customer_id', $transaction['customer_id'])
                ->get();
            $customer_info = json_decode(json_encode($customer_info), true);
            if ($customer_info[0]['customer_gender'] == 'male') {
                $m++;
            } else {
                $f++;
            }
        }
        $data->addStringColumn('Reasons')
            ->addNumberColumn('Percent')
            ->addRow(['Male', $m])
            ->addRow(['Female', $f]);

        $lava->PieChart('visitPartner', $data, [
            'title' => 'Gender Demographics',
            'is3D' => true,
            'slices' => [
                ['offset' => 0], //0.2
                ['offset' => 0], //0.25
                ['offset' => 0], //0.3
            ],
        ]);
    }

    public function getRatingCounter($partner_account_id)
    {
        $star_1 = Review::where('partner_account_id', $partner_account_id)->where('rating', 1)->count();
        $star_2 = Review::where('partner_account_id', $partner_account_id)->where('rating', 2)->count();
        $star_3 = Review::where('partner_account_id', $partner_account_id)->where('rating', 3)->count();
        $star_4 = Review::where('partner_account_id', $partner_account_id)->where('rating', 4)->count();
        $star_5 = Review::where('partner_account_id', $partner_account_id)->where('rating', 5)->count();
        $total_review = $star_1 + $star_2 + $star_3 + $star_4 + $star_5;

        return ['1_star' => $star_1, '2_star' => $star_2, '3_star' => $star_3, '4_star' => $star_4, '5_star' => $star_5, 'total' => $total_review];
    }

    //function to get daily sales statistics (money)
    public function partnerSalesStatistics($lava, $partnerId)
    {
        $data = $lava->DataTable();
        $data->addStringColumn('Days')
            ->addNumberColumn('Sales');
        $current_date = date('Y-m');
        //set every day's sales statistics value
        for ($i = 1; $i <= 31; $i++) {
            $j = $i < 10 ? 0 : '';
            //get every day's total sales
            $total_spent = DB::table('transaction_table as tt')
                ->join('partner_branch as pb', 'pb.id', '=', 'tt.branch_id')
                ->where('pb.partner_account_id', $partnerId)
                ->where('tt.posted_on', 'like', $current_date.'-'.$j.$i.'%')
                ->sum('tt.amount_spent');
            $data->addRow([
                $i, $total_spent,
            ]);
        }

        $lava->AreaChart('Sales', $data, [
            'title' => 'Daily sales analysis of '.date('F').' \n(Amount of money in BDT against number of days)',
            'legend' => [
                'position' => 'out',
            ],
        ]);
    }

    //function to get age & gender statistics
    public function partnerAgeAndGenderStatistics($lava, $partnerId, $totalTransactions)
    {
        $data = $lava->DataTable();
        //get customer id by group
        $transactions = DB::table('transaction_table as tt')
            ->join('partner_branch as pb', 'pb.id', '=', 'tt.branch_id')
            ->select('tt.customer_id')
            ->where('pb.partner_account_id', $partnerId)
            ->groupby('tt.customer_id')
            ->get();
        $transactions = json_decode(json_encode($transactions), true);
        $users = [];
        $i = 0;
        foreach ($transactions as $transaction) {
            //get dob & gender of each customer
            $info = DB::table('customer_info')
                ->select('customer_dob', 'customer_gender')
                ->where('customer_id', $transaction['customer_id'])
                ->get();
            $info = json_decode(json_encode($info), true);
            $info = $info[0];
            //get how many times customer visited the partner
            $visited = DB::table('transaction_table as tt')
                ->join('partner_branch as pb', 'pb.id', '=', 'tt.branch_id')
                ->where('pb.partner_account_id', $partnerId)
                ->where('tt.customer_id', $transaction['customer_id'])
                ->count();

            $age = $this->getAge($info['customer_dob'], date('Y-m-d'));
            $users[$transaction['customer_id']][$i]['age'] = $age;
            $users[$transaction['customer_id']][$i]['gender'] = $info['customer_gender'];
            $users[$transaction['customer_id']][$i]['total_visits'] = $visited;
            $i++;
        }
        //get visiting info of male & female according to age
        //initialize some variables
        $male10 = $female10 = $male20 = $female20 = $male30 = $female30 = $male40 = $female40 = $male50 = $female50 = $male50plus = $female50plus = 0;
        $result['0-10']['male'] = $result['0-10']['female']
            = $result['10-20']['male'] = $result['10-20']['female']
            = $result['20-30']['male'] = $result['20-30']['female']
            = $result['30-40']['male'] = $result['30-40']['female']
            = $result['40-50']['male'] = $result['40-50']['female']
            = $result['50+']['male'] = $result['50+']['female'] = 0;

        foreach ($users as $key => $value) {
            foreach ($value as $user) {
                if (0 <= $user['age'] && $user['age'] <= 10) {
                    $user['gender'] == 'male' ? $male10 += $user['total_visits'] : $female10 += $user['total_visits'];
                } elseif (10 < $user['age'] && $user['age'] <= 20) {
                    $user['gender'] == 'male' ? $male20 += $user['total_visits'] : $female20 += $user['total_visits'];
                } elseif (20 < $user['age'] && $user['age'] <= 30) {
                    $user['gender'] == 'male' ? $male30 += $user['total_visits'] : $female30 += $user['total_visits'];
                } elseif (30 < $user['age'] && $user['age'] <= 40) {
                    $user['gender'] == 'male' ? $male40 += $user['total_visits'] : $female40 += $user['total_visits'];
                } elseif (40 < $user['age'] && $user['age'] <= 50) {
                    $user['gender'] == 'male' ? $male50 += $user['total_visits'] : $female50 += $user['total_visits'];
                } else {
                    $user['gender'] == 'male' ? $male50plus += $user['total_visits'] : $female50plus += $user['total_visits'];
                }
            }
        }
        //set male & female percentage according to age
        if ($male10 != 0) {
            $result['0-10']['male'] = round(($male10 / $totalTransactions) * 100);
        }
        if ($female10 != 0) {
            $result['0-10']['female'] = round(($female10 / $totalTransactions) * 100);
        }

        if ($male20 != 0) {
            $result['10-20']['male'] = round(($male20 / $totalTransactions) * 100);
        }
        if ($female20 != 0) {
            $result['10-20']['female'] = round(($female20 / $totalTransactions) * 100);
        }

        if ($male30 != 0) {
            $result['20-30']['male'] = round(($male30 / $totalTransactions) * 100);
        }
        if ($female30 != 0) {
            $result['20-30']['female'] = round(($female30 / $totalTransactions) * 100);
        }

        if ($male40 != 0) {
            $result['30-40']['male'] = round(($male40 / $totalTransactions) * 100);
        }
        if ($female40 != 0) {
            $result['30-40']['female'] = round(($female40 / $totalTransactions) * 100);
        }

        if ($male50 != 0) {
            $result['40-50']['male'] = round(($male50 / $totalTransactions) * 100);
        }
        if ($female50 != 0) {
            $result['40-50']['female'] = round(($female50 / $totalTransactions) * 100);
        }

        if ($male50plus != 0) {
            $result['50+']['male'] = round(($male50plus / $totalTransactions) * 100);
        }
        if ($female50plus != 0) {
            $result['50+']['female'] = round(($female50plus / $totalTransactions) * 100);
        }

        $data->addStringColumn('Age')
            ->addNumberColumn('Male')
            ->addNumberColumn('Female')
            ->setDateTimeFormat('Y')
            ->addRow(['0-10', $result['0-10']['male'], $result['0-10']['female']])
            ->addRow(['10-20', $result['10-20']['male'], $result['10-20']['female']])
            ->addRow(['21-30', $result['20-30']['male'], $result['20-30']['female']])
            ->addRow(['31-40', $result['30-40']['male'], $result['30-40']['female']])
            ->addRow(['41-50', $result['40-50']['male'], $result['40-50']['female']])
            ->addRow(['50+', $result['50+']['male'], $result['50+']['female']]);

        $lava->ColumnChart('ageGender', $data, [
            'title' => 'Age & gender statistics (Percentage of Gender ratio against Age)',
            'titleTextStyle' => [
                'fontSize' => 14,
            ],
            'legend' => [
                'position' => 'out',
            ],
        ]);
    }

    //function to get total like number of a customer
    public static function likeNumber($customerId)
    {
        //all review ids of this specific customer
        $review_ids = Review::select('id')->where('customer_id', $customerId)->get();
        $review_ids = json_decode(json_encode($review_ids), true);
        $likeNumber = 0;
        foreach ($review_ids as $id) {
            //total likes of each review
            $like_num = DB::table('likes_review')
                ->where('review_id', $id)
                ->count();
            $likeNumber += $like_num;
        }

        return $likeNumber;
    }

    //function to get total review number of a customer
    public static function reviewNumber($customerId)
    {
        $reviewNumber = Review::where('customer_id', $customerId)->count();

        return $reviewNumber;
    }

    //function to get total branch of a partner
    public function branchCount($partnerId)
    {
        $branchCount = DB::table('partner_branch')
            ->where('partner_account_id', $partnerId)
            ->where('active', '=', 1)
            ->count();

        return $branchCount;
    }

    //function to get all branches of a partner
    public function branchesOfPartner($partnerId)
    {
        $all_branches = PartnerAccount::where('partner_account_id', $partnerId)
            ->with(['branches' => function ($query) {
                $query->where('active', '=', 1);
            }])->first();

        return $all_branches;
    }

    //function to get main branch id of a partner
    public function mainBranchOfPartner($partnerId)
    {
        $main_branch = PartnerBranch::where('partner_account_id', $partnerId)
            ->where('main_branch', 1)->get();

        return $main_branch;
    }

    //function to get branch info of a partner
    public function BranchInfoOfPartner($branchId)
    {
        $branch_info = PartnerBranch::where('id', $branchId)->with('info.account')->first();

        return $branch_info;
    }

    //function to get partner info by ID
    public function partnerInfoById($partnerId)
    {
        $partner_info = PartnerInfo::where('partner_account_id', $partnerId)->first();

        return $partner_info;
    }

    //function to get B2B2C client info by ID
    public function clientInfoById($clientId)
    {
        $client_info = B2b2cInfo::where('id', $clientId)->first();

        return $client_info;
    }

    //function to get B2B2C client info by Customer ID
    public function clientInfoByCusId($customer_id)
    {
        $client_info = B2b2cUser::with('b2b2cInfo')->where('customer_id', $customer_id)->first();

        return $client_info;
    }

    //function to get partner having multiple branches in same area
    public function hasMultipleBranchSameArea($partnerId)
    {
        $total_branch_count = PartnerBranch::where('partner_account_id', $partnerId)->count();
        $unique_area_count = PartnerBranch::where('partner_account_id', $partnerId)->groupby('partner_area')->count();

        if ($total_branch_count != $unique_area_count) {
            return 1;
        } else {
            return 0;
        }
    }

    //function to pass change status of a customer
    public function passChangeCount()
    {
        $pass_counter = DB::table('pass_changed')->select('pass_change')->where('customer_id', Session::get('customer_id'))->first();

        return $pass_counter;
    }

    //function to get nearby partners for partner profile
    public function nearbyPartners($name, $area, $lat, $long)
    {
        $nearbyPartners = DB::table('partner_info as pi')
            ->join('partner_account as pa', 'pa.partner_account_id', '=', 'pi.partner_account_id')
            ->join('partner_branch as pb', 'pb.partner_account_id', '=', 'pi.partner_account_id')
            ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
            ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
            ->select(
                'pi.partner_account_id',
                'pi.partner_name',
                'pb.id',
                'pb.longitude',
                'pb.latitude',
                'pb.partner_area',
                'pb.partner_address',
                'pi.partner_category',
                'rat.average_rating',
                'ppi.partner_profile_image'
            )
            ->where('pb.partner_area', $area)
            ->where('pa.active', 1)
            ->where('pb.active', 1)
            ->whereRaw('REPLACE (pi.partner_name,"\'","")!="'.$name.'"')
            ->get();
        $i = 0;
        foreach ($nearbyPartners as $nearbyPartner) {
            //get reviews number of nearby partners
            $reviews = DB::table('review')
                ->where('partner_account_id', $nearbyPartner->partner_account_id)
                ->count();
            $nearbyPartners[$i]->review_number = $reviews;
            //get distance from this partner to nearby partners
            $distance = $this->calculateDistance($lat, $long, $nearbyPartner->latitude, $nearbyPartner->longitude, 'K');
            $nearbyPartners[$i]->distance = $distance;
            $i++;
        }

        return $nearbyPartners;
    }

    //function to calculate distance between two partners
    public function calculateDistance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);
        //distance in kilometer
        if ($unit == 'K') {
            return round(($miles * 1.609344), 2);
        //distance in nautical mile
        } elseif ($unit == 'N') {
            return $miles * 0.8684;
        //distance in mile
        } else {
            return $miles;
        }
    }

    //function to get transaction history of customer
    public function customerTransaction($customerID)
    {
        $transactions = TransactionTable::where('customer_id', $customerID)->with('branch.info.profileImage', 'offer')
            ->orderBy('id', 'DESC')
            ->get();
        $transactions = $transactions->where('offer.selling_point', null);
        $point_sum = TransactionTable::where('customer_id', $customerID)->sum('transaction_point');

        $transactions = array_values(json_decode(json_encode($transactions), true));
        $transactionsHistory['transactions'] = $transactions;
        $transactionsHistory['total_point'] = $point_sum;

        return $transactionsHistory;
    }

    //function to get transaction history of a partner
    //    public function partnerTransaction($partnerID)
    //    {
    //        $transactions = DB::table('transaction_table as tt')
    //            ->join('partner_branch as pb','pb.id','=','tt.branch_id')
    //            ->leftJoin('customer_info as ci', 'ci.customer_id', '=', 'tt.customer_id')
    //            ->leftJoin('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
    //            ->leftJoin('bonus_request as br', 'br.req_id', '=', 'tt.req_id')
    //            ->leftJoin('all_coupons as ac', function ($join) {
    //                $join->on('ac.branch_id', '=', 'tt.branch_id')
    //                    ->on('ac.id', '=', 'br.coupon_id');
    //            })
    //            ->select('tt.customer_id', 'tt.amount_spent', 'tt.posted_on', 'tt.discount_amount', 'ci.customer_first_name',
    //                'ci.customer_last_name', 'ca.customer_username', 'ac.coupon_type', 'ac.reward_text')
    //            ->where('pb.partner_account_id', $partnerID)
    //            ->orderBy('tt.posted_on', 'DESC')
    //            ->get();
    //        $transactions = json_decode(json_encode($transactions), true);
    //        //total amount that all users spent on this partner
    //        $amount_sum = DB::table('transaction_table as tt')
    //            ->join('partner_branch as pb','pb.id','=','tt.branch_id')
    //            ->where('pb.partner_account_id', $partnerID)
    //            ->sum('tt.amount_spent');
    //        //total amount of discounts partner provided to its user
    //        $discount_sum = DB::table('transaction_table as tt')
    //            ->join('partner_branch as pb','pb.id','=','tt.branch_id')
    //            ->where('pb.partner_account_id', $partnerID)
    //            ->sum('tt.discount_amount');
    //        $topTransaction = $this->partnerTopTransactions($partnerID);
    //        $transactionHistory['transaction'] = $transactions;
    //        $transactionHistory['amount_sum'] = $amount_sum;
    //        $transactionHistory['discount_sum'] = $discount_sum;
    //        $transactionHistory['top_transaction'] = $topTransaction;
    //        return $transactionHistory;
    //    }

    //function to get transaction history of a Branch
    public function branchTransaction($branchID)
    {
        $transactions = TransactionTable::where('branch_id', $branchID)->with('customer', 'bonus.coupon', 'offer')
            ->orderBy('id', 'DESC')->get();
        $point_sum = $transactions->sum('transaction_point');
        $transactionsHistory['transactions'] = $transactions;
        $transactionsHistory['total_point'] = $point_sum;

        return $transactionsHistory;
    }

    //function to get top 5 transaction of a partner
    //    public function partnerTopTransactions($partnerID)
    //    {
    //        $topTransaction = DB::table('transaction_table as tt')
    //            ->join('partner_branch as pb','pb.id','=','tt.branch_id')
    //            ->join('customer_info as ci', 'ci.customer_id', '=', 'tt.customer_id')
    //            ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
    //            ->select('ci.customer_first_name', 'ci.customer_last_name', 'ci.customer_profile_image',
    //                'ca.customer_username', 'tt.amount_spent')
    //            ->orderBy('tt.amount_spent', 'DESC')
    //            ->where('pb.partner_account_id', $partnerID)
    //            ->limit(5)
    //            ->get();
    //        $topTransaction = json_decode(json_encode($topTransaction), true);
    //        return $topTransaction;
    //    }

    //function to get top 5 transaction of a Branch
    public function branchTopTransactions($branchID)
    {
        $topTransaction = DB::table('transaction_table as tt')
            ->join('customer_info as ci', 'ci.customer_id', '=', 'tt.customer_id')
            ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
            ->select(
                'ci.customer_first_name',
                'ci.customer_last_name',
                'ci.customer_profile_image',
                'ca.customer_username',
                'tt.amount_spent'
            )
            ->orderBy('tt.amount_spent', 'DESC')
            ->where('tt.branch_id', $branchID)
            ->limit(5)
            ->get();
        $topTransaction = json_decode(json_encode($topTransaction), true);

        return $topTransaction;
    }

    //function to get top users who spent most to a specific partner
    public function topUsersInTransaction($partnerID)
    {
        $topUsers = DB::table('transaction_table as tt')
            ->join('partner_branch as pb', 'pb.id', '=', 'tt.branch_id')
            ->select('tt.customer_id', DB::raw('SUM(tt.amount_spent) as total_amount'))
            ->where('pb.partner_account_id', $partnerID)
            ->groupBy('tt.customer_id')
            ->get();
        $topUsers = json_decode(json_encode($topUsers), true);
        //sorting this array in DESC order according to total spent amount
        $array_column = array_column($topUsers, 'total_amount');
        array_multisort($array_column, SORT_DESC, $topUsers);
        //get top 5 user who spent most
        $topUsers = array_slice($topUsers, 0, 5, true);
        //get info of user
        $i = 0;
        foreach ($topUsers as $user) {
            $userInfo = DB::table('customer_info as ci')
                ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
                ->select('ci.customer_first_name', 'ci.customer_last_name', 'ci.customer_profile_image', 'ca.customer_username')
                ->where('ci.customer_id', $user['customer_id'])
                ->get();
            $userInfo = json_decode(json_encode($userInfo), true);
            $topUsers[$i]['info'] = $userInfo[0];
            $i++;
        }

        return $topUsers;
    }

    //function to get top users who spent most to a specific Branch
    public function topUsersTransactionInBranch($branchID)
    {
        $topUsers = DB::table('transaction_table as tt')
            ->join('partner_branch as pb', 'pb.id', '=', 'tt.branch_id')
            ->select('tt.customer_id', DB::raw('COUNT(tt.customer_id) as total_trans'))
            ->where('tt.branch_id', $branchID)
            ->groupBy('tt.customer_id')
            ->orderBy('total_trans', 'DESC')
            ->get();
        $topUsers = $topUsers->take(5);
        //get user info
        $i = 0;
        foreach ($topUsers as $user) {
            $userInfo = DB::table('customer_info as ci')
                ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
                ->select('ci.customer_full_name', 'ci.customer_profile_image', 'ca.customer_username')
                ->where('ci.customer_id', $user->customer_id)
                ->first();
            $topUsers[$i]->info = $userInfo;
            $i++;
        }

        return $topUsers;
    }

    //function to get top reviewers of a specific partner
    public function topReviewers($partnerID)
    {
        $topReviewers = DB::table('review')
            ->select('customer_id', DB::raw('count(customer_id) as total_review'))
            ->where('partner_account_id', $partnerID)
            ->groupBy('customer_id')
            ->orderBy('total_review', 'DESC')
            ->take(5)
            ->get();
        $topReviewers = json_decode(json_encode($topReviewers), true);
        //get info of user
        $i = 0;
        foreach ($topReviewers as $reviewer) {
            $userInfo = DB::table('customer_info as ci')
                ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
                ->select('ci.customer_first_name', 'ci.customer_last_name', 'ci.customer_profile_image', 'ca.customer_username')
                ->where('ci.customer_id', $reviewer['customer_id'])
                ->get();
            $userInfo = json_decode(json_encode($userInfo), true);
            $topReviewers[$i]['info'] = $userInfo[0];
            $i++;
        }

        return $topReviewers;
    }

    //function to get recent 5 followers of partner
    public function recentFollowers($partnerID)
    {
        $recentFollowers = DB::table('follow_partner as fp')
            ->join('customer_info as ci', 'ci.customer_id', '=', 'fp.follower')
            ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
            ->select('ci.customer_first_name', 'ci.customer_last_name', 'ci.customer_profile_image', 'ca.customer_username')
            ->where('fp.following', $partnerID)
            ->orderBy('fp.posted_on', 'DESC')
            ->limit(5)
            ->get();
        $recentFollowers = json_decode(json_encode($recentFollowers), true);

        return $recentFollowers;
    }

    //function to get recent 5 followers of customer
    public function recentFollowersOfCustomer($customerID)
    {
        $recentFollowers = DB::table('follow_customer as fc')
            ->join('customer_info as ci', 'ci.customer_id', '=', 'fc.follower')
            ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
            ->select('ci.customer_first_name', 'ci.customer_last_name', 'ci.customer_profile_image', 'ca.customer_username', 'fc.follow_request')
            ->where('fc.following', $customerID)
            ->where('fc.follow_request', 1)
            ->orderBy('fc.posted_on', 'DESC')
            ->limit(5)
            ->get();
        $recentFollowers = json_decode(json_encode($recentFollowers), true);

        return $recentFollowers;
    }

    //function to handle multiple login error attempts
    public function invalidLoginAttempts()
    {
        //get ip of the user
        $ip_address = request()->ip();
        //check if this ip already exists or not
        $existing_ip = DB::table('error')
            ->where('ip_address', $ip_address)
            ->get();
        $existing_ip = json_decode(json_encode($existing_ip), true);
        $existing_ip = $existing_ip[0];
        //if ip already exists & attempt is more than 5
        if (count($existing_ip) > 0 && $existing_ip['login_error'] >= 5) {
            //            set start & end time of ip blocking
            if ($existing_ip['starttime'] == null && $existing_ip['endtime'] == null) {
                DB::table('error')
                    ->where('ip_address', $ip_address)
                    ->update([
                        'starttime' => date('Y-m-d h:i:s a', strtotime('+6 hours')),
                        'endtime' => date('Y-m-d h:i:s a', strtotime('+370 minutes')),
                    ]);
            //                redirect user to home page as ip is blocked
            } elseif ($existing_ip['endtime'] > date('Y-m-d h:i:s a', strtotime('+6 hours'))) {
                header('Location: https://www.royaltybd.com');
                die();
            //                set end time to null as blocking time is expired
            } else {
                DB::table('error')
                    ->where('ip_address', $ip_address)
                    ->update([
                        'login_error' => 0,
                        'starttime' => null,
                        'endtime' => null,
                    ]);
            }
            //if ip already exists but attempt is less than 5
        } elseif (count($existing_ip) > 0 && $existing_ip['login_error'] < 5) {
            DB::table('error')->where('ip_address', '=', $ip_address)->increment('login_error', 1);
        } else {/*if ip doesn't exist*/
            DB::table('error')->insert([
                [
                    'ip_address' => $ip_address,
                    'login_error' => 1,
                ],
            ]);
        }
    }

    //function to encrypt and decrypt password
    public function encrypt_decrypt($action, $string)
    {
        $output = false;
        $encrypt_method = 'AES-256-CBC';
        $secret_key = 'Royalty';
        $secret_iv = 'BD';
        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } elseif ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }

    //function to encrypt something at social media share (Reviews)
    public function socialShareEncryption($action, $string)
    {
        $output = false;
        $encrypt_method = 'AES-256-CBC';
        $secret_key = 'REVIEW_LIVE1';
        $secret_iv = 'RBD_LIVE1';
        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } elseif ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }

    //function to encrypt something at social media share (POSTS)
    public function postShareEncryption($action, $string)
    {
        $output = false;
        $encrypt_method = 'AES-256-CBC';
        $secret_key = 'POSTS_LIVE1';
        $secret_iv = 'RBD_LIVE1';
        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } elseif ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }

    //function for get all unseen notifications of customer
    public function customerUnseenNotifications($customer_id)
    {
        //customer's all unseen notifications
        $notifications = DB::table('customer_notification')
            ->where('user_id', $customer_id)
            ->where('seen', 0)
            ->orderBy('id', 'DESC')
            ->get();
        $notifications = json_decode(json_encode($notifications), true);
        for ($i = 0; $i < count($notifications); $i++) {
            if ($notifications[$i]['notification_type'] == '1') { //other customers liked one's review
                $likerType = DB::table('likes_review')
                    ->select('liker_type')
                    ->where('id', $notifications[$i]['source_id'])
                    ->first();
                if ($likerType->liker_type == LikerType::customer) {
                    $customerInfo = DB::table('customer_info as ci')
                        ->join('likes_review as lr', 'lr.liker_id', '=', 'ci.customer_id')
                        ->select('ci.customer_first_name', 'ci.customer_last_name', 'ci.customer_profile_image', 'lr.review_id')
                        ->where('lr.id', $notifications[$i]['source_id'])
                        ->get();
                    $customerInfo = json_decode(json_encode($customerInfo), true);
                    if ($customerInfo) {
                        $customerInfo = $customerInfo[0];
                        $notifications[$i]['liker_name'] = $customerInfo['customer_first_name'].' '.$customerInfo['customer_last_name'];
                        $notifications[$i]['liker_profile_image'] = $customerInfo['customer_profile_image'];
                        $notifications[$i]['liked_review_id'] = $customerInfo['review_id'];
                    }
                } elseif ($likerType->liker_type == LikerType::partner) {
                    $partnerInfo = DB::table('partner_info as pi')
                        ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                        ->join('likes_review as lr', 'lr.liker_id', '=', 'pi.partner_account_id')
                        ->select('pi.partner_name', 'ppi.partner_profile_image', 'lr.review_id')
                        ->where('lr.id', $notifications[$i]['source_id'])
                        ->get();
                    $partnerInfo = json_decode(json_encode($partnerInfo), true);
                    if ($partnerInfo) {
                        $partnerInfo = $partnerInfo[0];
                        $notifications[$i]['liker_name'] = $partnerInfo['partner_name'];
                        $notifications[$i]['liker_profile_image'] = $partnerInfo['partner_profile_image'];
                        $notifications[$i]['liked_review_id'] = $partnerInfo['review_id'];
                    }
                }
            } elseif ($notifications[$i]['notification_type'] == '3') { //discount notification
                $partnerInfo = DB::table('transaction_table as tran')
                    ->join('partner_branch as pb', 'pb.id', '=', 'tran.branch_id')
                    ->join('partner_info as pi', 'pi.partner_account_id', '=', 'pb.partner_account_id')
                    ->select('pi.partner_name', 'tran.customer_id', 'tran.branch_id', 'pb.partner_area')
                    ->where('tran.id', $notifications[$i]['source_id'])
                    ->get();
                $partnerInfo = json_decode(json_encode($partnerInfo), true);
                if ($partnerInfo) {
                    $partnerInfo = $partnerInfo[0];
                    $notifications[$i]['partner_name'] = $partnerInfo['partner_name'];
                    $notifications[$i]['branch_id'] = $partnerInfo['branch_id'];
                    $notifications[$i]['partner_area'] = $partnerInfo['partner_area'];
                }
            } elseif ($notifications[$i]['notification_type'] == '6') { //reply notification
                $partnerInfo = DB::table('review_comment as rc')
                    ->join('review as rev', 'rev.id', '=', 'rc.review_id')
                    ->join('partner_info as pi', 'pi.partner_account_id', '=', 'rev.partner_account_id')
                    ->select('pi.partner_name', 'rev.id')
                    ->where('rev.id', $notifications[$i]['source_id'])
                    ->get();
                $partnerInfo = json_decode(json_encode($partnerInfo), true);
                if ($partnerInfo) {
                    $partnerInfo = $partnerInfo[0];
                    $notifications[$i]['partner_name'] = $partnerInfo['partner_name'];
                    $notifications[$i]['review_id'] = $partnerInfo['id'];
                }
            } elseif ($notifications[$i]['notification_type'] == '8') { //customer follow notification
                $customerInfo = DB::table('customer_account as ca')
                    ->join('customer_info as ci', 'ci.customer_id', '=', 'ca.customer_id')
                    ->join('follow_customer as fc', 'fc.follower', '=', 'ci.customer_id')
                    ->select('ca.customer_username', 'ci.customer_first_name', 'ci.customer_last_name', 'fc.follow_request')
                    ->where('fc.id', $notifications[$i]['source_id'])
                    ->get();
                $customerInfo = json_decode(json_encode($customerInfo), true);
                if ($customerInfo != null) {
                    $customerInfo = $customerInfo[0];
                    $notifications[$i]['customer_name'] = $customerInfo['customer_first_name'].' '.$customerInfo['customer_last_name'];
                    $notifications[$i]['customer_username'] = $customerInfo['customer_username'];
                    $notifications[$i]['follow_request'] = $customerInfo['follow_request'];
                }
            } elseif ($notifications[$i]['notification_type'] == '9') { //accept follow request notification
                $customerInfo = DB::table('customer_account as ca')
                    ->join('customer_info as ci', 'ci.customer_id', '=', 'ca.customer_id')
                    ->join('follow_customer as fc', 'fc.following', '=', 'ci.customer_id')
                    ->select('ca.customer_username', 'ci.customer_full_name', 'fc.follow_request')
                    ->where('fc.id', $notifications[$i]['source_id'])
                    ->get();
                $customerInfo = json_decode(json_encode($customerInfo), true);
                if ($customerInfo != null) {
                    $customerInfo = $customerInfo[0];
                    $notifications[$i]['customer_name'] = $customerInfo['customer_full_name'];
                    $notifications[$i]['customer_username'] = $customerInfo['customer_username'];
                    $notifications[$i]['follow_request'] = $customerInfo['follow_request'];
                }
            } elseif ($notifications[$i]['notification_type'] == '10') { //refer notification
                $customerInfo = DB::table('customer_account')
                    ->select('customer_username')
                    ->where('customer_id', $notifications[$i]['user_id'])
                    ->get();
                $customerInfo = json_decode(json_encode($customerInfo), true);
                if ($customerInfo != null) {
                    $customerInfo = $customerInfo[0];
                    $notifications[$i]['customer_username'] = $customerInfo['customer_username'];
                }
            } elseif ($notifications[$i]['notification_type'] == '11') { //refer notification
                $customerInfo = DB::table('customer_account')
                    ->select('customer_username')
                    ->where('customer_id', $notifications[$i]['user_id'])
                    ->get();
                $customerInfo = json_decode(json_encode($customerInfo), true);
                if ($customerInfo != null) {
                    $customerInfo = $customerInfo[0];
                    $notifications[$i]['customer_username'] = $customerInfo['customer_username'];
                }
            } else {
                //do nothing
            }
        }

        return $notifications;
    }

    //function for get all unseen notifications of customer
    public function customerSeenNotifications($customer_id)
    {
        //customer's all seen notifications
        $notifications = DB::table('customer_notification')
            ->where('user_id', $customer_id)
            ->where('seen', 1)
            ->orderBy('id', 'DESC')
            ->get();
        $notifications = json_decode(json_encode($notifications), true);
        for ($i = 0; $i < count($notifications); $i++) {
            if ($notifications[$i]['notification_type'] == '1') { //other customers liked one's review

                $likerType = DB::table('likes_review')
                    ->select('liker_type')
                    ->where('id', $notifications[$i]['source_id'])
                    ->first();
                if ($likerType->liker_type == LikerType::customer) {
                    $customerInfo = DB::table('customer_info as ci')
                        ->join('likes_review as lr', 'lr.liker_id', '=', 'ci.customer_id')
                        ->select('ci.customer_first_name', 'ci.customer_last_name', 'ci.customer_profile_image', 'lr.review_id')
                        ->where('lr.id', $notifications[$i]['source_id'])
                        ->get();
                    $customerInfo = json_decode(json_encode($customerInfo), true);
                    if ($customerInfo) {
                        $customerInfo = $customerInfo[0];
                        $notifications[$i]['liker_name'] = $customerInfo['customer_first_name'].' '.$customerInfo['customer_last_name'];
                        $notifications[$i]['liker_profile_image'] = $customerInfo['customer_profile_image'];
                        $notifications[$i]['liked_review_id'] = $customerInfo['review_id'];
                    }
                } elseif ($likerType->liker_type == LikerType::partner) {
                    $partnerInfo = DB::table('partner_info as pi')
                        ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                        ->join('likes_review as lr', 'lr.liker_id', '=', 'pi.partner_account_id')
                        ->select('pi.partner_name', 'ppi.partner_profile_image', 'lr.review_id')
                        ->where('lr.id', $notifications[$i]['source_id'])
                        ->get();
                    $partnerInfo = json_decode(json_encode($partnerInfo), true);
                    if ($partnerInfo) {
                        $partnerInfo = $partnerInfo[0];
                        $notifications[$i]['liker_name'] = $partnerInfo['partner_name'];
                        $notifications[$i]['liker_profile_image'] = $partnerInfo['partner_profile_image'];
                        $notifications[$i]['liked_review_id'] = $partnerInfo['review_id'];
                    }
                }
            } elseif ($notifications[$i]['notification_type'] == '3') { //customer gets discount
                $partnerInfo = DB::table('transaction_table as tran')
                    ->join('partner_branch as pb', 'pb.id', '=', 'tran.branch_id')
                    ->join('partner_info as pi', 'pi.partner_account_id', '=', 'pb.partner_account_id')
                    ->select('pi.partner_name', 'tran.branch_id', 'pb.partner_area')
                    ->where('tran.id', $notifications[$i]['source_id'])
                    ->get();
                $partnerInfo = json_decode(json_encode($partnerInfo), true);
                if ($partnerInfo) {
                    $partnerInfo = $partnerInfo[0];
                    $notifications[$i]['partner_name'] = $partnerInfo['partner_name'];
                    $notifications[$i]['branch_id'] = $partnerInfo['branch_id'];
                    $notifications[$i]['partner_area'] = $partnerInfo['partner_area'];
                }
            } elseif ($notifications[$i]['notification_type'] == '6') { //partner replies to review
                $partnerInfo = DB::table('review_comment as rc')
                    ->join('review as rev', 'rev.id', '=', 'rc.review_id')
                    ->join('partner_info as pi', 'pi.partner_account_id', '=', 'rev.partner_account_id')
                    ->select('pi.partner_name')
                    ->where('rev.id', $notifications[$i]['source_id'])
                    ->get();
                $partnerInfo = json_decode(json_encode($partnerInfo), true);
                if ($partnerInfo) {
                    $partnerInfo = $partnerInfo[0];
                    $notifications[$i]['partner_name'] = $partnerInfo['partner_name'];
                }
            } elseif ($notifications[$i]['notification_type'] == '8') { //customer follow notification
                $customerInfo = DB::table('customer_account as ca')
                    ->join('customer_info as ci', 'ci.customer_id', '=', 'ca.customer_id')
                    ->join('follow_customer as fc', 'fc.follower', '=', 'ci.customer_id')
                    ->select('ca.customer_username', 'ci.customer_first_name', 'ci.customer_last_name', 'ci.customer_profile_image', 'fc.follow_request')
                    ->where('fc.id', $notifications[$i]['source_id'])
                    ->get();
                $customerInfo = json_decode(json_encode($customerInfo), true);
                if ($customerInfo != null) {
                    $customerInfo = $customerInfo[0];
                    $notifications[$i]['customer_name'] = $customerInfo['customer_first_name'].' '.$customerInfo['customer_last_name'];
                    $notifications[$i]['customer_username'] = $customerInfo['customer_username'];
                    $notifications[$i]['follow_request'] = $customerInfo['follow_request'];
                }
            } elseif ($notifications[$i]['notification_type'] == '9') { //accept follow request
                $customerInfo = DB::table('customer_account as ca')
                    ->join('customer_info as ci', 'ci.customer_id', '=', 'ca.customer_id')
                    ->join('follow_customer as fc', 'fc.following', '=', 'ci.customer_id')
                    ->select('ca.customer_username', 'ci.customer_full_name', 'fc.follow_request')
                    ->where('fc.id', $notifications[$i]['source_id'])
                    ->get();
                $customerInfo = json_decode(json_encode($customerInfo), true);
                if ($customerInfo != null) {
                    $customerInfo = $customerInfo[0];
                    $notifications[$i]['customer_name'] = $customerInfo['customer_full_name'];
                    $notifications[$i]['customer_username'] = $customerInfo['customer_username'];
                    $notifications[$i]['follow_request'] = $customerInfo['follow_request'];
                }
            } elseif ($notifications[$i]['notification_type'] == '10') { //refer notification
                $customerInfo = DB::table('customer_account')
                    ->select('customer_username')
                    ->where('customer_id', $notifications[$i]['user_id'])
                    ->get();
                $customerInfo = json_decode(json_encode($customerInfo), true);
                if ($customerInfo != null) {
                    $customerInfo = $customerInfo[0];
                    $notifications[$i]['customer_username'] = $customerInfo['customer_username'];
                }
            } elseif ($notifications[$i]['notification_type'] == '11') { //refer notification
                $customerInfo = DB::table('customer_account')
                    ->select('customer_username')
                    ->where('customer_id', $notifications[$i]['user_id'])
                    ->get();
                $customerInfo = json_decode(json_encode($customerInfo), true);
                if ($customerInfo != null) {
                    $customerInfo = $customerInfo[0];
                    $notifications[$i]['customer_username'] = $customerInfo['customer_username'];
                }
            } else {
            }
        }

        return $notifications;
    }

    //function ot get info of a specific  partner
    public function partnerData($partner_id)
    {
        $partnerData = PartnerAccount::where('partner_account_id', $partner_id)->with('info.profileImage')->first();

        return $partnerData;
    }

    //function to get newsfeed from RBD
    public function newsfeedFromRBD($customerID)
    {
        $posts = Post::with('like')->get();

        foreach ($posts as $post) {
            //for naming and image of poster
            if ($post->poster_type == PostType::admin) {
                $post->poster_image = 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/all/box-logo.png';
                $post->poster_name = 'Royalty';
            } elseif ($post->poster_type == PostType::b2b2c) {
                $client = B2b2cInfo::where('id', $post->poster_id)->first();
                $post->poster_image = $client->image;
                $post->poster_name = $client->name;
            } else {
                $partner = PartnerInfo::where('partner_account_id', $post->poster_id)->with('profileImage')->first();
                $post->poster_image = $partner->profileImage->partner_profile_image;
                $post->poster_name = $partner->partner_name;
            }

            //for previous like
            $previous_like = 0;
            $previous_like_id = 0;
            $post_likes = $post->like;
            foreach ($post_likes as $like) {
                if ($like->liker_id == $customerID) {
                    $previous_like_id = $like->id;
                    $previous_like = 1;
                    break;
                }
            }
            $post->previous_like = $previous_like;
            $post->previous_like_id = $previous_like_id;
        }

        return $posts;
    }

    //function to get newsfeed from following partners
    public function newsfeedFromFollowingPartners($customerID)
    {
        $newsFeed = DB::table('partner_post as pp')
            ->join('partner_post_header as pph', 'pph.post_id', '=', 'pp.id')
            ->join('partner_info as pi', 'pi.partner_account_id', '=', 'pp.partner_account_id')
            ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
            ->join('follow_partner as fp', 'fp.following', '=', 'pi.partner_account_id')
            ->select('pp.*', 'pph.*', 'pi.partner_account_id', 'pi.partner_name', 'pi.partner_category', 'ppi.partner_profile_image')
            ->where('fp.follower', $customerID)
            ->where('pp.moderate_status', 1)
            ->orderBy('pp.posted_on', 'DESC')
            ->get();
        $newsFeed = json_decode(json_encode($newsFeed), true);
        $i = 0;
        foreach ($newsFeed as $partner) {
            $main_branch = (new self)->mainBranchOfPartner($partner['partner_account_id']);
            $newsFeed[$i]['main_branch_id'] = $main_branch[0]->id;
            $i++;
        }
        $result = [];
        if ($newsFeed) {
            //previous like
            $id_array = [];
            foreach ($newsFeed as $news) {
                array_push($id_array, $news['id']);
            }
            $previousLike = 0;
            if (Session::has('customer_id')) {
                $previousLike = DB::table('likes_post')
                    ->select('post_id')
                    ->whereIn('post_id', $id_array)
                    ->where('customer_id', Session::get('customer_id'))
                    ->get();
                $previousLike = json_decode(json_encode($previousLike), true);
            }
            $liked_ids = [];
            if ($previousLike > 0) {
                foreach ($previousLike as $prevLike) {
                    array_push($liked_ids, $prevLike['post_id']);
                }
                $count = count($newsFeed);
                for ($i = 0; $i < $count; $i++) {
                    if (in_array($newsFeed[$i]['post_id'], $liked_ids)) {
                        $newsFeed[$i]['liked'] = 1;
                    } else {
                        $newsFeed[$i]['liked'] = 0;
                    }
                }
            }
            //total likes of each post
            $i = 0;
            foreach ($newsFeed as $news) {
                $total_likes = $this->total_likes_of_a_post($news['id']);
                $newsFeed[$i]['total_likes'] = $total_likes;
                $newsFeed[$i]['type'] = NewsFeedType::post;
                $i++;
            }
            //sort array according to posted on
            foreach ($newsFeed as $key => $part) {
                $sort[$key] = strtotime($part['posted_on']);
            }
            array_multisort($sort, SORT_DESC, $newsFeed);
            $result = $newsFeed;
        }

        return $result;
    }

    //function to get newsfeed from following customers
    public function newsfeedFromFollowingCustomers($customerID)
    {
        $info = DB::table('follow_customer')
            ->select('following')
            ->where([['follower', $customerID], ['follow_request', 1]])
            ->get();
        $info = json_decode(json_encode($info), true);
        if ($info != null) {
            $newsFeed = [];
            foreach ($info as $value) {
                //reviews of following customer
                $reviews = DB::table('review as rev')
                    ->join('partner_info as pi', 'pi.partner_account_id', '=', 'rev.partner_account_id')
                    ->join('customer_info as ci', 'ci.customer_id', '=', 'rev.customer_id')
                    ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
                    ->select('rev.*', 'pi.partner_name', 'ca.customer_username', 'ci.customer_first_name', 'ci.customer_last_name', 'ci.customer_profile_image')
                    ->where('rev.customer_id', $value['following'])
                    ->orderBy('rev.id', 'DESC')
                    ->get();
                $reviews = json_decode(json_encode($reviews), true);

                if ($reviews) {
                    foreach ($reviews as $review) {
                        $review['type'] = NewsFeedType::review;
                        array_push($newsFeed, $review);
                    }
                }
                //visiting place of following customer
                $visited_places = DB::table('transaction_table as tt')
                    ->join('partner_branch as pb', 'pb.id', '=', 'tt.branch_id')
                    ->join('partner_info as pi', 'pi.partner_account_id', '=', 'pb.partner_account_id')
                    ->join('customer_info as ci', 'ci.customer_id', '=', 'tt.customer_id')
                    ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
                    ->select('tt.*', 'pi.partner_name', 'ca.customer_username', 'ci.customer_first_name', 'ci.customer_last_name', 'ci.customer_profile_image')
                    ->where('tt.customer_id', $value['following'])
                    ->orderBy('tt.id', 'DESC')
                    ->get();
                $visited_places = json_decode(json_encode($visited_places), true);
                if ($visited_places) {
                    foreach ($visited_places as $place) {
                        $place['type'] = NewsFeedType::visit;
                        array_push($newsFeed, $place);
                    }
                }
            }
            if (! empty($newsFeed)) {
                //sort array according to posted on
                foreach ($newsFeed as $key => $part) {
                    $sort[$key] = strtotime($part['posted_on']);
                }
                array_multisort($sort, SORT_DESC, $newsFeed);
            }
            $result = $newsFeed;
        } else {
            $result = [];
        }

        return $result;
    }

    //function to get recent activity of customer for account
    public function recentActivityOfCustomer($customerID)
    {
        //recent transaction of this customer
        $recentTransaction = DB::table('transaction_table as tt')
            ->join('partner_branch as pb', 'pb.id', '=', 'tt.branch_id')
            ->join('partner_info as pi', 'pi.partner_account_id', '=', 'pb.partner_account_id')
            ->select('pi.partner_name', 'tt.posted_on')
            ->where('tt.customer_id', $customerID)
            ->get();
        $recentTransaction = json_decode(json_encode($recentTransaction), true);

        //recent review of this customer
        $recentReview = DB::table('review as rev')
            ->join('partner_info as pi', 'pi.partner_account_id', '=', 'rev.partner_account_id')
            ->select('pi.partner_name', 'rev.id', 'rev.posted_on')
            ->where('rev.customer_id', $customerID)
            ->get();
        $recentReview = json_decode(json_encode($recentReview), true);

        //recent likes of this customer
        $recentLike = DB::table('likes_review as lr')
            ->join('review as rev', 'rev.id', '=', 'lr.review_id')
            ->join('partner_info as pi', 'pi.partner_account_id', '=', 'rev.partner_account_id')
            ->select('rev.id', 'pi.partner_name', 'lr.posted_on')
            ->where('lr.liker_id', $customerID)
            ->get();
        $recentLike = json_decode(json_encode($recentLike), true);

        $recentActivity['transaction'] = $recentTransaction;
        $recentActivity['review'] = $recentReview;
        $recentActivity['like'] = $recentLike;

        return $recentActivity;
    }

    //updated recent activity of cusotmer
    public function recentActivity($customerID)
    {
        $newsFeed = [];
        //reviews of following customer
        $reviews = DB::table('review as rev')
            ->join('partner_info as pi', 'pi.partner_account_id', '=', 'rev.partner_account_id')
            ->join('customer_info as ci', 'ci.customer_id', '=', 'rev.customer_id')
            ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
            ->select('rev.*', 'pi.partner_name', 'pi.partner_account_id', 'ca.customer_username', 'ci.customer_first_name', 'ci.customer_last_name', 'ci.customer_profile_image')
            ->where('rev.customer_id', $customerID)
            ->orderBy('rev.id', 'DESC')
            ->get();
        $reviews = json_decode(json_encode($reviews), true);
        $i = 0;
        foreach ($reviews as $partner) {
            $main_branch = (new self)->mainBranchOfPartner($partner['partner_account_id']);
            $reviews[$i]['main_branch_id'] = $main_branch[0]->id;
            $i++;
        }

        if ($reviews) {
            foreach ($reviews as $review) {
                $review['type'] = NewsFeedType::review;
                array_push($newsFeed, $review);
            }
        }

        //visiting place of following customer
        $visited_places = DB::table('transaction_table as tt')
            ->join('partner_branch as pb', 'pb.id', '=', 'tt.branch_id')
            ->join('partner_info as pi', 'pi.partner_account_id', '=', 'pb.partner_account_id')
            ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
            ->join('customer_info as ci', 'ci.customer_id', '=', 'tt.customer_id')
            ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
            ->select(
                'tt.*',
                'pi.partner_name',
                'pi.partner_account_id',
                'ppi.partner_profile_image',
                'ca.customer_username',
                'ci.customer_first_name',
                'ci.customer_last_name',
                'ci.customer_profile_image'
            )
            ->where('tt.customer_id', $customerID)
            ->orderBy('tt.id', 'DESC')
            ->get();
        $visited_places = json_decode(json_encode($visited_places), true);
        $i = 0;
        foreach ($visited_places as $partner) {
            $main_branch = (new self)->mainBranchOfPartner($partner['partner_account_id']);
            $visited_places[$i]['main_branch_id'] = $main_branch[0]->id;
            $i++;
        }
        if ($visited_places) {
            foreach ($visited_places as $place) {
                $place['type'] = NewsFeedType::visit;
                array_push($newsFeed, $place);
            }
        }

        //recent likes of this customer
        $recentLike = DB::table('likes_review as lr')
            ->join('review as rev', 'rev.id', '=', 'lr.review_id')
            ->join('customer_info as ci', 'ci.customer_id', '=', 'lr.liker_id')
            ->join('partner_info as pi', 'pi.partner_account_id', '=', 'rev.partner_account_id')
            ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'rev.partner_account_id')
            ->select('rev.id', 'pi.partner_name', 'pi.partner_account_id', 'lr.posted_on', 'ppi.partner_profile_image', 'ci.customer_full_name', 'ci.customer_profile_image')
            ->where('lr.liker_id', $customerID)
            ->get();
        $recentLike = json_decode(json_encode($recentLike), true);

        $i = 0;
        foreach ($recentLike as $partner) {
            $main_branch = (new self)->mainBranchOfPartner($partner['partner_account_id']);
            $recentLike[$i]['main_branch_id'] = $main_branch[0]->id;
            $i++;
        }

        if ($recentLike) {
            foreach ($recentLike as $like) {
                $like['type'] = NewsFeedType::like;
                array_push($newsFeed, $like);
            }
        }
        if (! empty($newsFeed)) {
            //sort array according to posted on
            foreach ($newsFeed as $key => $part) {
                $sort[$key] = strtotime($part['posted_on']);
            }
            array_multisort($sort, SORT_DESC, $newsFeed);
        }

        //custom pagination to apply on an array variable
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($newsFeed);
        $perPage = 15;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $activities = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);
        //custom pagination ends

        return $activities;
    }

    //function to get newsfeed in customer account
    public function newsFeed($customerID)
    {
        $pinned_posts = Post::with('like')
            ->where('moderate_status', 1)
            ->where('pinned_post', 1)
            ->orderBy('id', 'DESC')
            ->get();
        $posts = Post::with('like')
            ->where('moderate_status', 1)
            ->where('pinned_post', 0)
            ->orderBy('id', 'DESC')
            ->get();

        //for normal post
        foreach ($posts as $key => $post) {
            //for naming and image of poster
            if ($post->poster_type == PostType::admin) {
                $post->poster_image = 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/all/box-logo.png';
                $post->poster_name = 'Royalty';
            } elseif ($post->poster_type == PostType::b2b2c) {
                $client = B2b2cInfo::where('id', $post->poster_id)->first();
                $post->poster_image = $client->image;
                $post->poster_name = $client->name;
            } else {
                $partner = PartnerInfo::where('partner_account_id', $post->poster_id)->with([
                    'profileImage', 'category',
                    'branches' => function ($query) {
                        $query->where('main_branch', '=', 1);
                    },
                ])->first();
                if ($partner) {
                    $post->poster_image = $partner->profileImage->partner_profile_image;
                    $post->poster_name = $partner->partner_name;
                    $post->poster_category = $partner->category->name;
                    $post->poster_main_branch = $partner->branches[0]->id;
                } else {
                    unset($posts[$key]);
                }
            }

            //for previous like
            $previous_like = 0;
            $previous_like_id = 0;
            $post_likes = $post->like;
            foreach ($post_likes as $like) {
                if ($like->liker_id == $customerID) {
                    $previous_like_id = $like->id;
                    $previous_like = 1;
                    break;
                }
            }
            $total_likes = LikePost::where('post_id', $post->id)->count();

            $post->previous_like = $previous_like;
            $post->previous_like_id = $previous_like_id;
            $post->total_likes = $total_likes;
        }

        //for pinned post
        foreach ($pinned_posts as $post) {
            //for naming and image of poster
            if ($post->poster_type == PostType::admin) {
                $post->poster_image = 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/all/box-logo.png';
                $post->poster_name = 'Royalty';
            } else {
                $partner = PartnerInfo::where('partner_account_id', $post->poster_id)->with([
                    'profileImage', 'category',
                    'branches' => function ($query) {
                        $query->where('main_branch', '=', 1);
                    },
                ])->first();
                $post->poster_image = $partner->profileImage->partner_profile_image;
                $post->poster_name = $partner->partner_name;
                $post->poster_category = $partner->category->name;
                $post->poster_main_branch = $partner->branches[0]->id;
            }

            //for previous like
            $previous_like = 0;
            $previous_like_id = 0;
            $post_likes = $post->like;
            foreach ($post_likes as $like) {
                if ($like->liker_id == $customerID) {
                    $previous_like_id = $like->id;
                    $previous_like = 1;
                    break;
                }
            }
            $total_likes = LikePost::where('post_id', $post->id)->count();

            $post->previous_like = $previous_like;
            $post->previous_like_id = $previous_like_id;
            $post->total_likes = $total_likes;
        }
        $pinned_posts = json_decode(json_encode($pinned_posts), true);
        $posts = json_decode(json_encode($posts), true);

        $merged_posts = array_merge($pinned_posts, $posts);
        //custom pagination
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($merged_posts);
        $perPage = 20;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedItems = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);

        return $paginatedItems;
    }

    //function to get total likes of a post
    public function total_likes_of_a_post($id)
    {
        $total_likes = DB::table('likes_post')->where('post_id', $id)->count();

        return $total_likes;
    }

    //function to get partner's all posts
    public function allPosts($partnerID)
    {
        $allPosts = DB::table('post as p')
            ->join('partner_info as pi', 'pi.partner_account_id', '=', 'p.poster_id')
            ->select('p.*', 'pi.partner_account_id', 'pi.partner_name')
            ->where('p.poster_id', $partnerID)
            ->where('p.moderate_status', 1)
            ->orderBy('p.id', 'DESC')
            ->get();
        $allPosts = json_decode(json_encode($allPosts), true);
        $i = 0;
        foreach ($allPosts as $post) {
            $total_likes = $this->total_likes_of_a_post($post['id']);
            $allPosts[$i]['total_likes'] = $total_likes;
            $i++;
        }

        return $allPosts;
    }

    //function to get total number of partners, customer visited
    public static function totalPartnersCustomerVisited($customerID)
    {
        $result = DB::table('transaction_table as tt')
            ->join('partner_branch as pb', 'pb.id', '=', 'tt.branch_id')
            ->select('pb.partner_account_id')
            ->groupBy('pb.partner_account_id')
            ->where('tt.customer_id', $customerID)
            ->where('tt.deleted_at', null)
            ->get();
        return count($result);
    }

    //function to get all follow requests of a user
    public function allFollowRequests($customerId)
    {
        $follow_requests = DB::table('customer_notification')
            ->where('user_id', $customerId)
            ->where('notification_type', 8)
            ->orderBy('posted_on', 'DESC')
            ->get();
        $follow_requests = json_decode(json_encode($follow_requests), true);
        $i = 0;
        foreach ($follow_requests as $request) {
            $customerInfo = DB::table('customer_account as ca')
                ->join('customer_info as ci', 'ci.customer_id', '=', 'ca.customer_id')
                ->join('follow_customer as fc', 'fc.follower', '=', 'ci.customer_id')
                ->join('user_type as ut', 'ut.id', '=', 'ci.customer_type')
                ->select(
                    'ca.customer_username',
                    'ci.customer_first_name',
                    'ci.customer_last_name',
                    'ci.customer_profile_image',
                    'ci.customer_type',
                    'fc.follower',
                    'fc.follow_request'
                )
                ->where('fc.id', $request['source_id'])
                ->where('fc.follow_request', 0)
                ->get();
            $customerInfo = json_decode(json_encode($customerInfo), true);
            if ($customerInfo != null) {
                $customerInfo = $customerInfo[0];
                $follow_requests[$i]['customer_id'] = $customerInfo['follower'];
                $follow_requests[$i]['customer_name'] = $customerInfo['customer_first_name'].' '.$customerInfo['customer_last_name'];
                $follow_requests[$i]['customer_username'] = $customerInfo['customer_username'];
                $follow_requests[$i]['profile_image'] = $customerInfo['customer_profile_image'];
                $follow_requests[$i]['follow_request'] = $customerInfo['follow_request'];
                $follow_requests[$i]['customer_type'] = $customerInfo['customer_type'];
            } else {
                unset($follow_requests[$i]);
            }
            $i++;
        }

        return $follow_requests;
    }

    //function to get top 5 transaction of a customer
    public function customerTopTransactions($customerId)
    {
        $topTransactions = DB::table('transaction_table as tt')
            ->join('partner_branch as pb', 'pb.id', '=', 'tt.branch_id')
            ->join('partner_info as pi', 'pb.partner_account_id', '=', 'pi.partner_account_id')
            ->select('tt.amount_spent', 'pi.partner_name', 'pb.partner_area', 'pb.partner_account_id', 'pb.id')
            ->orderBy('tt.amount_spent', 'DESC')
            ->where('tt.customer_id', $customerId)
            ->limit(5)
            ->get();
        $topTransactions = json_decode(json_encode($topTransactions), true);

        return $topTransactions;
    }

    //function to update image link notification table
    public function updateImgLinkInNotification($old_image, $new_image)
    {
        CustomerNotification::where('image_link', $old_image)->update([
            'image_link' => $new_image,
        ]);
        PartnerNotification::where('image_link', $old_image)->update([
            'image_link' => $new_image,
        ]);

        return true;
    }

    //function to upload partner pro pic to aws
    public function uploadProPicToAWS($image_name, $folder)
    {
        //get filename with extension
        $filenamewithextension = $image_name->getClientOriginalName();

        //get filename without extension
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);

        //get file extension
        $extension = $image_name->getClientOriginalExtension();

        //filename to store with specific folder name
        $filenametostore = $folder.'/'.$filename.'_'.time().'.'.$extension;

        //upload file to s3 bucket
        Storage::disk('s3')->put($filenametostore, fopen($image_name, 'r+'), 'public');
        //get image url to store to database
        $url = Storage::disk('s3')->url($filenametostore);

        return $url;
    }

    //function to upload image to aws
    public function uploadImageToAWS($image_name, $folder)
    {
        //get filename with extension
        $filenamewithextension = $image_name->getClientOriginalName();

        //get filename without extension
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);

        //get file extension
        $extension = $image_name->getClientOriginalExtension();

        //filename to store with specific folder name
        $filenametostore = $folder.'/'.$filename.'_'.time().'.'.$extension;

        //get width & height of the image
        $width = Image::make($image_name)->width();
        $height = Image::make($image_name)->height();

        //if width & height are less than default(600x400) then resize with default dimension and upload it
        if ($width < 600 || $height < 400) {
            if ($width > $height) {
                //resize the image with default dimension
                $resize_image = Image::make($image_name)->resize(600, 400)->encode('jpg');
                //upload file to s3 bucket
                Storage::disk('s3')->put($filenametostore, $resize_image->__toString(), 'public');
            } elseif ($height > $width) {
                //upload file to s3 bucket
                Storage::disk('s3')->put($filenametostore, fopen($image_name, 'r+'), 'public');
            } elseif ($width == $height) {
                $resize_image = Image::make($image_name)->resize(600, 600)->encode('jpg');
                //upload file to s3 bucket
                Storage::disk('s3')->put($filenametostore, $resize_image->__toString(), 'public');
            }
        } else {
            //create new width & height of the image
            $new_widthBy3 = round($width / 3);
            $new_heightBy3 = round($height / 3);

            if ($new_widthBy3 < 600 || $new_heightBy3 < 400) {
                $new_widthBy2 = round($width / 2);
                $new_heightBy2 = round($height / 2);

                if ($new_widthBy2 < 600 || $new_heightBy2 < 400) {
                    if ($new_widthBy2 > $new_heightBy2) {
                        //resize the image with default dimension
                        $resize_image = Image::make($image_name)->resize(600, 400)->encode('jpg');
                        //upload file to s3 bucket
                        Storage::disk('s3')->put($filenametostore, $resize_image->__toString(), 'public');
                    } elseif ($new_heightBy2 > $new_widthBy2) {
                        //upload file to s3 bucket
                        Storage::disk('s3')->put($filenametostore, fopen($image_name, 'r+'), 'public');
                    } elseif ($new_widthBy2 == $new_heightBy2) {
                        $resize_image = Image::make($image_name)->resize(600, 600)->encode('jpg');
                        //upload file to s3 bucket
                        Storage::disk('s3')->put($filenametostore, $resize_image->__toString(), 'public');
                    }
                } else {
                    //resize the image with new dimension
                    $resize_image = Image::make($image_name)->resize($new_widthBy2, $new_heightBy2)->encode('jpg');
                    //upload file to s3 bucket
                    Storage::disk('s3')->put($filenametostore, $resize_image->__toString(), 'public');
                }
            } else {
                $new_widthBy2 = round($new_widthBy3 / 2);
                $new_heightBy2 = round($new_heightBy3 / 2);

                if ($new_widthBy2 < 600 || $new_heightBy2 < 400) {
                    if ($new_widthBy2 > $new_heightBy2) {
                        //resize the image with default dimension
                        $resize_image = Image::make($image_name)->resize(600, 400)->encode('jpg');
                        //upload file to s3 bucket
                        Storage::disk('s3')->put($filenametostore, $resize_image->__toString(), 'public');
                    } elseif ($new_heightBy2 > $new_widthBy2) {
                        //resize the image with new dimension (for menu image)
                        $resize_image = Image::make($image_name)->resize($new_widthBy2, $new_heightBy2)->encode('jpg');
                        //upload file to s3 bucket
                        Storage::disk('s3')->put($filenametostore, $resize_image->__toString(), 'public');
                    } elseif ($new_widthBy2 == $new_heightBy2) {
                        $resize_image = Image::make($image_name)->resize(600, 600)->encode('jpg');
                        //upload file to s3 bucket
                        Storage::disk('s3')->put($filenametostore, $resize_image->__toString(), 'public');
                    }
                } else {
                    //resize the image with new dimension
                    $resize_image = Image::make($image_name)->resize($new_widthBy2, $new_heightBy2)->encode('jpg');
                    //upload file to s3 bucket
                    Storage::disk('s3')->put($filenametostore, $resize_image->__toString(), 'public');
                }
            }
        }
        //get image url to store to database
        $url = Storage::disk('s3')->url($filenametostore);

        return $url;
    }

    //function to upload video to aws
    public function uploadVideoToAWS($file, $folder)
    {
        //get filename with extension
        $filenamewithextension = $file->getClientOriginalName();

        //get filename without extension
        $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);

        //get file extension
        $extension = $file->getClientOriginalExtension();

        //filename to store with specific folder name
        $filenametostore = $folder.'/'.$filename.'_'.time().'.'.$extension;
        Storage::disk('s3')->put($filenametostore, file_get_contents($file));
        //get video url to store to database
        $url = Storage::disk('s3')->url($filenametostore);

        return $url;
    }

    //function to delete notification
    public function deleteNotification($user_id, $source_id, $type)
    {
        if ($type == notificationType::partner_follow) {
            DB::table('partner_notification')
                ->where('partner_account_id', $user_id)
                ->where('notification_type', $type)
                ->where('source_id', $source_id)
                ->delete();
        } else {
            //delete from customer notification table
            DB::table('customer_notification')
                ->where('user_id', $user_id)
                ->where('notification_type', $type)
                ->where('source_id', $source_id)
                ->delete();
        }
    }

    //function to generate bar & QR Code for cards
    public function generateBarQR()
    {
        for ($i = 1101; $i <= 1200; $i++) {
            A:
            $card_serial_10 = mt_rand(1000000000, mt_getrandmax());
            $card_serial_6 = sprintf('%06d', $i);
            $customer_id = $card_serial_10.$card_serial_6;
            $card_exists = CustomerAccount::where('customer_id', $customer_id)->count();
            //regenerate card if already exists
            if ($card_exists > 0) {
                goto A;
            }
            // $last_3_digits = substr($customer_id, -3);
            $last_4_digits = substr($customer_id, -4);
            //generate & save QR code
            $qrcode = new BaconQrCodeGenerator;
            //create "images->qr" folder in public folder manually before run this function
            $qrcode->size(100)->generate($customer_id, '../public/images/qr/'.$last_4_digits.'-qr-'.$customer_id.'.svg');
            //generate & save bar code
            $barcode = new BarcodeGenerator();
            $barcode->setText($customer_id);
            $barcode->setType(BarcodeGenerator::Code128);
            $barcode->setScale(2);
            $barcode->setThickness(25);
            $barcode->setFontSize(10);
            $code = $barcode->generate();
            //create "images->bar" folder in public folder manually before run this function
            file_put_contents('../public/images/bar/'.$last_4_digits.'-bar-'.$customer_id.'.png', base64_decode($code));
            //save customer ids to a text file
            File::append('card_numbers.txt', $i.'. '.$customer_id."\r\n");
        }
        echo 'Successful';
    }

    //function to generate bar & QR for specific customer_ids
    public function generateCustomBarQR()
    {
        $ids = ['6969696969696969'];
        $i = 0;
        foreach ($ids as $key => $customer_id) {
            $last_3_digits = substr($customer_id, -3);
            //generate & save QR code
            $qrcode = new BaconQrCodeGenerator;
            $qrcode->size(100)->generate($customer_id, '../public/images/qr/'.$last_3_digits.'-qr-'.$customer_id.'.svg');
            //generate & save bar code
            $barcode = new BarcodeGenerator();
            $barcode->setText($customer_id);
            $barcode->setType(BarcodeGenerator::Code128);
            $barcode->setScale(2);
            $barcode->setThickness(25);
            $barcode->setFontSize(10);
            $code = $barcode->generate();

            file_put_contents('../public/images/bar/'.$last_3_digits.'-bar-'.$customer_id.'.png', base64_decode($code));
            //save customer ids to a text file
            File::append('card_numbers.txt', $i.'. '.$customer_id."\r\n");
            $i++;
        }
        echo 'Successful';
    }

    //function to get customized point according to its validity
    public function customizedPointValidity($partnerInfo)
    {
        if ($partnerInfo->discount[0]->customizedPoint) {
            $date = date('d-m-Y');
            $week_Day = strtolower(date('D'));
            $time = date('H:i');
            $date_valid = false;
            $time_valid = false;
            $week_valid = false;
            $customize_point_date = $partnerInfo->discount[0]->customizedPoint->date_duration;
            $customize_point_week = $partnerInfo->discount[0]->customizedPoint->weekdays;
            $customize_point_times = $partnerInfo->discount[0]->customizedPoint->time_duration;

            if ($customize_point_date[0]['from'] <= $date && $customize_point_date[0]['to'] >= $date) {
                $date_valid = true;
            }
            foreach ($customize_point_times as $customize_point_time) {
                if ($customize_point_time['from'] <= $time && $customize_point_time['to'] >= $time) {
                    $time_valid = true;
                    break;
                }
            }
            if ($customize_point_week[0][$week_Day] == 1) {
                $week_valid = true;
            }

            if (! $date_valid || ! $time_valid || ! $week_valid) {
                $partnerInfo->discount[0]->customizedPoint->point_multiplier = 1;
                if (isset($partnerInfo->discount[1])) {
                    $partnerInfo->discount[1]->customizedPoint->point_multiplier = 1;
                }
            }

            return $partnerInfo;
        } else {
            return $partnerInfo;
        }
    }

    //function to delete review
    public function deleteReview($id, $admin_id = null)
    {
        //partner id from review id
        $review = Review::find($id);
        $review->admin_id = $admin_id;
        $review->save();
        $review->delete();

        return 0;
        $partner_id = $review->partner_account_id;

        //increment "delete counter" in customer_info table
        DB::table('customer_info as ci')
            ->join('review as rv', 'rv.customer_id', '=', 'ci.customer_id')
            ->where('rv.id', $id)
            ->increment('ci.review_deleted', 1);

        //delete review likes notifications
        DB::table('customer_notification as cn')
            ->join('likes_review as lr', 'cn.source_id', '=', 'lr.id')
            ->join('review as rev', 'lr.review_id', '=', 'rev.id')
            ->where('rev.id', $id)
            ->where('cn.notification_type', 1)
            ->delete();

        //delete review reply notifications
        DB::table('customer_notification as cn')
            ->join('review as rev', 'rev.id', '=', 'cn.source_id')
            ->where('rev.id', $id)
            ->where('cn.notification_type', 6)
            ->delete();

        //delete review notification from partner_notification table
        DB::table('partner_notification as pn')
            ->join('review as rev', 'rev.id', '=', 'pn.source_id')
            ->where('rev.id', $id)
            ->where('pn.notification_type', 2)
            ->delete();
        // set review id null to transaction table
        TransactionTable::where('review_id', $id)->update(['review_id' => null]);
        //Delete review
//        DB::table('likes_review')->where('review_id', $id)->delete();
//        DB::table('review_comment')->where('review_id', $id)->delete();
        $review->delete();

        //delete customer point
        $customer_point = CustomerPoint::where('customer_id', $review->customer_id)
            ->where('point_type', PointType::rating_point)
            ->orWhere('point_type', PointType::review_point)
            ->where('source_id', $id)
            ->first();

        $reward_notification = CustomerNotification::where('source_id', $customer_point->id)->where('notification_type', notificationType::reward)->first();
        if ($reward_notification) {
            $reward_notification->delete();
        }
        if ($customer_point) {
            $customer_point->delete();
        }

        //total reviews partner got
        $totalReview = Review::where('partner_account_id', $partner_id)->count();
        // average rating
        if ($totalReview != 0) {
            $average = Review::where('partner_account_id', $partner_id)->sum('rating');
            $average = $average / $totalReview;
            $average = round($average, 2);
            for ($i = 1; $i <= 5; $i++) {
                //total specific star partner got
                $totalStars = Review::where('rating', $i)
                    ->where('partner_account_id', $partner_id)
                    ->count();
                //percentage of the specific star
                $starPercentage = $totalStars / $totalReview * 100;
                DB::table('rating')
                    ->where('partner_account_id', $partner_id)
                    ->update([
                        $i.'_star' => $starPercentage,
                        'average_rating' => $average,
                    ]);
            }
        } else {
            $average = 0;
            for ($i = 1; $i <= 5; $i++) {
                DB::table('rating')
                    ->where('partner_account_id', $partner_id)
                    ->update([
                        $i.'_star' => '0.00',
                        'average_rating' => $average,
                    ]);
            }
        }
    }

    //function to get top brands
    public function topBrands()
    {
        $topBrands = DB::table('partner_info as pi')
            ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
            ->join('top_brands as tb', 'tb.partner_account_id', '=', 'pi.partner_account_id')
            ->select('ppi.partner_profile_image', 'pi.partner_name', 'pi.partner_category', 'pi.partner_account_id')
            ->limit(6)
            ->get();
        $topBrands = json_decode(json_encode($topBrands), true);
        foreach ($topBrands as $key => $value) {
            $main_branch = (new self)->mainBranchOfPartner($value['partner_account_id']);
            if (count($main_branch) > 0) {
                $topBrands[$key]['main_branch_id'] = $main_branch[0]['id'];
            } else {
                unset($topBrands[$key]);
            }
        }

        return $topBrands;
    }

    //function to send message for wishing a birthday boy/girl
    public function birthdayWish()
    {
        //get current month and date only
        $current_date = date('m-d');

        //all users
        $users = DB::table('customer_info')
            ->select('customer_id', 'customer_first_name', 'customer_dob', 'customer_contact_number')
            ->get();
        $users = json_decode(json_encode($users), true);
        $i = 0;
        foreach ($users as $key => $value) {
            $birthday = substr($value['customer_dob'], 5);
            $text = 'Happy Birthday '.$value['customer_first_name'].'.';
            if ($birthday == $current_date) { //match user birthday with today
                try {
                    DB::beginTransaction(); //to do query rollback

                    DB::table('customer_notification')->insert(
                        [
                            'user_id' => $value['customer_id'],
                            'image_link' => 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/images/birthday.png',
                            'notification_text' => $text,
                            'notification_type' => 5,
                            'source_id' => 0,
                            'seen' => 0,
                        ]
                    );

                    DB::commit(); //to do query rollback
                } catch (\Exception $e) {
                    DB::rollback(); //rollback all successfully executed queries
                }
                //trigger 'liveBirthdayNotification' function to do live push notification
                (new pusherController)->liveBirthdayNotification($value['customer_id']);
            }
            $i++;
        }

        return '';
    }

    //function to update profile image link
    public function update_profile_image_link($new_image, $customer_id)
    {
        //update image path in database
        DB::table('customer_info')
            ->where('customer_id', $customer_id)
            ->update([
                'customer_profile_image' => $new_image,
            ]);
        DB::table('info_at_buy_card')
            ->where('customer_id', $customer_id)
            ->update([
                'customer_profile_image' => $new_image,
            ]);
        $customer_all_notification = CustomerNotification::all();
        foreach ($customer_all_notification as $key => $notification) {
            if ($notification->type == 1) {
                $likes_review = LikesReview::where('id', $notification->source_id)->first();
                if ($likes_review->liker_type == 1) {
                    if ($likes_review->liker_id == $customer_id) {
                        $cus_noti = CustomerNotification::find($notification->id);
                        $cus_noti->image_link = $new_image;
                        $cus_noti->save();
                    }
                }
            } elseif ($notification->type == 10) {
                if ($notification->source_id == $customer_id) {
                    $cus_noti = CustomerNotification::find($notification->id);
                    $cus_noti->image_link = $new_image;
                    $cus_noti->save();
                }
            }
        }
        $partner_all_notification = PartnerNotification::all();
        foreach ($partner_all_notification as $key => $notification) {
            if ($notification->type == 2) {
                $review = Review::where('id', $notification->source_id)->first();
                if ($review->customer_id == $customer_id) {
                    $part_noti = PartnerNotification::find($notification->id);
                    $part_noti->image_link = $new_image;
                    $part_noti->save();
                }
            } elseif ($notification->type == 7) {
                $like_post = LikePost::where('id', $notification->source_id)->first();
                if ($like_post->liker_type == 1) {
                    if ($like_post->liker_id == $customer_id) {
                        $part_noti = PartnerNotification::find($notification->id);
                        $part_noti->image_link = $new_image;
                        $part_noti->save();
                    }
                }
            }
        }
    }

    //function to check influencer & return data
    public function influencersPromoUsed($customer_id)
    {
        $user = CardPromoCodes::where('influencer_id', $customer_id)->with('promoUsage.customerInfo', 'promoUsage.ssl')->first();
        if ($user != null) {
            $usage = $user->promoUsage->count();
        } else {
            $user = null;
            $usage = 0;
        }

        return ['user' => $user, 'usage' => $usage];
    }

    //update influencer payment info at buy card
    public function updateInfluencerPaymentInfo($promoId, $paidAmount)
    {
        $influencer = CardPromoCodes::where('id', $promoId)->select('influencer_id')->first();
        if ($influencer->influencer_id != null) {
            $influencer_profit = round(($paidAmount * InfluencerPercentage::percentage) / 100);
            InfluencerPayment::where('influencer_id', $influencer->influencer_id)
                ->increment('total_amount', $influencer_profit);
        }
    }

    //function to update customer id
    public function updateCustomerId($old_id, $new_id, $ssl_update)
    {
        DB::table('customer_info')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
        DB::table('customer_account')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
        DB::table('social_id')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
        if ($ssl_update == 1) {
            DB::table('ssl_transaction_table')->where('customer_id', $old_id)
                ->update(['customer_id' => $new_id]);
        }
        DB::table('card_delivery')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
        DB::table('info_at_buy_card')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
        DB::table('birthday_wish')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
        DB::table('bonus_request')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
        DB::table('customer_notification')->where('user_id', $old_id)
            ->update(['user_id' => $new_id]);
        DB::table('follow_customer')->where('follower', $old_id)
            ->update(['follower' => $new_id]);
        DB::table('follow_customer')->where('following', $old_id)
            ->update(['following' => $new_id]);
        DB::table('follow_partner')->where('follower', $old_id)
            ->update(['follower' => $new_id]);
        DB::table('likes_post')->where('liker_id', $old_id)
            ->update(['liker_id' => $new_id]);
        DB::table('likes_review')->where('liker_id', $old_id)
            ->update(['liker_id' => $new_id]);
        DB::table('pass_changed')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
        DB::table('rbd_statistics')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
        DB::table('reset_user')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
        DB::table('review')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
        DB::table('transaction_table')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
        DB::table('wish')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
        DB::table('customer_miscellaneous')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
        DB::table('card_promo')->where('influencer_id', $old_id)
            ->update(['influencer_id' => $new_id]);
        DB::table('customer_card_promo_usage')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
        DB::table('share_post')->where('sharer_id', $old_id)
            ->update(['sharer_id' => $new_id]);
        DB::table('b2b2c_user')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
        DB::table('rbd_influencer_payment')->where('influencer_id', $old_id)
            ->update(['influencer_id' => $new_id]);
        DB::table('customer_transaction_request')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
        DB::table('branch_user_notification')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
        DB::table('assigned_card')->where('card_number', $old_id)
            ->update(['card_number' => $new_id]);
        DB::table('customer_history')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
        DB::table('customer_reward_redeems')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
        DB::table('customer_points')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
        DB::table('customer_login_sessions')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
        DB::table('customer_activity_sessions')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
        DB::table('search_stats')->where('customer_id', $old_id)
            ->update(['customer_id' => $new_id]);
    }

    //function to get total number of offers of a partner
    public function partnerOffers($object)
    {
        $i = 0;
        $date = date('d-m-Y');
        foreach ($object as $partner) {
            $branches = PartnerBranch::where([['partner_account_id', $partner->partner_account_id], ['active', 1]])
                ->with(['offers' => function ($query) {
                    $query->where('selling_point', null);
                }])->get();
            $offers = 0;
            foreach ($branches as $branch) {
                foreach ($branch->offers as $offer) {
                    $offer_date = $offer['date_duration'][0];
                    if (
                        new DateTime($offer_date['from']) <= new DateTime($date) && new DateTime($offer_date['to']) >= new DateTime($date)
                        && $offer->active == 1
                    ) {
                        $offers += 1;
                    } else {
                        $offers += 0;
                    }
                }
            }
            $object[$i]->offers = $offers;
            $object[$i]->branch_number = count($branches);
            $i++;
        }

        return $object;
    }

    //function to get recently visited partners profile
    public function recentlyVisitedProfile($customerID)
    {
        //visiting profile of following customer
        $partners = RbdStatistics::where('customer_id', $customerID)->with(['partner.info.profileImage',
            'partner' => function ($query) {
                $query->where('active', 1);
            }, ])
            ->orderBy('id', 'desc')
            ->get()
            ->unique('partner_id');
        $data = collect();
        foreach ($partners as $partner) {
            if ($partner->partner) {
                $data->push($partner);
                if (count($data) == 6) {
                    break;
                }
            }
        }

        return $data;
    }

    //comments & likes of reviews for partner profile
//    public function likesCommentsOfReview($reviews)
//    {
//        $i = 0;
//        foreach ($reviews as $review) {
//            $comments = DB::table('review_comment')
//                ->select('comment', 'comment_type', 'posted_on')
//                ->where('review_id', $review['id'])
//                ->get();
//            $comments = json_decode(json_encode($comments), true);
//            $reviews[$i]['comments'] = $comments;
//            //total likes of a specific review
//            $total_likes_of_a_review = DB::table('likes_review')->where('review_id', $review['id'])->count();
//            $reviews[$i]['total_likes_of_a_review'] = $total_likes_of_a_review;
//            $i++;
//        }
//        return $reviews;
//    }

    //get refer value at buy card
    public function referValue()
    {
        $all_amounts = AllAmounts::where('type', 'refer_bonus')->first();

        return $all_amounts->price;
    }

    // CUSTOMER NOTIFICATION
    public function allNotifications($customer_id)
    {
        //notifications
        $today = $this->todayNotification($customer_id);
        $yesterday = $this->yesterdayNotification($customer_id);
        $this_week = $this->lastWeekNotification($customer_id);
        $earlier = $this->earlierNotification($customer_id);
        $unseen = CustomerNotification::where([['user_id', $customer_id], ['seen', 0]])->count();
        $total_notifications = CustomerNotification::where('user_id', $customer_id)->count();

        return [
            'today' => $today, 'yesterday' => $yesterday, 'this_week' => $this_week, 'earlier' => $earlier,
            'unseen' => $unseen, 'total_notifications' => $total_notifications,
        ];
    }

    public function todayNotification($customer_id)
    {
        $today = date('Y-m-d');
        //customer's all unseen notifications
        $notifications = DB::select("select *, DATE_FORMAT(posted_on,'%h:%i %p') AS 'relative_time'
                                        from customer_notification
                                        where user_id = '$customer_id'
                                          and posted_on like '$today %'
                                        order by id desc");

        return $this->getCustomerNotifications($notifications);
    }

    public function yesterdayNotification($customer_id)
    {
        $yesterday = date('Y-m-d', strtotime('-1 days'));
        //customer's all unseen notifications
        $notifications = DB::select("select *, DATE_FORMAT(posted_on,'%h:%i %p') AS 'relative_time'
                                        from customer_notification
                                        where user_id = '$customer_id'
                                          and posted_on like '$yesterday %'
                                        order by id desc");

        return $this->getCustomerNotifications($notifications);
    }

    public function lastWeekNotification($customer_id)
    {
        $prev_day = date('Y-m-d', strtotime('-1 days'));
        $seven_days_before = date('Y-m-d', strtotime('-8 days'));
        //customer's all unseen notifications
        $notifications = DB::select("select *, DATE_FORMAT(posted_on,'%a at %h:%i %p') AS 'relative_time'
                                        from customer_notification
                                        where user_id = '$customer_id'
                                          and posted_on > '$seven_days_before'
                                             and posted_on < '$prev_day'
                                        order by id desc");

        return $this->getCustomerNotifications($notifications);
    }

    public function earlierNotification($customer_id)
    {
        $seven_days_before = date('Y-m-d', strtotime('-8 days'));
        //customer's all unseen notifications
        $notifications = DB::select("select *, DATE_FORMAT(posted_on,'%b %d, %Y at %h:%i %p') AS 'relative_time'
                                        from customer_notification
                                        where user_id = '$customer_id'
                                          and posted_on < '$seven_days_before'
                                        order by id desc
                                        limit 100");

        return $this->getCustomerNotifications($notifications);
    }

    public function getCustomerNotifications($notifications)
    {
        $notifications = json_decode(json_encode($notifications), true);
        for ($i = 0; $i < count($notifications); $i++) {
            if ($notifications[$i]['notification_type'] == '1') { //other customers liked one's review
                $likerType = DB::table('likes_review')
                    ->select('liker_type')
                    ->where('id', $notifications[$i]['source_id'])
                    ->first();
                if ($likerType->liker_type == LikerType::customer) {
                    $customerInfo = DB::table('customer_info as ci')
                        ->join('likes_review as lr', 'lr.liker_id', '=', 'ci.customer_id')
                        ->select('ci.customer_full_name', 'ci.customer_profile_image', 'lr.review_id')
                        ->where('lr.id', $notifications[$i]['source_id'])
                        ->get();
                    $customerInfo = json_decode(json_encode($customerInfo), true);
                    if ($customerInfo) {
                        $customerInfo = $customerInfo[0];
                        $notifications[$i]['liker_name'] = $customerInfo['customer_full_name'];
                        $notifications[$i]['liker_profile_image'] = $customerInfo['customer_profile_image'];
                        $notifications[$i]['liked_review_id'] = $customerInfo['review_id'];
                    }
                } elseif ($likerType->liker_type == LikerType::partner) {
                    $like = LikesReview::where('id', $notifications[$i]['source_id'])->with('review')->first();
                    if ($like) {
                        if ($like->review->transaction) {
                            $partner_name = $like->review->transaction->branch->info->partner_name;
                            $partner_area = $like->review->transaction->branch->partner_area;
                            $profile_image = $like->review->transaction->branch->info->profileImage->partner_profile_image;
                        } else {
                            $partner_name = $like->review->dealPurchase->voucher->branch->info->partner_name;
                            $partner_area = $like->review->dealPurchase->voucher->branch->info->partner_name;
                            $profile_image = $like->review->dealPurchase->voucher->branch->info->profileImage->partner_profile_image;
                        }
                        $notifications[$i]['liker_name'] = $partner_name.', '.$partner_area;
                        $notifications[$i]['liker_profile_image'] = $profile_image;
                        $notifications[$i]['liked_review_id'] = $like->review->id;
                    }
                }
            } elseif ($notifications[$i]['notification_type'] == '3') { //discount notification
                $partnerInfo = DB::table('transaction_table as tran')
                    ->join('partner_branch as pb', 'pb.id', '=', 'tran.branch_id')
                    ->join('partner_info as pi', 'pi.partner_account_id', '=', 'pb.partner_account_id')
                    ->select('pi.partner_name', 'tran.customer_id', 'tran.branch_id', 'pb.partner_area')
                    ->where('tran.id', $notifications[$i]['source_id'])
                    ->get();
                $partnerInfo = json_decode(json_encode($partnerInfo), true);
                if ($partnerInfo) {
                    $partnerInfo = $partnerInfo[0];
                    $notifications[$i]['partner_name'] = $partnerInfo['partner_name'];
                    $notifications[$i]['branch_id'] = $partnerInfo['branch_id'];
                    $notifications[$i]['partner_area'] = $partnerInfo['partner_area'];
                }
            } elseif ($notifications[$i]['notification_type'] == '6') { //reply notification
                $partnerInfo = DB::table('review_comment as rc')
                    ->join('review as rev', 'rev.id', '=', 'rc.review_id')
                    ->join('partner_info as pi', 'pi.partner_account_id', '=', 'rev.partner_account_id')
                    ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                    ->select('pi.partner_name', 'ppi.partner_profile_image', 'rev.id')
                    ->where('rev.id', $notifications[$i]['source_id'])
                    ->get();
                $partnerInfo = json_decode(json_encode($partnerInfo), true);
                if ($partnerInfo) {
                    $partnerInfo = $partnerInfo[0];
                    $notifications[$i]['partner_name'] = $partnerInfo['partner_name'];
                    $notifications[$i]['partner_profile_image'] = $partnerInfo['partner_profile_image'];
                    $notifications[$i]['review_id'] = $partnerInfo['id'];
                }
            } elseif ($notifications[$i]['notification_type'] == '8') { //customer follow notification
                $customerInfo = DB::table('customer_account as ca')
                    ->join('customer_info as ci', 'ci.customer_id', '=', 'ca.customer_id')
                    ->join('follow_customer as fc', 'fc.follower', '=', 'ci.customer_id')
                    ->select('ca.customer_username', 'ci.customer_full_name', 'fc.follow_request')
                    ->where('fc.id', $notifications[$i]['source_id'])
                    ->get();
                $customerInfo = json_decode(json_encode($customerInfo), true);
                if ($customerInfo != null) {
                    $customerInfo = $customerInfo[0];
                    $notifications[$i]['customer_name'] = $customerInfo['customer_full_name'];
                    $notifications[$i]['customer_username'] = $customerInfo['customer_username'];
                    $notifications[$i]['follow_request'] = $customerInfo['follow_request'];
                }
            } elseif ($notifications[$i]['notification_type'] == '9') { //accept follow request notification
                $customerInfo = DB::table('customer_account as ca')
                    ->join('customer_info as ci', 'ci.customer_id', '=', 'ca.customer_id')
                    ->join('follow_customer as fc', 'fc.following', '=', 'ci.customer_id')
                    ->select('ca.customer_username', 'ci.customer_full_name', 'fc.follow_request')
                    ->where('fc.id', $notifications[$i]['source_id'])
                    ->get();
                $customerInfo = json_decode(json_encode($customerInfo), true);
                if ($customerInfo != null) {
                    $customerInfo = $customerInfo[0];
                    $notifications[$i]['customer_name'] = $customerInfo['customer_full_name'];
                    $notifications[$i]['customer_username'] = $customerInfo['customer_username'];
                    $notifications[$i]['follow_request'] = $customerInfo['follow_request'];
                }
            } elseif ($notifications[$i]['notification_type'] == '10') { //refer notification
                $customerInfo = DB::table('customer_account')
                    ->select('customer_username')
                    ->where('customer_id', $notifications[$i]['user_id'])
                    ->get();
                $customerInfo = json_decode(json_encode($customerInfo), true);
                if ($customerInfo != null) {
                    $customerInfo = $customerInfo[0];
                    $notifications[$i]['customer_username'] = $customerInfo['customer_username'];
                }
            } elseif ($notifications[$i]['notification_type'] == '11') { //reward notification
                $customerInfo = DB::table('customer_account')
                    ->select('customer_username')
                    ->where('customer_id', $notifications[$i]['user_id'])
                    ->get();
                $customerInfo = json_decode(json_encode($customerInfo), true);
                if ($customerInfo != null) {
                    $customerInfo = $customerInfo[0];
                    $notifications[$i]['customer_username'] = $customerInfo['customer_username'];
                }
            } elseif ($notifications[$i]['notification_type'] == '12') { //deal notification
                $customerInfo = DB::table('customer_account')
                    ->select('customer_username')
                    ->where('customer_id', $notifications[$i]['user_id'])
                    ->first();
                if ($customerInfo) {
                    $notifications[$i]['customer_username'] = $customerInfo->customer_username;
                }
            } elseif ($notifications[$i]['notification_type'] == '13') { //deal reject notification
                $customerInfo = DB::table('customer_account')
                    ->select('customer_username')
                    ->where('customer_id', $notifications[$i]['user_id'])
                    ->first();
                if ($customerInfo) {
                    $notifications[$i]['customer_username'] = $customerInfo->customer_username;
                }
            }
        }

        return $notifications;
    }

    public function getNotificationView($notifications)
    {
        $output = '';

        foreach ($notifications as $notification) {
            if ($notification['notification_type'] == 1) { //liked notification
                if ($notification['seen'] == 0) {
                    $output .= "<li class='unseen_notification'>";
                } else {
                    $output .= '<li>';
                }
                if (isset($notification['liked_review_id'])) {
                    $output .= '<a class="notification_title_color" href="'.url('likedNotification/'.
                            $notification['liked_review_id'].'_'.$notification['id']).'">';
                    $output .= '<div class="row">';
                    $output .= '<div class="col-md-3 col-sm-3 col-xs-3"><div class="notify-img">';
                    $output .= '<img src="'.asset($notification['image_link']).'" class="img-circle n-img img-40 primary-border" alt="notif-img">';
                    $output .= '</div></div>';
                    $output .= '<div class="col-md-9 col-sm-9 col-xs-9 pd-l0">';
                    $output .= '<p style="width: 220px; white-space: normal">'.$notification['liker_name'].' '.
                        $notification['notification_text'].'</p>';
                    $output .= '<p class="time"><i class="bx bx-time-five" aria-hidden="true"></i> ';

                    $output .= $notification['relative_time'];

                    $output .= '</p></div></div></a>';
                }
                $output .= '</li>';
            } elseif ($notification['notification_type'] == 3) { //discount notification
                if ($notification['seen'] == 0) {
                    $output .= "<li class='unseen_notification'>";
                } else {
                    $output .= '<li>';
                }
                $output .= '<a class="notification_title_color" href="'.url('discountNotification/'.
                        $notification['id'].'/'.$notification['user_id']).'">';
                $output .= '<div class="row">';
                $output .= '<div class="col-md-3 col-sm-3 col-xs-3"><div class="notify-img">';
                $output .= '<img src="'.asset($notification['image_link']).'" class="img-circle n-img img-40 primary-border" alt="notif-img">';
                $output .= '</div></div>';
                $output .= '<div class="col-md-9 col-sm-9 col-xs-9 pd-l0">';
                $output .= '<p style="width: 220px; white-space: normal">'.$notification['notification_text'].' '.
                    $notification['partner_name']. ', '.$notification['partner_area'].'.'.'</p>';
                $output .= '<p class="time"><i class="bx bx-time-five" aria-hidden="true"></i> ';

                $output .= $notification['relative_time'];

                $output .= '</p></div></div></a>';
                $output .= '</li>';
            } elseif ($notification['notification_type'] == 6) { //reply notification
                if ($notification['seen'] == 0) {
                    $output .= "<li class='unseen_notification'>";
                } else {
                    $output .= '<li>';
                }
                $output .= '<a class="notification_title_color" href="'.url('replyNotification/'.$notification['source_id']
                        .'_'.$notification['id']).'">';
                if (isset($notification['partner_name'])) {
                    $output .= '<div class="row">';
                    $output .= '<div class="col-md-3 col-sm-3 col-xs-3"><div class="notify-img">';
                    $output .= '<img src="'.asset($notification['image_link']).'" class="img-circle n-img img-40 primary-border" alt="notif-img">';
                    $output .= '</div></div>';
                    $output .= '<div class="col-md-9 col-sm-9 col-xs-9 pd-l0">';
                    $output .= '<p style="width: 220px; white-space: normal">'.$notification['notification_text'].'</p>';
                    $output .= '<p class="time"><i class="bx bx-time-five" aria-hidden="true"></i> ';

                    $output .= $notification['relative_time'];
                    $output .= '</p></div></div>';
                }
                $output .= '</a>';
                $output .= '</li>';
            } elseif ($notification['notification_type'] == 8) { //customer follow notification
                if ($notification['seen'] == 0) {
                    $output .= "<li class='unseen_notification'>";
                } else {
                    $output .= '<li>';
                }
                if (isset($notification['customer_name']) && $notification['follow_request'] == 0) {
                    $output .= '<a class="notification_title_color" href="'.url('followNotification/'.
                            $notification['customer_username'].'_'.$notification['id']).'">';
                    $output .= '<div class="row">';
                    $output .= '<div class="col-md-3 col-sm-3 col-xs-3"><div class="notify-img">';
                    $output .= '<img src="'.asset($notification['image_link']).'" class="img-circle n-img img-40 primary-border" alt="notif-img">';
                    $output .= '</div></div>';
                    $output .= '<div class="col-md-9 col-sm-9 col-xs-9 pd-l0">';
                    $output .= '<p style="width: 220px; white-space: normal">'.$notification['customer_name'].' '.$notification['notification_text'].'</p>';
                    $output .= '<p class="time"><i class="bx bx-time-five" aria-hidden="true"></i> ';

                    $output .= $notification['relative_time'];

                    $output .= '</p></div></div></a>';
                } else {
                    $output .= '<a class="notification_title_color" href="'.url('user-profile/'.
                            $notification['customer_username']).'" target="_blank">';
                    $output .= '<div class="row">';
                    $output .= '<div class="col-md-3 col-sm-3 col-xs-3"><div class="notify-img">';
                    $output .= '<img src="'.asset($notification['image_link']).'" class="img-circle n-img img-40 primary-border" alt="notif-img">';
                    $output .= '</div></div>';
                    $output .= '<div class="col-md-9 col-sm-9 col-xs-9 pd-l0">';
                    $output .= '<p style="width: 220px; white-space: normal">'.$notification['customer_name'].' started following you'.'</p>';
                    $output .= '<p class="time"><i class="bx bx-time-five" aria-hidden="true"></i> ';

                    $output .= $notification['relative_time'];

                    $output .= '</p></div></div></a>';
                }
                $output .= '</li>';
            } elseif ($notification['notification_type'] == 9) { //accept follow request
                if ($notification['seen'] == 0) {
                    $output .= "<li class='unseen_notification'>";
                } else {
                    $output .= '<li>';
                }
                $output .= '<a class="notification_title_color" href="'.url('acceptFollowRequestNotification/'.
                        $notification['source_id'].'_'.$notification['id']).'">';
                if (isset($notification['customer_name'])) {
                    $output .= '<div class="row">';
                    $output .= '<div class="col-md-3 col-sm-3 col-xs-3"><div class="notify-img">';
                    $output .= '<img src="'.asset($notification['image_link']).'" class="img-circle n-img img-40 primary-border" alt="notif-img">';
                    $output .= '</div></div>';
                    $output .= '<div class="col-md-9 col-sm-9 col-xs-9 pd-l0">';
                    $output .= '<p style="width: 220px; white-space: normal">'.$notification['customer_name'].' '.
                        $notification['notification_text'].'</p>';
                    $output .= '<p class="time"><i class="bx bx-time-five" aria-hidden="true"></i> ';

                    $output .= $notification['relative_time'];

                    $output .= '</p></div></div>';
                }
                $output .= '</a>';
                $output .= '</li>';
            } elseif ($notification['notification_type'] == 10) {
                //refer notification
                if ($notification['seen'] == 0) {
                    $output .= "<li class='unseen_notification'>";
                } else {
                    $output .= '<li>';
                }
                $output .= '<a class="notification_title_color" href="'.url('referNotification/'.$notification['customer_username'].'/'.$notification['id']).'">';
                $output .= '<div class="row">';
                $output .= '<div class="col-md-3 col-sm-3 col-xs-3"><div class="notify-img">';
                $output .= '<img src="'.asset($notification['image_link']).'" class="img-circle n-img img-40 primary-border" alt="notif-img">';
                $output .= '</div></div>';
                $output .= '<div class="col-md-9 col-sm-9 col-xs-9 pd-l0">';
                $output .= '<p style="width: 220px; white-space: normal">'.$notification['notification_text'].'</p>';
                $output .= '<p class="time"><i class="bx bx-time-five" aria-hidden="true"></i> ';
                $output .= $notification['relative_time'];

                $output .= '</p></div></div>';
                $output .= '</a>';
                $output .= '</li>';
            //refer notification ends
            } elseif ($notification['notification_type'] == 11) {
                //reward notification
                if ($notification['seen'] == 0) {
                    $output .= "<li class='unseen_notification'>";
                } else {
                    $output .= '<li>';
                }
                $output .= '<a class="notification_title_color" href="'.url('rewardNotification/'.$notification['customer_username'].'/'.$notification['id']).'">';
                $output .= '<div class="row">';
                $output .= '<div class="col-md-3 col-sm-3 col-xs-3"><div class="notify-img">';
                $output .= '<img src="'.asset($notification['image_link']).'" class="img-circle n-img img-40 primary-border" alt="notif-img">';
                $output .= '</div></div>';
                $output .= '<div class="col-md-9 col-sm-9 col-xs-9 pd-l0">';
                $output .= '<p style="width: 220px; white-space: normal">'.$notification['notification_text'].'</p>';
                $output .= '<p class="time"><i class="bx bx-time-five" aria-hidden="true"></i> ';
                $output .= $notification['relative_time'];

                $output .= '</p></div></div>';
                $output .= '</a>';
                $output .= '</li>';
            //refer notification ends
            } elseif ($notification['notification_type'] == 12) { //deal notification
                if ($notification['seen'] == 0) {
                    $output .= "<li class='unseen_notification'>";
                } else {
                    $output .= '<li>';
                }
                $output .= '<a class="notification_title_color" href="'.url('dealNotification/'.$notification['customer_username'].'/'.$notification['id']).'">';
                $output .= '<div class="row">';
                $output .= '<div class="col-md-3 col-sm-3 col-xs-3"><div class="notify-img">';
                $output .= '<img src="'.asset($notification['image_link']).'" class="img-circle n-img img-40 primary-border" alt="notif-img">';
                $output .= '</div></div>';
                $output .= '<div class="col-md-9 col-sm-9 col-xs-9 pd-l0">';
                $output .= '<p style="width: 220px; white-space: normal">'.$notification['notification_text'].'</p>';
                $output .= '<p class="time"><i class="bx bx-time-five" aria-hidden="true"></i> ';
                $output .= $notification['relative_time'];

                $output .= '</p></div></div>';
                $output .= '</a>';
                $output .= '</li>';
            } elseif ($notification['notification_type'] == 13) { //deal reject notification
                if ($notification['seen'] == 0) {
                    $output .= "<li class='unseen_notification'>";
                } else {
                    $output .= '<li>';
                }
                $output .= '<a class="notification_title_color" href="'.url('dealRejectNotification/'.
                        $notification['customer_username'].'/'.$notification['id']).'">';
                $output .= '<div class="row">';
                $output .= '<div class="col-md-3 col-sm-3 col-xs-3"><div class="notify-img">';
                $output .= '<img src="'.asset($notification['image_link']).
                    '" class="img-circle n-img img-40 primary-border" alt="notif-img">';
                $output .= '</div></div>';
                $output .= '<div class="col-md-9 col-sm-9 col-xs-9 pd-l0">';
                $output .= '<p style="width: 220px; white-space: normal">'.$notification['notification_text'].'</p>';
                $output .= '<p class="time"><i class="bx bx-time-five" aria-hidden="true"></i> ';
                $output .= $notification['relative_time'];

                $output .= '</p></div></div>';
                $output .= '</a>';
                $output .= '</li>';
            }
        }

        return $output;
    }

    public function allNotificationView($notifications)
    {
        $output = '';
        $output .= '<ol class="activity-feed">';
        foreach ($notifications as $notification) {
            if ($notification['notification_type'] == 1) {
                //When other user likes your review
                $output .= '<li class="feed-item" data-content="&#xf164;" data-time="'.$notification['relative_time'].'" data-color="red">';
                $output .= '<section>';
                $output .= '<a class="notification_title_color"
                         href="'.url('likedNotification/'.$notification['liked_review_id'].'_'.$notification['id']).'">';
                $output .= '<img src="'.$notification['liker_profile_image'].'"
                         class="img-circle img-40 primary-border" style="object-fit: cover;" alt="RoyaltyBD Customer Notification">';
                $output .= '<span class="notification_line">'.$notification['liker_name'].' '.$notification['notification_text'].'</span>';
                $output .= '</a>';
                $output .= '</section>';
                $output .= '</li>';
            } elseif ($notification['notification_type'] == 3 && isset($notification['partner_name'])) {
                //discount notification
                $output .= '<li class="feed-item" data-content="&#xf541;" data-time="'.$notification['relative_time'].'" data-color="darkblue">';
                $output .= '<section>';
                $output .= '<a class="notification_title_color"
                         href="'.url('discountNotification/'.$notification['id'].'/'.Session::get('customer_id')).'">';
                $output .= '<img src="'.$notification['image_link'].'"
                         class="img-circle img-40 primary-border" style="object-fit: cover;" alt="RoyaltyBD Customer Notification">';
                $output .= '<span class="notification_line">'.$notification['notification_text'].' '.$notification['partner_name'].
                    ', '.$notification['partner_area'].'.'.'</span>';
                $output .= '</a>';
                $output .= '</section>';
                $output .= '</li>';
            } elseif ($notification['notification_type'] == 6) {
                //reply notification
                $output .= '<li class="feed-item" data-content="&#xf3e5;" data-time="'.$notification['relative_time'].'" data-color="lightblue">';
                $output .= '<section>';
                $output .= '<a class="notification_title_color yob"
                         href="'.url('replyNotification/'.$notification['source_id'].'_'.$notification['id']).'">';
                $output .= '<img src="'.$notification['partner_profile_image'].'"
                         class="img-circle img-40 primary-border" style="object-fit: cover;" alt="RoyaltyBD Customer Notification">';
                $output .= '<span class="notification_line">'.$notification['partner_name'].' replied to your review.'.'</span>';
                $output .= '</a>';
                $output .= '</section>';
                $output .= '</li>';
            } elseif ($notification['notification_type'] == 8) {
                //follow notification
                $output .= '<li class="feed-item follow-request-'.$notification['customer_id'].'" data-content="&#xf234;" 
                    data-time="'.$notification['relative_time'].'" data-color="darkblue" id="follow-request-'.$notification['customer_id'].'">';
                $output .= '<section>';
                if (isset($notification['customer_name']) && $notification['follow_request'] == 1) {
                    $output .= '<a href="'.url('user-profile/'.$notification['customer_username']).'" target="_blank">';
                    $output .= '<img src="'.$notification['image_link'].'"
                         class="img-circle img-40 primary-border" alt="RoyaltyBD Customer Notification">';
                    $output .= '</a>';

                    $output .= '<a href="'.url('user-profile/'.$notification['customer_username']).'" target="_blank">';
                    $output .= '<span class="notification_line">'.$notification['customer_name'].' started following you.'.'</span>';
                    $output .= '</a>';
                } else {
                    $output .= '<a href="'.url('user-profile/'.$notification['customer_username']).'" target="_blank">';
                    $output .= '<img src="'.$notification['image_link'].'"
                         class="img-circle img-40 primary-border" alt="RoyaltyBD Customer Notification">';
                    $output .= '</a>';

                    $output .= '<a href="'.url('user-profile/'.$notification['customer_username']).'" target="_blank">';
                    $output .= '<span class="notification_line">'.$notification['customer_name'].'</span>';
                    $output .= '<span class="notification_line follow-request-text-'.$notification['customer_id'].'" 
                        id="follow-request-text-'.$notification['customer_id'].'">'.$notification['notification_text'].'.'.'</span>';
                    $output .= '</a>';
                    //accept or ignore button
                    $output .= '<div style="float: right">';
                    $output .= '<button class="btn btn-accept-notif accept-follow-request-'.$notification['customer_id'].'"
                        id="accept-follow-request" value="'.$notification['customer_id'].'">';
                    $output .= 'Accept';
                    $output .= '</button>';
                    $output .= '<button class="btn btn-reject-notif ignore-follow-request-'.$notification['customer_id'].'"
                        id="ignore-follow-request" value="'.$notification['customer_id'].'">';
                    $output .= 'Ignore';
                    $output .= '</button>';
                    $output .= '</div>';
                }
                $output .= '</section>';
                $output .= '</li>';
            } elseif ($notification['notification_type'] == 9) {
                //accepts follow request
                $output .= '<li class="feed-item" data-content="&#xf234;" data-time="'.$notification['relative_time'].'" data-color="lightblue">';
                $output .= '<section>';
                $output .= '<a class="notification_title_color yob"
                     href="'.url('acceptFollowRequestNotification/'.$notification['source_id'].'_'.$notification['id']).' ">';
                $output .= '<img src="'.$notification['image_link'].'"
                     class="img-circle img-40 primary-border" style="object-fit: cover;" alt="RoyaltyBD Customer Notification">';
                $output .= '<span class="notification_line">';
                $output .= $notification['customer_name'].' '.$notification['notification_text'];
                $output .= '</span>';
                $output .= '</a>';
                $output .= '</section>';
                $output .= '</li>';
            } elseif ($notification['notification_type'] == 10) {
                //refer notification
                $output .= '<li class="feed-item" data-content="&#xf2b5;" data-time="'.$notification['relative_time'].'" data-color="lightblue">';
                $output .= '<section>';
                $output .= '<a class="notification_title_color yob"
                  href="'.url('referNotification/'.$notification['customer_username'].'/'.$notification['id']).'">';
                $output .= '<img src="'.$notification['image_link'].'" class="img-circle img-40 primary-border" style="object-fit: cover;" alt="RoyaltyBD Customer Notification">';
                $output .= '<span class="notification_line">'.$notification['notification_text'].'</span>';
                $output .= '</a>';
                $output .= '</section>';
                $output .= '</li>';
            } elseif ($notification['notification_type'] == 11) {
                //reward notification
                $output .= '<li class="feed-item" data-content="&#xf53a;" data-time="'.$notification['relative_time'].'" data-color="lightblue">';
                $output .= '<section>';
                $output .= '<a class="notification_title_color yob"
                  href="'.url('rewardNotification/'.$notification['customer_username'].'/'.$notification['id']).'">';
                $output .= '<img src="'.$notification['image_link'].'" class="img-circle img-40 primary-border" style="object-fit: cover;" alt="RoyaltyBD Customer Notification">';
                $output .= '<span class="notification_line">'.$notification['notification_text'].'</span>';
                $output .= '</a>';
                $output .= '</section>';
                $output .= '</li>';
            } elseif ($notification['notification_type'] == 12) { //deal notification
                $output .= '<li class="feed-item" data-content="&#xf53a;" data-time="'.$notification['relative_time'].'" data-color="lightblue">';
                $output .= '<section>';
                $output .= '<a class="notification_title_color yob"
                  href="'.url('dealNotification/'.$notification['customer_username'].'/'.$notification['id']).'">';
                $output .= '<img src="'.$notification['image_link'].'" class="img-circle img-40 primary-border" style="object-fit: cover;" alt="RoyaltyBD Customer Notification">';
                $output .= '<span class="notification_line">'.$notification['notification_text'].'</span>';
                $output .= '</a>';
                $output .= '</section>';
                $output .= '</li>';
            } elseif ($notification['notification_type'] == 13) { //deal reject notification
                $output .= '<li class="feed-item" data-content="&#xf53a;" data-time="'.$notification['relative_time'].'" data-color="lightblue">';
                $output .= '<section>';
                $output .= '<a class="notification_title_color yob"
                  href="'.url('dealRejectNotification/'.$notification['customer_username'].'/'.$notification['id']).'">';
                $output .= '<img src="'.$notification['image_link'].'" class="img-circle img-40 primary-border" style="object-fit: cover;" alt="RoyaltyBD Customer Notification">';
                $output .= '<span class="notification_line">'.$notification['notification_text'].'</span>';
                $output .= '</a>';
                $output .= '</section>';
                $output .= '</li>';
            }
        }
        $output .= '</ol>';

        return $output;
    }

    // CUSTOMER NOTIFICATION ENDS

    // PARTNER NOTIFICATION
    public function partnerAllNotifications($partner_id)
    {
        //notifications
        $today = $this->partnerTodayNotification($partner_id);
        $yesterday = $this->partnerYesterdayNotification($partner_id);
        $this_week = $this->partnerLastWeekNotification($partner_id);
        $earlier = $this->partnerEarlierNotification($partner_id);
        $seen = PartnerNotification::where([['partner_account_id', $partner_id], ['seen', 0]])->count();
        $total_notifications = PartnerNotification::where('partner_account_id', $partner_id)->count();

        return [
            'today' => $today, 'yesterday' => $yesterday, 'this_week' => $this_week, 'earlier' => $earlier,
            'unseen' => $seen, 'total_notifications' => $total_notifications,
        ];
    }

    public function partnerTodayNotification($partner_id)
    {
        $today = date('Y-m-d');
        //customer's all unseen notifications
        $notifications = DB::table('partner_notification')
            ->where('partner_account_id', $partner_id)
            ->where('posted_on', 'like', $today.'%')
            ->orderBy('id', 'DESC')
            ->get();

        return $this->getPartnerNotifications($notifications);
    }

    public function partnerYesterdayNotification($partner_id)
    {
        $yesterday = date('Y-m-d', strtotime('-1 days'));
        //customer's all unseen notifications
        $notifications = DB::table('partner_notification')
            ->where('partner_account_id', $partner_id)
            ->where('posted_on', 'like', $yesterday.'%')
            ->orderBy('id', 'DESC')
            ->get();

        return $this->getPartnerNotifications($notifications);
    }

    public function partnerLastWeekNotification($partner_id)
    {
        $prev_day = date('Y-m-d', strtotime('-1 days'));
        $seven_days_before = date('Y-m-d', strtotime('-8 days'));
        //customer's all unseen notifications
        $notifications = DB::table('partner_notification')
            ->where('partner_account_id', $partner_id)
            ->where('posted_on', '>', $seven_days_before)
            ->where('posted_on', '<', $prev_day)
            ->orderBy('id', 'DESC')
            ->get();

        return $this->getPartnerNotifications($notifications);
    }

    public function partnerEarlierNotification($partner_id)
    {
        $seven_days_before = date('Y-m-d', strtotime('-8 days'));
        //customer's all unseen notifications
        $notifications = DB::table('partner_notification')
            ->where('partner_account_id', $partner_id)
            ->where('posted_on', '<', $seven_days_before)
            ->orderBy('id', 'DESC')
            ->take(100)
            ->get();

        return $this->getPartnerNotifications($notifications);
    }

    public function getPartnerNotifications($notifications)
    {
        $notifications = json_decode(json_encode($notifications), true);
        for ($i = 0; $i < count($notifications); $i++) {
            if ($notifications[$i]['notification_type'] == '2') { //partner gets review notification
                $notify_partner = DB::table('customer_account as ca')
                    ->join('customer_info as ci', 'ci.customer_id', '=', 'ca.customer_id')
                    ->join('review as rev', 'rev.customer_id', '=', 'ci.customer_id')
                    ->select('ca.customer_username', 'ci.customer_full_name')
                    ->where('rev.id', $notifications[$i]['source_id'])
                    ->get();
                $notify_partner = json_decode(json_encode($notify_partner), true);
                $notify_partner = $notify_partner[0];
                $notifications[$i]['customer_name'] = $notify_partner['customer_full_name'];
            } elseif ($notifications[$i]['notification_type'] == '4') { //partner gets follow notification
                $notify_partner = DB::table('customer_account as ca')
                    ->join('customer_info as ci', 'ci.customer_id', '=', 'ca.customer_id')
                    ->join('follow_partner as fp', 'fp.follower', '=', 'ci.customer_id')
                    ->select('ca.customer_username', 'ci.customer_full_name', 'ci.customer_profile_image')
                    ->where('fp.id', $notifications[$i]['source_id'])
                    ->get();
                $notify_partner = json_decode(json_encode($notify_partner), true);
                $notify_partner = $notify_partner[0];
                $notifications[$i]['customer_username'] = $notify_partner['customer_username'];
                $notifications[$i]['customer_name'] = $notify_partner['customer_full_name'];
            } else { //partner gets post like notification (notification type => 7)
                $notify_partner = DB::table('customer_account as ca')
                    ->join('customer_info as ci', 'ci.customer_id', '=', 'ca.customer_id')
                    ->join('likes_post as lp', 'lp.liker_id', '=', 'ci.customer_id')
                    ->select('ca.customer_username', 'ci.customer_full_name')
                    ->where('lp.id', $notifications[$i]['source_id'])
                    ->get();
                $notify_partner = json_decode(json_encode($notify_partner), true);

                $notify_partner = $notify_partner[0];
                $notifications[$i]['customer_name'] = $notify_partner['customer_full_name'];
            }
        }

        return $notifications;
    }

    public function getPartnerNotificationView($notifications)
    {
        $output = '';
        foreach ($notifications as $notification) {
            if ($notification['notification_type'] == 2) { //review notification of partner
                if ($notification['seen'] == 0) {
                    $output .= "<li class='unseen_notification'>";
                } else {
                    $output .= '<li>';
                }
                $output .= '<a class="notification_title_color" href="'.url('/unseen_review_notification_of_partner/'
                        .$notification['source_id'].'_'.$notification['id']).'">';
                $output .= '<div class="row">';
                $output .= '<div class="col-md-3 col-sm-3 col-xs-3"><div class="notify-img">';
                $output .= '<img src="'.asset($notification['image_link']).'" class="img-circle n-img img-40 primary-border w-100" alt="notif-img">';
                $output .= '</div></div>';
                $output .= '<div class="col-md-9 col-sm-9 col-xs-9 pd-l0">';
                $output .= '<p>'.$notification['customer_name'].' '.$notification['notification_text'].'</p>';
                $output .= '<p class="time"><i class="bx bx-time-five" aria-hidden="true"></i> ';

                $posted_on = date('Y-M-d H:i:s', strtotime($notification['posted_on']));
                $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                $output .= $created->diffForHumans();

                $output .= '</p></div></div></a>';
                $output .= '</li>';
            } elseif ($notification['notification_type'] == 4) { //follow notification
                if ($notification['seen'] == 0) {
                    $output .= "<li class='unseen_notification'>";
                } else {
                    $output .= '<li>';
                }
                $output .= '<a class="notification_title_color" href="'.url('/unseen_follow_notification_of_partner/'
                        .$notification['id']).'">';
                $output .= '<div class="row">';
                $output .= '<div class="col-md-3 col-sm-3 col-xs-3"><div class="notify-img">';
                $output .= '<img src="'.asset($notification['image_link']).'" class="img-circle n-img img-40 primary-border w-100" alt="notif-img">';
                $output .= '</div></div>';
                $output .= '<div class="col-md-9 col-sm-9 col-xs-9 pd-l0">';
                $output .= '<p>'.$notification['customer_name'].' '.$notification['notification_text'].'</p>';
                $output .= '<p class="time"><i class="bx bx-time-five" aria-hidden="true"></i> ';

                $posted_on = date('Y-M-d H:i:s', strtotime($notification['posted_on']));
                $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                $output .= $created->diffForHumans();

                $output .= '</p></div></div></a>';
                $output .= '</li>';
            } else { //like post notification of partner
                if ($notification['seen'] == 0) {
                    $output .= "<li class='unseen_notification'>";
                } else {
                    $output .= '<li>';
                }
                $output .= '<a class="notification_title_color" href="'.url('/post_like_notification_of_partner/'
                        .$notification['id'].'_'.$notification['source_id']).'">';
                $output .= '<div class="row">';
                $output .= '<div class="col-md-3 col-sm-3 col-xs-3"><div class="notify-img">';
                $output .= '<img src="'.asset($notification['image_link']).'" class="img-circle n-img img-40 primary-border w-100" alt="notif-img">';
                $output .= '</div></div>';
                $output .= '<div class="col-md-9 col-sm-9 col-xs-9 pd-l0">';
                $output .= '<p>'.$notification['customer_name'].' '.$notification['notification_text'].'</p>';
                $output .= '<p class="time"><i class="bx bx-time-five" aria-hidden="true"></i> ';

                $posted_on = date('Y-M-d H:i:s', strtotime($notification['posted_on']));
                $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                $output .= $created->diffForHumans();

                $output .= '</p></div></div></a>';
                $output .= '</li>';
            }
        }

        return $output;
    }

    public function partnerAllNotificationView($notifications)
    {
        $output = '';
        $output .= '<ol class="activity-feed">';
        foreach ($notifications as $notification) {
            if ($notification['notification_type'] == 2) {
                //review notification of partner
                $posted_on = date('Y-M-d H:i:s', strtotime($notification['posted_on']));
                $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                $output .= '<li class="feed-item" data-content="&#xf044;" data-time="'.$created->diffForHumans().'" data-color="darkblue">';
                $output .= '<section>';
                $output .= '<a class="notification_title_color"
                         href="'.url('seen_review_notification_of_partner/'.$notification['source_id'].'_'.$notification['id']).'">';
                $output .= '<img src="'.$notification['image_link'].'"
                         class="img-circle img-40 primary-border lazyload" alt="RoyaltyBD Partner Notification">';
                $output .= '<span class="notification_line">'.$notification['customer_name'].' '.$notification['notification_text'].'</span>';
                $output .= '</a>';
                $output .= '</section>';
                $output .= '</li>';
            } elseif ($notification['notification_type'] == 4) {
                //follow notification of partner
                $posted_on = date('Y-M-d H:i:s', strtotime($notification['posted_on']));
                $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                $output .= '<li class="feed-item" data-content="&#xf234;" data-time="'.$created->diffForHumans().'" data-color="darkblue">';
                $output .= '<section>';
                $output .= '<a class="notification_title_color"
                         href="'.url('seen_follow_notification_of_partner/'.$notification['id']).'">';
                $output .= '<img src="'.$notification['image_link'].'"
                         class="img-circle img-40 primary-border lazyload" alt="RoyaltyBD Partner Notification">';
                $output .= '<span class="notification_line">'.$notification['customer_name'].' '.$notification['notification_text'].'</span>';
                $output .= '</a>';
                $output .= '</section>';
                $output .= '</li>';
            } else {
                //like post notification of partner
                $posted_on = date('Y-M-d H:i:s', strtotime($notification['posted_on']));
                $created = \Carbon\Carbon::createFromTimeStamp(strtotime($posted_on));
                $output .= '<li class="feed-item" data-content="&#xf164;" data-time="'.$created->diffForHumans().'" data-color="red">';
                $output .= '<section>';
                $output .= '<a class="notification_title_color"
                  href="'.url('post_like_notification_of_partner/'.$notification['id'].'_'.$notification['source_id']).'">';
                $output .= '<img src="'.$notification['image_link'].'" class="img-circle img-40 primary-border lazyload" alt="RoyaltyBD Partner Notification">';
                $output .= '<span class="notification_line">'.$notification['customer_name'].' '.$notification['notification_text'].'</span>';
                $output .= '</a>';
                $output .= '</section>';
                $output .= '</li>';
            }
        }
        $output .= '</ol>';

        return $output;
    }

    // PARTNER NOTIFICATION ENDS

    public function branchOffers($branch_id)
    {
        $branch_offers = BranchOffers::offers($branch_id)->where('active', 1)->orderBy('priority', 'DESC')->get();

        $date = date('d-m-Y');
        foreach ($branch_offers as $key => $offer) {
            //check expiry
            $offer_date = $offer->date_duration[0];
            if (
                new DateTime($offer_date['from']) <= new DateTime($date) && new DateTime($offer_date['to']) >= new DateTime($date)
                && $offer->active == 1
            ) {
                //nothing
            } else {
                unset($branch_offers[$key]);
            }
        }
        $branch_offers = json_decode(json_encode($branch_offers), true);
        $branch_offers = array_values($branch_offers);

        return $branch_offers;
    }

    //function to branch offers count
    public function branchOffersCount($object)
    {
        $i = 0;
        foreach ($object as $value) {
            $total_offers = 0;
            $date = date('d-m-Y');
            foreach ($value->branches as $key => $branch) {
                $offers = BranchOffers::offers($branch->id)->orderBy('id', 'DESC')->get();
                foreach ($offers as $offer) {
                    //check expiry
                    $offer_date = $offer->date_duration[0];
                    if (
                        new DateTime($offer_date['from']) <= new DateTime($date) && new DateTime($offer_date['to']) >= new DateTime($date)
                        && $offer->active == 1
                    ) {
                        $total_offers++;
                    }
                }
            }
            $main_branch_info = (new self)->mainBranchOfPartner($value['partner_account_id']);
            $branchOffers = (new self)->branchOffers($main_branch_info[0]->id);
            $object[$i]['offers'] = $total_offers;
            $object[$i]['branch_offers'] = $branchOffers;
            $i++;
        }

        return $object;
    }

    //function to get transaction analytics of gold & platinum
    public function getSMSCustomerList($user_type, $date = null)
    {
        //find customer phone numbers
        $to = ''; //for saving in sent message history table
        if ($user_type == 'all_customers') {
            $users = DB::table('customer_info')
                ->select('customer_contact_number')
                ->get();
            $to = 'All Members';
        } elseif ($user_type == 'trial') {
            if ($date != null) {
                $users = DB::table('customer_info as ci')
                    ->join('card_delivery', function ($join) {
                        $join->on('card_delivery.customer_id', '=', 'ci.customer_id')
                            ->on('card_delivery.id', '=', DB::raw('(SELECT max(id) from card_delivery WHERE card_delivery.customer_id = ci.customer_id)'));
                    })
                    ->join('ssl_transaction_table as stt', 'stt.id', '=', 'card_delivery.ssl_id')
                    ->select('ci.customer_contact_number')
                    ->where('ci.customer_type', 2)
                    ->where('card_delivery.delivery_type', 11)
                    ->where('stt.tran_date', $date)
                    ->get();
            } else {
                $trial_start_date = date('2019-10-17');
                $users = DB::table('customer_info as ci')
                    ->join('card_delivery', function ($join) {
                        $join->on('card_delivery.customer_id', '=', 'ci.customer_id')
                            ->on('card_delivery.id', '=', DB::raw('(SELECT max(id) from card_delivery WHERE card_delivery.customer_id = ci.customer_id)'));
                    })
                    ->join('ssl_transaction_table as stt', 'stt.id', '=', 'card_delivery.ssl_id')
                    ->select('ci.customer_contact_number')
                    ->where('ci.customer_type', 2)
                    ->where('card_delivery.delivery_type', 11)
                    ->where('stt.tran_date', '>', $trial_start_date)
                    ->get();
            }
            $to = 'All trial Members';
        } elseif ($user_type == 'cardholders') {
            $users = DB::table('customer_info')
                ->select('customer_contact_number')
                ->where('customer_type', 2)
                ->get();
            $to = 'All Premium Members';
        } elseif ($user_type == 'guest') {
            $users = DB::table('customer_info')
                ->select('customer_contact_number')
                ->where('customer_type', 3)
                ->get();
            $to = 'All Guest Members';
        } elseif ($user_type == 'influencer') {
            $users = DB::table('customer_info as ci')
                ->join('card_promo as cp', 'cp.influencer_id', '=', 'ci.customer_id')
                ->select('ci.customer_contact_number')
                ->get();
            $to = 'All Influencers';
        } elseif ($user_type == 'expired') {
            $users = (new functionController2())->getExpiredCustomers();
            $to = 'All Expired Members';
        } elseif ($user_type == 'active') {
            $users = DB::table('transaction_table as tt')
                ->join('customer_info as ci', 'ci.customer_id', '=', 'tt.customer_id')
                ->select('ci.customer_contact_number')
                ->distinct('tt.customer_id')
                ->get();
            $to = 'All Active Members';
        } elseif ($user_type == 'inactive') {
            $tt_ids = DB::table('transaction_table')->distinct('customer_id')->pluck('customer_id');
            $users = DB::table('customer_info')
                ->select('customer_contact_number')
                ->whereNotIn('customer_id', $tt_ids)
                ->get();
            $to = 'All Inactive Members';
        } elseif ($user_type == 'owners') {
            $users = DB::table('branch_owner')->select('phone as customer_contact_number')->get();
            $to = 'All Owners';
        } elseif ($user_type == 'scanners') {
            $users = DB::table('branch_user')->select('phone as customer_contact_number')->get();
            $to = 'All Scanners';
        }
        $users = json_decode(json_encode($users), true);

        return ['users' => $users, 'to' => $to];
    }

    //funciton to generate qr for branch transaction
    public function generateBranchQr($id, $encrypted_data)
    {
        $output = null;
        $encrypt_method = 'AES-256-CBC';
        $secret_key = 'RoyaltyBD';
        $secret_iv = 'PartnerBranch**';
        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ($id != null) {
            $output = openssl_encrypt($id, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } elseif ($encrypted_data != null) {
            $output = openssl_decrypt(base64_decode($encrypted_data), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }

    //partner branch qr
    public function partnerBranchQr($partner_id, $branch_id)
    {
        //*************generate specific branch qr*****************
        $partner = PartnerInfo::where('partner_account_id', $partner_id)
            ->with(['branches' => function ($query) use ($branch_id) {
                $query->where('id', '=', $branch_id);
            }])
            ->first();
        if (!Storage::exists('downloads/branchqr/')) {
            Storage::makeDirectory('downloads/branchqr/');
        }
        $hashed_id = $this->generateBranchQr($branch_id, null);
        $qrcode = new BaconQrCodeGenerator;
        $file_name = '../storage/app/downloads/branchqr/'.$partner->partner_name.'-'.$partner->branches[0]->partner_area.'-'.$partner->branches[0]->username.'.svg';
        $qrcode->size(100)->generate($hashed_id, $file_name);

        return response()->download($file_name);

        //***************generate all branch qr*******************
//        $partners = PartnerInfo::with('branches')->get();
//        foreach ($partners as $partner) {
//            foreach ($partner->branches as $branch) {
//                $hashed_id = $this->generateBranchQr($branch->id, null);
//                $qrcode = new BaconQrCodeGenerator;
//                //create "images->branchqr" folder in public folder manually before run this function
//                $qrcode->size(100)->generate($hashed_id, '../public/images/branchqr/' . $partner->partner_name . '-' .
//                    $branch->partner_area . '-' . $branch->username . '.svg');
//            }
//        }
//        return 'qr created successfully';
    }

    //function to generate pin for scanner
    public function generateScannerPin()
    {
        for ($i = 1; $i <= 500; $i++) {
            A:
            $pin = mt_rand(100000, 999999);
            $pin_exists = BranchUser::where('pin_code', $pin)->count();
            //regenerate pin if already exists
            if ($pin_exists > 0) {
                goto A;
            }
            File::append('pin_numbers.txt', $i.'. '.$pin."\r\n");
        }
        echo 'Successful';
    }

    public function vgdShorten($url, $shorturl = null)
    {
        //$url - The original URL you want shortened
        //$shorturl - Your desired short URL (optional)

        //This function returns an array giving the results of your shortening
        //If successful $result["shortURL"] will give your new shortened URL
        //If unsuccessful $result["errorMessage"] will give an explanation of why
        //and $result["errorCode"] will give a code indicating the type of error

        //See https://v.gd/apishorteningreference.php#errcodes for an explanation of what the
        //error codes mean. In addition to that list this function can return an
        //error code of -1 meaning there was an internal error e.g. if it failed
        //to fetch the API page.

        $url = urlencode($url);
        $basepath = 'https://is.gd/create.php?format=simple';
        //if you want to use is.gd instead, just swap the above line for the commented out one below
        //$basepath = "https://is.gd/create.php?format=simple";
        $result = [];
        $result['errorCode'] = -1;
        $result['shortURL'] = null;
        $result['errorMessage'] = null;

        //We need to set a context with ignore_errors on otherwise PHP doesn't fetch
        //page content for failure HTTP status codes (v.gd needs this to return error
        //messages when using simple format)
        $opts = ['http' => ['ignore_errors' => true]];
        $context = stream_context_create($opts);

        if ($shorturl) {
            $path = $basepath."&shorturl=$shorturl&url=$url";
        } else {
            $path = $basepath."&url=$url";
        }

        $response = @file_get_contents($path, false, $context);

        if (! isset($http_response_header)) {
            $result['errorMessage'] = 'Local error: Failed to fetch API page';

            return $result;
        }

        //Hacky way of getting the HTTP status code from the response headers
        if (! preg_match('{[0-9]{3}}', $http_response_header[0], $httpStatus)) {
            $result['errorMessage'] = 'Local error: Failed to extract HTTP status from result request';

            return $result;
        }

        $errorCode = -1;
        switch ($httpStatus[0]) {
            case 200:
                $errorCode = 0;
                break;
            case 400:
                $errorCode = 1;
                break;
            case 406:
                $errorCode = 2;
                break;
            case 502:
                $errorCode = 3;
                break;
            case 503:
                $errorCode = 4;
                break;
        }

        if ($errorCode == -1) {
            $result['errorMessage'] = 'Local error: Unexpected response code received from server';

            return $result;
        }

        $result['errorCode'] = $errorCode;
        if ($errorCode == 0) {
            $result['shortURL'] = $response;
        } else {
            $result['errorMessage'] = $response;
        }

        return $result;
    }
}//controller ends
