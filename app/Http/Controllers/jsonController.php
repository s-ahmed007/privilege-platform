<?php

namespace App\Http\Controllers;

use App\AllAmounts;
use App\AllCoupons;
use App\B2b2cInfo;
use App\CardDelivery;
use App\CardPrice;
use App\CardPromoCodes;
use App\CardPromoCodeUsage;
use App\CardSellerInfo;
use App\Categories;
use App\CustomerAccount;
use App\CustomerInfo;
use App\CustomerLoginSession;
use App\CustomerNotification;
use App\CustomerPoint;
use App\CustomerReward;
use App\Discount;
use App\Events\append_notification;
use App\Events\like_review;
use App\Events\offer_availed;
use App\Events\refer_bonus;
use App\Helpers\LengthAwarePaginator;
use App\Http\Controllers\Enum\CustomerType;
use App\Http\Controllers\Enum\GlobalTexts;
use App\Http\Controllers\Enum\LikerType;
use App\Http\Controllers\Enum\LoginStatus;
use App\Http\Controllers\Enum\MembershipPriceType;
use App\Http\Controllers\Enum\NewsFeedType;
use App\Http\Controllers\Enum\notificationType;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\Enum\PointType;
use App\Http\Controllers\Enum\PostType;
use App\Http\Controllers\Enum\PushNotificationType;
use App\Http\Controllers\Enum\SellerCommissionType;
use App\Http\Controllers\Enum\ssl_validation_type;
use App\Http\Controllers\Renew\apiController;
use App\Http\Controllers\Reward\functionController as rewardFunctionController;
use App\LikesReview;
use App\PartnerAccount;
use App\PartnerBranch;
use App\PartnerGalleryImages;
use App\PartnerInfo;
use App\Post;
use App\Review;
use App\SellerBalance;
use App\SocialId;
use App\SslTransactionTable;
use App\TopBrands;
use App\transaction_table;
use App\TransactionTable;
use App\TrendingOffers;
use App\Wish;
use Carbon\Carbon;
use Datetime;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request as Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Image;
use Mail;
use Response;
use SMTPValidateEmail\Validator as SmtpEmailValidator;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Tymon\JWTAuth\Facades\JWTAuth;
use View;

class jsonController extends Controller
{
    public function getCarousalImages()
    {
        return [
            'https://res.cloudinary.com/royaltybd/image/upload/v1601534031/Home/App/Banner/car-intro-app.png',
            'https://res.cloudinary.com/royaltybd/image/upload/v1601534031/Home/App/Banner/car-food.png',
            'https://res.cloudinary.com/royaltybd/image/upload/v1601534031/Home/App/Banner/car-health.png',
            'https://res.cloudinary.com/royaltybd/image/upload/v1601534031/Home/App/Banner/car-life.png',
            'https://res.cloudinary.com/royaltybd/image/upload/v1601534032/Home/App/Banner/car-beauty.png',
            'https://res.cloudinary.com/royaltybd/image/upload/v1601534030/Home/App/Banner/car-ent.png',
            'https://res.cloudinary.com/royaltybd/image/upload/v1601534031/Home/App/Banner/car-get.png',
        ];
    }

    public function homepageJson()
    {
        $date = date('d-m-Y');

        //trending offers
        $profileImages = DB::table('partner_info as pi')
            ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
            ->join('trending_offers as to', 'to.partner_account_id', '=', 'pi.partner_account_id')
            ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
            ->join('categories as ca', 'ca.id', '=', 'pi.partner_category')
            ->join('partner_account as pa', 'pa.partner_account_id', '=', 'pi.partner_account_id')
            ->select('ppi.partner_profile_image', 'ppi.partner_cover_photo', 'ppi.partner_account_id', 'pi.partner_name', 'ca.name as partner_category', 'rat.average_rating')
            ->where('pa.active', 1)
            ->get();

        $i = 0;
        foreach ($profileImages as $profileImage) {
            $branches = PartnerBranch::where('partner_account_id', $profileImage->partner_account_id)->where('active', 1)->with('offers')->get();
            $offers = 0;
            foreach ($branches as $branch) {
                foreach ($branch->offers as $offer) {
                    $offer_date = $offer['date_duration'][0];
                    try {
                        if (new DateTime($offer_date['from']) <= new DateTime($date) && new DateTime($offer_date['to']) >= new DateTime($date)
                            && $offer->active == 1) {
                            $offers += 1;
                        } else {
                            $offers += 0;
                        }
                    } catch (\Exception $e) {
                    }
                }
            }
            $profileImage->offer_count = $offers;
            $profileImage->offer_heading = (new functionController2())
                ->partnerOfferHeading($profileImage->partner_account_id);
            $gallery_image = PartnerGalleryImages::where('partner_account_id', $profileImage->partner_account_id)
                ->where('pinned', 1)->first();
            if (empty($gallery_image)) {
                $profileImage->partner_gallery_image = PartnerGalleryImages::
                where('partner_account_id', $profileImage->partner_account_id)->first()->partner_gallery_image;
            } else {
                $profileImage->partner_gallery_image = $gallery_image['partner_gallery_image'];
            }
            $profileImage->location = (new functionController2())->getBranchLocations($branches);
            $i++;
        }

        $trending = TrendingOffers::orderBy('order_num', 'ASC')->get();
        $trending_collection = collect();
        foreach ($trending as $item) {
            foreach ($profileImages as $trendingOffer) {
                if ($item->partner_account_id == $trendingOffer->partner_account_id) {
                    $trending_collection->push($trendingOffer);
                }
            }
        }
        //top brands
        $topBrands = DB::table('partner_info as pi')
            ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
            ->join('top_brands as tb', 'tb.partner_account_id', '=', 'pi.partner_account_id')
            ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
            ->join('categories as ca', 'ca.id', '=', 'pi.partner_category')
            ->join('partner_account as pa', 'pa.partner_account_id', '=', 'pi.partner_account_id')
            ->select('ppi.partner_profile_image', 'ppi.partner_cover_photo', 'ppi.partner_account_id',
                'pi.partner_name', 'ca.name as partner_category', 'rat.average_rating')
            ->where('pa.active', 1)
            ->get();

        $i = 0;
        foreach ($topBrands as $topBrand) {
            $branches = PartnerBranch::where('partner_account_id', $topBrand->partner_account_id)->where('active', 1)
                ->with('offers')->get();
            $offers = 0;
            foreach ($branches as $branch) {
                foreach ($branch->offers as $offer) {
                    $offer_date = $offer['date_duration'][0];
                    try {
                        if (new DateTime($offer_date['from']) <= new DateTime($date) && new DateTime($offer_date['to']) >= new DateTime($date)
                            && $offer->active == 1) {
                            $offers += 1;
                        } else {
                            $offers += 0;
                        }
                    } catch (\Exception $e) {
                    }
                }
            }
            $topBrand->offer_count = $offers;
            $topBrand->offer_heading = (new functionController2())->partnerOfferHeading($topBrand->partner_account_id);
            $gallery_image = PartnerGalleryImages::where('partner_account_id', $topBrand->partner_account_id)
                ->where('pinned', 1)->first();
            if (empty($gallery_image)) {
                $topBrand->partner_gallery_image = PartnerGalleryImages::
                where('partner_account_id', $topBrand->partner_account_id)->first()->partner_gallery_image;
            } else {
                $topBrand->partner_gallery_image = $gallery_image['partner_gallery_image'];
            }
            $topBrand->location = (new functionController2())->getBranchLocations($branches);
            $i++;
        }

        $top = TopBrands::orderBy('order_num', 'ASC')->get();
        $top_collection = collect();
        foreach ($top as $item) {
            foreach ($topBrands as $topBrand) {
                if ($item->partner_account_id == $topBrand->partner_account_id) {
                    $top_collection->push($topBrand);
                }
            }
        }

        //carousel Images
        $carousel_image = $this->getCarousalImages();

        $images_card_holder = [
            'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/carousel/usage2qr.png',
            'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/carousel/usage1scan.png',
        ];

        //banner image er for app home
        $banner_image = 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/app/app-banner-user.png';
        $banner_image_for_guest_user = 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/app/app-banner-guest.png';
        $banner_image_for_skip_user = 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/home-page/homebanner1.png';

        $image_refer = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/refercode.png';
        $image_all = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/card.png';

        //public
        $public_image_first = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/public.png';
        $public_image_refer = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/public.png';

        //Guest
        $guest_image_first = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/guest.png';
        $guest_image_refer = 'https://royalty-bd.s3-ap-southeast-1.amazonaws.com/static-images/home-page/refer.png';

        //top transacted partners
        $partner_account = PartnerAccount::where('active', 1)
            ->with('info.profileImage', 'info.category', 'info.rating', 'info.branches.transaction')
            ->get();
//        $partnerInfo = PartnerInfo::with('profileImage', 'category', 'rating', 'branches.transaction')->get();
        $partnerInfo = [];
        $i = 0;
        foreach ($partner_account as $account) {
            $partnerInfo[$i] = $account->info;
            $i++;
        }
        $i = 0;
        foreach ($partnerInfo as $partner) {
            $tran_num = 0;
            if (count($partner->branches) > 0) {
                foreach ($partner->branches as $branch) {
                    $tran_num = count($branch->transaction) + $tran_num;
                    $partner['total_tran_num'] = $tran_num;
                }
            } else {
                $partner['total_tran_num'] = $tran_num;
            }
            $i++;
        }
        $topPartners = json_decode(json_encode($partnerInfo), true);
        $array_column = array_column($topPartners, 'total_tran_num');
        array_multisort($array_column, SORT_DESC, $topPartners);

        $top4Partners = [];
        $loop_size = 0;
        if (count($topPartners) > 6) {
            $loop_size = 6;
        } else {
            $loop_size = count($topPartners);
        }
        for ($i = 0; $i < $loop_size; $i++) {
            $top4Partners[$i]['partner_profile_image'] = $topPartners[$i]['profile_image']['partner_profile_image'];
            $top4Partners[$i]['partner_cover_photo'] = $topPartners[$i]['profile_image']['partner_cover_photo'];
            $top4Partners[$i]['partner_account_id'] = $topPartners[$i]['partner_account_id'];
            $top4Partners[$i]['partner_name'] = $topPartners[$i]['partner_name'];
            $top4Partners[$i]['partner_category'] = $topPartners[$i]['category']['name'];
            $top4Partners[$i]['average_rating'] = $topPartners[$i]['rating']['average_rating'];

            $branches = PartnerBranch::where('partner_account_id', $top4Partners[$i]['partner_account_id'])->where('active', 1)->with('offers')->get();
            $offers = 0;
            foreach ($branches as $branch) {
                foreach ($branch->offers as $offer) {
                    $offer_date = $offer['date_duration'][0];
                    try {
                        if (new DateTime($offer_date['from']) <= new DateTime($date) && new DateTime($offer_date['to']) >= new DateTime($date)
                            && $offer->active == 1) {
                            $offers += 1;
                        } else {
                            $offers += 0;
                        }
                    } catch (\Exception $e) {
                    }
                }
            }
            $top4Partners[$i]['offer_count'] = $offers;
            $top4Partners[$i]['offer_heading'] = (new functionController2())
                ->partnerOfferHeading($top4Partners[$i]['partner_account_id']);
            $gallery_image = PartnerGalleryImages::where('partner_account_id', $top4Partners[$i]['partner_account_id'])
                ->where('pinned', 1)->first();
            if (empty($gallery_image)) {
                $top4Partners[$i]['partner_gallery_image'] = PartnerGalleryImages::
                where('partner_account_id', $top4Partners[$i]['partner_account_id'])->first()->partner_gallery_image;
            } else {
                $top4Partners[$i]['partner_gallery_image'] = $gallery_image['partner_gallery_image'];
            }
            $top4Partners[$i]['location'] = (new functionController2())->getBranchLocations($branches);
        }

        return Response::json([
            'trending_offer' => $trending_collection,
            'top_brands' => $top_collection,
            'news_feed' => [],
            'top_partners' => $top4Partners,
            'banner_image' => $banner_image,
            'banner_image_for_guest_user' => $banner_image_for_guest_user,
            'banner_image_for_skip_user' => $banner_image_for_skip_user,
            'image_refer' => $image_refer,
            'image_all' => $image_all,
            'carousel_image' => $carousel_image,
            'images_card_holder' => $images_card_holder,
            'public_image_first' => $public_image_first,
            'public_image_refer' => $public_image_refer,
            'guest_image_first' => $guest_image_first,
            'guest_image_refer' => $guest_image_refer,
            'top_card_name' => 'New Partners',
        ], 200);
    }

