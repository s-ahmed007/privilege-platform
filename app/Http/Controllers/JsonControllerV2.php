<?php

namespace App\Http\Controllers;

use App\AllAmounts;
use App\B2b2cInfo;
use App\BonusRequest;
use App\BranchOffers;
use App\BranchUser;
use App\CardPromoCodes;
use App\CardPromoCodeUsage;
use App\CardSellerInfo;
use App\Categories;
use App\CustomerAccount;
use App\CustomerInfo;
use App\CustomerRewardRedeem;
use App\GeoLocation;
use App\Helpers\LengthAwarePaginator;
use App\Http\Controllers\Enum\Constants;
use App\Http\Controllers\Enum\DeliveryType;
use App\Http\Controllers\Enum\InfluencerPercentage;
use App\Http\Controllers\Enum\LikerType;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\Enum\PostType;
use App\Http\Controllers\Enum\PromoType;
use App\Http\Controllers\Enum\SharerType;
use App\Http\Controllers\Enum\ssl_validation_type;
use App\Http\Controllers\Enum\VerificationType;
use App\InfoAtBuyCard;
use App\LikePost;
use App\LikesReview;
use App\PartnerAccount;
use App\PartnerBranch;
use App\PartnerGalleryImages;
use App\PartnerInfo;
use App\Post;
use App\RbdCouponPayment;
use App\RbdStatistics;
use App\ResetUser;
use App\Review;
use App\RoyaltyLogEvents;
use App\SocialId;
use App\SslTransactionTable;
use App\TransactionTable;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request as Input;
use Illuminate\Support\Facades\Response;
use SMTPValidateEmail\Validator as SmtpEmailValidator;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Tymon\JWTAuth\Facades\JWTAuth;

class JsonControllerV2 extends Controller
{
    //function for login of customer & partner
    public function branchLogin()
    {
        //get data from login form
        $username = Input::get('login_username');
        $password = Input::get('login_password');
        $encrypted_password = null;
        $partner_info = null;
        //get encrypted password from partner username
        $partner = PartnerBranch::where('username', $username)->first();
        if (! empty($partner)) {
            $partner->makeVisible('password');
            $encrypted_password = $partner->password;
            $partner->makeHidden('password');
        }
        if ($encrypted_password) {
            $decrypted_password = (new functionController)->encrypt_decrypt('decrypt', $encrypted_password);
            if ($password == $decrypted_password) {
                //checking login credential for customer
                $branch = PartnerBranch::where('username', $username)->with('info.profileImage', 'info.discount')->where(
                    'password',
                    $encrypted_password
                )->first();
            }
        }

        // =====================================================================================================================================
        // ==========================================================partner branch login here=========================================================
        // =====================================================================================================================================
        if (! empty($branch)) {
            //check partner validity
            $curDate = date('Y-m-d');
            $exp_date = $branch->info->expiry_date;
            $cur_date = new DateTime($curDate);
            $exp_date = new DateTime($exp_date);
            $interval = $cur_date->diff($exp_date);
            $daysRemaining = $interval->days;
            $branch->days_remaining = $daysRemaining;

            return Response::json($branch);
        } else {
            $error = 'Username or Password did not match!';

            return Response::json(['error' => $error], 201);
        }
    }

    public function getPartnerTransactionHistory()
    {
        $branch_id = Input::get('branch_id');

        $transactions = DB::table('transaction_table as tt')
            ->leftJoin('customer_info as ci', 'ci.customer_id', '=', 'tt.customer_id')
            ->leftJoin('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
            ->leftJoin('all_coupons as ac', function ($join) {
                $join->on('ac.branch_id', '=', 'tt.branch_id')
                    ->on('ac.id', '=', 'br.coupon_id');
            })
            ->select(
                'tt.customer_id',
                'tt.amount_spent',
                'tt.posted_on',
                'tt.discount_amount',
                'ci.customer_full_name',
                'ci.customer_profile_image',
                'ca.customer_username',
                'ac.coupon_type',
                'ac.reward_text'
            )
            ->where('tt.branch_id', $branch_id)
            ->orderBy('tt.posted_on', 'DESC')
            ->get();
        $transactions = json_decode(json_encode($transactions), true);

        $amount_sum = DB::table('transaction_table')
            ->where('branch_id', $branch_id)
            ->sum('amount_spent');
        $discount_sum = DB::table('transaction_table')
            ->where('branch_id', $branch_id)
            ->sum('discount_amount');

        return Response::json(['transactionHistory' => $transactions, 'amount_sum' => $amount_sum, 'discount_sum' => $discount_sum], 200);
    }

    //function to check user validity && user requests
    public function checkUser()
    {
        $customer_id = Input::get('customer_id');
        $branch_id = Input::get('branch_id');
        $today = date('Y-m-d');
        //check if customer id exists or not
        $customer = CustomerInfo::where('customer_id', $customer_id)->with('type')->first();
        if (empty($customer)) {
            $data['result'] = 'Invalid User';

            return Response::json($data, 201);
        } else {
            $requests = DB::table('bonus_request as brq')
                ->join('all_coupons as acp', 'acp.id', '=', 'brq.coupon_id')
                ->select('acp.reward_text', 'acp.coupon_type', 'brq.*')
                ->where('brq.customer_id', $customer_id)
                ->where('acp.branch_id', $branch_id)
                ->where('brq.used', 0)
                ->where('brq.expiry_date', '>=', $today)
                ->get();
            $requests = json_decode(json_encode($requests), true);
            $customer->requests = $requests;

            return Response::json($customer, 200);
        }
    }

    //function to calculate customer bill
    public function calculateBill()
    {
        $bill = Input::get('bill');
        $customerID = Input::get('customer_id');
        $partnerID = Input::get('partner_account_id');
        $coupon_type = Input::get('coupon_type');
        $request_code = Input::get('request_code');
        $discount = Input::get('discount');

        if (empty($discount)) {
            $discount_percentage = DB::table('customer_info as ci')
                ->join('discount as dis', 'dis.user_type', '=', 'ci.customer_type')
                ->select('discount_percentage')
                ->where('ci.customer_id', $customerID)
                ->where('dis.partner_account_id', $partnerID)
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

        return Response::json($transaction_details);
    }

    public function partnerBranchAccountInfo()
    {
        $branch_id = Input::get('branch_id');
        $branch = PartnerBranch::where('id', $branch_id)->with('info.account', 'info.profileImage', 'info.discount')->first();
        //check partner validity
        $cur_date = new DateTime(date('Y-m-d'));
        $exp_date = new DateTime($branch->info->expiry_date);
        $interval = $cur_date->diff($exp_date);
        $daysRemaining = $interval->days;
        $branch->daysRemaining = $daysRemaining;

        return Response::json($branch);
    }

    public function rbdCouponPayment()
    {
        $branch_id = Input::get('branch_id');
        $partner_payment_stats = RbdCouponPayment::where('branch_id', $branch_id)->first();

        return Response::json($partner_payment_stats);
    }

    public function partnerBranchList()
    {
        $partner_account_id = Input::get('partner_account_id');
        $partnerInfo = PartnerAccount::where('partner_account_id', $partner_account_id)->where('active', 1)->with([
            'branches' => function ($query) {
                $query->where('active', '=', 1);
            },
        ])->first();

        if (! $partnerInfo) {
            $partnerInfo = [];

            return Response::json($partnerInfo);
        } else {
            foreach ($partnerInfo->branches as $key => $branch) {
                $partnerInfo->branches[$key]['offers'] = $this->activeOffers($branch->id, null);
            }

            return Response::json($partnerInfo->branches);
        }
    }

    public function news_feed()
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $pinned_posts = Post::with('like')
            ->where('moderate_status', 1)
            ->where('pinned_post', 1)
            ->orderBy('posted_on', 'DESC')
            ->get();
        $posts = Post::with('like')
            ->where('moderate_status', 1)
            ->where('pinned_post', 0)
            ->orderBy('posted_on', 'DESC')
            ->get();

        //for normal post
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
                if ($like->liker_id == $customer_id) {
                    $previous_like_id = $like->id;
                    $previous_like = 1;
                    break;
                }
            }
            $post->previous_like = $previous_like;
            $post->previous_like_id = $previous_like_id;
        }

        //for pinned post
        foreach ($pinned_posts as $post) {

            //for naming and image of poster
            if ($post->poster_type == PostType::admin) {
                $post->poster_image = 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/all/box-logo.png';
                $post->poster_name = 'Royalty';
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
                if ($like->liker_id == $customer_id) {
                    $previous_like_id = $like->id;
                    $previous_like = 1;
                    break;
                }
            }
            $post->previous_like = $previous_like;
            $post->previous_like_id = $previous_like_id;
        }
        $pinned_posts = json_decode(json_encode($pinned_posts), true);
        $posts = json_decode(json_encode($posts), true);

        $merged_posts = array_merge($pinned_posts, $posts);
        //pagination
        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // Create a new Laravel collection from the array data
        $itemCollection = collect($merged_posts);

        // Define how many items we want to be visible in each page
        $perPage = 10;

        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

        // Create our paginator and pass it to the view
        $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);

        $paginatedItems->setPath('');
        $paginatedItems->setArrayName('news_feed');

        return Response::json($paginatedItems, 200);
    }

    public function feedUrl()
    {
        $id = Input::get('id');
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;

        $enc_review_id = (new functionController)->postShareEncryption('encrypt', $id);
        $this->postShareCount($id, $customer_id);

        return Response::json(['result' => 'post-share/'.$enc_review_id], 200);
    }

    //share counter
    //function to post share count
    public function postShareCount($post_id, $sharer_id)
    {
        $sharer_type = SharerType::customer;
        try {
            DB::beginTransaction(); //to do query rollback
            //insert post share count table
            $inserted = DB::table('share_post')->insert([
                'post_id' => $post_id,
                'sharer_id' => $sharer_id,
                'sharer_type' => $sharer_type,
            ]);
            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack();

            return Response::json('Something went wrong');
        }

        return Response::json($inserted);
    }

    //function to do backend of post like
    public function like_post()
    {
        $post_id = Input::get('post_id');
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $customer = CustomerInfo::where('customer_id', $customer_id)->first();
        $post = Post::Where('id', $post_id)->first();
        $likes_post_count = LikePost::where('post_id', $post_id)
            ->where('liker_id', $customer_id)->where('liker_type', LikerType::customer)->first();
        if (! $likes_post_count) {
            $like = new LikePost([
                'post_id' => $post_id,
                'liker_id' => $customer_id,
                'liker_type' => LikerType::customer,
            ]);
            $like->save();

            if ($post->poster_type == PostType::partner) {
                (new \App\Http\Controllers\Newsfeed\functionController())->setPostLikeNotification($post, $customer, $like->id);
            }

            return Response::json($like);
        } else {
            return Response::json($likes_post_count);
        }
    }

    public function unlike_post()
    {
        $like_id = Input::get('like_id');
        $liked_post = LikePost::find($like_id);
        $liked_post->delete();

        return Response::json(['result' => 'Unliked']);
    }

    public function partnerProfile(Request $request)
    {
        $date = date('d-m-Y');
        $week_Day = strtolower(date('D'));
        $time = date('H:i');
        $branch_id = Input::get('branch_id');
        $customer_id = Input::get('customer_id');
        $platform = $request->header('platform', null);

        $temp_branch = PartnerBranch::where('id', $branch_id)
            ->with('info.reviews.comments', 'info.reviews.likes', 'info.reviews.customer', 'info.reviews.partnerInfo.profileImage')
            ->first();
        $branch = PartnerBranch::where('id', $branch_id)
            ->with(
                'info.discount.customizedPoint',
                'info.profileImage',
                'info.galleryImages',
                'info.menuImages',
                'info.rating',
                'info.tnc',
                'openingHours'
            )
            ->first();

        //customize point time wise dynamic
        if ($branch->info->discount[1]->customizedPoint) {
            $date_valid = false;
            $time_valid = false;
            $week_valid = false;
            $customize_point_date = $branch->info->discount[1]->customizedPoint->date_duration;
            $customize_point_week = $branch->info->discount[1]->customizedPoint->weekdays;
            $customize_point_times = $branch->info->discount[1]->customizedPoint->time_duration;

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
                $branch->info->discount[0]->customizedPoint->point_multiplier = 1;
                $branch->info->discount[1]->customizedPoint->point_multiplier = 1;
            }
        }

        //remaining days
        $curDate = date('Y-m-d');
        $exp_date = $branch->info->expiry_date;
        $cur_date = new DateTime($curDate);
        $exp_date = new DateTime($exp_date);
        $interval = $cur_date->diff($exp_date);
        $daysRemaining = $interval->days;
        $branch->days_remaining = $daysRemaining;

        $reviews = $temp_branch->info->reviews;

        foreach ($reviews as $review) {
            $previous_like = 0;
            $previous_like_id = 0;
            $review_likes = $review->likes;
            foreach ($review_likes as $like) {
                if ($like->liker_id == $customer_id) {
                    $previous_like_id = $like->id;
                    $previous_like = 1;
                    break;
                }
            }
            $review->previous_like = $previous_like;
            $review->previous_like_id = $previous_like_id;
        }
        $branch->info->reviews = $reviews;

        $nearbyPartners = $this->nearbyPartners(
            $branch->info->partner_name,
            $branch->partner_area,
            $branch->latitude,
            $branch->longitude
        );
        $branch->nearbyPartners = $nearbyPartners;

        $datetime = date('F j, Y, g:i a');
        $browser_data = 'Android Application'.','.$datetime;
        if ($platform) {
            if ($platform == PlatformType::ios) {
                $browser_data = 'iOS Application'.','.$datetime;
            }
        }
        DB::table('rbd_statistics')->insert([
            'customer_id' => $customer_id,
            'partner_id' => $branch->partner_account_id,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'browser_data' => $browser_data,
        ]);

        return Response::json($branch);
    }

    //function to get nearby partners for partner profile
    public function nearbyPartners($name, $area, $lat, $long)
    {
        $date = date('d-m-Y');
        $nearbyPartners = DB::table('partner_info as pi')
            ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
            ->join('partner_branch as pb', 'pb.partner_account_id', '=', 'pi.partner_account_id')
            ->join('partner_account as pa', 'pa.partner_account_id', '=', 'pi.partner_account_id')
            ->join('categories as c', 'c.id', '=', 'pi.partner_category')
            ->where('pa.active', 1)
            ->where('pb.active', 1)
            ->select('pi.partner_account_id', 'pb.id as branch_id', 'pi.partner_name', 'pb.longitude',
                'pb.latitude', 'pi.partner_category', 'c.name as category_type', 'ppi.partner_profile_image')
            ->where('pb.partner_area', $area)
            ->where('pi.partner_name', '!=', $name)
            ->get();
        $nearbyPartners = json_decode(json_encode($nearbyPartners), true);
        $i = 0;
        foreach ($nearbyPartners as $nearbyPartner) {
            //get reviews number of nearby partners
            $reviews = DB::table('review')
                ->where('partner_account_id', $nearbyPartner['partner_account_id'])
                ->count();
            $nearbyPartners[$i]['review_number'] = $reviews;
            //get distance from this partner to nearby partners
            $distance = (new functionController)
                ->calculateDistance($lat, $long, $nearbyPartner['latitude'], $nearbyPartner['longitude'], 'K');
            $nearbyPartners[$i]['distance'] = $distance;
            $nearbyPartners[$i]['offer_heading'] = (new functionController2())
                ->partnerOfferHeading($nearbyPartner['partner_account_id']);
            $gallery_image = PartnerGalleryImages::where('partner_account_id', $nearbyPartner['partner_account_id'])
                ->where('pinned', 1)->first();
            if (empty($gallery_image)) {
                $nearbyPartners[$i]['partner_gallery_image'] = PartnerGalleryImages::
                where('partner_account_id', $nearbyPartner['partner_account_id'])->first()->partner_gallery_image;
            } else {
                $nearbyPartners[$i]['partner_gallery_image'] = $gallery_image['partner_gallery_image'];
            }

            $branch = PartnerBranch::where('id', $nearbyPartner['branch_id'])->with('offers')->first();
            $offers = 0;
            foreach ($branch->offers as $offer) {
                $offer_date = $offer['date_duration'][0];
                try {
                    if (
                        new DateTime($offer_date['from']) <= new DateTime($date) && new DateTime($offer_date['to']) >= new DateTime($date)
                        && $offer->active == 1
                    ) {
                        $offers += 1;
                    } else {
                        $offers += 0;
                    }
                } catch (\Exception $e) {
                }
            }
            $nearbyPartners[$i]['offer_count'] = $offers;
            $nearbyPartners[$i]['average_rating'] = (new \App\Http\Controllers\Review\functionController())->getAverageBranchRating($branch->id);
            $i++;
        }

        return $nearbyPartners;
    }

    //function to get nearby partners for customer
    public function partnersNearCustomer(Request $request)
    {
        $date = date('d-m-Y');
        $lat = $request->post('lat');
        $long = $request->post('long');

        $nearbyPartners = DB::table('partner_info as pi')
            ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
            ->join('partner_branch as pb', 'pb.partner_account_id', '=', 'pi.partner_account_id')
            ->join('partner_account as pa', 'pa.partner_account_id', '=', 'pi.partner_account_id')
            ->join('categories as c', 'c.id', '=', 'pi.partner_category')
            ->where('pa.active', 1)
            ->select('pi.partner_account_id', 'pi.partner_name', 'pb.longitude', 'pb.id as branch_id', 'pb.latitude', 'pi.partner_category', 'c.name as category_type',
                'ppi.partner_profile_image')
            ->get();
        $nearbyPartners = json_decode(json_encode($nearbyPartners), true);
        $i = 0;
        foreach ($nearbyPartners as $nearbyPartner) {
            //get reviews number of nearby partners
            $reviews = DB::table('review')
                ->where('partner_account_id', $nearbyPartner['partner_account_id'])
                ->count();
            $nearbyPartners[$i]['review_number'] = $reviews;
            //get distance from this partner to nearby partners
            $customer_location = GeoLocation::fromDegrees($lat, $long);
            $partner_location = GeoLocation::fromDegrees($nearbyPartner['latitude'], $nearbyPartner['longitude']);
            $distance = $customer_location->distanceTo($partner_location, 'km');
            //            return response()->json(round($distance, 2));
            //            $distance = (new functionController)->calculateDistance($lat, $long, $nearbyPartner['latitude'], $nearbyPartner['longitude'], 'K');
            $nearbyPartners[$i]['distance'] = round($distance, 2);

            $branch = PartnerBranch::where('id', $nearbyPartner['branch_id'])->with('offers')->first();
            $offers = 0;
            foreach ($branch->offers as $offer) {
                $offer_date = $offer['date_duration'][0];
                try {
                    if (
                        new DateTime($offer_date['from']) <= new DateTime($date) && new DateTime($offer_date['to']) >= new DateTime($date)
                        && $offer->active == 1
                    ) {
                        $offers += 1;
                    } else {
                        $offers += 0;
                    }
                } catch (\Exception $e) {
                }
            }
            $nearbyPartners[$i]['offer_count'] = $offers;
            $nearbyPartners[$i]['average_rating'] = (new \App\Http\Controllers\Review\functionController())->getAverageBranchRating($branch->id);
            $nearbyPartners[$i]['offer_heading'] = (new functionController2())->partnerOfferHeading($nearbyPartner['partner_account_id']);
            $i++;
        }

        foreach ($nearbyPartners as $key => $value) {
            // 1 km
            if ($value['distance'] > 1) {
                //unset specific array index if not match
                unset($nearbyPartners[$key]);
            }
        }
        $array_column = array_column($nearbyPartners, 'distance');
        array_multisort($array_column, SORT_ASC, $nearbyPartners);

        //pagination
        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // Create a new Laravel collection from the array data
        $itemCollection = collect($nearbyPartners);

        // Define how many items we want to be visible in each page
        $perPage = 10;

        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

        // Create our paginator and pass it to the view
        $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);

        $paginatedItems->setPath('');
        $paginatedItems->setArrayName('partners');

        return response()->json($paginatedItems);
    }

    public function getSSLTransactionId()
    {
        $random_text = '';
        $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
        $codeAlphabet .= '0123456789';
        $max = strlen($codeAlphabet); // edited
        for ($i = 0; $i < 15; $i++) {
            $random_text .= $codeAlphabet[random_int(0, $max - 1)];
        }
        $random_text = 'ROYALTYBD'.$random_text;

        return $random_text;
    }

    public function insertSSLInfo(Request $request)
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $delivery_type = Input::get('delivery_type');
        $amount = Input::get('amount');
        $customer_type = Input::get('customer_type');

        $promo_id = Input::get('promo_id');
        $month = Input::get('month');
        $customer = CustomerAccount::where('customer_id', $customer_id)->with('info', 'social')->first();
        $random_text = $this->getSSLTransactionId();
        $customer->makeVisible('password');
        $platform = $request->header('platform', null);

        if ($month == null) {
            $month = 12;
        }
        if ($customer_type == 7) {
            $customer_type = 1;
        } elseif ($customer_type == 8) {
            $customer_type = 2;
        } elseif ($customer_type == 9) {
            $customer_type = 1;
        } elseif ($customer_type == 10) {
            $customer_type = 2;
        }

        $cod_members = InfoAtBuyCard::where('delivery_type', DeliveryType::cod)
            ->orWhere('delivery_type', DeliveryType::guest_user)->get();

        foreach ($cod_members as $cod_member) {
            if ($cod_member->customer_username == $customer->customer_username) {
                return Response::json(['result' => 'Your username is already taken. Please contact Royalty Support Team.'], 201);
            }

            if ($cod_member->customer_email == $customer->info->customer_email) {
                return Response::json(['result' => 'Your E-mail is already taken. Please change your E-mail id from your Royalty Profile'], 201);
            }

            if ($cod_member->customer_contact_number == $customer->info->customer_contact_number) {
                return Response::json(['result' => 'Your phone number is already taken. Please change your phone number from your Royalty Profile'], 201);
            }
        }

        $card_active = 0;
        if ($delivery_type == DeliveryType::home_delivery) {
            $card_active = 1;
        }

        try {
            \DB::beginTransaction();

            if ($customer->social == null) {
                $info_at_buy_card = new InfoAtBuyCard([
                    'customer_id' => $customer_id,
                    'tran_id' => $random_text,
                    'customer_serial_id' => $customer->customer_serial_id,
                    'customer_username' => $customer->customer_username,
                    'password' => 'Asdf1234',
                    'moderator_status' => $customer->moderator_status,
                    'customer_first_name' => 'first_name',
                    'customer_last_name' => 'last_name',
                    'customer_full_name' => $customer->info->customer_full_name,
                    'customer_email' => $customer->info->customer_email,
                    'customer_dob' => $customer->info->customer_dob,
                    'customer_gender' => $customer->info->customer_gender,
                    'customer_contact_number' => $customer->info->customer_contact_number,
                    'customer_address' => $customer->info->customer_address,
                    'customer_profile_image' => $customer->info->customer_profile_image,
                    'customer_type' => $customer_type,
                    'month' => $month,
                    'expiry_date' => $customer->info->expiry_date,
                    'member_since' => $customer->info->member_since,
                    'referral_number' => 0,
                    'reference_used' => $customer->info->reference_used,
                    'card_active' => $card_active,
                    'card_activation_code' => $customer->info->card_activation_code,
                    'firebase_token' => $customer->info->firebase_token,
                    'delivery_status' => 0,
                    'review_deleted' => $customer->info->review_deleted,
                    'delivery_type' => $delivery_type,
                    'card_promo_id' => $promo_id,
                    'order_date' => date('Y-m-d H:i:s'),
                    'platform' => $platform,
                    'paid_amount' => $amount,

                ]);
            } else {
                $info_at_buy_card = new InfoAtBuyCard([
                    'customer_id' => $customer_id,
                    'tran_id' => $random_text,
                    'customer_serial_id' => $customer->customer_serial_id,
                    'customer_username' => $customer->customer_username,
                    'password' => 'Asdf1234',
                    'moderator_status' => $customer->moderator_status,
                    'customer_first_name' => 'first_name',
                    'customer_last_name' => 'last_name',
                    'customer_full_name' => $customer->info->customer_full_name,
                    'customer_email' => $customer->info->customer_email,
                    'customer_dob' => $customer->info->customer_dob,
                    'customer_gender' => $customer->info->customer_gender,
                    'customer_contact_number' => $customer->info->customer_contact_number,
                    'customer_address' => $customer->info->customer_address,
                    'customer_profile_image' => $customer->info->customer_profile_image,
                    'customer_type' => $customer_type,
                    'month' => $month,
                    'expiry_date' => $customer->info->expiry_date,
                    'member_since' => $customer->info->member_since,
                    'referral_number' => 0,
                    'reference_used' => $customer->info->reference_used,
                    'card_active' => $card_active,
                    'card_activation_code' => $customer->info->card_activation_code,
                    'firebase_token' => $customer->info->firebase_token,
                    'delivery_status' => 0,
                    'review_deleted' => $customer->info->review_deleted,
                    'delivery_type' => $delivery_type,
                    'customer_social_id' => $customer->social->id,
                    'card_promo_id' => $promo_id,
                    'customer_social_type' => $customer->social->customer_social_type,
                    'order_date' => date('Y-m-d H:i:s'),
                    'platform' => $platform,
                    'paid_amount' => $amount,
                ]);
            }
            $info_at_buy_card->save();
            //insert info into ssl transaction table
            if ($delivery_type == DeliveryType::home_delivery || $delivery_type == DeliveryType::card_customization) {
                $temp_ssL_data = new SslTransactionTable([
                    'customer_id' => $customer_id,
                    'status' => ssl_validation_type::not_valid,
                    'tran_id' => $random_text,
                    'amount' => $amount,
                    'platform' => PlatformType::android,
                ]);
                $temp_ssL_data->save();
            }
            (new \App\Http\Controllers\AdminNotification\functionController())
                ->buyCardAttemptNotification($info_at_buy_card);
            \DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            \DB::rollBack(); //rollback all successfully executed queries
            return Response::json(['error' => $e->getMessage()]);
        }

        return Response::json(['result' => $random_text], 200);
    }

    //function to check coupon validity
    public function couponValidityCheck()
    {
        $code = Input::get('code');
        $card_price = Input::get('card_price');
        $renew = Input::get('renew');
        $month = Input::get('month');

        $code_exists = CardPromoCodes::where('code', $code)->first();
        if (! $code_exists) {
            return Response::json(['result' => 'Invalid code.'], 201);
        }
        $count = CardPromoCodeUsage::where('promo_id', $code_exists['id'])->count();
        $today = date('Y-m-d');

        if ($code_exists->active != 1) {
            return Response::json(['result' => 'This promo code is not in use anymore.'], 201);
        } elseif ($code_exists->expiry_date < $today) {
            return Response::json(['result' => 'Expired code.'], 201);
        } elseif ($code_exists->usage != 'unlimited' && $code_exists->usage <= $count) {
            return Response::json(['result' => 'This promo code was limited and it is out of stock now.'], 201);
        } else {
            $seller_info = CardSellerInfo::where('promo_ids', 'like', "%\"{$code_exists->id}\"%")->with('account')->first();
            if ($renew == PromoType::RENEW && $seller_info) {
                return Response::json(['result' => 'This promo code is not applicable on renew or upgrade.'], 201);
            } elseif ($renew == PromoType::TRIAL && ! $seller_info) {
                return Response::json(['result' => 'Invalid code.'], 201);
            } elseif ($code_exists->membership_type != PromoType::ALL && $renew != $code_exists->membership_type) {
                if ($code_exists->membership_type == PromoType::CARD_PURCHASE) {
                    $message = 'This promo code will only work for new members.';
                } elseif ($code_exists->membership_type == PromoType::RENEW) {
                    $message = 'This promo code will only work while renewing the membership.';
                } elseif ($code_exists->membership_type == PromoType::UPGRADE) {
                    $message = 'This promo code will only work while upgrading the membership.';
                } elseif ($code_exists->membership_type == PromoType::TRIAL) {
                    $message = 'This promo code will only work while activating the trial membership.';
                } else {
                    $message = 'Invalid Code';
                }

                return Response::json(['result' => $message], 201);
            } elseif ($month && $code_exists->month && $code_exists->month != $month) {
                if ($month > 1) {
                    $txt_month = 'months';
                } else {
                    $txt_month = 'month';
                }

                return Response::json(['result' => 'This promo code is not applicable for '.$month.' '.$txt_month.' membership.'], 201);
            } elseif ($code_exists->type == 1) {
                $final_price = $card_price - $code_exists->flat_rate;

                return Response::json(['promo_id' => $code_exists->id, 'new_card_price' => round($final_price),
                    'discount_price' => round($code_exists->flat_rate), 'seller' => $seller_info, ], 200);
            } elseif ($code_exists->type == 2) {
                $final_price = ($card_price * $code_exists->percentage) / 100;
                $card_price -= $final_price;

                return Response::json(['promo_id' => $code_exists->id, 'new_card_price' => round($card_price),
                    'discount_price' => round($final_price), 'seller' => $seller_info, ], 200);
            } else {
                return Response::json(['result' => 'Something Went wrong.'], 201);
            }
        }
    }

    public function getPartnerToFilter()
    {
        $date = date('d-m-Y');
        $category = Input::get('category');
        $categoryWisePartners = Categories::where('type', $category)->with('info.branches', 'info.branches',
            'info.rating', 'info.reviews', 'info.profileImage', 'info.account', 'info.PartnerCategoryRelation')->get();

        if (count($categoryWisePartners) == 0) {
            return response()->json(['result' => 'Nothing found!'], 404);
        }

        foreach ($categoryWisePartners as $partner) {
            foreach ($partner->info as $info) {
                $branches = $info->branches;
                $partner_gallery_image = (new \App\Http\Controllers\Categories\functionController())
                    ->getPinnedGallery($info->partner_account_id);
                if ($partner_gallery_image) {
                    $info->partner_gallery_image = $partner_gallery_image;
                } else {
                    $info->partner_gallery_image = null;
                }
                $info->featured = (new \App\Http\Controllers\FeaturedPartners\functionController())->isFeaturedPartner($info->partner_account_id, $category);
                $info->locations = (new functionController2())->getBranchLocations($info->activeBranches);
                $offers_counts = 0;
                foreach ($branches as $key => $branch) {
                    if ($branch->active == 1) {
                        $offers = ($this)->activeOffers($branch->id, null);
                        $offers = Collection::make($offers)->unique();
                        $branches[$key]->offers = $offers;
                        foreach ($branch->offers as $offer) {
                            $offer_date = $offer->date_duration;
                            try {
                                if (
                                    new DateTime($offer_date['from']) <= new DateTime($date) && new DateTime($offer_date['to']) >= new DateTime($date)
                                    && $offer->active == 1
                                ) {
                                    $offers_counts += 1;
                                } else {
                                    $offers_counts += 0;
                                }
                            } catch (\Exception $e) {
                            }
                        }
                    }
                }
                $info->offer_count = $offers_counts;
                $info->offer_heading = (new functionController2())->partnerOfferHeading($info->partner_account_id);
            }
        }

        $sub_cats = DB::table('category_relation as cr')
            ->join('sub_cat_1 as sc1', 'sc1.id', '=', 'cr.sub_cat_1_id')
            ->where('cr.main_cat', $categoryWisePartners->first()->id)
            ->distinct('sc1.id')
            ->select('sc1.*')
            ->get();

        foreach ($sub_cats as $sub_cat) {
            $sub_cats_2 = DB::table('category_relation as cr')
                ->join('sub_cat_2 as sc2', 'sc2.id', '=', 'cr.sub_cat_2_id')
                ->where('cr.sub_cat_1_id', $sub_cat->id)
                ->where('cr.main_cat', $categoryWisePartners->first()->id)
                ->select('sc2.*', 'cr.*')
                ->get();
            $sub_cat->sub_cat_2 = $sub_cats_2;
        }

        if (count($sub_cats->first()->sub_cat_2) <= 0) {
            $sub_cats = DB::table('category_relation as cr')
                ->join('sub_cat_1 as sc1', 'sc1.id', '=', 'cr.sub_cat_1_id')
                ->where('cr.main_cat', $categoryWisePartners->first()->id)
                ->distinct('sc1.id')
                ->select('sc1.*', 'cr.*')
                ->get();
        }
        $sorted_info = $categoryWisePartners[0]->info->toArray();
        usort($sorted_info, function ($a, $b) {
            return $a['featured'] < $b['featured'];
        });
        unset($categoryWisePartners[0]['info']);
        $categoryWisePartners[0]['info'] = $sorted_info;

        return Response::json(['sub_cats' => $sub_cats, 'partners' => $categoryWisePartners], 200);
    }

    public function getRandomTextForEmail()
    {
        $digits = 6; // Amount of digits
        $pin = str_pad(rand(0, pow(10, $digits) - 1), $digits, '0', STR_PAD_LEFT);

        return $pin;
    }

    //function for user email verification
    public function mailExist($email, $prev_email)
    {
        $previous_email1 = DB::table('customer_info')
            ->where('customer_email', '!=', $prev_email)
            ->where('customer_email', $email)
            ->count();
        $delivery_types = [3, 4, 6, 7];
        $previous_email2 = DB::table('info_at_buy_card')
            ->where('customer_email', $email)
            ->where('customer_email', '!=', $prev_email)
            ->whereIn('delivery_type', $delivery_types)
            ->count();
        if ($previous_email1 > 0 || $previous_email2 > 0) {
            return true;
        } else {
            return false;
        }
    }

    //function to send verification email to verify customer email
    public function sendMailVerification()
    {
        $prev_mail = Input::get('prev_email');
        $email = Input::get('email');
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;

        //check if user with this email exists or not
        $user = DB::table('customer_info as ci')
            ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
            ->select('ci.customer_id', 'ci.customer_full_name')
            ->where('ci.customer_id', $customer_id)
            ->get();
        $user = json_decode(json_encode($user), true);

        $verification_type = VerificationType::email_verification;

        if ($this->mailExist($email, $prev_mail)) {
            return Response::json(['result' => 'Email already exists.'], 201);
        } elseif ($x = (new functionController2())->isVerificationMailSent($email)) {
            $current = Carbon::now();
            $dt = $x->created_at;
            $diff = $dt->diffInMinutes($current);

            return Response::json(['result' => 'We have already sent your verification e-mail. Please check your inbox or other email folders. You will be able to re-send another verification email after '.(Constants::resend_time - $diff).' minutes.'], 201);
        } elseif (count($user) > 0) {
            $user_id = $user[0]['customer_id'];

            $name = $user[0]['customer_full_name'];
            //generate reset token
            $email_token = $this->getRandomTextForEmail();
            $verification_token = (new functionController)->encrypt_decrypt('encrypt', $email_token);

            try {
                DB::beginTransaction(); //to do query rollback

                $reset_user = new ResetUser();
                $reset_user->customer_id = $user_id;
                $reset_user->token = $verification_token;
                $reset_user->verification_type = $verification_type;
                $reset_user->sent_value = $email;
                $reset_user->save();
                DB::commit(); //to do query rollback
            } catch (\Exception $e) {
                DB::rollBack();

                return Response::json(['result' => 'Internal Server Error. Please try again.'], 201);
            }

            if ((new functionController2())->sendVerificationEmail($email, $name, $verification_token)) {
                return Response::json(['result' => 'A verification link has been sent to your E-mail. Please check your E-mail to verify.'], 200);
            } else {
                return Response::json(['result' => 'Internal Server Error. Please try again.'], 201);
            }
        } else {
            return Response::json(['result' => 'User does not exist!'], 201);
        }
    }

    public function getUsernameFromEmail($email)
    {
        $username = explode('@', $email);

        return $username[0];
    }

    public function randomUsername($first_name)
    {
        $username = $first_name.'.'.rand(1, 99999);

        return $username;
    }

    public function update_gender()
    {
        $customer_gender = Input::get('customer_gender');
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;

        if ($customer_gender) {
            DB::table('customer_info')
                ->where('customer_id', $customer_id)
                ->update([
                    'customer_gender' => $customer_gender,
                ]);
            return Response::json(['result' => 'Success'], 200);
        } else {
            return Response::json(['result' => 'Please select your gender'], 400);
        }
    }

    public function update_dob()
    {
        $dob = Input::get('dob');
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        if ($dob) {
            DB::table('customer_info')
                ->where('customer_id', $customer_id)
                ->update([
                    'customer_dob' => $dob,
                ]);
            return Response::json(['result' => 'Success'], 200);
        } else {
            return Response::json(['result' => 'Please select a date'], 400);
        }
    }

    public function update_profile_image()
    {
        $image_url = Input::get('image_url');
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;

        if ($image_url) {
            (new functionController)->update_profile_image_link($image_url, $customer_id);
            return Response::json(['result' => 'Success'], 200);
        } else {
            return Response::json(['result' => 'Please provide an image url'], 400);
        }
    }

    public function createUserName($name, $email)
    {
        A:
        if ((new jsonController)->usernameExist($this->getUsernameFromEmail($email))) {
            $username = $this->randomUsername($this->getFirstName($name));
            if ((new jsonController)->usernameExist($username)) {
                goto A;
            } else {
                return $username;
            }
        } else {
            $username = $this->getUsernameFromEmail($email);

            return $username;
        }
    }

    public function getFirstName($name)
    {
        $full_name = explode(' ', $name);

        return $full_name[0];
    }

    //function to register a new customer
    public function registration()
    {
        if ((new jsonController)->emailExist(Input::get('email')) || (new jsonController)->partnerEmailExist(Input::get('email'))) {
            return Response::json(['result' => 'Email already exists'], 201);
        } elseif ((new jsonController)->phoneNumberExist(Input::get('phone')) || (new jsonController)->partnerPhoneNumberExist(Input::get('phone'))) {
            return Response::json(['result' => 'Phone number already exists'], 201);
        } else {
            $first = Input::get('first_name');
            $last = Input::get('last_name');
            $email = Input::get('email');
            $contact = Input::get('phone');
            $facebook_signUp_id = Input::get('fb_id');
            $google_signUp_id = Input::get('gmail_id');
            $password = Input::get('password');
            $username = $this->createUserName(($first.' '.$last), $email);
            $image_url = 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/registration/user.png';

            // make password encrypted
            $encrypted_password = (new functionController)->encrypt_decrypt('encrypt', $password);
            $genCustomerID = (new \App\Http\Controllers\LoginRegister\functionController())->generate_customer_id();
            $main_customer_id = $genCustomerID->customer_id.$genCustomerID->customer_id_6;
            //save data in customer_account table
            DB::table('customer_account')->insert([
                'customer_id' => $main_customer_id,
                'customer_serial_id' => $genCustomerID->customer_id_6,
                'customer_username' => $username,
                'password' => $encrypted_password,
                'moderator_status' => 2,
            ]);

            //generate referral number
            $token = (new \App\Http\Controllers\LoginRegister\functionController())->generate_refer_code();

            //save data in customer_info table
            DB::table('customer_info')->insert([
                'customer_id' => $main_customer_id,
                'customer_first_name' => $first,
                'customer_last_name' => $last,
                'customer_full_name' => $first.' '.$last,
                'customer_email' => $email,
                'customer_contact_number' => $contact,
                'customer_profile_image' => $image_url,
                'customer_type' => 3,
                'month' => 0,
                'card_active' => 0,
                'card_activation_code' => 0,
                'expiry_date' => '1971-03-26',
                'member_since' => date('Y-m-d'),
                'firebase_token' => 0,
                'referral_number' => $token,
            ]);

            //save email in subscribers table
            DB::table('subscribers')->insert([
                'email' => $email,
            ]);

            //create reward table with 0 value to all fields
            DB::table('customer_reward')->insert([
                'customer_id' => $main_customer_id,
                'customer_reward' => 0,
                'coupon' => 0,
                'refer_bonus' => 0,
                'bonus_counter' => 0,
            ]);

            //increment usage of referrer if exists in database
            //            if ($this->referUserExist($referrer)) {
            //                DB::table('customer_info')
            //                    ->where('referral_number', $referrer)
            //                    ->increment('reference_used', 1);
            //            }

            //store social sign up id and type in social_id table
            if ($facebook_signUp_id != null) {
                DB::table('social_id')->insert([
                    'customer_id' => $main_customer_id,
                    'customer_social_id' => $facebook_signUp_id,
                    'customer_social_type' => 'facebook',
                ]);
            } elseif ($google_signUp_id != null) {
                DB::table('social_id')->insert([
                    'customer_id' => $main_customer_id,
                    'customer_social_id' => $google_signUp_id,
                    'customer_social_type' => 'google',
                ]);
            }

            return Response::json([
                'result' => 'Congratulations! You have successfully created an account. Please use your phone number to login',
                'customer_id' => $main_customer_id, 'customer_username' => $username,
            ], 200);
        }
    }

    public function userProfile()
    {
        $customer_id = Input::get('customer_id');
        $data = $this->getUserProfileData($customer_id);
        if ($data) {
            return Response::json($data, 200);
        } else {
            $error = 'User does not exist.';

            return Response::json(['error' => $error], 201);
        }
    }

    public function loginCheck()
    {
        //get data from login form
        $username = Input::get('login_username');
        $password = Input::get('login_password');

        //get encrypted password from customer username
        $encrypted_password = DB::table('customer_account')
            ->select('password')
            ->where('customer_username', $username)
            ->get();
        $encrypted_password = json_decode(json_encode($encrypted_password), true);

        //get encrypted password from customer phone
        $encrypted_password_from_phone_one = DB::table('customer_info as ci')
            ->join('customer_account as ca', 'ci.customer_id', '=', 'ca.customer_id')
            ->select('ca.password')
            ->where('ci.customer_contact_number', $username)
            ->get();
        $encrypted_password_from_phone_one = json_decode(json_encode($encrypted_password_from_phone_one), true);

        //get encrypted password from customer phone with country code
        $encrypted_password_from_phone_two = DB::table('customer_info as ci')
            ->join('customer_account as ca', 'ci.customer_id', '=', 'ca.customer_id')
            ->select('ca.password')
            ->where('ci.customer_contact_number', '+88'.$username)
            ->get();
        $encrypted_password_from_phone_two = json_decode(json_encode($encrypted_password_from_phone_two), true);

        if ($encrypted_password) {
            $encrypted_password = $encrypted_password[0];
            $decrypted_password = (new functionController)->encrypt_decrypt('decrypt', $encrypted_password['password']);
            if ($password == $decrypted_password) {
                //checking login credential for customer
                $customer_info = DB::table('customer_account')
                    ->select('customer_id', 'moderator_status')
                    ->where('customer_username', $username)
                    ->where('password', $encrypted_password)
                    ->get();
                //                $customer_info->toArray();
                $customer_info = json_decode(json_encode($customer_info), true);
            }
        } elseif ($encrypted_password_from_phone_one) {
            $encrypted_password_from_phone_one = $encrypted_password_from_phone_one[0];
            $decrypted_password_from_phone_one = (new functionController)->encrypt_decrypt('decrypt', $encrypted_password_from_phone_one['password']);
            if ($password == $decrypted_password_from_phone_one) {
                //checking login credential for customer
                $customer_info = DB::table('customer_info as ci')
                    ->join('customer_account as ca', 'ci.customer_id', '=', 'ca.customer_id')
                    ->select('ca.customer_id', 'ca.moderator_status')
                    ->where('ci.customer_contact_number', $username)
                    ->where('ca.password', $encrypted_password_from_phone_one)
                    ->get();
                //                $customer_info->toArray();
                $customer_info = json_decode(json_encode($customer_info), true);
            }
        } elseif ($encrypted_password_from_phone_two) {
            $encrypted_password_from_phone_two = $encrypted_password_from_phone_two[0];
            $decrypted_password_from_phone_two = (new functionController)->encrypt_decrypt('decrypt', $encrypted_password_from_phone_two['password']);
            if ($password == $decrypted_password_from_phone_two) {
                //checking login credential for customer
                $customer_info = DB::table('customer_info as ci')
                    ->join('customer_account as ca', 'ci.customer_id', '=', 'ca.customer_id')
                    ->select('ca.customer_id', 'ca.moderator_status')
                    ->where('ci.customer_contact_number', '+88'.$username)
                    ->where('ca.password', $encrypted_password_from_phone_two)
                    ->get();
                //                $customer_info->toArray();
                $customer_info = json_decode(json_encode($customer_info), true);
            }
        } else {
            $error = 'Wrong username or mobile number. If you are trying to login with your mobile number do not forget to add your country code (+88) before the phone number.';

            return Response::json(['error' => $error], 201);
        }

        if (! empty($customer_info) && $customer_info[0]['moderator_status'] != 1) {
            $data = $this->getUserProfileData($customer_info[0]['customer_id']);

            return Response::json($data, 200);
        } elseif (! empty($customer_info) && $customer_info[0]['moderator_status'] == 1) {
            $error = 'Your account has been deactivated. Please contact our customer support at support@royaltybd.com or call us at +880-963-862-0202.';

            return Response::json(['error' => $error], 201);
        } else {
            //            (new functionController)->invalidLoginAttempts();
            $error = 'Username or Password did not match!';

            return Response::json(['error' => $error], 201);
        }
    }

    public function checkSocialLogin()
    {
        $social_id = Input::get('social_id');
        $social_count = SocialId::where('customer_social_id', $social_id)->count();
        if ($social_count > 0) {
            $social = SocialId::where('customer_social_id', $social_id)->first();
            $data = $this->getUserProfileData($social->customer_id);

            return Response::json($data, 200);
        } else {
            return Response::json(['result' => 'Redirect Login'], 201);
        }
    }

    public function getUserProfileData($customer_id)
    {
        $customer_info = CustomerInfo::where('customer_id', $customer_id)->with('cardDelivery')->first();

        //checking birthday
        $curDate = date('m-d');
        $birthday = date('m-d', strtotime($customer_info->customer_dob));
        if ($birthday === $curDate) {
            $customer_info->birthday = 1;
        } else {
            $customer_info->birthday = 0;
        }

        //checking if user has applied for COD or not
        //check if already applied for cod
        $info_at_buy_card = InfoAtBuyCard::where('customer_username', $customer_info->account->customer_username)->where('delivery_type', 4)->get();
        if (count($info_at_buy_card) > 0) {
            $customer_info->applied_cod = 1;
        } else {
            $customer_info->applied_cod = 0;
        }

        //total review of a user
        $review_number = Review::where('customer_id', $customer_id)->count();
        //total partner(unique) number customer visited
        $partner_number = (new functionController)->totalPartnersCustomerVisited($customer_id);
        //total number of card used
        $card_used = TransactionTable::where('customer_id', $customer_id)->count();
        //check customers validity
        $curDate = date('Y-m-d');
        $exp_date = $customer_info->expiry_date;
        $cur_date = new DateTime($curDate);
        $expiry_date = new DateTime($exp_date);
        $interval = date_diff($cur_date, $expiry_date);
        $daysRemaining = $interval->format('%R%a');

        //insert to customer info
        $customer_info->total_review = $review_number;
        $customer_info->total_partner_visited = $partner_number;
        $customer_info->total_card_used = $card_used;
        $customer_info->days_remaining = $daysRemaining;

        return $customer_info;
    }

    public function getUserReviews()
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $reviews = Review::where('customer_id', $customer_id)->where('moderation_status', 1)
            ->with('customerInfo', 'partnerInfo.profileImage', 'comments', 'likes')
            ->orderBy('posted_on', 'DESC')->get();

        if ($reviews) {
            //check if the user liked this review or not
            foreach ($reviews as $review) {
                //for previous like
                $previous_like = 0;
                $previous_like_id = 0;
                $review_likes = $review->likes;
                foreach ($review_likes as $like) {
                    if ($like->liker_id == $customer_id) {
                        $previous_like_id = $like->id;
                        $previous_like = 1;
                        break;
                    }
                }
                $review->previous_like = $previous_like;
                $review->previous_like_id = $previous_like_id;
            }

            //pagination
            // Get current page form url e.x. &page=1
            $currentPage = LengthAwarePaginator::resolveCurrentPage();

            // Create a new Laravel collection from the array data
            $itemCollection = collect($reviews);

            // Define how many items we want to be visible in each page
            $perPage = 10;

            // Slice the collection to get the items to display in current page
            $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

            // Create our paginator and pass it to the view
            $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);

            $paginatedItems->setPath('');
            $paginatedItems->setArrayName('reviews');

            return Response::json($paginatedItems, 200);
        } else {
            $error = 'User does not exist.';

            return Response::json(['error' => $error], 201);
        }
    }

    public function getUserTransactions()
    {
        $customer_id = Input::get('customer_id');
        $transactions = TransactionTable::where('customer_id', $customer_id)->with('branch.info.profileImage', 'bonus.coupon')->get();

        //total spent amount of this customer
        $amount_sum = TransactionTable::where('customer_id', $customer_id)->sum('amount_spent');
        //total discount a customer got
        $discount_sum = TransactionTable::where('customer_id', $customer_id)->sum('discount_amount');

        if ($transactions) {

            //pagination
            // Get current page form url e.x. &page=1
            $currentPage = LengthAwarePaginator::resolveCurrentPage();

            // Create a new Laravel collection from the array data
            $itemCollection = collect($transactions);
            // Define how many items we want to be visible in each page
            $perPage = 10;

            // Slice the collection to get the items to display in current page
            $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

            // Create our paginator and pass it to the view
            $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);

            $paginatedItems->setPath('');
            $paginatedItems->setArrayName('transactions');

            return Response::json(['transactionHistory' => $paginatedItems, 'total_spent' => $amount_sum, 'total_discount' => $discount_sum], 201);
        } else {
            $error = 'User does not exist.';

            return Response::json(['error' => $error], 201);
        }
    }

    //function to get transaction history of customer
    public function getUserSortedTransaction(Request $request)
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $month = $request->post('month');
        $year = $request->post('year');

        $transactions = (new functionController())->customerTransaction($customer_id)['transactions'];

        if ($month != null && $year != null) {
            foreach ($transactions as $key => $value) {
                $ex = explode('-', $value['posted_on']);
                //checking if DB=>"month,year" & selected=>"month,year" are same or not
                if ($ex[0] != $year || $ex[1] != $month) {
                    //unset specific array index if not match
                    unset($transactions[$key]);
                }
            }
        }

        return Response::json(['transactionHistory' => (new JsonBranchUserController())
            ->makePagination($transactions, 'transactions')], 200);
    }

    public function getUserRequestedCoupons()
    {
        $customer_id = Input::get('customer_id');
        $bonus_requests = BonusRequest::where('customer_id', $customer_id)->with('coupon.branch.info.profileImage', 'transaction')->get();

        if ($bonus_requests) {
            //pagination
            // Get current page form url e.x. &page=1
            $currentPage = LengthAwarePaginator::resolveCurrentPage();

            // Create a new Laravel collection from the array data
            $itemCollection = collect($bonus_requests);

            // Define how many items we want to be visible in each page
            $perPage = 10;

            // Slice the collection to get the items to display in current page
            $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

            // Create our paginator and pass it to the view
            $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);

            $paginatedItems->setPath('');
            $paginatedItems->setArrayName('bonus_requests');

            return Response::json($paginatedItems, 200);
        } else {
            $error = 'User does not exist.';

            return Response::json(['error' => $error], 201);
        }
    }

    public function getUserVisitedList()
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;

        $visited_list = (new jsonController())->totalPartnersCustomerVisited($customer_id);

        //pagination
        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // Create a new Laravel collection from the array data
        $itemCollection = collect($visited_list);

        // Define how many items we want to be visible in each page
        $perPage = 10;

        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

        // Create our paginator and pass it to the view
        $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);

        $paginatedItems->setPath('');
        $paginatedItems->setArrayName('visited_list');

        return Response::json($paginatedItems, 200);
    }

    public function getPartnerProfile(Request $request)
    {
        $branch_id = Input::get('branch_id');
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $platform = $request->header('platform', null);
        $branch = PartnerBranch::where('id', $branch_id)
            ->with('info.profileImage', 'info.rating', 'info.tnc', 'openingHours')
            ->first();

        if ($branch) {
            $branch->facilities_list = null;
            if ($branch->facilities) {
                $branch->facilities_list = \App\BranchFacility::whereIn('id', $branch->facilities)->get();
            }
            $branch->rating = (new \App\Http\Controllers\Review\functionController())->getRatings($branch->id);
            //remaining days
            $curDate = date('Y-m-d');
            $exp_date = $branch->info->expiry_date;
            $cur_date = new DateTime($curDate);
            $exp_date = new DateTime($exp_date);
            $interval = $cur_date->diff($exp_date);
            $daysRemaining = $interval->days;
            $branch->days_remaining = $daysRemaining;
            $datetime = date('F j, Y, g:i a');
            $browser_data = 'Android Application'.','.$datetime;
            if ($platform) {
                if ($platform == PlatformType::ios) {
                    $browser_data = 'iOS Application'.','.$datetime;
                }
            }

            DB::table('rbd_statistics')->insert([
                'customer_id' => $customer_id,
                'partner_id' => $branch->partner_account_id,
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'browser_data' => $browser_data,
            ]);
            $branch->info->rating->rating_counter = (new functionController())->getRatingCounter($branch->partner_account_id);
            //isOpen now
            $openingHours2 = [];
            $openingHours = $branch->openingHours->toArray();
            $openingHours2['sat'] = $openingHours['sat'];
            $openingHours2['sun'] = $openingHours['sun'];
            $openingHours2['mon'] = $openingHours['mon'];
            $openingHours2['tue'] = $openingHours['tue'];
            $openingHours2['wed'] = $openingHours['wed'];
            $openingHours2['thu'] = $openingHours['thurs'];
            $openingHours2['fri'] = $openingHours['fri'];

            if ($openingHours2[strtolower(date('D'))] == 'Always Open') {
                $branch->isOpen = 'Open now';
            } else {
                $open = (new functionController2())->isOpen(time(), $openingHours2);
                if ($open == 0) {
                    $branch->isOpen = 'Closed';
                } else {
                    $branch->isOpen = 'Open now';
                }
            }

            return Response::json($branch, 200);
        } else {
            $error = 'Partner does not exist.';

            return Response::json(['error' => $error], 201);
        }
    }

    public function getP2PNearby()
    {
        $branch_id = Input::get('branch_id');

        $branch = PartnerBranch::where('id', $branch_id)
            ->with('info')
            ->first();
        if ($branch) {
            //near by partners
            $nearbyPartners = $this->nearbyPartners(
                $branch->info->partner_name,
                $branch->partner_area,
                $branch->latitude,
                $branch->longitude
            );
            $nearbyPartners = collect($nearbyPartners)->sortBy('distance');
            $branch->nearbyPartners = $nearbyPartners;

            //pagination
            // Get current page form url e.x. &page=1
            $currentPage = LengthAwarePaginator::resolveCurrentPage();

            // Create a new Laravel collection from the array data
            $itemCollection = collect($nearbyPartners);

            // Define how many items we want to be visible in each page
            $perPage = 10;

            // Slice the collection to get the items to display in current page
            $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

            // Create our paginator and pass it to the view
            $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);

            $paginatedItems->setPath('');
            $paginatedItems->setArrayName('partners');

            return Response::json($paginatedItems, 200);
        } else {
            $error = 'Partner does not exist.';

            return Response::json(['error' => $error], 201);
        }
    }

    public function getPartnerReviews()
    {
        $branch_id = Input::get('branch_id');
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;

        $reviews = (new \App\Http\Controllers\Review\functionController())
            ->getReviews($branch_id, $customer_id, LikerType::customer);
        $total_review_count = $reviews->where('heading', '!=', 'n/a')->where('body', '!=', 'n/a')->count();
        $total_rating_count = $reviews->where('heading', 'n/a')->where('body', 'n/a')->count();
        $reviews = $reviews->where('heading', '!=', 'n/a')->where('body', '!=', 'n/a');
        //pagination
        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // Create a new Laravel collection from the array data
        $itemCollection = collect($reviews);

        // Define how many items we want to be visible in each page
        $perPage = 10;

        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

        // Create our paginator and pass it to the view
        $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);

        $paginatedItems->setPath('');
        $paginatedItems->setArrayName('reviews');

        return Response::json(['reviews' => $paginatedItems, 'review_count' => $total_review_count, 'rating_count' => $total_rating_count], 200);
    }

    public function getBranchOffers()
    {
        $branch_id = Input::get('branch_id');
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $with_deal = Input::get('with_deal');
        $branch_offers = $this->activeOffers($branch_id, $customer_id, $with_deal);

        if ($branch_offers) {
            //pagination
            // Get current page form url e.x. &page=1
            $currentPage = LengthAwarePaginator::resolveCurrentPage();

            // Create a new Laravel collection from the array data
            $itemCollection = collect($branch_offers);

            // Define how many items we want to be visible in each page
            $perPage = 10;

            // Slice the collection to get the items to display in current page
            $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

            // Create our paginator and pass it to the view
            $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);
            $paginatedItems->setPath('');
            $paginatedItems->setArrayName('branch_offers');

            return response()->json($paginatedItems, 200);
        } else {
            $error = 'Nothing found.';

            return response()->json(['error' => $error], 201);
        }
    }

    public function activeOffers($branch_id, $customer_id, $withDeal = false)
    {
        $date = date('d-m-Y');
        $week_Day = strtolower(date('D'));
        $time = date('H:i');
        $branch = PartnerBranch::where('id', $branch_id)->with('account')->first();
        if ($branch) {
            if ($branch->active == 0 || $branch->account->active == 0) {
//            return response()->json(['message' => 'Merchant not available.'], 404);
                return null;
            }
        } else {
            return null;
        }
        $branch_offers = [];
        $i = 0;
        $branch_col_offers = BranchOffers::where('branch_id', $branch_id)
            ->where('active', 1)
            ->where('selling_point', '=', null)
            ->with('customizedPoint')
            ->orderBy('priority', 'DESC')
            ->get();
        foreach ($branch_col_offers as $col_offer) {
            $col_offer->isDeal = false;
            $branch_offers[$i++] = $col_offer;
        }
        $redeem_rewards = [];
        $i = 0;
        $_redeems = CustomerRewardRedeem::where('customer_id', $customer_id)->where('used', 0)->get();
        foreach ($_redeems as $key => $customer_redeem) {
            $reward = BranchOffers::where('id', $customer_redeem->offer_id)->where('branch_id', $branch_id)->first();
            if ($reward) {
                $reward->redeem = $_redeems[$key];
                $reward->isDeal = false;
                $redeem_rewards[$i++] = $reward;
            }
        }
        $all_offers = array_merge($redeem_rewards, $branch_offers);
        if ($withDeal == 'true') {
            $deal_list = [];
            $i = 0;
            $_deals = \App\BranchVoucher::with('branch.info.profileImage', 'branch.info.rating')->where('branch_id', $branch_id)->where('active', 1)->orderBy('priority', 'DESC')->get();
            if (count($_deals) > 0) {
                //cur user purchased deal list
                $purchased_deal_ids = [];
                $purchased_deal = (new \App\Http\Controllers\Voucher\functionController())->purchasedData($customer_id);
                $purchased_deal = $purchased_deal->where('redeemed', 0);
                if (count($purchased_deal) > 0) {
                    foreach ($purchased_deal as $key => $value) {
                        if ($value->redeemed == 0 && date('Y-m-d', strtotime($value->expiry_date)) >= date('Y-m-d')) {
                            if (! in_array($value->voucher_id, $purchased_deal_ids)) {
                                array_push($purchased_deal_ids, $value->voucher_id);
                            }
                        }
                    }
                }

                foreach ($_deals as $deal) {
                    $deal->isDeal = true;
                    if ($purchased_deal->where('voucher_id', $deal->id)->count() > 0) {
                        $deal->purchase_id = $purchased_deal->where('voucher_id', $deal->id)->first()->id;
                    } else {
                        $deal->purchase_id = null;
                    }
                    if (in_array($deal->id, $purchased_deal_ids)) {
                        $deal->purchased = true;
                    } else {
                        $deal->purchased = false;
                    }
                    $deal_list[$i++] = $deal;
                }
            }
            $all_offers = array_merge($deal_list, $all_offers);
        }

        foreach ($all_offers as $branch_offer) {
            //check expiry
            $offer_date = $branch_offer->date_duration;

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
                $expiry_status = true;
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
            //customize point time wise dynamic
        }
        if ($all_offers) {
            foreach ($all_offers as $key => $value) {
                // 1 km
                if ($value['expired']) {
                    //unset specific array index if not match
                    unset($all_offers[$key]);
                }
            }
            $all_offers = array_values($all_offers);

            return $all_offers;
        } else {
            return null;
        }
    }

    public function getPartnerDiscounts()
    {
        $branch_id = Input::get('branch_id');
        $date = date('d-m-Y');
        $week_Day = strtolower(date('D'));
        $time = date('H:i');

        $branch = PartnerBranch::where('id', $branch_id)
            ->with('info.discount.customizedPoint')
            ->first();

        //customize point time wise dynamic
        if ($branch->info->discount[1]->customizedPoint) {
            $date_valid = false;
            $time_valid = false;
            $week_valid = false;
            $customize_point_date = $branch->info->discount[1]->customizedPoint->date_duration;
            $customize_point_week = $branch->info->discount[1]->customizedPoint->weekdays;
            $customize_point_times = $branch->info->discount[1]->customizedPoint->time_duration;

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
                $branch->info->discount[0]->customizedPoint->point_multiplier = 1;
                $branch->info->discount[1]->customizedPoint->point_multiplier = 1;
            }
        }

        if ($branch) {
            return Response::json($branch->info->discount, 200);
        } else {
            $error = 'Partner does not exist.';

            return Response::json(['error' => $error], 201);
        }
    }

    public function getPartnerGallery()
    {
        $branch_id = Input::get('branch_id');
        $branch = PartnerBranch::where('id', $branch_id)
            ->with(['info.galleryImages' => function ($query) {
                $query->orderBy('id', 'DESC');
            }])
            ->first();

        if ($branch) {
            $galleryImages = $branch->info->galleryImages;

            return Response::json($galleryImages, 200);
        } else {
            $error = 'Partner does not exist.';

            return Response::json(['error' => $error], 201);
        }
    }

    public function getPartnerMenu()
    {
        $branch_id = Input::get('branch_id');
        $branch = PartnerBranch::where('id', $branch_id)
            ->with('info.menuImages')
            ->first();

        if ($branch) {
            return Response::json($branch->info->menuImages, 200);
        } else {
            $error = 'Partner does not exist.';

            return Response::json(['error' => $error], 201);
        }
    }

    public function isRoyaltyMember()
    {
        $phone_number = Input::get('phone');
        $info = CustomerInfo::where('customer_contact_number', $phone_number)->with('account')->first();
        if ($info) {
            return Response::json(['result' => $info->account->customer_username], 200);
        } else {
            return Response::json(['result' => 'Not an user.'], 201);
        }
    }

    public function connectWithSocial()
    {
        $social_id = Input::get('social_id');
        $customer_id = Input::get('customer_id');
        $social_type = Input::get('customer_social_type');
        $social_count = DB::table('social_id')
            ->where('customer_social_id', $social_id)
            ->count();
        if ($social_count > 0) {
            return Response::json(['error' => 'This social account is already in use. Please try with a different one.'], 201);
        } else {
            $saved_social = new SocialId([
                'customer_id' => $customer_id,
                'customer_social_id' => $social_id,
                'customer_social_type' => $social_type,
            ]);
            $saved_social->save();

            return Response::json($saved_social, 200);
        }
    }

    public function checkReferralCodes(Request $request)
    {
        $referral_number = $request->post('referral_number');
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $refer_customer = CustomerInfo::where('referral_number', $referral_number)->get();

        if (count($refer_customer) > 0) {
            if ($refer_customer[0]->customer_id == $customer_id) {
                return response()->json(['result' => 'You can not use your own referral code'], 201);
            } else {
                return response()->json(['result' => 'Valid'], 200);
            }
        } else {
            if ($referral_number == null) {
                return response()->json(['result' => 'Valid'], 200);
            } else {
                return response()->json(['result' => 'Incorrect referral code'], 201);
            }
        }
    }

    public function recentlyViewed(Request $request)
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $sub = RbdStatistics::where('customer_id', $customer_id)
            ->with('partnerInfo.profileImage', 'partnerInfo.account')
            ->orderBy('id', 'DESC')
            ->get()
            ->unique('partner_id')
            ->take(6);
        $partners = [];
        $i = 0;
        foreach ($sub as $s) {
            if ($s->partnerInfo->account->active == 1) {
                $partners[$i] = $s;
                $i++;
            }
        }
        //        $values = DB::table(DB::raw("({$sub->toSql()}) as sub"))
        //            ->select('partner_id')
        //            ->where('customer_id', $customer_id)
        //            ->distinct('partner_id')
        //            ->get()->take(6);
        //        $i = 0;
        //        foreach ($values as $value) {
        //            $info = PartnerInfo::where('partner_account_id', $value->partner_id)->with('profileImage')->first();
        //            $values[$i] = $info;
        //            $i++;
        //        }
        return response()->json($partners, 200);
    }

    public function customerNotifications(Request $request)
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        //notifications
        $today = $this->todayNotification($customer_id);
        $yesterday = $this->yesterdayNotification($customer_id);
        $this_week = $this->lastWeekNotification($customer_id);
        $earlier = $this->earlierNotification($customer_id);

        return response()->json(['today' => $today, 'yesterday' => $yesterday, 'this_week' => $this_week, 'earlier' => $earlier]);
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

        return $this->getCustomerNotifications(collect($notifications));
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

        return $this->getCustomerNotifications(collect($notifications));
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

        return $this->getCustomerNotifications(collect($notifications));
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

        return $this->getCustomerNotifications(collect($notifications));
    }

    //function for get all unseen notifications of customer
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
                        ->select('ci.customer_first_name', 'ci.customer_last_name', 'ci.customer_full_name', 'ci.customer_profile_image', 'lr.review_id')
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
                    ->select('ca.customer_username', 'ci.customer_first_name', 'ci.customer_last_name', 'ci.customer_full_name', 'fc.follow_request')
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
            } elseif ($notifications[$i]['notification_type'] == '12') { //refer notification
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

    public function getCardDetailList(Request $request)
    {
        $card_name = $request->post('card_name');
        $cards = AllAmounts::where('type', $card_name)->orderBy('price', 'ASC')->get();

        return response()->json($cards, 200);
    }

    public function getMinCardValue()
    {
        $gold = AllAmounts::where('type', 'gold_android')->orderBy('price', 'ASC')->first();

        $platinum = AllAmounts::where('type', 'platinum_android')->orderBy('price', 'ASC')->first();

        return Response::json(['gold' => $gold, 'platinum' => $platinum], 200);
    }

    public function getReviewLikeList(Request $request)
    {
        $review_id = $request->post('review_id');
        $likes = LikesReview::where('review_id', $review_id)->with('customer', 'branch.info.ProfileImage')->orderBy('id', 'DESC')->get();
        //pagination
        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        // Create a new Laravel collection from the array data
        $itemCollection = collect($likes);

        // Define how many items we want to be visible in each page
        $perPage = 10;

        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

        // Create our paginator and pass it to the view
        $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);

        $paginatedItems->setPath('');
        $paginatedItems->setArrayName('likes');

        return response()->json($paginatedItems, 200);
    }

    public function getPostLikeList(Request $request)
    {
        $post_id = $request->post('post_id');
        $likes = LikePost::where('post_id', $post_id)->with('customer', 'partner.ProfileImage')->orderBy('id', 'DESC')->get();
        //pagination
        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        // Create a new Laravel collection from the array data
        $itemCollection = collect($likes);

        // Define how many items we want to be visible in each page
        $perPage = 10;

        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

        // Create our paginator and pass it to the view
        $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);

        $paginatedItems->setPath('');
        $paginatedItems->setArrayName('likes');

        return response()->json($paginatedItems, 200);
    }

    public function logEvents(Request $request)
    {
        $event = $request->post('event');
        $event_value = $request->post('event_value');
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        if (! $event) {
            $event = 'ANDROID_DEVICE_BUG';
        }
        $log = new RoyaltyLogEvents([
            'event' => $event,
            'event_value' => $event_value,
            'customer_id' => $customer_id,
            'posted_on' => date('Y-m-d H:i:s'),
        ]);
        $log->save();

        return response()->json($log, 200);
    }

    public function getBranchByScan(Request $request)
    {
        $encrypted_id = $request->post('encrypted_id');
        $branch_id = (new functionController)->generateBranchQr(null, $encrypted_id);
        $branch = PartnerBranch::where('id', $branch_id)->with('info')->first();
        if ($branch) {
            return response()->json($branch, 200);
        } else {
            return response()->json(['error' => 'Invalid QR'], 201);
        }
    }

    public function checkBranchUser(Request $request)
    {
        $pin_code = $request->post('pin_code');
        $branch_user = BranchUser::where('pin_code', $pin_code)->with('branchScanner')->first();
        if ($branch_user) {
            return response()->json($branch_user, 200);
        } else {
            return response()->json(['error' => 'Invalid PIN.'], 201);
        }
    }

    public function influencerUsage(Request $request)
    {
        $gold_card = 1;
        $platinum_card = 2;
        $customerID = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $promo_used = (new functionController)->influencersPromoUsed($customerID);
        if (!$promo_used['user']) {
            return response()->json(['status' => 'User not found'], 401);
        }
        $influencer_usage = $promo_used['user'];
        $promo_used = $influencer_usage->promoUsage;
        $final_usage = [];
        $i = 0;
        $total_commission = 0;
        foreach ($promo_used as $promo) {
            $final_usage[$i]['date'] = $promo->ssl->tran_date;
            $final_usage[$i]['month'] = $promo->customerInfo->month;
            if ($promo->customerInfo->customer_type == $gold_card) {
                $final_usage[$i]['type_of_card'] = 'Gold card';
            } else {
                $final_usage[$i]['type_of_card'] = 'Platinum card';
            }
            $final_usage[$i]['commission'] = round($promo->ssl->amount * (InfluencerPercentage::percentage / 100));
            $total_commission = $total_commission + $final_usage[$i]['commission'];
            $i++;
        }

        //pagination
        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // Create a new Laravel collection from the array data
        $itemCollection = collect($final_usage);

        // Define how many items we want to be visible in each page
        $perPage = 10;

        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

        // Create our paginator and pass it to the view
        $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);

        $paginatedItems->setPath('');
        $paginatedItems->setArrayName('influencer_usage');

        return response()->json(['commissions' => $paginatedItems, 'total_commission' => $total_commission], 200);
    }

    public function getPin(Request $request)
    {
        $customerID = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $account = CustomerAccount::where('customer_id', $customerID)->first();

        return response()->json(['pin' => $account->pin], 200);
    }

    public function setPin(Request $request)
    {
        $customerID = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $pin = $request->post('pin');
        $encrypted_pin = (new functionController)->encrypt_decrypt('encrypt', $pin);
        CustomerAccount::where('customer_id', $customerID)
            ->update(['pin' => $encrypted_pin]);

        return response()->json(['pin' => $encrypted_pin], 200);
    }
}