    public function partnerAccountInfo()
    {
        $partner_account_id = Input::get('partner_account_id');
        $partner_data = DB::table('partner_info as pi')
            ->join('partner_profile_images as ppi', 'pi.partner_account_id', 'ppi.partner_account_id')
            ->select('pi.partner_account_id', 'pi.partner_name', 'ppi.partner_profile_image', 'pi.partner_email',
                'pi.partner_mobile', 'pi.partner_address', 'pi.partner_location', 'pi.expiry_date')
            ->where('pi.partner_account_id', $partner_account_id)
            ->get();
        $array = get_object_vars($partner_data[0]);

        /* $name = $array['partner_name'];
         $partner_image = DB::table('partner_profile_images')
             ->select('partner_profile_image')
             ->where('partner_account_id', $partner_info['partner_account_id'])
             ->get();
         $partner_image = json_decode(json_encode($partner_image), true);
         $partner_image = $partner_image[0];*/

        //get discount number of the specific partner
        $discount = DB::table('discount')
            ->where('partner_account_id', $partner_account_id)
            ->get();
        $discount = json_decode(json_encode($discount), true);

        //check partner validity
        $curDate = date('Y-m-d');
        $exp_date = $array['expiry_date'];
        $cur_date = new DateTime($curDate);
        $exp_date = new DateTime($exp_date);
        $interval = $cur_date->diff($exp_date);
        $monthRemaining = $interval->format('%R%m');
        $daysRemaining = $interval->format('%R%d');

        //get all unseen notification of this partner
        $unseenNotifications = (new functionController)->partnerUnseenNotifications($partner_account_id);

        //get all seen notification of this partner
        $seenNotifications = (new functionController)->partnerSeenNotifications($partner_account_id);

//           $allPosts = (new functionController)->allPosts($partner_info['partner_account_id']);

        //send all data to the respective account page
        return Response::json(['type' => 'partner', 'partner_info' => $array, 'discount' => $discount, /*'customer_number' => $customer_number, "totalReviews" =>$totalReviews,'card_used' => $card_used,*/
            'monthRemaining' => $monthRemaining,
            'daysRemaining' => $daysRemaining, 'unseenNotifications' => $unseenNotifications, 'seenNotifications' => $seenNotifications, ], 200);
    }

    public function getPartnerReviews()
    {
        $partner_account_id = Input::get('partner_account_id');
        //all followers info of the partner
        $reviews = (new functionController)->partnerAllReviews($partner_account_id);

        return Response::json(['reviews' => $reviews], 200);
    }

    public function getPartnerFollowers()
    {
        $partner_account_id = Input::get('partner_account_id');
        //all followers info of the partner
        $followers_info = (new functionController)->followerListOfPartner($partner_account_id);

        return Response::json(['followers_info' => $followers_info], 200);
    }

    public function getPartnerTransactionHistory()
    {
        $partner_account_id = Input::get('partner_account_id');
        $transactionHistory = $this->partnerTransaction($partner_account_id);
        $amount_sum = DB::table('transaction_table')
            ->where('partner_account_id', $partner_account_id)
            ->sum('amount_spent');
        $discount_sum = DB::table('transaction_table')
            ->where('partner_account_id', $partner_account_id)
            ->sum('discount_amount');

        return Response::json(['transactionHistory' => $transactionHistory, 'amount_sum' => $amount_sum, 'discount_sum' => $discount_sum], 200);
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

            $topReviewers[$i]['customer_first_name'] = $userInfo[0]['customer_first_name'];
            $topReviewers[$i]['customer_last_name'] = $userInfo[0]['customer_last_name'];
            $topReviewers[$i]['customer_profile_image'] = $userInfo[0]['customer_profile_image'];
            $topReviewers[$i]['customer_username'] = $userInfo[0]['customer_username'];
            $i++;
        }

        return $topReviewers;
    }

    //function to get top users who spent most to a specific partner
    public function topUsersInTransaction($partnerID)
    {
        $topUsers = DB::table('transaction_table')
            ->select('customer_id', DB::raw('SUM(amount_spent) as total_amount'))
            ->where('partner_account_id', $partnerID)
            ->groupBy('customer_id')
            ->get();
        $topUsers = json_decode(json_encode($topUsers), true);
        //sorting this array in DESC order according to total spent amount
        array_multisort(array_column($topUsers, 'total_amount'), SORT_DESC, $topUsers);
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
            $topUsers[$i]['customer_first_name'] = $userInfo[0]['customer_first_name'];
            $topUsers[$i]['customer_last_name'] = $userInfo[0]['customer_last_name'];
            $topUsers[$i]['customer_profile_image'] = $userInfo[0]['customer_profile_image'];
            $topUsers[$i]['customer_username'] = $userInfo[0]['customer_username'];
            $i++;
        }

        return $topUsers;
    }

    //function to get transaction history of a partner
    public function partnerTransaction($partnerID)
    {
        $transactions = DB::table('transaction_table as tt')
            ->leftJoin('customer_info as ci', 'ci.customer_id', '=', 'tt.customer_id')
            ->leftJoin('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
            ->leftJoin('all_coupons as ac', function ($join) {
                $join->on('ac.partner_account_id', '=', 'tt.partner_account_id')
                    ->on('ac.id', '=', 'br.coupon_id');
            })
            ->select('tt.customer_id', 'tt.amount_spent', 'tt.posted_on', 'tt.discount_amount',
                'ci.customer_full_name', 'ci.customer_profile_image', 'ca.customer_username', 'ac.coupon_type', 'ac.reward_text')
            ->where('tt.partner_account_id', $partnerID)
            ->orderBy('tt.posted_on', 'DESC')
            ->get();
        $transactions = json_decode(json_encode($transactions), true);
        //total amount that all users spent on this partner
//        $amount_sum = DB::table('transaction_table')
//            ->where('partner_account_id', $partnerID)
//            ->sum('amount_spent');
        //total amount of discounts partner provided to its user
//        $discount_sum = DB::table('transaction_table')
//            ->where('partner_account_id', $partnerID)
//            ->sum('discount_amount');
        //$topTransaction = (new functionController)->partnerTopTransactions($partnerID);
//        $topUserInTransaction = $this->topUserInTransaction($partnerID);
        //$transactionHistory['transaction'] = $transactions;
        //$transactionHistory['top_transaction'] = $topTransaction;
        return $transactions;
    }

    //===================function for all offers========================
    public function allOffers($category)
    {
        $data = (new \App\Http\Controllers\FeaturedPartners\functionController())->getPartners($category);
        //pagination
        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // Create a new Laravel collection from the array data
        $itemCollection = collect($data);

        // Define how many items we want to be visible in each page
        $perPage = 10;

        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

        // Create our paginator and pass it to the view
        $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);

        $paginatedItems->setPath('json_offers/all');
        $paginatedItems->setArrayName('partners');

        //send all data to offerpage for "all offer" link
        return Response::json($paginatedItems, 200);
    }

    public function categoryWiseFacilities()
    {
        $category_id = intval(Input::get('category_id'));

        $facilities = \App\BranchFacility::whereRaw('JSON_CONTAINS(category_ids, ?)', [json_encode($category_id)])->get();

        return Response::json($facilities, 200);
    }

    //function to send activation code through sms
    public function activationSMS()
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $user = DB::table('customer_info')
            ->select('customer_id', 'customer_full_name', 'customer_contact_number', 'card_active', 'customer_type')
            ->where('customer_id', $customer_id)
            ->get();
        $user = json_decode(json_encode($user), true);

        if (count($user) > 0) {
            $user = $user[0];
            $active = $user['card_active'];
            $phone = $user['customer_contact_number'];
            $customer_type = $user['customer_type'];
            if ($active == 2) {
                return Response::json(['result' => "You're card is already activated"], 201);
            } elseif ($customer_type > 2) {
                return Response::json(['result' => "Sorry, You're not a cardholder."], 201);
            } else {
                $code = '';
                $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
                $codeAlphabet .= '0123456789';
                $max = strlen($codeAlphabet); // edited
                for ($i = 0; $i < 5; $i++) {
                    $code .= $codeAlphabet[random_int(0, $max - 1)];
                }

                if ($code != null) {
                    //save code to the DB
                    DB::table('customer_info')
                        ->where('customer_id', $user['customer_id'])
                        ->update([
                            'card_activation_code' => $code,
                        ]);
                    //send password via SMS
                    $text = 'Hello '.$user['customer_full_name'].','."\r\n";
                    $text .= 'This is your card activation code,'."\r\n"."\r\n";
                    $text .= 'Code : '.$code;
                    $user = 'Royaltybd';
                    $pass = '66A6Q13d';
                    $sid = 'RoyaltybdMasking';
                    $url = 'http://sms.sslwireless.com/pushapi/dynamic/server.php';
                    $param = "user=$user&pass=$pass&sms[$i][0]= $phone &sms[$i][1]=".urlencode($text)."&sms[$i][2]=123456789&sid=$sid";
                    $crl = curl_init();
                    curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
                    curl_setopt($crl, CURLOPT_URL, $url);
                    curl_setopt($crl, CURLOPT_HEADER, 0);
                    curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($crl, CURLOPT_POST, 1);
                    curl_setopt($crl, CURLOPT_POSTFIELDS, $param);
                    $response = curl_exec($crl);
                    curl_close($crl);
                    //code successfully sent
                    return Response::json(['result' => "Please check your phone's inbox to get the activation code."], 200);
                }
            }
        } else {
            return Response::json(['result' => "This card number doesn't exist."], 201);
        }
    }

    //function to activate card with the provided code of customer
    public function activateCard()
    {
        $code = Input::get('code');
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $provided_code = DB::table('customer_info')
            ->select('card_activation_code')
            ->where('customer_id', $customer_id)
            ->get();
        $info = CustomerInfo::where('customer_id', $customer_id)->first();
        $provided_code = json_decode(json_encode($provided_code), true);
        $provided_code = $provided_code[0]['card_activation_code'];
        if ($code == $provided_code) {
            //get date after months
            $date = date_create(date('Y-m-d'));
            $expiry_date = date_add($date, date_interval_create_from_date_string($info->month.' month'));
            $expiry_date = $expiry_date->format('Y-m-d');
            //update 3 columns if code matches
            DB::table('customer_info')
                ->where('customer_id', $customer_id)
                ->update([
                    'expiry_date' => $expiry_date,
                    'card_active' => 2,
                    'card_activation_code' => 0,
                ]);

            return Response::json(['result' => 'Your card is successfully activated. Thanks for being with us.'], 200);
        } else {
            return Response::json(['result' => 'Please enter the code correctly.'], 201);
        }
    }

    //function for customer profile
    public function userProfile()
    {
        //logged in userId
        $customerID = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $username = CustomerAccount::where('customer_id', $customerID)->first()->customer_username;

        //get id from user's username
        $customer = DB::table('customer_account')
            ->select('*')
            ->where('customer_username', $username)
            ->first();
        if ($customer) {
            //get info of user from id
            $customer_data = DB::table('customer_info as ci')
                ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
                ->join('user_type as ut', 'ut.id', '=', 'ci.customer_type')
                ->select('ci.*', 'ut.type', 'ca.customer_username')
                ->where('ci.customer_id', $customerID)
                ->first();
            $customer_data = json_decode(json_encode($customer_data), true);
            $curDate = date('m-d');
            $birthday = date('m-d', strtotime($customer_data['customer_dob']));
            if ($birthday === $curDate) {
                $customer_data['birthday'] = 1;
            } else {
                $customer_data['birthday'] = 0;
            }

            //email id from subscribers table
            $email = DB::table('subscribers')
                ->select('email')
                ->where('email', $customer_data['customer_email'])
                ->first();
            $customer_data['subscribed'] = $email->email ?? null;

            //get total review number of the customer
            $review_number = DB::table('review')
                ->where('customer_id', $customer_data['customer_id'])
                ->count();
            //total partner number customer visited
            $partner_number = (new functionController)->totalPartnersCustomerVisited($customer_data['customer_id']);

            //total number of card used
            $card_used = DB::table('transaction_table')
                ->where('customer_id', $customer_data['customer_id'])
                ->count();

            //check customers validity
            $curDate = date('Y-m-d');
            $exp_date = $customer_data['expiry_date'];

            $cur_date = new DateTime($curDate);
            $expiry_date = new DateTime($exp_date);
            $interval = date_diff($cur_date, $expiry_date);
            $daysRemaining = $interval->format('%R%a');

            //following list
            $following_list = (new functionController)->customerFollowingList($customer_data['customer_id']);
            //follower list
            $follower_list = (new functionController)->userFollowerList($customer_data['customer_id']);
            //get all requested coupon of the customer which are not used or expired

            //get all reviews of this customer
            $reviews = (new functionController)->customerAllReviews($customer_data['customer_id']);

            //get transaction history of this customer
            $transactionHistory = (new functionController)->customerTransaction($customer_data['customer_id'])['transactions'];
            $transactionHistory = json_decode(json_encode($transactionHistory), true);
            $transactionHistory = array_values($transactionHistory);
            $fin_transactions['transactions'] = $transactionHistory;
            //get top 5 transaction if this user
            $topTransactions = (new functionController)->customerTopTransactions($customer_data['customer_id']);

            //customer visited partner
            $visitedList = $this->totalPartnersCustomerVisited($customer_data['customer_id']);
            //get follow request
            $follow_request = $this->followRequested($customerID, $customer_data['customer_id']);

            //check if already applied for cod
            $info_at_buy_card = DB::table('info_at_buy_card')
                ->where('customer_username', $username)
                ->where('delivery_type', 4)
                ->get();
            if (count($info_at_buy_card) > 0) {
                $customer_data['applied_cod'] = 1;
            } else {
                $customer_data['applied_cod'] = 0;
            }
            $card_delivery = CardDelivery::where('customer_id', $customerID)->orderBy('id', 'DESC')->first();
            $customer_data['card_delivery'] = $card_delivery;
            $customer_social = SocialId::where('customer_id', $customerID)->get();
            $customer_data['social'] = $customer_social;

            $customer_data['customer_point'] = (new \App\Http\Controllers\Reward\functionController())->getTotalPoints($customerID);

            //affiliation count
            $customer_data['affiliation_count'] = null;
            $affiliation = CardPromoCodes::where('influencer_id', $customerID)->with('promoUsage')->first();
            if ($affiliation) {
                $customer_data['affiliation_count'] = count($affiliation->promoUsage);
                $customer_data['affiliation_code'] = $affiliation->code;
            }
            //send all data to profile page
            return Response::json(['type' => 'customer', 'customer_data' => $customer_data, 'reviews' => $reviews,
                'review_number' => $review_number, 'partner_number' => $partner_number, 'card_used' => $card_used, 'daysRemaining' => $daysRemaining, 'following_list' => $following_list, 'follower_list' => $follower_list, 'transactionHistory' => $fin_transactions, 'topTransactions' => $topTransactions, 'visitedList' => $visitedList, 'follow_request' => $follow_request, ], 200);
        } else {
            $error = 'User does not exist.';

            return Response::json(['error' => $error], 201);
        }
    }

    //function to get transaction history of customer
    public function customerTransactionSort()
    {
        $customerID = Input::get('customer_id');
        $month = Input::get('month');
        $year = Input::get('year');
        //get transaction history of a customer
        $transactions = DB::table('transaction_table as tt')
            ->join('partner_branch as pb', 'pb.id', '=', 'tt.branch_id')
            ->leftJoin('partner_info as pi', 'pi.partner_account_id', '=', 'pb.partner_account_id')
            ->leftJoin('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pb.partner_account_id')
            ->leftJoin('all_coupons as ac', function ($join) {
                $join->on('ac.branch_id', '=', 'pb.id')
                    ->on('ac.id', '=', 'br.coupon_id');
            })
            ->select('tt.amount_spent', 'tt.posted_on', 'tt.discount_amount', 'pb.partner_account_id', 'ac.coupon_type',
                'ac.reward_text', 'pi.partner_name', 'pb.partner_area', 'pb.partner_address', 'ppi.partner_profile_image', 'pb.id')
            ->where('tt.customer_id', $customerID)
            ->orderBy('tt.posted_on', 'DESC')
            ->get();
        $transactions = json_decode(json_encode($transactions), true);
        foreach ($transactions as $key => $value) {
            $ex = explode('-', $value['posted_on']);
            //checking if DB=>"month,year" & selected=>"month,year" are same or not
            if ($ex[0] != $year || $ex[1] != $month) {
                //unset specific array index if not match
                unset($transactions[$key]);
            }
        }
        $transactions = array_values($transactions);
        //total spent amount of this customer
        $amount_sum = DB::table('transaction_table')->where('customer_id', $customerID)->where('posted_on', 'like', $year.'-'.$month.'%')->sum('amount_spent');
        //total discount a customer got
        $discount_sum = DB::table('transaction_table')->where('customer_id', $customerID)->where('posted_on', 'like', $year.'-'.$month.'%')->sum('discount_amount');
        //create an array with all transaction info
        $transactionHistory['transaction'] = $transactions;
        //$transactionHistory['branch_name'] = $partnerName;
        $transactionHistory['total_spent'] = $amount_sum;
        $transactionHistory['total_discount'] = $discount_sum;
        //return the result
        return $transactionHistory;
    }

    //function to get all follow requests of a user
    public function followRequested($checkCustomerId, $customerId)
    {
        $customerInfo = DB::table('customer_account as ca')
            ->join('customer_info as ci', 'ci.customer_id', '=', 'ca.customer_id')
            ->join('follow_customer as fc', 'fc.follower', '=', 'ci.customer_id')
            ->join('user_type as ut', 'ut.id', '=', 'ci.customer_type')
            ->select('fc.follow_request')
            ->where('fc.following', $customerId)
            ->where('fc.follower', $checkCustomerId)
            ->get();
        $customerInfo = json_decode(json_encode($customerInfo), true);

        return $customerInfo;
    }

    //function for unsubscribe
    public function unsubscribe()
    {
        $email = Input::get('email');
        DB::table('subscribers')
            ->where('email', $email)
            ->delete();

        return Response::json(['result' => 'unsubscribe']);
    }

    //function to re subscribe
    public function subscribe()
    {
        $email = Input::get('email');
        DB::table('subscribers')->insert(
            ['email' => $email]
        );

        return Response::json(['result' => 'subscribe']);
    }

    //function to save user's wish to database
    public function makeWish()
    {
        $comment = Input::get('comment');
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $wish = new Wish([
            'customer_id' => $customer_id,
            'comment' => $comment,
            'posted_on' => date('Y-m-d H:i:s'),
        ]);
        $wish->save();
        (new \App\Http\Controllers\AdminNotification\functionController())->userWishNotification($wish);

        return Response::json(['result' => 'We got your feedback. Stay connected.']);
    }

    //function for follow partner option
    public function followPartner()
    {
        $id = Input::get('id');
        $customer_id = Input::get('customer_id');
        $customer_profile_image = Input::get('customer_profile_image');
        $text = 'started following you';
        //check if this user already following this partner or not
        $alreadyFollowing = DB::table('follow_customer')
            ->where('follower', $customer_id)
            ->where('following', $id)
            ->count();
        if ($alreadyFollowing > 0) {
            //do nothing
            return Response::json($id);
        } else {
            DB::table('follow_partner')->insert(
                [
                    'follower' => $customer_id,
                    'following' => $id,
                ]
            );
            //get last inserted id of follow_partner table
            $last_inserted_id = DB::table('follow_partner')
                ->select('id')
                ->orderBy('id', 'DESC')
                ->take(1)
                ->get();
            $last_inserted_id = json_decode(json_encode($last_inserted_id), true);
            $follow_id = $last_inserted_id[0]['id'];
            //insert info into notification table
            DB::table('partner_notification')->insert(
                [
                    'partner_account_id' => $id,
                    'image_link' => $customer_profile_image,
                    'notification_text' => $text,
                    'notification_type' => 4,
                    'source_id' => $follow_id,
                    'seen' => 0,
                ]
            );

            //send notification to phone
            $receiver_info = DB::table('partner_info')
                ->select('*')
                ->where('partner_account_id', $id)
                ->first();

            $sender_info = DB::table('customer_info as ci')
                ->select('ci.customer_full_name')
                ->where('customer_id', $customer_id)
                ->get();
            $sender_info = json_decode(json_encode($sender_info), true);
            $name = $sender_info[0]['customer_full_name'];
            $message = $name.' '.$text;

            (new pusherController)->livePartnerFollowNotification($id);
//            $this->functionSendGlobalPushNotification($message, $receiver_info);

            return Response::json(['result' => $id]);
        }
    }

    //function for unfollow option of partner
    public function unfollowPartner()
    {
        $id = Input::get('id');
        $customer_id = Input::get('customer_id');
        //get specific id of follow partner table
        $follow_id = DB::table('follow_partner')
            ->select('id')
            ->where('follower', $customer_id)
            ->where('following', $id)
            ->first();
        //delete from partner notification table
        (new functionController)->deleteNotification($id, $follow_id->id, notificationType::partner_follow);

        //delete from follow partner table
        DB::table('follow_partner')
            ->where('follower', $customer_id)
            ->where('following', $id)
            ->delete();

        return Response::json(['result' => $id]);
    }

    //function for create review for user
    public function createReview(Request $request)
    {
        //get values from form
        $id = Input::get('partner_account_id');
        $star = Input::get('rate_star');
        $heading = Input::get('heading');
        $comment = Input::get('content');
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $transaction_id = Input::get('transaction_id');
        $review_type = Input::get('review_type');
        $platform = $request->header('platform', null);
        $partner = PartnerAccount::where('partner_account_id', $id)->first();

        if ($partner->active == 0) {
            return response()->json(['message' => GlobalTexts::deactivated_partner_no_review], 400);
        } else {
            if ($heading == 'n/a' && $comment == 'n/a' && $star > 2) {//publish review without moderation
                $review = (new \App\Http\Controllers\Review\functionController())
                    ->saveReview($id, $customer_id, $star, $heading, $platform, $comment, $transaction_id, false, $review_type);

                return response()->json((new \App\Http\Controllers\Review\functionController())
                    ->acceptReviewModeration($review->id, false));
            } else {//save review for moderation
                return response()->json((new \App\Http\Controllers\Review\functionController())
                    ->saveReview($id, $customer_id, $star, $heading, $platform, $comment, $transaction_id, true, $review_type));
            }
        }
    }

    public function singleReview()
    {
        $review_id = Input::get('review_id');
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $previous_like = 0;
        $previous_like_id = 0;
        $review_response = Review::where('id', $review_id)
            ->with('comments', 'likes', 'customer_info', 'partnerInfo.profileImage')
            ->first();

        $review_likes = $review_response->likes;
        foreach ($review_likes as $like) {
            if ($like->liker_id == $customer_id) {
                $previous_like = 1;
                $previous_like_id = $like->id;
                break;
            }
        }
        $review_response->previous_like = $previous_like;
        $review_response->previous_like_id = $previous_like_id;

        return Response::json($review_response);
    }

    public function customerReviews()
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $review_response = Review::where('customer_id', $customer_id)
            ->with('comments', 'likes', 'customer', 'partnerInfo.profileImage')
            ->get();

        foreach ($review_response as $review) {
            $previous_like = 0;
            $previous_like_id = 0;
            $review_likes = $review->likes;
            foreach ($review_likes as $like) {
                if ($like->liker_id == $customer_id) {
                    $previous_like = 1;
                    $previous_like_id = $like->id;
                    break;
                }
            }
            $review->previous_like = $previous_like;
            $review->previous_like_id = $previous_like_id;
        }

        return Response::json($review_response);
    }

    public function singleLikeReview()
    {
        $like_id = Input::get('like_id');
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $previous_like = 0;
        $previous_like_id = 0;
        $like_review_response = LikesReview::where('id', $like_id)
            ->with('review.comments', 'review.likes', 'review.customer_info', 'review.partnerInfo.profileImage')
            ->first();
        $review_response = $like_review_response->review;
        $review_likes = $review_response->likes;
        foreach ($review_likes as $like) {
            if ($like->liker_id == $customer_id) {
                $previous_like = 1;
                $previous_like_id = $like->id;
                break;
            }
        }
        $review_response->previous_like = $previous_like;
        $review_response->previous_like_id = $previous_like_id;

        return Response::json($review_response);
    }

    //function to do backend of review like
    public function like()
    {
        $review_id = Input::get('review_id');
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;

        return Response::json((new \App\Http\Controllers\Review\functionController())->likeReview($customer_id, LikerType::customer, $review_id));
    }

    public function unlike_review()
    {
        $like_id = Input::get('like_id');
        (new \App\Http\Controllers\Review\functionController())->unlikeReview($like_id);

        return Response::json(['result' => 'Unliked']);
    }

    //Delete Review
    public function deleteReview()
    {
        $review_id = Input::get('review_id');
        try {
            DB::beginTransaction(); //to do query rollback

            //delete review
            (new functionController)->deleteReview($review_id);

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return Response::json(['result' => 'Unable to delete'], 201);
        }

        return Response::json(['result' => 'Deleted'], 200);
    }

    //function for edit user profile info in user account
    public function editProfile()
    {
        $username = Input::get('username');
        $mobile = Input::get('mobile');
        $password = Input::get('password');
        $old_password = Input::get('old_password');
        $image_url = Input::get('image_url');
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;

        $old_customer_info = CustomerInfo::where('customer_id', $customer_id)->first();
        $old_customer_account = CustomerAccount::where('customer_id', $customer_id)->first();

        if ($password != null) {
            $decrypted_password = (new functionController)->encrypt_decrypt('decrypt', $old_customer_account->password);
            if ($decrypted_password == $old_password) {
                $encrypted_password = (new functionController)->encrypt_decrypt('encrypt', $password);
                //update password if exists
                DB::table('customer_account')
                    ->where('customer_id', $customer_id)
                    ->update([
                        'password' => $encrypted_password,
                    ]);
            } else {
                return Response::json(['result' => 'Please enter your old password correctly.'], 201);
            }
        }

        if ($this->notOwnUsername($username, $customer_id)) {
            return Response::json(['result' => 'Username Already Exists.'], 201);
        } else {
            DB::table('info_at_buy_card')
                ->where('customer_username', $old_customer_info->customer_username)
                ->update([
                    'customer_username' => $username,
                ]);

            DB::table('customer_account')
                ->where('customer_id', $customer_id)
                ->update([
                    'customer_username' => $username,
                ]);
        }

        if ($image_url != null) {
            try {
                (new functionController)->update_profile_image_link($image_url, $customer_id);
                //update customer info
                DB::table('customer_info')
                    ->where('customer_id', $customer_id)
                    ->update([
                        'customer_contact_number' => $mobile,
                    ]);

                return Response::json(['result' => 'Information updated successfully.']);
            } catch (QueryException $e) {
                return Response::json(['result' => 'Phone Number Already Exists.']);
            }
        } else {
            try {
                //update customer info
                DB::table('customer_info')
                    ->where('customer_id', $customer_id)
                    ->update([
                        'customer_contact_number' => $mobile,
                    ]);

                return Response::json(['result' => 'Information updated successfully.']);
            } catch (QueryException $e) {
                return Response::json(['result' => 'Phone Number Already Exists.']);
            }
        }
    }

    //function to send mail to reset Customer Username or Password
    public function sendMail()
    {
        $email = Input::get('reset_email');

        //check if user with this email exists or not
        $user = DB::table('customer_info as ci')
            ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
            ->select('ci.customer_id', 'ci.customer_full_name', 'ca.customer_username')
            ->where('ci.customer_email', $email)
            ->get();
        $user = json_decode(json_encode($user), true);

        if (count($user) > 0) {
            $user_id = $user[0]['customer_id'];
            $name = $user[0]['customer_full_name'];
            $username = $user[0]['customer_username'];
            //generate reset token
            $reset_token = sha1(mt_rand(1, 90000).$user_id);

            //generate reset table
            DB::table('reset_user')->insert(
                [
                    'customer_id' => $user_id,
                    'token' => $reset_token,
                ]
            );
            //send mail
            $to = $email;
            $subject = 'Forgot password or username';
            $message_text = 'Hello '.$name.','.'<br><br>';
            // $message_text .= 'Your Username is: ' . $username . "<br><br>";
            $message_text .= 'To reset your password please use the following link.'.'<br>'.'<br>';
            $message_text .= url('/reset/'.$reset_token.'<br>'.'<br>');
            $message_text .= 'Thank you'.'<br>'.'<br>';
            $message_text .= 'Royalty';

            //using zoho mail service
            $smtpAddress = 'smtp.zoho.com';
            $port = 465;
            $encryption = 'ssl';
            $yourEmail = 'support@royaltybd.com';
            $yourPassword = 'SUp963**';

            // Prepare transport
            $transport = new Swift_SmtpTransport($smtpAddress, $port, $encryption);
            $transport->setUsername($yourEmail);
            $transport->setPassword($yourPassword);
            $mailer = new Swift_Mailer($transport);

            $message = new Swift_Message($subject);
            $message->setFrom(['support@royaltybd.com' => 'Royalty']);
            $message->setTo([$to => $name]);
            // If you want plain text instead, remove the second paramter of setBody
            $message->setBody($message_text, 'text/html');

            if ($mailer->send($message)) {
                return Response::json(['result' => 'We have sent you an E-mail with your username along with a link to reset your password. This may take a minute and also don’t forget to check the spam folder.']);
            } else {
                return Response::json(['result' => 'Internal Server Error']);
            }
        } else {
            return Response::json(['result' => 'Email does not exist!']);
        }
    }

    public function ucfirstSentence($str)
    {
        $str = ucfirst(strtolower($str));
//        $str = preg_replace_callback('/([.!?])\s*(\w)/',
//            create_function('$matches', 'return strtoupper($matches[0]);'), $str);

        // @note create_function which was deprecated in PHP 7.2
        $str = preg_replace_callback('/([.!?])\s*(\w)/',
            function ($matches) {
                return strtoupper($matches[0]);
            }, $str);

        return $str;
    }

    //================search function for home search bar====================
    public function searchWebsite()
    {
        //get keyword from search bar
        $keyword = Input::get('search');
//        $partner_list  = DB::select("SELECT * FROM partner_info WHERE partner_name SOUNDS LIKE '%$keyword%' OR partner_name LIKE '%$keyword%'");
        //match partner's names with the keyword
        $partner_list_1 = DB::select("SELECT pi.partner_account_id, pi.partner_name, ppi.partner_profile_image
                                    FROM partner_info pi
                                    LEFT JOIN partner_profile_images ppi
                                    ON pi.partner_account_id = ppi.partner_account_id
                                    WHERE pi.partner_name SOUNDS LIKE '%$keyword%' OR pi.partner_name LIKE '%$keyword%'");
        $partner_list_1 = json_decode(json_encode($partner_list_1), true);

        //trying to match partner names with their pronunciation
        //get pronunciation of the keyword
        $pronunciation = soundex($keyword);
        //all partner's names
        $partnerData = DB::table('partner_info')->select('partner_name')->get();
        $partnerData = json_decode(json_encode($partnerData), true);
        $i = 0;
        foreach ($partnerData as $key => $name) {
            $partner_exploded_names[$i]['cut_name'] = [];
            $partner_exploded_names[$i]['full_name'] = [];
            $arr = explode(' ', $name['partner_name']);
            array_push($partner_exploded_names[$i]['cut_name'], $arr);
            array_push($partner_exploded_names[$i]['full_name'], $name['partner_name']);
            $i++;
        }
        //initialize final array of the searched partner name
        $partner_list_2 = [];
        foreach ($partner_exploded_names as $name) {
            $cut_name_var = $name['cut_name'];
            $full_name_var = $name['full_name'];

            foreach ($cut_name_var as $value) {
                foreach ($value as $matching) {
                    if (soundex($matching) == $pronunciation) {
                        array_push($partner_list_2, $full_name_var);
                    }
                }
            }
        }
        $i = 0;
        $partner_final_list = [];
        foreach ($partner_list_2 as $partner) {
            $partner_info = DB::table('partner_info as pi')
                ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                ->select('pi.partner_account_id', 'pi.partner_name', 'ppi.partner_profile_image')
                ->where('pi.partner_name', $partner[0])
                ->get();
            $partner_info = json_decode(json_encode($partner_info), true);
            $partner_info = $partner_info[0];
            array_push($partner_final_list, $partner_info);
            $i++;
        }

        return Response::json(['partner_final_list' => $partner_final_list]);
    }

    //function for live search suggestion
    public function autocomplete()
    {
        $key = Input::get('search');

        if ($key != '' || ! empty($key)) {
            $partnerData = (new \App\Http\Controllers\Search\functionController())->getSoundSearch($key);
//            //get customers name with keyword
//            $userData = DB::table('customer_info as ci')
//                ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
//                ->select('ci.customer_full_name as name', 'ca.customer_username as username', 'ci.customer_profile_image as image')
//                ->where('ci.customer_full_name', 'LIKE', '%' . $key . '%')
//                ->orWhereRaw('REPLACE (ci.customer_full_name," ","") LIKE "%' . str_replace(' ', '%', $key) . '%"')
//                ->get();
//
//            $merged = $partnerData->merge($userData);
//
//            $result = $merged->all();
            if (count($partnerData) < 1) {
                //trying to match partner names with their pronunciation
                //get pronunciation of the keyword
                $pronunciation = soundex($key);
                //all partner's names
                $partnerData = DB::table('partner_info')->select('partner_name')->get();
                $partnerData = json_decode(json_encode($partnerData), true);
                $i = 0;
                foreach ($partnerData as $key => $name) {
                    $partner_exploded_names[$i]['cut_name'] = [];
                    $partner_exploded_names[$i]['full_name'] = [];
                    $arr = explode(' ', $name['partner_name']);
                    array_push($partner_exploded_names[$i]['cut_name'], $arr);
                    array_push($partner_exploded_names[$i]['full_name'], $name['partner_name']);
                    $i++;
                }
                //initialize final array of the searched partner name
                $partner_list_2 = [];
                foreach ($partner_exploded_names as $name) {
                    $cut_name_var = $name['cut_name'];
                    $full_name_var = $name['full_name'];

                    foreach ($cut_name_var as $value) {
                        $i = 0;
                        foreach ($value as $matching) {
                            if (soundex($matching) == $pronunciation) {
                                array_push($partner_list_2, $full_name_var);
                            }
                            $i++;
                        }
                    }
                }
                $i = 0;
                $partner_final_list = [];
                foreach ($partner_list_2 as $partner) {
                    $partner_info = DB::table('partner_info as pi')
                        ->join('partner_account as pa', 'pa.partner_account_id', '=', 'pi.partner_account_id')
                        ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
                        ->join('partner_branch as pb', 'pb.partner_account_id', '=', 'pi.partner_account_id')
                        ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
                        ->select(
                            'pi.partner_account_id as id',
                            'pi.partner_name as name',
                            'ppi.partner_profile_image as image',
                            'pb.partner_area as partner_area',
                            'pb.id as branch_id'
                        )
                        ->where('pa.active', 1)
                        ->where('pi.partner_name', $partner[0])
                        ->get();
                    $partner_info = json_decode(json_encode($partner_info), true);
                    if ($partner_info) {
                        array_push($partner_final_list, $partner_info);
                    }
                    $i++;
                }
                if (count($partner_final_list) > 0) {
                    return Response::json(['data' => $partner_final_list[0]]);
                } else {
                    return Response::json(['data' => $partner_final_list]);
                }
            } else {
                return Response::json(['data' => $partnerData]);
            }
        }
    }

    public function customerNotifications()
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $unseenNotifications = (new functionController)->CustomerUnseenNotifications($customer_id);
        $seenNotifications = (new functionController)->customerSeenNotifications($customer_id);
//        for ($i = 0; $i < count($seenNotifications); $i++) {
//            $seenNotifications[$i]["posted_on"] = $this->restructureTimeStamp($seenNotifications[$i]["posted_on"]);
//        }
//        for ($i = 0; $i < count($unseenNotifications); $i++) {
//            $unseenNotifications[$i]["posted_on"] = $this->restructureTimeStamp($unseenNotifications[$i]["posted_on"]);
//        }
        return Response::json(['unseen' => $unseenNotifications, 'seen' => $seenNotifications]);
    }

    public function partnerNotifications()
    {
        $partner_account_id = Input::get('partner_account_id');
        $unseenNotifications = (new functionController)->partnerUnseenNotifications($partner_account_id);
        $seenNotifications = (new functionController)->partnerSeenNotifications($partner_account_id);
        for ($i = 0; $i < count($seenNotifications); $i++) {
            $seenNotifications[$i]['posted_on'] = $this->restructureTimeStamp($seenNotifications[$i]['posted_on']);
        }
        for ($i = 0; $i < count($unseenNotifications); $i++) {
            $unseenNotifications[$i]['posted_on'] = $this->restructureTimeStamp($unseenNotifications[$i]['posted_on']);
        }

        return Response::json(['unseen' => $unseenNotifications, 'seen' => $seenNotifications]);
    }

//    //function to get total number of partners, customer visited
//    public function totalPartnersCustomerVisited($customerID)
//    {
//        $visitedList = DB::table('transaction_table as tpi')
//            ->join('partner_branch as pb', 'pb.id', '=', 'tpi.branch_id')
//            ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pb.partner_account_id')
//            ->join('partner_info as pi', 'pi.partner_account_id', '=', 'pb.partner_account_id')
//            ->select('pb.partner_account_id', 'ppi.partner_profile_image', 'pi.partner_name')
//            ->where('customer_id', $customerID)
//            ->distinct('tpi.branch_id')
//            ->count('tpi.branch_id')
//            ->get();
//
//        return $visitedList;
//
//    }

    public function totalPartnersCustomerVisited($customer_id)
    {
        $visited_partners = DB::select("select branch_id,
       count(branch_id)                              as total_visit,
       concat(partner_name, ' (', partner_area, ')') as partner_name,
       pi.partner_account_id,
       partner_profile_image
        from transaction_table
         join partner_branch on branch_id = partner_branch.id
         join partner_info pi on partner_branch.partner_account_id = pi.partner_account_id
         join partner_profile_images ppi on partner_branch.partner_account_id = ppi.partner_account_id
        where customer_id = $customer_id and deleted_at is null
        group by branch_id, partner_name, pi.partner_account_id, partner_profile_image, partner_area");

        return $visited_partners;
    }

    //function to get newsfeed in customer account
    public function recentActivity()
    {
        $customerID = Input::get('customer_id');

        //news feed from following customers
        $newsFeedArr['customer'] = (new functionController)->recentActivity($customerID);
        //get all news feed in a single array to sort as posted on
        $allNewsFeed = [];

        if ($newsFeedArr['customer'] != null) {
            foreach ($newsFeedArr['customer'] as $news) {
                array_push($allNewsFeed, $news);
            }
        }

        //pagination
        // Get current page form url e.x. &page=1
        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        // Create a new Laravel collection from the array data
        $itemCollection = collect($newsFeedArr['customer']);

        // Define how many items we want to be visible in each page
        $perPage = 10;

        // Slice the collection to get the items to display in current page
        $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

        // Create our paginator and pass it to the view
        $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);

        $paginatedItems->setPath('');
        $paginatedItems->setArrayName('recent_activity');

        return Response::json($paginatedItems, 200);
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
        $result = [];
        if ($newsFeed) {
            //previous like
            $id_array = [];
            foreach ($newsFeed as $news) {
                array_push($id_array, $news['id']);
            }
            $previousLike = 0;
            if ($customerID) {
                $previousLike = DB::table('likes_post')
                    ->select('post_id')
                    ->whereIn('post_id', $id_array)
                    ->where('customer_id', $customerID)
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
                $total_likes = (new functionController)->total_likes_of_a_post($news['id']);
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

    //all partner post
    public function partnerPost($partnerAccountID)
    {
        $newsFeed = DB::table('partner_post as pp')
            ->join('partner_post_header as pph', 'pph.post_id', '=', 'pp.id')
            ->join('partner_info as pi', 'pi.partner_account_id', '=', 'pp.partner_account_id')
            ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
            ->select('pp.*', 'pph.*', 'pi.partner_account_id', 'pi.partner_name', 'pi.partner_category', 'ppi.partner_profile_image')
            ->where('pp.partner_account_id', $partnerAccountID)
            ->where('pp.moderate_status', 1)
            ->orderBy('pp.posted_on', 'DESC')
            ->get();
        $newsFeed = json_decode(json_encode($newsFeed), true);
        $result = [];
        if ($newsFeed) {
            //previous like
            $id_array = [];
            foreach ($newsFeed as $news) {
                array_push($id_array, $news['id']);
            }

            //total likes of each post
            $i = 0;
            foreach ($newsFeed as $news) {
                $total_likes = (new functionController)->total_likes_of_a_post($news['id']);
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

    //partner get the liked post for single notification
    public function singleLikedPost()
    {
        $notification_id = Input::get('notification_id');
        $id = Input::get('post_id');

        DB::table('partner_notification')
            ->where('id', $notification_id)
            ->update([
                'seen' => 1,
            ]);
        $single_post = DB::table('partner_post as pp')
            ->join('partner_post_header as pph', 'pph.post_id', '=', 'pp.id')
            ->select('pp.*', 'pph.*')
            ->where('pp.id', $id)
            ->get();
        $single_post = json_decode(json_encode($single_post), true);
        $single_post = $single_post[0];
        $total_likes = (new functionController)->total_likes_of_a_post($id);
        $single_post['total_likes'] = $total_likes;

        return Response::json($single_post);
    }

    //function to save select bonus coupon request of customer
    public function select_coupon()
    {
        $coupon_id = Input::get('coupon_id');
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        //get 10 digit random code
        $request_code = mt_rand(1000000000, mt_getrandmax());

        $coupon = DB::table('all_coupons')
            ->select('coupon_type')
            ->where('id', $coupon_id)
            ->get();
        $coupon = json_decode(json_encode($coupon), true);
        $coupon_type = $coupon[0]['coupon_type'];
        //coupon
        if ($coupon_type == 1) {
            //get date after 3 days
            $date = date_create(date('Y-m-d'));
            $expiry_date = date_add($date, date_interval_create_from_date_string('2 days'));
            $expiry_date = $expiry_date->format('Y-m-d');

            DB::table('bonus_request')->insert([
                'customer_id' => $customer_id,
                'coupon_id' => $coupon_id,
                'used' => 0,
                'request_code' => $request_code,
                'expiry_date' => $expiry_date,
            ]);

            //decremented coupon counter by 1
            DB::table('customer_reward')
                ->where('customer_id', $customer_id)
                ->decrement('coupon', 1);

            DB::table('all_coupons')
                ->where('id', $coupon_id)
                ->decrement('stock', 1);
        } //refer bonus
        elseif ($coupon_type == 2) {
            //get date after 3 days
            $date = date_create(date('Y-m-d'));
            $expiry_date = date_add($date, date_interval_create_from_date_string('7 days'));
            $expiry_date = $expiry_date->format('Y-m-d');

            DB::table('bonus_request')->insert([
                'customer_id' => $customer_id,
                'coupon_id' => $coupon_id,
                'used' => 0,
                'request_code' => $request_code,
                'expiry_date' => $expiry_date,
            ]);

            //decremented refer_bonus counter by 1
            DB::table('customer_reward')
                ->where('customer_id', $customer_id)
                ->decrement('bonus_counter', 1);
        } //birthday
        else {
            //get date after 3 days
            $date = date_create(date('Y-m-d'));
            $expiry_date = date_add($date, date_interval_create_from_date_string('2 days'));
            $expiry_date = $expiry_date->format('Y-m-d');

            DB::table('bonus_request')->insert([
                'customer_id' => $customer_id,
                'coupon_id' => $coupon_id,
                'used' => 0,
                'request_code' => $request_code,
                'expiry_date' => $expiry_date,
            ]);

            DB::table('birthday_wish')
                ->where('customer_id', $customer_id)
                ->update([
                    'used' => 1,
                ]);

            DB::table('all_coupons')
                ->where('id', $coupon_id)
                ->decrement('stock', 1);
        }

        //last inserted coupon request id
        $last_inserted_id = DB::table('bonus_request')
            ->select('req_id')
            ->orderBy('req_id', 'DESC')
            ->take(1)
            ->get();
        $last_inserted_id = json_decode(json_encode($last_inserted_id), true);
        $request_id = $last_inserted_id[0]['req_id'];
        /*   //the coupon request for response
           $requestedCoupon = DB::table('bonus_request as brq')
               ->join('all_coupons as acp', 'brq.partner_id', '=', 'acp.partner_account_id')
               ->select('acp.reward_text', 'brq.*')
               ->where('brq.req_id', $request_id)
               ->get();*/

//        return Response::json($requestedCoupon, 200);

        return Response::json(['result' => $coupon_id]);
    }

    public function partnerLocationList()
    {
        $date = date('d-m-Y');
        //get all partners images and name for offers page
        $profileImages = DB::table('partner_info as pi')
            ->join('partner_profile_images as ppi', 'pi.partner_account_id', '=', 'ppi.partner_account_id')
            ->join('partner_branch as pb', 'pb.partner_account_id', '=', 'pi.partner_account_id')
            ->join('partner_account as pa', 'pa.partner_account_id', '=', 'pi.partner_account_id')
            ->where('pa.active', 1)
            ->where('pb.active', 1)
            ->select('pi.partner_account_id', 'pi.partner_name', 'pb.id as branch_id', 'pb.longitude', 'pb.latitude', 'ppi.partner_profile_image')
            ->get();
        $i = 0;
        foreach ($profileImages as $profileImage) {
            $branch = PartnerBranch::where('id', $profileImage->branch_id)->with('offers')->first();
            $offers = 0;
            foreach ($branch->offers as $offer) {
                $offer_date = $offer['date_duration'][0];
                try {
                    if (new DateTime($offer_date['from']) <= new DateTime($date) && new DateTime($offer_date['to']) >= new DateTime($date)
                        && $offer->active == 1) {
                        $offers += 1;
                    } else {
                        $offers += 0;
                    }
                } catch (\Exception $e) {
                }
            }
            $profileImage->offer_count = $offers;
            $i++;
        }

        //send all data to offerpage for "map" link
        return Response::json($profileImages, 200);
    }

    //function to register a new customer
    public function imageUpload()
    {
        //check image url or to upload image to aws
        if (isset($_FILES['image']['name']) && $_FILES['image']['name'] != '') {
            $response['file_name'] = basename($_FILES['image']['name']);
            Storage::disk('s3')->put('dynamic-images/users/'.$response['file_name'], file_get_contents($_FILES['image']['tmp_name']), 'public');
            $image_url = Storage::disk('s3')->url('dynamic-images/users/'.$response['file_name']);
            $response['url'] = $image_url;
            try {
                $response['message'] = 'File uploaded successfully!';
            } catch (Exception $e) {
                // Exception occurred. Make error flag true
                $response['error'] = true;
                $response['message'] = $e->getMessage();
            }

            return Response::json(['result' => $image_url], 200);
        } else {
            return Response::json(['result' => 'Please select an image'], 400);
        }
    }

    //function to register a new customer
    public function registration()
    {
        $email = Input::get('email');
        $contact = Input::get('phone');
        $username = Input::get('username');

        if ($this->emailExist($email) || $this->partnerEmailExist($email)) {
            return Response::json(['result' => 'Email already exists'], 201);
        } elseif ($this->phoneNumberExist($contact) || $this->partnerPhoneNumberExist($contact)) {
            return Response::json(['result' => 'Phone number already exists'], 201);
        } elseif ($this->usernameExist($username)) {
            return Response::json(['result' => 'Username already exists'], 201);
        } else {
            $first = Input::get('first_name');
            $last = Input::get('last_name');
            $dob = Input::get('dob');
            $facebook_signUp_id = Input::get('fb_id');
            $google_signUp_id = Input::get('gmail_id');
            $password = Input::get('password');
            $image_url = Input::get('image_url');
            $customer_gender = Input::get('customer_gender');

            // make password encrypted
            $encrypted_password = (new functionController)->encrypt_decrypt('encrypt', $password);

            $customer_id = mt_rand(1000000000, mt_getrandmax());
            //$customer_id = random_int(1111111111, 9999999999);//generates 10 digit random number
            $customer_id_10 = $customer_id;

            //get previous serial number of previous user
            $id = DB::table('customer_account')
                ->select('customer_serial_id')
                ->take(1)
                ->orderBy('customer_serial_id', 'DESC')
                ->first();
            $id = json_decode(json_encode($id), true);
            //generate 6 digit ID for new user
            for ($i = $id['customer_serial_id'] + 1; $i <= 999999; $i++) {
                $customer_id_6 = sprintf('%06d', $i);
                break;
            }
            //save data in customer_account table
            DB::table('customer_account')->insert([
                'customer_id' => $customer_id_10.$customer_id_6,
                'customer_serial_id' => $customer_id_6,
                'customer_username' => $username,
                'password' => $encrypted_password,
                'moderator_status' => 2,
            ]);

            //generate referral number
            $token = '';
            $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $codeAlphabet .= 'abcdefghijklmnopqrstuvwxyz';
            $codeAlphabet .= '0123456789';
            $max = strlen($codeAlphabet); // edited
            for ($i = 0; $i < 5; $i++) {
                $token .= $codeAlphabet[random_int(0, $max - 1)];
            }

            //save data in customer_info table
            DB::table('customer_info')->insert([
                'customer_id' => $customer_id_10.$customer_id_6,
                'customer_first_name' => $first,
                'customer_last_name' => $last,
                'customer_full_name' => $first.' '.$last,
                'customer_email' => $email,
                'customer_gender' => $customer_gender,
                'customer_dob' => $dob,
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

            //increment usage of referrer if exists in database
//            if ($this->referUserExist($referrer)) {
//                DB::table('customer_info')
//                    ->where('referral_number', $referrer)
//                    ->increment('reference_used', 1);
//            }

            //store social sign up id and type in social_id table
            if ($facebook_signUp_id != null) {
                DB::table('social_id')->insert([
                    'customer_id' => $customer_id_10.$customer_id_6,
                    'customer_social_id' => $facebook_signUp_id,
                    'customer_social_type' => 'facebook',
                ]);
            } elseif ($google_signUp_id != null) {
                DB::table('social_id')->insert([
                    'customer_id' => $customer_id_10.$customer_id_6,
                    'customer_social_id' => $google_signUp_id,
                    'customer_social_type' => 'google',
                ]);
            }

            return Response::json(['result' => 'You have successfully created an account.'], 200);
        }
    }

    //check phone number
    public function phoneNumberExist($phone)
    {
        $exist = DB::table('customer_info')
            ->where('customer_contact_number', $phone)
            ->count();
        if ($exist > 0) {
            return true;
        } else {
            return false;
        }
    }

    //check partner phone number
    public function partnerPhoneNumberExist($phone)
    {
        $exist = DB::table('partner_branch')
            ->where('partner_mobile', $phone)
            ->count();
        if ($exist > 0) {
            return true;
        } else {
            return false;
        }
    }

    //check referenced
    public function referUserExist($referrer)
    {
        if ($referrer != '') {
            //check if refer number exists or not
            $reference = DB::table('customer_info')->where('referral_number', $referrer)->count();
            if ($reference == 1) {
                return true;
            } else {
                return false;
            }
        }
    }

    //check username
    public function usernameExist($username)
    {
        $exist = DB::table('customer_account')
            ->where('customer_username', $username)
            ->count();
        if ($exist > 0) {
            return true;
        } else {
            return false;
        }
    }

    //check own username
    public function notOwnUsername($username, $customer_id)
    {
        $exist = DB::table('customer_account')
            ->where('customer_username', $username)
            ->where('customer_id', '!=', $customer_id)
            ->count();
        if ($exist > 0) {
            return true;
        } else {
            return false;
        }
    }

    //check email
    public function emailExist($email)
    {
        $exist = DB::table('customer_info')
            ->where('customer_email', $email)
            ->count();
        if ($exist > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function firebaseExist($username)
    {
        $exist = DB::table('customer_account as ca')
            ->join('customer_info as ci', 'ci.customer_id', '=', 'ca.customer_id')
            ->where('ca.customer_username', $username)
            ->where('ci.firebase_token', '!=', '0')
            ->count();
        if ($exist > 0) {
            return true;
        } else {
            return false;
        }
    }

    //check partner email
    public function partnerEmailExist($email)
    {
        $exist = DB::table('partner_branch')
            ->where('partner_email', $email)
            ->count();
        if ($exist > 0) {
            return true;
        } else {
            return false;
        }
    }

    //function for follow customer option
    public function followCustomer()
    {
        $following_id = Input::get('following_id');
        $follower_id = Input::get('follower_id');
        $follower_pic = Input::get('follower_pic');
        DB::table('follow_customer')->insert(
            [
                'follower' => $follower_id,
                'following' => $following_id,
            ]
        );
        //get last inserted id of follow_customer table
        $last_inserted_id = DB::table('follow_customer')
            ->select('id')
            ->orderBy('id', 'DESC')
            ->take(1)
            ->get();
        $last_inserted_id = json_decode(json_encode($last_inserted_id), true);
        $follow_id = $last_inserted_id[0]['id'];

        DB::table('customer_notification')->insert(
            [
                'user_id' => $following_id,
                'image_link' => $follower_pic,
                'notification_text' => 'wants to follow you',
                'notification_type' => 8,
                'source_id' => $follow_id,
                'seen' => 0,
            ]
        );

        //send notification to phone
        $sender_info = DB::table('customer_info')
            ->select('*')
            ->where('customer_id', $follower_id)
            ->get();
        $sender_info = json_decode(json_encode($sender_info), true);
        $name = $sender_info[0]['customer_full_name'];
        $message = $name.' wants to follow you';

        $receiver_info = DB::table('customer_info')
            ->select('*')
            ->where('customer_id', $following_id)
            ->first();

        (new pusherController)->liveCustomerFollowNotification($following_id);
        $this->functionSendGlobalPushNotification($message, $receiver_info);

        return Response::json(['result' => $following_id], 200);
    }

    //function to cancel follow customer request option
    public function cancelFollowRequest()
    {
        $following_id = Input::get('following_id');
        $follower_id = Input::get('follower_id');
        //get specific id of follow partner table
        $follow_id = DB::table('follow_customer')
            ->select('id')
            ->where('follower', $follower_id)
            ->where('following', $following_id)
            ->first();
        //delete from customer notification table
        DB::table('customer_notification')
            ->where('user_id', $following_id)
            ->where('notification_type', 8)
            ->where('source_id', $follow_id->id)
            ->delete();
        //delete from follow customer table
        DB::table('follow_customer')
            ->where('follower', $follower_id)
            ->where('following', $following_id)
            ->delete();

        return Response::json(['result' => $following_id], 200);
    }

    //function for unfollow option of user
    public function unfollowCustomer()
    {
        $following_id = Input::get('following_id');
        $follower_id = Input::get('follower_id');
        //get specific id of follow customer table
        $follow_id = DB::table('follow_customer')
            ->select('id')
            ->where('follower', $follower_id)
            ->where('following', $following_id)
            ->first();
        //delete from follow customer table
        DB::table('follow_customer')
            ->where('follower', $follower_id)
            ->where('following', $following_id)
            ->delete();
        //delete from customer notification table
        (new functionController)->deleteNotification($following_id, $follow_id->id, notificationType::customer_follow);

        //delete from customer notification table
        (new functionController)->deleteNotification($follower_id, $follow_id->id, notificationType::follow_accept);

        return Response::json(['result' => $following_id], 200);
    }

    //function to make like notification seen
    public function partnerLikedNotification()
    {
        $notification_id = Input::get('notification_id');
        $review_id = Input::get('review_id');

        DB::table('partner_notification')
            ->where('id', $notification_id)
            ->update([
                'seen' => 1,
            ]);

        $singleReviewDetails = (new homeController)->singleReviewDetails($review_id);

        return Response::json($singleReviewDetails);
    }

    //seen  notification
    public function seenNotification()
    {
        $notification_id = Input::get('notification_id');
        //make notification seen
        DB::table('customer_notification')
            ->where('id', $notification_id)
            ->update([
                'seen' => 1,
            ]);

        return Response::json(['result' => $notification_id], 200);
    }

    //seen  notification
    public function seenPartnerNotification()
    {
        $notification_id = Input::get('notification_id');
        //make notification seen
        DB::table('partner_notification')
            ->where('id', $notification_id)
            ->update([
                'seen' => 1,
            ]);

        return Response::json(['result' => $notification_id], 200);
    }

    //function to make acceptFollowRequestNotification seen & other works
    public function acceptFollowRequestNotification()
    {
        $notification_id = Input::get('notification_id');
        $source_id = Input::get('source_id');
        $customer_id = Input::get('customer_id');
        //make notification seen
        DB::table('customer_notification')
            ->where('id', $notification_id)
            ->update([
                'seen' => 1,
            ]);
        //get username from source id
        $follower = DB::table('customer_account as ca')
            ->join('follow_customer as fc', 'fc.follower', 'ca.customer_id')
            ->select('ca.customer_username', 'ca.customer_id')
            ->where('fc.id', $source_id)
            ->first();
        $follower_id = $follower->customer_id;
        $follower_name = $follower->customer_username;

        //update follow_request in follow_customer table
        DB::table('follow_customer')
            ->where('follower', $follower_id)
            ->where('following', $customer_id)
            ->update([
                'follow_request' => 1,
            ]);

        //get profile image from customer id
        $profile_image = DB::table('customer_info')
            ->select('customer_profile_image')
            ->where('customer_id', $customer_id)
            ->get();
        $profile_image = json_decode(json_encode($profile_image), true);
        $profile_image = $profile_image[0];

        $follow_id = DB::table('follow_customer')
            ->select('id')
            ->where('follower', $follower_id)
            ->where('following', $customer_id)
            ->get();
        $follow_id = json_decode(json_encode($follow_id), true);
        $follow_id = $follow_id[0];
        $profile_pic_link = $profile_image['customer_profile_image'];

        //insert into partner notification table
        DB::table('customer_notification')->insert([
            'user_id' => $follower_id,
            'image_link' => $profile_pic_link,
            'notification_text' => 'accepted your follow request',
            'notification_type' => 9,
            'source_id' => $follow_id['id'],
            'seen' => 0,
        ]);

        //send notification to phone
        $sender_info = DB::table('customer_info')
            ->select('*')
            ->where('customer_id', $customer_id)
            ->get();
        $sender_info = json_decode(json_encode($sender_info), true);
        $name = $sender_info[0]['customer_full_name'];
        $message = $name.' accepted your follow request';

        $receiver_info = DB::table('customer_info')
            ->select('*')
            ->where('customer_id', $follower_id)
            ->first();

        (new pusherController)->liveFollowRequestAcceptNotification($follower_id);
        $this->functionSendGlobalPushNotification($message, $receiver_info);

        return Response::json(['result' => $follower_name], 200);
    }

    //function to ignore follow request
    public function ignoreFollowRequest()
    {
        $source_id = Input::get('source_id');
        $customer_id = Input::get('customer_id');

        //get username from source id
        $follower = DB::table('customer_account as ca')
            ->join('follow_customer as fc', 'fc.follower', 'ca.customer_id')
            ->select('ca.customer_username', 'ca.customer_id')
            ->where('fc.id', $source_id)
            ->first();
        $follower_id = $follower->customer_id;
        $follower_name = $follower->customer_username;

        //delete follow request from follow_customer table
        DB::table('follow_customer')
            ->where('follower', $follower_id)
            ->where('following', $customer_id)
            ->where('follow_request', 0)
            ->delete();

        //delete follow notification from customer-notification table
        DB::table('customer_notification')
            ->where('user_id', $customer_id)
            ->where('notification_type', 8)
            ->where('source_id', $source_id)
            ->delete();

        return Response::json(['result' => $follower_name], 200);
    }

    //function to make birthday notification seen
    public function birthdayNotification()
    {
        $notification_id = Input::get('notification_id');
        $customer_id = Input::get('customer_id');
        $today = date('Y-m-d');
        //make notification seen
        DB::table('customer_notification')
            ->where('id', $notification_id)
            ->update([
                'seen' => 1,
            ]);
        //check if the birthday gift is invalid or not
        $birthday = DB::table('birthday_wish')
            ->where('customer_id', $customer_id)
            ->where('used', 0)
            ->where('expiry_date', '>=', $today)
            ->count();
        if ($birthday == 1) {
            //gift is not expired or used
            $coupons = DB::table('all_coupons as ac')
                ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'ac.partner_account_id')
                ->join('partner_info as pi', 'pi.partner_account_id', '=', 'ac.partner_account_id')
                ->select('ac.*', 'ppi.partner_profile_image', 'pi.partner_name')
                ->where('ac.coupon_type', 3)
                ->get();

            $coupons = json_decode(json_encode($coupons), true);

            return Response::json(['coupons' => $coupons], 200);
        } else {//gift is expired or used
            return Response::json(['result' => 'Sorry, you have already availed this gift or the coupon is expired!'], 201);
        }
    }

    public function checkSocialLogin()
    {
        $social_id = Input::get('social_id');
        $social_count = DB::table('social_id')
            ->where('customer_social_id', $social_id)
            ->count();
        if ($social_count > 0) {
            $social = DB::table('social_id as si')
                ->select('si.*')
                ->where('si.customer_social_id', $social_id)
                ->get()
                ->first();
            $result = $this->socialLogin($social->customer_id);

            return Response::json($result->original);
        } else {
            return Response::json(['result' => 'Redirect Login'], 201);
        }
    }

    public function reviewUrl()
    {
        $id = Input::get('id');
        $enc_review_id = (new functionController)->socialShareEncryption('encrypt', $id);

        return Response::json(['result' => 'review-share/'.$enc_review_id], 200);
    }

    public function restructureTimeStamp($timestamp)
    {
        $posted_on = date('Y-m-d H:i:s', strtotime($timestamp));

        return $posted_on;
    }

    public function saveFirebaseToken()
    {
        //update cusotmer firebase token
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $firebase_token = Input::get('firebase_token');
        DB::table('customer_info')
            ->where('customer_id', $customer_id)
            ->update(
                [
                    'firebase_token' => $firebase_token,
                ]
            );
    }

    public function getReferBonusPartners()
    {
        $result = collect();
        $coupons = AllCoupons::with('branch.info.profileImage', 'branch.account')->get();
        foreach ($coupons as $key => $value) {
            if ($value->branch->active == 1 && $value->branch->account->active == 1) {
                $result->push($coupons[$key]);
            }
        }

        return Response::json($result);
    }

    //function to get nearby partners for partner profile
    public function nearbyPartners($name, $area, $category, $lat, $long)
    {
        $nearbyPartners = DB::table('partner_info as pi')
            ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
            ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
            ->join('categories as c', 'c.id', '=', 'pi.partner_category')
            ->select('pi.partner_account_id', 'pi.partner_name', 'pi.longitude', 'pi.latitude', 'pi.partner_category', 'c.type as category_type', 'rat.average_rating', 'ppi.partner_profile_image')
            ->where('pi.partner_area', $area)
            ->where('pi.partner_category', $category)
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
            $distance = (new functionController)->calculateDistance($lat, $long, $nearbyPartner['latitude'], $nearbyPartner['longitude'], 'K');
            $nearbyPartners[$i]['distance'] = $distance;

            $max_dis = DB::table('discount')
                ->where('partner_account_id', $nearbyPartner['partner_account_id'])
                ->max('discount_percentage');
            $nearbyPartners[$i]['max_discount'] = $max_dis;

            $i++;
        }

        return $nearbyPartners;
    }

    public function getPrices()
    {
        //all partners
        $prices = DB::table('all_amounts')
            ->select('*')
            ->get();

        return Response::json(['prices' => $prices], 200);
    }

    public function getFirebaseAPIkey()
    {
        return 'AAAAUQgCAbA:APA91bFhCb5O-stybgVpjOC0pe1qTVMkruRyW2E9oqUv2Sc22eA2cn1sCqLYaRiY2C94E8Ewq6dE8CJ56uC9tDUVoDuhrJEK1LpL3cwxJU0xewTbI2f4zA6pHlAFx6eFwq_d6ZLHxKEl';
    }

    // sending push message to single user by firebase reg id
    public function sendFirebaseMessage($to, $message)
    {
        $fields = [
            'to' => $to,
            'data' => $message,
        ];

        return $this->sendPushNotification($fields);
    }

    public function sendMultipleAndroidFirebaseMessage($reg_ids, $message)
    {
        $fields = [
            'registration_ids' => $reg_ids,
            'data' => $message,
        ];

        return $this->sendPushNotification($fields);
    }

    // sending push message to multiple user by firebase reg id
    public function sendMultipleFirebaseMessage($reg_ids, $message)
    {
        $fields = [
            'registration_ids' => $reg_ids,
            'notification' => $message,
        ];

        return $this->sendPushNotification($fields);
    }

    // sending push message to single iOS user by firebase reg id
    public function sendIosFirebaseMessage($to, $message)
    {
        $fields = [
            'to' => $to,
            'notification' => $message,
        ];

        return $this->sendPushNotification($fields);
    }

    // function makes curl request to firebase servers
    private function sendPushNotification($fields)
    {

        // Set POST variables
        $url = 'https://fcm.googleapis.com/fcm/send';

        $headers = [
            'Authorization: key='.$this->getFirebaseAPIkey(),
            'Content-Type: application/json',
        ];
        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Disabling SSL Certificate support temporarly
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        // Execute post
        $result = curl_exec($ch);
        if ($result === false) {
            die('Curl failed: '.curl_error($ch));
        }

        // Close connection
        curl_close($ch);

        return $result;
    }

    public function functionSendGlobalPushNotification($message, $customer, $type = null)
    {
        $session = CustomerLoginSession::where('customer_id', $customer->customer_id)->orderBy('id', 'DESC')->first();
        if ($session && $session->status == LoginStatus::logged_in) {
            if ($session->platform == PlatformType::android) {
                $this->functionSendAndroidGlobalPushNotification($message, $session->physical_address);
            } elseif ($session->platform == PlatformType::ios) {
                $this->functionSendIOSGlobalPushNotification($message, $session->physical_address, $type);
            }
        }
    }

    public function functionSendAndroidGlobalPushNotification($message, $firebaseRegId)
    {
        // notification title
        $res = [];
        $res['data']['title'] = 'Royalty';
        $res['data']['is_background'] = false;
        $res['data']['message'] = $message;
        //$res['data']['image'] = 'N/A';
        //$res['data']['payload'] = 'N/A';
        $res['data']['timestamp'] = date('Y-m-d G:i:s');

        $this->sendFirebaseMessage($firebaseRegId, $res);
    }

    public function functionSendIOSGlobalPushNotification($message, $firebaseRegId, $type = null)
    {
        // notification title
        $res = [];
        $res['title'] = 'Royalty';
        $res['is_background'] = false;
        $res['body'] = $message;
        $res['timestamp'] = date('Y-m-d G:i:s');

        // optional payload
        $payload = [];
        $payload['notification_type'] = $type;
        $res['payload'] = $payload;

        $this->sendIosFirebaseMessage($firebaseRegId, $res);
    }

    public function sendFirebaseDiscountNotification($message, $customer, $type, $branch_id, $transaction_id)
    {
        event(new offer_availed($customer->customer_id));
        $session = CustomerLoginSession::where('customer_id', $customer->customer_id)->orderBy('id', 'DESC')->first();
        if ($session && $session->status == LoginStatus::logged_in) {
            if ($session->platform == PlatformType::android) {
                $this->sendFirebaseAndroidDiscountNotification(
                    $message,
                    $session->physical_address,
                    $type,
                    $branch_id,
                    $transaction_id
                );
            } elseif ($session->platform == PlatformType::ios) {
                $this->sendFirebaseIOSDiscountNotification(
                    $message,
                    $session->physical_address,
                    $type,
                    $branch_id,
                    $transaction_id
                );
            }
        }
    }

    public function sendFirebaseIOSDiscountNotification($message, $firebaseRegId, $type, $branch_id, $transaction_id)
    {
        // optional payload
        $payload = [];
        $payload['branch_id'] = $branch_id;
        $payload['transaction_id'] = $transaction_id;
        $payload['notification_type'] = $type;

        // iOS notification block
        $iRes = [];
        $iRes['title'] = 'Royalty';
        $iRes['is_background'] = false;
        $iRes['body'] = $message;
        $iRes['timestamp'] = date('Y-m-d G:i:s');
        // optional payload
        $iRes['payload'] = $payload;

        $this->sendIosFirebaseMessage($firebaseRegId, $iRes);
    }

    public function sendFirebaseAndroidDiscountNotification($message, $firebaseRegId, $type, $branch_id, $transaction_id)
    {
        // notification title
        $res = [];
        $res['data']['title'] = 'Royalty';
        $res['data']['is_background'] = false;
        $res['data']['message'] = $message;
        $res['data']['timestamp'] = date('Y-m-d G:i:s');
        // optional payload
        $payload = [];
        $payload['branch_id'] = $branch_id;
        $payload['transaction_id'] = $transaction_id;
        $payload['notification_type'] = $type;
        $res['data']['payload'] = $payload;
        $this->sendFirebaseMessage($firebaseRegId, $res);
    }

    public function sendFirebaseFeedNotification($title, $message, $firebaseRegIds, $scroll_id, $image_url, $type)
    {
        // notification title
        $res = [];
        $res['data']['title'] = $title;
        $res['data']['is_background'] = false;
        $res['data']['message'] = $message;
        $res['data']['timestamp'] = date('Y-m-d G:i:s');
        if ($image_url) {
            $res['data']['image'] = $image_url;
        }
        // optional payload
        $feed = [];
        $feed['scroll_id'] = $scroll_id;
        $feed['notification_type'] = $type; //feed type notification
        if ($type == PushNotificationType::FROM_ADMIN) {
            $res['data']['admin'] = $feed;
        } else {
            $res['data']['feed'] = $feed;
        }
        $this->sendMultipleAndroidFirebaseMessage($firebaseRegIds, $res);
    }

    public function sendFirebaseIOSFeedNotification($title, $message, $firebaseRegIds, $scroll_id, $image_url, $type)
    {
        // notification title
        $res = [];
        $res['title'] = $title;
        $res['is_background'] = false;
        $res['body'] = $message;
        $res['timestamp'] = date('Y-m-d G:i:s');
        $res['mutable_content'] = true;
        // optional payload
        $feed = [];
        $feed['scroll_id'] = $scroll_id;
        $feed['notification_type'] = $type; //feed type notification
        if ($image_url) {
            $feed['image_url'] = $image_url; //feed type notification
        }
        if ($type == PushNotificationType::FROM_ADMIN) {
            $res['data']['admin'] = $feed;
        } else {
            $res['data']['feed'] = $feed;
        }
        $this->sendMultipleFirebaseMessage($firebaseRegIds, $res);
    }

    public function sendFirebaseB2BCFeedNotification($message, $firebaseRegId, $post_id, $scroll_id, $image_url)
    {
        // notification title
        $res = [];
        $res['data']['title'] = 'Royalty';
        $res['data']['is_background'] = false;
        $res['data']['message'] = $message;
        $res['data']['timestamp'] = date('Y-m-d G:i:s');
        $res['data']['image'] = $image_url;
        // optional payload
        $client_feed = [];
        $client_feed['post_id'] = $post_id;
        $client_feed['scroll_id'] = $scroll_id;
        $client_feed['notification_type'] = 113; //feed type notification
        $res['data']['client_feed'] = $client_feed;
        $this->sendFirebaseMessage($firebaseRegId, $res);
    }

    public function insertSSLInfo()
    {
        $customer_id = Input::get('customer_id');
        $first_name = Input::get('first_name');
        $last_name = Input::get('last_name');
        $promo_id = Input::get('promo_id');
        $reference = Input::get('reference');
        if ($reference == null) {
            $reference = '0';
        }

        $customer_info = CustomerInfo::where('customer_id', $customer_id)->first();
        $card_type = Input::get('card_type');
        $taka = Input::get('taka');
        $random_text = $this->getSSLTransactionId();

        //store info in temporary table
        DB::table('info_at_buy_card')->insert([
            'customer_id' => $customer_id,
            'first_name' => 'first name',
            'last_name' => 'last_name',
            'reference' => $reference,
            'card_type' => $card_type,
            'firebase_token' => $customer_info->firebase_token,
            'card_promo_id' => $promo_id,
            'tran_id' => $random_text,
        ]);

        //insert info into ssl transaction table
        DB::table('ssl_transaction_table')->insert([
            'customer_id' => $customer_id,
            'status' => ssl_validation_type::not_valid,
            'tran_id' => $random_text,
            'amount' => $taka,
        ]);

        return Response::json(['result' => $random_text], 200);
    }

    //function to reply to the specific reply
    public function partnerReplyReview()
    {
        $reply = Input::get('reply');
        $customer_id = Input::get('customerID');
        $review_id = Input::get('review_id');
        $branch_id = Input::get('branch_id');

        $branch = PartnerBranch::where('id', $branch_id)->with('info.profileImage')->first();
        try {
            DB::beginTransaction(); //to do query rollback

            $reply_exist = DB::table('review_comment')
                ->where('id', $review_id)
                ->where('comment_type', 'partner')
                ->count();
            if ($reply_exist != 0) {
                //save reply to the database
                DB::table('review_comment')
                    ->where('review_id', $review_id)
                    ->where('comment_type', 'partner')
                    ->update(
                        [
                            'comment' => $reply,
                        ]
                    );
            } else {
                //save reply to the database
                DB::table('review_comment')->insert(
                    [
                        'review_id' => $review_id,
                        'comment' => $reply,
                        'comment_type' => 'partner',
                    ]
                );
            }

            //insert info into customer notification table
            DB::table('customer_notification')->insert(
                [
                    'user_id' => $customer_id,
                    'image_link' => $branch->info->profileImage->partner_profile_image,
                    'notification_text' => 'replied to your review',
                    'notification_type' => 6,
                    'source_id' => $review_id,
                    'seen' => 0,
                ]
            );

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            $error = 'Please try again!';

            return Response::json(['result' => $error], 201);
        }

        //message to send as parameter
        $message = $branch->info->partner_name.' replied to your review';
        $customer = DB::table('customer_info')->where('customer_id', $customer_id)->first();
        //send notification to app
        (new self)->functionSendGlobalPushNotification($message, $customer);
        //back to review page

        return Response::json(['result' => $review_id], 200);
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

    public function logout()
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        DB::table('customer_info')
            ->where('customer_id', $customer_id)
            ->update([
                'firebase_token' => 0,
            ]);

        return Response::json(['result' => $customer_id], 200);
    }

    public function partnerLogout()
    {
        $partner_account_id = Input::get('partner_account_id');
        DB::table('partner_info')
            ->where('partner_account_id', $partner_account_id)
            ->update([
                'firebase_token' => '',
            ]);

        return Response::json(['result' => $partner_account_id], 200);
    }

    public function SSLSuccessPayment()
    {
        $customer_id = JWTAuth::toUser(JWTAuth::getToken())->customer_id;
        $tran_id = Input::get('tran_id');
        $amount = Input::get('amount');
        $tran_date = Input::get('tran_date');
        $val_id = Input::get('val_id');
        $store_amount = Input::get('store_amount');
        $card_type = Input::get('card_type');
        $currency = Input::get('currency');
        $card_no = Input::get('card_no');
        $bank_tran_id = Input::get('bank_tran_id');
        $card_issuer = Input::get('card_issuer');
        $card_brand = Input::get('card_brand');
        $card_issuer_country = Input::get('card_issuer_country');
        $card_issuer_country_code = Input::get('card_issuer_country_code');
        $currency_amount = Input::get('currency_amount');
        $month = Input::get('month');
        $transaction = SslTransactionTable::where('amount', $amount)->where('tran_id', $tran_id)->first();
        if (! $transaction) {
            return Response::json(['result' => 'There is problem with the payment please try again.'], 201);
        }
        try {
            DB::beginTransaction(); //to do query rollback

            $customer_data = DB::table('customer_info as ci')
                ->join('user_type as ut', 'ut.id', '=', 'ci.customer_type')
                ->select('ci.*', 'ut.type')
                ->where('ci.customer_id', $customer_id)
                ->get();
            $customer_data = json_decode(json_encode($customer_data), true);
            $customer_data = $customer_data[0];

            //check customers extra remaining days for the card
            $curDate = date('Y-m-d');
            $exp_date = $customer_data['expiry_date'];

            $cur_date = new DateTime($curDate);
            $expiry_date = new DateTime($exp_date);
            $interval = date_diff($cur_date, $expiry_date);
            $daysRemaining = $interval->format('%R%a');

            //get date after months
            $date = date_create(date('Y-m-d'));
            $expiry_date = date_add($date, date_interval_create_from_date_string($month.' month'));
            if ($daysRemaining > 0) {
                $expiry_date = date_add($date, date_interval_create_from_date_string($daysRemaining.' days'));
            }
            $expiry_date = $expiry_date->format('Y-m-d');

            //get temporarily saved info at payment time
            $temporary_info = DB::table('info_at_buy_card')->where('tran_id', $tran_id)->first();
            DB::table('ssl_transaction_table')
                ->where('tran_id', $tran_id)
                ->where('amount', $amount)
                ->update([
                    'status' => ssl_validation_type::valid,
                    'tran_date' => $tran_date,
                    'val_id' => $val_id,
                    'store_amount' => $store_amount,
                    'card_type' => $card_type,
                    'card_no' => $card_no,
                    'currency' => $currency,
                    'bank_tran_id' => $bank_tran_id,
                    'card_issuer' => $card_issuer,
                    'card_brand' => $card_brand,
                    'card_issuer_country' => $card_issuer_country,
                    'card_issuer_country_code' => $card_issuer_country_code,
                    'currency_amount' => $currency_amount,
                    'month' => $temporary_info->month,
                ]);

            //update data in customer_info table
            DB::table('customer_info')
                ->where('customer_id', $customer_id)
                ->update([
                    'customer_type' => $temporary_info->customer_type,
                    'month' => $temporary_info->month,
                    'expiry_date' => $expiry_date,
                    'card_active' => 2,
                    'delivery_status' => 1,
                    'approve_date' => date('Y-m-d H:i:s'),
                ]);

            $ssl_id = DB::table('ssl_transaction_table')
                ->select('id')
                ->where('tran_id', $tran_id)
                ->first();

            $card_delivery = CardDelivery::where('ssl_id', $ssl_id->id)->first();
            if (! $card_delivery) {
                DB::table('card_delivery')->insert([
                    'customer_id' => $customer_id,
                    'delivery_type' => $temporary_info->delivery_type,
                    'shipping_address' => $temporary_info->shipping_address,
                    'order_date' => $temporary_info->order_date,
                    'paid_amount' => $amount,
                    'ssl_id' => $ssl_id->id,
                ]);
            }

            //save card promo usage data if exists
            if ($temporary_info->card_promo_id != 0) {
                CardPromoCodeUsage::insert([
                    'customer_id' => $temporary_info->customer_id,
                    'promo_id' => $temporary_info->card_promo_id,
                    'ssl_id' => $ssl_id->id,
                ]);
                //update influencer payment info if this promo belongs to anyone
                (new functionController)->updateInfluencerPaymentInfo($temporary_info->card_promo_id, $amount);
            }

            //sales
            $seller_info = CardSellerInfo::where('promo_ids', 'like', "%\"{$temporary_info->card_promo_id}\"%")->first();
            if ($seller_info) {
                $seller_balance = SellerBalance::where('seller_id', $seller_info->id)->first();
                $commission = $seller_info->commission;
            } else {
                $seller_balance = null;
                $commission = null;
            }
            $all_amount = AllAmounts::all();
            $per_card_sell = $all_amount[11]['price'];
            //make history
            if ($seller_info) {
                (new functionController2())->addToCustomerHistory($customer_id, $seller_info->seller_account_id,
                    CustomerType::card_holder,
                    $ssl_id->id, $temporary_info->card_promo_id);

                if ($commission) {
                    //get main price to calculate commission
                    $main_price = CardPrice::where('platform', PlatformType::android)
                        ->where('type', MembershipPriceType::buy)
                        ->where('month', $temporary_info->month)
                        ->first();
                    //update seller balance
                    $commission_received = (new JsonSalesController())->updateSellerBalance($main_price->price, $commission, $seller_balance,
                        $per_card_sell, $temporary_info->month, false);
                    //send sms to seller
                    (new JsonSalesController())->sendSellerBalanceSMS($seller_info->id, $seller_info->account->phone,
                        $main_price->price, $commission, $temporary_info->month, $temporary_info->customer_full_name);
                    //save seller commission history
                    (new JsonSalesController())->saveSellerCommissionHistory($seller_info->id, $ssl_id->id,
                        $commission_received, SellerCommissionType::ONLINE_PAY);
                }
            } else {
                (new functionController2())->addToCustomerHistory($customer_id, null,
                    CustomerType::card_holder,
                    $ssl_id->id, $temporary_info->card_promo_id);
            }

            DB::table('info_at_buy_card')->where('customer_id', $customer_id)->delete();

            (new \App\Http\Controllers\AdminNotification\functionController())->membershipPurchaseNotification($temporary_info);
            DB::commit(); //to do query rollback
            $validity = $temporary_info->month == 12 ? 'one year' : $temporary_info->month.' months';

            (new adminController)->OnlinePaymentMail($temporary_info->customer_full_name, $temporary_info->customer_email,
                $temporary_info, $validity);

            $mon_txt = $temporary_info->month > 1 ? ' months' : ' month';
            $msg = 'Congratulations! You have successfully purchased '.$temporary_info->month.$mon_txt.
                ' membership and it will expire on '.date('M d, Y', strtotime($expiry_date)).'.';

            return Response::json(['result' => $msg], 200);
        } catch (\Exception $e) {
            $info = CustomerInfo::where('customer_id', $customer_id)->first();
            $temporary_info = SslTransactionTable::where('tran_id', $tran_id)->first();
            $mon_txt = $temporary_info->month > 1 ? ' months' : ' month';
            $msg = 'Congratulations! You have successfully purchased '.$temporary_info->month.$mon_txt.
                ' membership and it will expire on '.date('M d, Y', strtotime($expiry_date)).'.';
            if ($info->customer_type != 3) {
                return Response::json(['result' => $msg], 200);
            } else {
                DB::rollBack(); //rollback all successfully executed queries
                return Response::json(['result' => 'Unsuccessful'], 201);
            }
        }
    }

    public function sslTransactionFailed()
    {
        $tran_id = Input::get('tran_id');
        DB::table('info_at_buy_card')->where('tran_id', $tran_id)->delete();
    }

    public function getFilterLocation()
    {
        $areas = DB::table('partner_branch as pb')
            ->select('pb.partner_division as name', 'd.id as id')
            ->join('partner_account as pa', 'pa.partner_account_id', '=', 'pb.partner_account_id')
            ->join('division as d', 'd.name', '=', 'pb.partner_division')
            ->where('pb.active', 1)
            ->where('pa.active', 1)
            ->groupBy('pb.partner_division')
            ->groupBy('d.id')
            ->get();

        foreach ($areas as $area) {
            $location = DB::table('partner_branch as pb')
                ->select('pb.partner_area as area_name', 'a.id as id')
                ->join('partner_account as pa', 'pa.partner_account_id', '=', 'pb.partner_account_id')
                ->join('area as a', 'a.area_name', '=', 'pb.partner_area')
                ->where('pb.active', 1)
                ->where('pa.active', 1)
                ->where('a.division_id', $area->id)
                ->groupBy('pb.partner_area')
                ->groupBy('a.id')
                ->get();
            $area->area = $location;
        }

        return Response::json(['divisions' => $areas], 200);
    }

    //function to check user validity && user requests
    public function checkUser()
    {
        $customer_id = Input::get('customer_id');
        $partner_account_id = Input::get('partner_account_id');
        $today = date('Y-m-d');
        //check if customer id exists or not
        $info = DB::table('customer_info')
            ->where('customer_id', $customer_id)
            ->get();
        $info = json_decode(json_encode($info), true);
        if (count($info) == 0) {
            $data['invalid_user'] = 'Invalid User';

            return Response::json($data);
        } else {
            $requests = DB::table('bonus_request as brq')
                ->join('all_coupons as acp', 'acp.id', '=', 'brq.coupon_id')
                ->select('acp.reward_text', 'acp.coupon_type', 'brq.*')
                ->where('brq.customer_id', $customer_id)
                ->where('acp.partner_account_id', $partner_account_id)
                ->where('brq.used', 0)
                ->where('brq.expiry_date', '>=', $today)
                ->get();
            $requests = json_decode(json_encode($requests), true);

            $customerInfo = DB::table('customer_info as ci')
                ->join('user_type as ut', 'ut.id', '=', 'ci.customer_type')
                ->select('ci.customer_id', 'ci.customer_first_name', 'ci.customer_last_name',
                    'ci.customer_contact_number', 'ci.customer_profile_image', 'ut.type')
                ->where('customer_id', $customer_id)
                ->get();
            $customerInfo = json_decode(json_encode($customerInfo), true);
            $customerInfo[0]['card_active'] = $info[0]['card_active'];
            $customerInfo[0]['expiry_date'] = $info[0]['expiry_date'];
            $customerInfo = $customerInfo[0];
            $data['customerInfo'] = $customerInfo;
            $data['requests'] = $requests;

            return Response::json($data);
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
        $discount_percentage = DB::table('customer_info as ci')
            ->join('discount as dis', 'dis.user_type', '=', 'ci.customer_type')
            ->select('discount_percentage')
            ->where('ci.customer_id', $customerID)
            ->where('dis.partner_account_id', $partnerID)
            ->get();
        $discount_percentage = json_decode(json_encode($discount_percentage), true);
        $discount_percentage = $discount_percentage[0]['discount_percentage'];

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
        $transaction_details['bill'] = intval($bill);
        $transaction_details['discount'] = intval($discount);
        $transaction_details['bill_amount'] = intval($payable_amount);

        return Response::json($transaction_details);
    }

    //function to send SMS with reset password
    public function sendCustomSMS()
    {
        $phone = Input::get('phone');
        $text_message = Input::get('text_message');
        if (substr($phone, 0, 4) === '+880') {
            $full_number = $phone;
        } elseif (substr($phone, 0, 1) === '0') {
            $full_number = '+88'.$phone;
        } else {
            $full_number = '+880'.$phone;
        }

        $user = 'Royaltybd';
        $pass = '66A6Q13d';
        $sid = 'RoyaltybdMasking';
        $url = 'http://sms.sslwireless.com/pushapi/dynamic/server.php';
        $param = "user=$user&pass=$pass&sms[0][0]= $full_number &sms[0][1]=".urlencode($text_message)."&sms[0][2]=123456789&sid=$sid";
        $crl = curl_init();
        curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($crl, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($crl, CURLOPT_URL, $url);
        curl_setopt($crl, CURLOPT_HEADER, 0);
        curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($crl, CURLOPT_POST, 1);
        curl_setopt($crl, CURLOPT_POSTFIELDS, $param);
        $response = curl_exec($crl);
        curl_close($crl);

        return Response::json(['result' => 'Message Sent']);
    }

    public function versionControl()
    {
        return Response::json($this->getAndroidVersionData());
    }

    public function getAndroidVersionData()
    {
        $data2 = [
            'version' => 94,
            'force_update' => true,
            'maintenance' => false,
            'maintenance_message' => 'Royalty is under maintenance now !',
            'app_url' => 'https://play.google.com/store/apps/details?id=com.royalty.bd&hl=en',
        ];

        return $data2;
    }

    public function iOSVersionControl()
    {
        return Response::json($this->getIOSversionData());
    }

    public function getIOSversionData()
    {
        $data2 = [
            'version' => '1.2.1',
            'force_update' => true,
            'maintenance' => false,
            'maintenance_message' => 'Royalty is under maintenance now !',
            'app_url' => 'https://apps.apple.com/sa/app/royalty-bd/id1300476271',
        ];

        return $data2;
    }

    public function checkoutVersionControl()
    {
        $data2 = [
            'version' => 7,
            'force_update' => true,
            'app_url' => 'https://play.google.com/store/apps/details?id=com.royaltybd.royaltybdcheckout',
        ];

        return Response::json($data2);
    }
}
