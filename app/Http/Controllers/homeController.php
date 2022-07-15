<?php

namespace App\Http\Controllers;

use App\AllAmounts;
use App\Area;
use App\BlogCategory;
use App\BlogPost;
use App\BranchOffers;
use App\CardPrice;
use App\CardPromoCodes;
use App\CardPromoCodeUsage;
use App\CardSellerAccount;
use App\CardSellerInfo;
use App\Categories;
use App\Contact;
use App\CustomerInfo;
use App\CustomerNotification;
use App\CustomerPoint;
use App\Discount;
use App\Division;
use App\Events\refer_bonus;
use App\FollowPartner;
use App\Helpers\LengthAwarePaginator;
use App\Http\Controllers\ActivitySession\functionController as activityFunctionController;
use App\Http\Controllers\Enum\Constants;
use App\Http\Controllers\Enum\GlobalTexts;
use App\Http\Controllers\Enum\LikerType;
use App\Http\Controllers\Enum\MembershipPriceType;
use App\Http\Controllers\Enum\notificationType;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\Enum\PointType;
use App\Http\Controllers\Enum\PromoType;
use App\Http\Controllers\Enum\SharerType;
use App\Http\Controllers\Enum\ssl_validation_type;
use App\Http\Controllers\Enum\VerificationType;
use App\Http\Controllers\LoginRegister\functionController as loginFunctionController;
use App\Http\Controllers\OTP\functionController as otpFunctionController;
use App\Http\Controllers\Reward\functionController as rewardFunctionController;
use App\InfluencerRequest;
use App\LikePost;
use App\LikesReview;
use App\PartnerAccount;
use App\PartnerBranch;
use App\PartnerInfo;
use App\PartnerProfileImage;
use App\Post;
use App\Rating;
use App\Review;
use App\ReviewComment;
use App\SearchTerm;
use App\Subscribers;
use App\TopBrands;
use App\TransactionTable;
use App\TrendingOffers;
use App\VoucherPurchaseDetails;
use App\Wish;
use Carbon\Carbon;
use Datetime;
use File;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Image;
use League\Flysystem\Filesystem;
use Mail;
use Response;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use View;

class homeController extends Controller
{
    //this is for client info (browser, version, os)
    private $agent = '';
    private $info = [];

    public function __construct()
    {
        $this->agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        $this->getBrowser();
        $this->getOS();
    }

    public function getBrowser()
    {
        $browser = [
            'Navigator' => '/Navigator(.*)/i',
            'Firefox' => '/Firefox(.*)/i',
            'Internet Explorer' => '/MSIE(.*)/i',
            'Google Chrome' => '/chrome(.*)/i',
            'MAXTHON' => '/MAXTHON(.*)/i',
            'Opera' => '/Opera(.*)/i',
        ];
        foreach ($browser as $key => $value) {
            if (preg_match($value, $this->agent)) {
                $this->info = array_merge($this->info, ['Browser' => $key]);
                $this->info = array_merge($this->info, [
                    'Version' => $this->getVersion($key, $value, $this->agent),
                ]);
                break;
            } else {
                $this->info = array_merge($this->info, ['Browser' => 'UnKnown']);
                $this->info = array_merge($this->info, ['Version' => 'UnKnown']);
            }
        }

        return $this->info['Browser'];
    }

    public function getOS()
    {
        $OS = [
            'Windows' => '/Windows/i',
            'Linux' => '/Linux/i',
            'Unix' => '/Unix/i',
            'Mac' => '/Mac/i',
        ];

        foreach ($OS as $key => $value) {
            if (preg_match($value, $this->agent)) {
                $this->info = array_merge($this->info, ['Operating System' => $key]);
                break;
            } else {
                $this->info = array_merge($this->info, ['Operating System' => 'Unknown']);
            }
        }

        return $this->info['Operating System'];
    }

    public function getVersion($browser, $search, $string)
    {
        $browser = $this->info['Browser'];
        $version = '';
        $browser = strtolower($browser);
        preg_match_all($search, $string, $match);
        switch ($browser) {
            case 'firefox':
                $version = str_replace('/', '', $match[1][0]);
                break;

            case 'internet explorer':
                $version = substr($match[1][0], 0, 4);
                break;

            case 'opera':
                $version = str_replace('/', '', substr($match[1][0], 0, 5));
                break;

            case 'navigator':
                $version = substr($match[1][0], 1, 7);
                break;

            case 'maxthon':
                $version = str_replace(')', '', $match[1][0]);
                break;

            case 'google chrome':
                $version = substr($match[1][0], 1, 10);
        }

        return $version;
    }

    public function showInfo($switch)
    {
        $switch = strtolower($switch);
        switch ($switch) {
            case 'browser':
                return $this->info['Browser'];
                break;

            case 'os':
                return $this->info['Operating System'];
                break;

            case 'version':
                return $this->info['Version'];
                break;

            case 'all':
                return [$this->info['Browser'], $this->info['Version'], $this->info['Operating System']];
                break;

            default:
                return 'Unknown';
                break;
        }
    }

    //client info ends

    //function for live search suggestion
    public function autocomplete(Request $request)
    {
        $key = $request->search;
        if ($key != '' || ! empty($key)) {
            $customer_id = session('customer_id') != null ? session('customer_id') : null;
            (new functionController2())->createSearchStats($customer_id, $branch_id = null, $key);
            //get partners name with keyword
            $partnerData = (new \App\Http\Controllers\Search\functionController())->getSearched($key);

//            $partnerData =  SearchTerm::where('name', 'LIKE', '%'.$key.'%')
//                ->orWhereRaw('REPLACE (name," ","") LIKE "%'.str_replace(' ', '%', $key).'%"')
//                ->orWhere([['area', 'LIKE', '%'.$key.'%']])
//                ->orWhereRaw('REPLACE (area," ","") LIKE "%'.str_replace(' ', '%', $key).'%"')
//                ->get();

            $url = url('/');
            if ($partnerData != null && count($partnerData) > 0) {
                echo '<ul>';
                foreach ($partnerData as $partner) {
                    echo '<li>';
                    $pname = str_replace("'", '', $partner->name);
                    echo "<a href='".$url.'/profile_from_search/'.$pname.'/'.$partner->branch_id.'/'.$key."' 
                            onMouseOver=\"this.style.color='#041328'\" onMouseOut=\"this.style.color='#007bff'\" 
                            style='color:#007bff; padding: 5px; font-size:14px'>".$partner->name.' - '.$partner->partner_area;
                    echo '</a></li>';
                }
                echo '</ul>';
            }
        }
    }

    //================search function for home search bar====================
    public function searchWebsite(Request $request)
    {
        $key = $request->get('search');
        if ($key == null) {
            $key = $keyword = Session::get('search_keyword');
        } else {
            $keyword = $request->get('search');
            session(['search_keyword' => $keyword]);
        }
        $customer_id = session('customer_id') != null ? session('customer_id') : null;
        (new functionController2())->createSearchStats($customer_id, $branch_id = null, $key);
        //get categories list
        $categories = Categories::all();
        //trending offers
        $profileImages = DB::table('partner_info as pi')
            ->join('partner_account as pa', 'pa.partner_account_id', '=', 'pi.partner_account_id')
            ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
            ->join('trending_offers as to', 'to.partner_account_id', '=', 'pi.partner_account_id')
            ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
            ->select('ppi.partner_profile_image', 'ppi.partner_account_id', 'pi.partner_name', 'pi.partner_category', 'rat.average_rating')
            ->where('pa.active', 1)
            ->get();
        $profileImages = json_decode(json_encode($profileImages), true);
        $i = 0;
        foreach ($profileImages as $profileImage) {
            $main_branch = (new functionController)->mainBranchOfPartner($profileImage['partner_account_id']);
            if (count($main_branch) > 0) {
                $profileImages[$i]['main_branch_id'] = $main_branch[0]->id;
            } else {
                unset($profileImages[$i]);
            }
            $i++;
        }
        //match partner's names with the keyword
        $first_partner_list = DB::select("SELECT DISTINCT pi.partner_account_id
                                    FROM partner_info pi
                                    LEFT JOIN partner_account pa
                                    ON pa.partner_account_id = pi.partner_account_id
                                    LEFT JOIN partner_branch pb
                                    ON pi.partner_account_id = pb.partner_account_id
                                    WHERE (pi.partner_name SOUNDS LIKE '%$keyword%' OR pi.partner_name LIKE '%$keyword%' 
                                        OR pb.partner_area SOUNDS LIKE '%$keyword%' OR pb.partner_area LIKE '%$keyword%')
                                    AND pa.active = 1 AND pb.active = 1");

        if (! empty($first_partner_list)) {
            $ids = array_pluck($first_partner_list, 'partner_account_id');
            $partners = PartnerInfo::whereIn('partner_account_id', $ids)->get();
            $partners = collect($partners)->where('account.active', 1);
            $partner_list_1 = (new \App\Http\Controllers\FeaturedPartners\functionController())
                ->getMappedData($partners, 'all');
            foreach ($partner_list_1 as $key => $data) {
                if (count($data['branches']) == 0) {
                    unset($partner_list_1[$key]);
                }
            }
            if (Session::has('customer_id')) {
                //get all notification of this customer
                $allNotifications = (new functionController)->allNotifications(Session::get('customer_id'));
                session(['customerAllNotifications' => $allNotifications]);
            }

            return view('searchWebsite', compact('profileImages', 'partner_list_1', 'categories', 'keyword'));
        } else {
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
            $ids = [];
            foreach ($partner_list_2 as $partner) {
                $partner_id = PartnerInfo::where('partner_name', $partner[0])->pluck('partner_account_id');
                if ($partner_id) {
                    array_push($ids, $partner_id);
                }
                $i++;
            }
            $partners = PartnerInfo::whereIn('partner_account_id', $ids)->get();
            $partners = collect($partners)->where('account.active', 1);
            $partner_final_list = (new \App\Http\Controllers\FeaturedPartners\functionController())
                ->getMappedData($partners, 'all');
        }
        foreach ($partner_final_list as $key => $data) {
            if (count($data['branches']) == 0) {
                unset($partner_final_list[$key]);
            }
        }
        //send all searched info to the search page
        if (count($partner_final_list) > 0) {
            if (Session::has('customer_id')) {
                //get all unseen notification of this customer
                $allNotifications = (new functionController)->allNotifications(Session::get('customer_id'));
                session(['customerAllNotifications' => $allNotifications]);
            }
            return view('searchWebsite', compact('profileImages', 'partner_final_list', 'categories', 'keyword'));
        } else {
            if (Session::has('customer_id')) {
                //get all unseen notification of this customer
                $allNotifications = (new functionController)->allNotifications(Session::get('customer_id'));
                session(['customerAllNotifications' => $allNotifications]);
            }
            return view('searchWebsite', compact('profileImages', 'categories', 'keyword'));
        }
    }

    //===============function for trending offers in home page=====================
    public function homePage()
    {
        //trending offers
        $trendingOffers = DB::table('trending_offers as to')
            ->join('partner_info as pi', 'pi.partner_account_id', '=', 'to.partner_account_id')
            ->join('partner_account as pa', 'pa.partner_account_id', '=', 'pi.partner_account_id')
            ->join('partner_branch as pb', 'pb.partner_account_id', '=', 'pi.partner_account_id')
            ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
            ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
            ->select(
                'ppi.partner_profile_image',
                'ppi.partner_account_id',
                'pi.partner_name',
                'pi.partner_category',
                'rat.average_rating',
                'pb.id as main_branch_id'
            )
            ->where('pb.main_branch', 1)
            ->where('pa.active', 1)
            ->orderBy('to.order_num', 'ASC')
            ->get();
        $trendingOffers = (new functionController)->partnerOffers($trendingOffers);

        $i = 0;
        foreach ($trendingOffers as $offer) {
            $trendingOffers[$i]->offer_heading = (new functionController2())->partnerOfferHeading($offer->partner_account_id);
            $branches = PartnerBranch::where('partner_account_id', $offer->partner_account_id)->where('active', 1)->get();
            $trendingOffers[$i]->location = (new functionController2())->getBranchLocations($branches);
            $i++;
        }

        //top brands
        $topBrands = DB::table('partner_info as pi')
            ->join('partner_account as pa', 'pa.partner_account_id', '=', 'pi.partner_account_id')
            ->join('partner_branch as pb', 'pb.partner_account_id', '=', 'pi.partner_account_id')
            ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'pi.partner_account_id')
            ->join('top_brands as tb', 'tb.partner_account_id', '=', 'pi.partner_account_id')
            ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
            ->select(
                'ppi.partner_profile_image',
                'ppi.partner_account_id',
                'pi.partner_name',
                'pi.partner_category',
                'rat.average_rating',
                'pb.id as main_branch_id'
            )
            ->where('pb.main_branch', 1)
            ->where('pa.active', 1)
            ->orderBy('tb.order_num', 'ASC')
            ->get();
        $topBrands = (new functionController)->partnerOffers($topBrands);
        $i = 0;
        foreach ($topBrands as $offer) {
            $topBrands[$i]->offer_heading = (new functionController2())->partnerOfferHeading($offer->partner_account_id);
            $branches = PartnerBranch::where('partner_account_id', $offer->partner_account_id)->where('active', 1)->get();
            $topBrands[$i]->location = (new functionController2())->getBranchLocations($branches);
            $i++;
        }

        $visited_profile = [];
        if (Session::has('customer_id')) {
            //get all unseen notification of this customer
            $allNotifications = (new functionController)->allNotifications(Session::get('customer_id'));
            session(['customerAllNotifications' => $allNotifications]);
            $visited_profile = (new functionController)->recentlyVisitedProfile(Session::get('customer_id'));
        }
        //        $hotspotsData = (new functionController)->hotspotsData();
        $categories = Categories::orderBy('priority', 'DESC')->get();

        //blog posts
        $blogPosts = BlogPost::orderBy('priority', 'DESC')->orderBy('posted_on', 'DESC')->get();

        //news feed posts
        $newsPosts = Post::orderBy('id', 'DESC')->get();

        //top transacted partners
        $partnerInfo = PartnerAccount::where('active', 1)->with('info.profileImage', 'branches.transaction')->get();

        $i = 0;
        foreach ($partnerInfo as $key => $partner) {
            if (count($partner->branches) == 0) {
                unset($partnerInfo[$key]);
                continue;
            }
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
        $topPartners = array_values($topPartners);

        $array_column = array_column($topPartners, 'total_tran_num');
        array_multisort($array_column, SORT_DESC, $topPartners);
        $topPartners = array_slice($topPartners, 0, 10);

        $i = 0;
        foreach ($topPartners as $offer) {
            $offers_count = PartnerBranch::where([['partner_account_id', $topPartners[$i]['partner_account_id']], ['active', 1]])->with('offers')->count();
            $main_branch_info = (new functionController)->mainBranchOfPartner($topPartners[$i]['partner_account_id']);
            if (isset($main_branch_info[0]->id)) {
                $topPartners[$i]['offer_heading'] = (new functionController2())
                    ->partnerOfferHeading($offer['partner_account_id']);
                $branches = PartnerBranch::where('partner_account_id', $offer['partner_account_id'])
                    ->where('active', 1)->get();
                $topPartners[$i]['location'] = (new functionController2())->getBranchLocations($branches);
                $rating = Rating::where('partner_account_id', $topPartners[$i]['partner_account_id'])->first();
                $topPartners[$i]['offers'] = $offers_count;
                $topPartners[$i]['main_branch_id'] = $main_branch_info[0]->id;
                $topPartners[$i]['average_rating'] = $rating->average_rating;
            }
            $i++;
        }
        if (Session::has('customer_id')) {
            $physical_address = (new loginFunctionController())->randomTextForLoginSession();
            (new activityFunctionController())->saveSession(session('customer_id'), PlatformType::web,
                $physical_address, $_SERVER['REMOTE_ADDR']);
        }
        if (Session::has('customer_id') && Session::get('user_type') != 3) {
            $card_prices = CardPrice::where([['platform', PlatformType::web], ['type', MembershipPriceType::renew]])
                ->orderBy('price', 'ASC')->get();
        } else {
            $card_prices = CardPrice::where([['platform', PlatformType::web], ['type', MembershipPriceType::buy]])
                ->orderBy('price', 'ASC')->get();
        }

        $title = 'Royalty - Discover offers, discounts and rewards';

        $carousel_images = (new functionController2())->getCarousalImagesForWeb();
        $prices_for_faq = CardPrice::all();

        //counters
        $counters['user_saved'] = TransactionTable::count() * 100;
        $counters['branch_count'] = DB::table('partner_account as pa')
                ->join('partner_branch as pb', 'pb.partner_account_id', '=', 'pa.partner_account_id')
                ->where('pa.active', 1)
                ->where('pb.active', 1)
                ->count();
        $counters['offer_availed'] = TransactionTable::count();
        $counters['all_reviews'] = Review::count();

        return view('index', compact(
            'trendingOffers',
            'topBrands',
            'categories',
            'topPartners',
            'blogPosts',
            'newsPosts',
            'card_prices',
            'visited_profile',
            'title',
            'carousel_images',
            'prices_for_faq',
            'counters'
        ));
    }

    //===============function for subscribe option==================
    public function subscribe(Request $request)
    {
        $this->validate($request, [
            'subscribe' => 'required',
        ]);
        $email = $request->get('subscribe');
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $request->session()->flash('alert-danger', 'Please provide a valid E-mail address!');

            return redirect()->back();
        } else {
            //insert or update
            $subscribers = Subscribers::firstOrNew(['email' => $email]);
            $subscribers->save();

            return redirect('/')->with('subscribe_status', 'We received your E-mail address. We will keep you updated with all the exciting offers and updates.');
        }
    }

    //===================function for all offers========================
    public function allOffers($selected_category)
    {
        $categories = Categories::orderBy('priority', 'DESC')->get();
        //check if visitors select all offers
        if ($selected_category == 'all') {
            $newArray = (new \App\Http\Controllers\FeaturedPartners\functionController())
                ->getPartners($selected_category);

            $profileImages = (new functionController2())->getPaginatedData($newArray, 20);

            if (Session::has('customer_id')) {
                //get all unseen notification of this customer
                $allNotifications = (new functionController)->allNotifications(Session::get('customer_id'));
                session(['customerAllNotifications' => $allNotifications]);
            }
            $title = 'All Privileges/Offers - Royalty | royaltybd.com';
            //send all data to offerpage for "all offer" link
            return view('offers', compact('profileImages', 'categories', 'title'));
        } else {
            $category = Categories::where('type', $selected_category)->first();
            if (!$category) {
                return redirect('offers/all');
            }

            $newArray = (new \App\Http\Controllers\FeaturedPartners\functionController())
                ->getPartners($selected_category);

            $profileImages = (new functionController2())->getPaginatedData($newArray, 20);

            //get subcategory list for filter
            $sub_cats = (new \App\Http\Controllers\adminController())->mainCatWiseSubCats($category->id);

            //get divisions and area
            $divisions = DB::table('partner_branch as pb')
                ->select('pb.partner_division as name', 'd.id as id')
                ->join('partner_account as pa', 'pa.partner_account_id', '=', 'pb.partner_account_id')
                ->join('division as d', 'd.name', '=', 'pb.partner_division')
                ->where('pb.active', 1)
                ->where('pa.active', 1)
                ->groupBy('pb.partner_division')
                ->groupBy('d.id')
                ->orderBy('d.id')
                ->get();

            foreach ($divisions as $division) {
                $areas = DB::table('partner_branch as pb')
                    ->select('pb.partner_area as area_name', 'a.id as id')
                    ->join('partner_account as pa', 'pa.partner_account_id', '=', 'pb.partner_account_id')
                    ->join('area as a', 'a.area_name', '=', 'pb.partner_area')
                    ->where('pb.active', 1)
                    ->where('pa.active', 1)
                    ->where('a.division_id', $division->id)
                    ->groupBy('pb.partner_area')
                    ->groupBy('a.id')
                    ->get();
                $division->areas = $areas;
            }

            if (Session::has('customer_id')) {
                //get all unseen notification of this customer
                $allNotifications = (new functionController)->allNotifications(Session::get('customer_id'));
                session(['customerAllNotifications' => $allNotifications]);
            }
            //category wise facilities
            $facilities = \App\BranchFacility::whereRaw('JSON_CONTAINS(category_ids, ?)', [json_encode($category->id)])
                ->get();

            $title = $category->name.' - Royalty | royaltybd.com';
            //send all data to offer page for specific category link
            return view('offers', compact(
                'sub_cats',
                'profileImages',
                'categories',
                'selected_category',
                'divisions',
                'areas',
                'title',
                'facilities'
            ));
        }
    }

    //function to show partners division wise
    public function divisionOffers($division_name)
    {
        //get all partners images and name for offers page
        $profileImages = DB::table('partner_info as pi')
            ->join('partner_profile_images as ppi', 'pi.partner_account_id', '=', 'ppi.partner_account_id')
            ->join('rating as rat', 'rat.partner_account_id', '=', 'pi.partner_account_id')
            ->where('pi.partner_division', $division_name)
            ->select(
                'pi.partner_account_id',
                'pi.partner_name',
                'pi.partner_category',
                'pi.partner_area',
                'ppi.partner_profile_image',
                'ppi.partner_thumb_image',
                'rat.average_rating'
            )
            ->orderBy('pi.partner_name', 'ASC')
            ->paginate(20);
        $i = 0;
        foreach ($profileImages as $profileImage) {
            $discount = DB::table('discount')
                ->where('partner_account_id', $profileImage->partner_account_id)
                ->max('discount_percentage');
            $profileImages[$i]->discount = $discount;
            //get 1 gallery image randomly of this partner
            $gallery_image = DB::table('partner_gallery_images')
                ->select('partner_gallery_image')
                ->where('partner_account_id', $profileImage->partner_account_id)
                ->inRandomOrder()
                ->first();
            $profileImages[$i]->gallery_image = $gallery_image->partner_gallery_image;
            //get total review number of this partner
            $reviews = DB::table('review')
                ->where('partner_account_id', $profileImage->partner_account_id)
                ->count();
            $profileImages[$i]->total_reviews = $reviews;
            $i++;
        }
        if (Session::has('customer_id')) {
            //get all unseen notification of this customer
            $allNotifications = (new functionController)->allNotifications(Session::get('customer_id'));
            session(['customerAllNotifications' => $allNotifications]);
        }
        if (Session::has('partner_id')) {
            //get all unseen notification of this partner
            $unseenNotifications = (new functionController)->partnerUnseenNotifications(Session::get('partner_id'));
            session(['unseenNotifications' => $unseenNotifications]);
        }
        //send all data to offerpage for "all offer" link
        return view('offers', compact('profileImages'));
    }

    public function profileFromSearch($partner_name, $branch_id, $key)
    {
        $customer_id = session('customer_id') != null ? session('customer_id') : null;
        (new functionController2())->createSearchStats($customer_id, $branch_id, $key);

        return redirect('partner-profile/'.$partner_name.'/'.$branch_id);
    }

    //================profile showing from offers page====================
    public function profileFromOffer($partner_name, $branch_id)
    {
        $partner_name = str_replace('"', '', $partner_name);
        $partner_name = str_replace("'", '', $partner_name);
        $info = PartnerInfo::WhereRaw('REPLACE (partner_name,"\'","")="'.$partner_name.'"')->first();

        if (!$info) {
            return redirect('page-not-found');
        }
        $partnerInfo = PartnerAccount::where('partner_account_id', $info->partner_account_id)->with([
            'info', 'branches', 'branches.openingHours', 'profileImage',
            'branches.offers' => function ($query) {
                $query->where('selling_point', null)->with('customizedPoint');
            }, 'branches.vouchers' => function ($query) {
                $query->where('active', 1);
            },
            'galleryImages' => function ($query) {
                $query->orderBy('id', 'DESC');
            },
            'menuImages', 'rating', 'reviews', 'followPartner.follower',
        ])->first();

        $partnerBranch = PartnerBranch::where('partner_account_id', $partnerInfo->partner_account_id)
            ->where('id', $branch_id)->first();
        if (!$partnerBranch) {
            return redirect('page-not-found');
        }
        $sorted_offers = $partnerBranch->offers->sortByDesc('priority');
        $vouchers = $partnerBranch->vouchers;

        foreach ($vouchers as $key => $voucher) {
            $voucher_date = $voucher->date_duration;
            if (
                new DateTime($voucher_date[0]['from']) <= new DateTime(date('d-m-Y'))
                && new DateTime($voucher_date[0]['to']) >= new DateTime(date('d-m-Y'))
            ) {
                $purchased = VoucherPurchaseDetails::where('voucher_id', $voucher->id)->with('ssl', 'refund')->get();
                $purchased = $purchased->where('ssl.status', 1);
                $purchased = $purchased->where('refund.refund_status', '!=', 1);
                $voucher->purchased = count($purchased);
            } else {
                unset($vouchers[$key]);
            }
        }
        $vouchers = $vouchers->values();

        //customize point time wise dynamic
        //       $partnerInfo = (new functionController)->customizedPointValidity($partnerInfo);

        $date = date('d-m-Y');
        foreach ($sorted_offers as $key => $offer) {
            //check expiry
            $offer_date = $offer->date_duration[0];
            if (
                new DateTime($offer_date['from']) <= new DateTime($date) && new DateTime($offer_date['to']) >= new DateTime($date)
                && $offer->active == 1
            ) {
                //ntng
            } else {
                unset($sorted_offers[$key]);
            }
        }

        $categories = Categories::all();
        if ($partnerInfo != null) {
        } else {
            $partnerInfo = null;
        }

        $exp_date = strtotime($partnerInfo->info->expiry_date);
        $remaining = $exp_date - time();
        $days_remaining = floor($remaining / 86400);
        $hours_remaining = floor(($remaining % 86400) / 3600);

        if ($partnerInfo->galleryImages != null) {
            $i = 0;
            foreach ($partnerInfo->galleryImages as $galleryImage) {
                $partnerInfo->galleryImages[$i]->serialNumber = $i;
                $i++;
            }
        }

        $reviews = (new \App\Http\Controllers\Review\functionController())
            ->getReviews($branch_id, Session::get('customer_id'), LikerType::customer);
        $total_review_count = $reviews->where('heading', '!=', 'n/a')->where('body', '!=', 'n/a')->count();
        $total_rating_count = $reviews->where('heading', 'n/a')->where('body', 'n/a')->count();
        $reviews = $reviews->where('heading', '!=', 'n/a')->where('body', '!=', 'n/a');
        $review_loadmore = Constants::review_loadmore;
        $ratings = (new \App\Http\Controllers\Review\functionController())->getRatings($branch_id);

        //opening hours
        if ($partnerBranch->openingHours != null) {
            $openingHour = $partnerBranch->openingHours->toArray();
            $openingHours[0] = $openingHour['sat'];
            $openingHours[1] = $openingHour['sun'];
            $openingHours[2] = $openingHour['mon'];
            $openingHours[3] = $openingHour['tue'];
            $openingHours[4] = $openingHour['wed'];
            $openingHours[5] = $openingHour['thurs'];
            $openingHours[6] = $openingHour['fri'];
        }
        //initialize days of a week
        $days = ['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'];
        //opening hours ends
        $nearbyPartners = (new functionController)->nearbyPartners(
            $partnerInfo->info->partner_name,
            $partnerBranch->partner_area,
            $partnerBranch->latitude,
            $partnerBranch->longitude
        );
//        $nearbyPartners = (new functionController)->partnerOffers($nearbyPartners);
        foreach ($nearbyPartners as $offer) {
            $offer->offer_heading = (new functionController2)->partnerOfferHeading($offer->partner_account_id);
            $offer->avg_rating = (new \App\Http\Controllers\Review\functionController())
                ->getAverageBranchRating($offer->id);
        }
        $nearbyPartners = $nearbyPartners->sortBy('distance');

        //get client info to save as statistics
        $client = $this->showInfo('all');
        $datetime = date('F j, Y, g:i a');
        //save data of user for statistics
        DB::table('rbd_statistics')->insert([
            'customer_id' => Session::has('customer_id') ? Session::get('customer_id') : 'visitor',
            'partner_id' => $partnerInfo['partner_account_id'],
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'browser_data' => $client[0].','.$client[1].','.$client[2].','.$datetime,
        ]);
        if (Session::has('customer_id')) {
            //get all unseen notification of this customer
            $allNotifications = (new functionController)->allNotifications(Session::get('customer_id'));
            session(['customerAllNotifications' => $allNotifications]);
        }
        if ($partnerBranch['facilities']) {
            $partnerBranch['facilities'] = \App\BranchFacility::whereIn('id', $partnerBranch['facilities'])->get();
        }

        $title = array_get($partnerInfo->info, 'partner_name', '').' '.
            array_get($partnerBranch, 'partner_area', '').' discover offers and deals on royaltybd.com';
        $description = $partnerInfo->info->about.' Discover offers, deals, vouchers and rewards on Royalty. Pay less, enjoy more!';
        $data = compact(
            'partnerInfo',
            'partnerBranch',
            'branch_id',
            'categories',
            'openingHours',
            'days',
            'days_remaining',
            'hours_remaining',
            'reviews',
            'total_review_count',
            'total_rating_count',
            'review_loadmore',
            'ratings',
            'nearbyPartners',
            'sorted_offers',
            'vouchers',
            'title',
            'description'
        );

        //send all info to the partner view page
        return view('partner-profile', $data);
    }

    //function for saving user comments
    public function contactForm(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email',
            'comment' => 'required',
            'g-recaptcha-response' => 'required',
        ]);
        $request->flashOnly(['name', 'email', 'comment', 'g-recaptcha-response']);

        $userName = $request->get('name');
        $userEmail = $request->get('email');
        $userComment = $request->get('comment');
        if (! filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
            $request->session()->flash('alert-danger', 'Please provide a valid E-mail address!');

            return redirect()->back();
        } else {
            $contact = new Contact([
                'name' => $userName,
                'email' => $userEmail,
                'comment' => $userComment,
            ]);
            $contact->save();
            if (Session::has('customer_id')) {
                //get all unseen notification of this customer
                $allNotifications = (new functionController)->allNotifications(Session::get('customer_id'));
                session(['customerAllNotifications' => $allNotifications]);
            }
            (new \App\Http\Controllers\AdminNotification\functionController())->newContactNotification($contact);

            return redirect()->back()->with('contact_status', 'Thank you for reaching to us. We will get back to you soon.');
        }
    }

    //function to show all partners name in the member zone page
    public function socialZone()
    {
        //serach all partners name from database
        $partnerInfo = DB::table('partner_info')->select('partner_account_id', 'partner_name')->get();
        $title = 'Socials | royaltybd.com';
        //send all data to the member zone page
        return view('/socialzone', compact('partnerInfo', 'title'));
    }

    //function to show influencer program page
    public function influencerProgram()
    {
        $total_branch_count = PartnerBranch::where('active', 1)->count();

        return view('/influencer_program', compact('total_branch_count'));
    }

    public function influencerRequest(Request $request)
    {
        //check validation of coupon fields
        $this->validate($request, [
            'name' => 'required',
            'blogname' => 'required',
            'email' => 'required',
        ]);
        $request->flashOnly(['name', 'blogname', 'email']);

        $blog_category = $request->input('influencer-type');
        $full_name = $request->input('name');
        $blog_name = $request->input('blogname');
        $email = $request->input('email');
        $fb_link = $request->input('fb-link');
        $ig_link = $request->input('ig-link');
        $yt_link = $request->input('yt-link');
        $web_link = $request->input('web-link');

        if ($fb_link == '' && $ig_link == '' && $yt_link == '' && $web_link == '') {
            return redirect()->back();
        }

        $influencer = new InfluencerRequest([
            'full_name' => $full_name,
            'blog_name' => $blog_name,
            'blog_category' => $blog_category,
            'email' => $email,
            'facebook_link' => $fb_link,
            'website_link' => $web_link,
            'youtube_link' => $yt_link,
            'instagram_link' => $ig_link,
            'posted_on' => date('Y-m-d H:i:s'),
        ]);
        $influencer->save();

        (new \App\Http\Controllers\AdminNotification\functionController())->influencerRequestNotification($influencer);

        return redirect()->back()->with('inflencer_request_successful', 'Thank you for applying to our Influencer program. We will contact you via e-mail once you are selected. ');
    }

    //function to show faq page
    public function faq()
    {
        $card_prices = CardPrice::all();
        $other_amounts = AllAmounts::all();
        $title = 'Frequently Asked Questions | royaltybd.com';

        return view('faqspage', compact('card_prices', 'other_amounts', 'title'));
    }

    //function to show career options
    public function career()
    {
        $url = url()->previous();
        $show_openings = false;
        if (strpos($url, 'job_opening_details') !== false) {
            $show_openings = true;
        }
        //get all career name from database
        $openingInfo = DB::table('openings')->where('active', 1)->get();
        $openingInfo = json_decode(json_encode($openingInfo), true);
        $title = 'Careers - Join us | royaltybd.com';
        //send all data to the career page
        return view('careers', compact('openingInfo', 'title', 'show_openings'));
    }

    //function to show details for job opening
    public function jobOpeningDetails($position)
    {
        //get all career name from database
        $openingInfo = DB::table('openings')
            ->where('position', $position)
            ->where('active', 1)
            ->first();
        $openingInfo = json_decode(json_encode($openingInfo), true);

        if ($openingInfo == null) {
            return redirect('/careers');
        }
        //send all data to the career page
        return view('job_opening_details', compact('openingInfo'));
    }

    public function online()
    {
        $promo = DB::table('promo_table')->get();
        $promo = json_decode(json_encode($promo), true);

        if (Session::has('customer_id')) {
            //get all unseen notification of this customer
            $allNotifications = (new functionController)->allNotifications(Session::get('customer_id'));
            session(['customerAllNotifications' => $allNotifications]);
        }
        if (Session::has('partner_id')) {
            //get all unseen notification of this partner
            $unseenNotifications = (new functionController)->partnerUnseenNotifications(Session::get('partner_id'));
            session(['unseenNotifications' => $unseenNotifications]);
        }
        if (Session::has('customer_id')) {
            $customer_data = DB::table('pass_changed')
                ->select('pass_change')
                ->where('customer_id', Session::get('customer_id'))
                ->get();
            $customer_data = json_decode(json_encode($customer_data), true);
            if ($customer_data) {
                $customer_data = $customer_data[0];
            } else {
                $customer_data['pass_change'] = null;
            }
        }

        return view('online', compact('promo', 'customer_data'));
    }

    public function pressView()
    {
        $news = DB::table('press')->paginate(5);

        if (Session::has('customer_id')) {
            //get all unseen notification of this customer
            $allNotifications = (new functionController)->allNotifications(Session::get('customer_id'));
            session(['customerAllNotifications' => $allNotifications]);
        }
        if (Session::has('partner_id')) {
            //get all unseen notification of this partner
            $unseenNotifications = (new functionController)->partnerUnseenNotifications(Session::get('partner_id'));
            session(['unseenNotifications' => $unseenNotifications]);
        }

        return view('press', compact('news'));
    }

    //function for create review for user
    public function createReview(Request $request, $partner_account_id, $transaction_id)
    {
        //get values from form
        $star = $request->get('rate_star');
        $heading = $request->get('heading');
        $body = $request->get('content');
        $review_type = $request->get('review_type');

        $partner = PartnerAccount::where('partner_account_id', $partner_account_id)->first();

        if (strlen(trim(preg_replace('/\xc2\xa0/', ' ', $body))) == 0) {
            $heading = $body = null;
        }

        if ($star != null) {
            if (($heading != null && $body == null) || ($heading == null && $body != null)) {
                return redirect()->back()->with('try_again', 'Please try again!');
            }
        } else {
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        if (strlen($heading) > 70 || strlen($body) > 700) {
            return redirect()->back()->with('try_again', 'Please try again!');
        }
        if ($partner->active == 0) {
            return redirect()->back()->with('try_again', GlobalTexts::deactivated_partner_no_review);
        } else {
            if ($heading == null && $body == null && $star > 2) {//publish review without moderation
                $review = (new \App\Http\Controllers\Review\functionController())
                    ->saveReview($partner_account_id, \session('customer_id'), $star, $heading,
                        PlatformType::web, $body, $transaction_id, false, $review_type);
                (new \App\Http\Controllers\Review\functionController())->acceptReviewModeration($review->id, false);

                return redirect()->back()->with('reviewSubmitted', GlobalTexts::review_submitted_text);
            } else {//save review for moderation
                (new \App\Http\Controllers\Review\functionController())
                    ->saveReview($partner_account_id, \session('customer_id'), $star, $heading,
                        PlatformType::web, $body, $transaction_id, true, $review_type);

                return redirect()->back()->with('reviewSubmitted', GlobalTexts::review_moderation_text);
            }
        }
    }

    //function to send mail to reset Customer Username or Password
    public function resetPassword(Request $request)
    {
        $this->validate($request, [
            'reset_email' => 'required',
        ]);
        $email = $request->get('reset_email');
        //check if user with this email exists or not
        $user = DB::table('customer_info as ci')
            ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
            ->select('ci.customer_id', 'ci.customer_full_name')
            ->where('ci.customer_email', $email)
            ->get();
        $user = json_decode(json_encode($user), true);

        if (count($user) > 0) {
            $user_id = $user[0]['customer_id'];
            $name = $user[0]['customer_full_name'];
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
            $subject = 'Forgot pin';
            $message_text = 'Hello '.$name.','.'<br><br>';

            $message_text .= 'To reset your PIN please click on the button below.'.'<br>'.'<br>';

            $message_text .= '<a style="color: #fff;background-color: #007bff;padding: 8px;border-radius: .25rem;text-decoration: none;border: 1px solid transparent;" 
                        href="'.url('/reset/'.$reset_token).'">Reset Pin</a>'.'<br>'.'<br>';
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
                return redirect()->back()->with([
                    'password sent' => 'We have sent you an E-mail with a link to reset your PIN. This may take a minute and also don’t forget to check the spam folder. If you still haven’t received any E-mail, please re-enter your E-mail address and we will send you another mail.',
                ]);
            } else {
                return redirect()->back()->with([
                    'server_error' => 'Internal Server Error. Please try again',
                ]);
            }
        } else {
            return redirect()->back()->with([
                'email not exist' => 'Email does not exist!',
            ]);
        }
    }

    //function to view shared reviews social media in website
    public function shareReview($id)
    {
        //review info
        $reviews = DB::table('customer_info')
            ->join('review', 'review.customer_id', '=', 'customer_info.customer_id')
            ->join('partner_profile_images', 'review.partner_account_id', '=', 'partner_profile_images.partner_account_id')
            ->join('partner_info', 'review.partner_account_id', '=', 'partner_info.partner_account_id')
            ->select('customer_info.customer_profile_image', 'customer_info.customer_id', 'customer_info.customer_first_name', 'customer_info.customer_last_name', 'review.id', 'review.heading', 'review.comment', 'review.partner_reply', 'review.thumbs_up', 'review.rating', 'review.posted_on', 'partner_info.partner_account_id', 'partner_info.partner_name', 'partner_profile_images.partner_profile_image')
            ->where('review.id', $id)
            ->get()->toArray();
        $reviews = json_decode(json_encode($reviews), true);
        $reviews = $reviews[0];

        $num = DB::table('review')
            ->select('customer_id')
            ->where('customer_id', $reviews['customer_id'])
            ->get();
        $num = json_decode(json_encode($num), true);
        $number = count($num);

        return view('shared_review', compact('reviews', 'number'));
    }

    //function to save user's wish to database
    public function makeWish(Request $request)
    {
        $this->validate($request, [
            'comment' => 'required',
        ]);
        $comment = $request->get('comment');

        $wish = new Wish([
            'customer_id' => Session::get('customer_id'),
            'comment' => $comment,
            'posted_on' => date('Y-m-d H:i:s'),
        ]);
        $wish->save();
        (new \App\Http\Controllers\AdminNotification\functionController())->userWishNotification($wish);

        return redirect()->back()->with([
            'wish' => 'We got your feedback. Stay connected!',
        ]);
    }

    //function to image crop at the registration time
    public function imageCrop(Request $request)
    {
        $data = $_POST['image'];
        list($type, $data) = explode(';', $data);
        list(, $data) = explode(',', $data);
        $data = base64_decode($data);
        $imageName = time().'.jpg';
        Session::put('customer_profile_image_name', $imageName);
        Session::put('customer_profile_image', $data);

        echo 'Image Uploaded';
    }

    //function to verify phone before sending to facebook account kit
    public function verify_phone($phone)
    {
        $ifPhoneExist = DB::table('customer_info')
            ->where('customer_contact_number', $phone)
            ->count();
        if ($ifPhoneExist > 0) {
            //Session::forget('registration_url');
            return Response::json('exists');
        } else {
            return Response::json('does not exist');
        }
    }

    //
    public function verifyUserPhone($phone)
    {
        $ifPhoneExist = DB::table('customer_info')
            ->where('customer_contact_number', $phone)
            ->count();
        if ($ifPhoneExist > 0) {
            Session::forget('registration_url');

            return redirect(session('phoneVerification_url'))->with([
                'phone exist' => 'This phone number already exists. Please try another one.',
            ]);
        } else {
            return redirect(session('registration_url'));
        }
    }

    //function to verify phone before sending to facebook account kit
    public function cus_edit_verify_phone($phone)
    {
        $ifPhoneExist = DB::table('customer_info')
            ->where('customer_contact_number', $phone)
            ->count();
        if ($ifPhoneExist > 0) {
            //Session::forget('registration_url');
            return redirect('edit-profile')->with(
                'phone_exist',
                'This phone number already exists. Please try another one.'
            );
        } else {
            DB::table('customer_info')
                ->where('customer_id', Session::get('customer_id'))
                ->update([
                    'customer_contact_number' => $phone,
                ]);

            return redirect('edit-profile')->with(
                'phone_updated',
                'Phone number successfully updated'
            );
        }
    }

    //function to show user public profile
    public function userPublicProfile($username)
    {
        $userInfo = DB::table('customer_info as ci')
            ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
            ->where('ca.customer_username', $username)
            ->get();
        $userInfo = json_decode(json_encode($userInfo), true);
        $userInfo = $userInfo[0];
        //following list
        $following_list = (new functionController)->customerFollowingList($userInfo['customer_id']);
        $i = 0;
        foreach ($following_list['partner'] as $partner) {
            $main_branch = (new functionController)->mainBranchOfPartner($partner['partner_account_id']);
            $following_list['partner'][$i]['main_branch_id'] = $main_branch[0]->id;
            $i++;
        }
        //follower list
        $follower_list = (new functionController)->followerListOfCustomer($userInfo['customer_id']);
        //total likes this user got
        $totalLike = (new functionController)->likeNumber($userInfo['customer_id']);
        //total review number of this user
        $totalReview = (new functionController)->reviewNumber($userInfo['customer_id']);
        //all reviews of this user
        $reviews = (new functionController)->customerAllReviews($userInfo['customer_id']);
        //recent activity of this user
        $recentActivity = (new functionController)->recentActivity($userInfo['customer_id']);
        if (Session::has('customer_id')) {
            //get all unseen notification of this customer
            $allNotifications = (new functionController)->allNotifications(Session::get('customer_id'));
            session(['customerAllNotifications' => $allNotifications]);
        }
        if (Session::has('partner_id')) {
            //get all unseen notification of this partner
            $unseenNotifications = (new functionController)->partnerUnseenNotifications(Session::get('partner_id'));
            session(['unseenNotifications' => $unseenNotifications]);
        }
        if (Session::has('customer_id')) {
            $customer_data = DB::table('pass_changed')
                ->select('pass_change')
                ->where('customer_id', Session::get('customer_id'))
                ->get();
            $customer_data = json_decode(json_encode($customer_data), true);
            if ($customer_data) {
                $customer_data = $customer_data[0];
            } else {
                $customer_data['pass_change'] = null;
            }
        }
        //send all data to user public profile page
        return view('user-profile-view', compact(
            'userInfo',
            'following_list',
            'follower_list',
            'totalLike',
            'totalReview',
            'reviews',
            'recentActivity',
            'customer_data'
        ));
    }

    //function to show select card page with dynamic card prices
    public function selectCard()
    {
        $prices = AllAmounts::all();
        $cards = CardPrice::where([['platform', PlatformType::web], ['type', MembershipPriceType::buy]])
            ->orderBy('price', 'ASC')->get();
        $active_offers = BranchOffers::where('active', 1)->where('selling_point', null)->count();

        return view('select_card', compact('prices', 'cards', 'active_offers'));
    }

    //function to change profile image before buy card page
    public function buyCardEditImage($card_type)
    {
        //send all data to the respective edit profile page
        return view('buyCardEditImage', compact('card_type'));
    }

    //function to check refer code
    public function checkReferCode(Request $request)
    {
        $refer_from_input = $request->input('refer');
        $refer_exists = DB::table('customer_info')->where('referral_number', $refer_from_input)->count();
        $self_refer_code = DB::table('customer_info')
            ->select('referral_number')
            ->where('customer_id', Session::get('customer_id'))
            ->orWhere('customer_id', Session::get('cus_id_buy_card'))
            ->first();
        if ($refer_exists == 0) {
            return Response::json(2);
        } elseif ($refer_from_input == $self_refer_code->referral_number) {
            return Response::json(1);
        } else {
            return Response::json(0);
        }
    }

    //function to check card promo code
    public function cardPromoValidityCheck(Request $request)
    {
        $promo = $request->input('card_promo');
        $card_price = $request->input('card_price');
        $renew = $request->input('mem_type');
        $month = $request->input('month');

        $code_exists = CardPromoCodes::where('code', $promo)->first();

        if ($code_exists) {
            $count = CardPromoCodeUsage::where('promo_id', $code_exists->id)->count();
        }
        $today = date('Y-m-d');
        if (! $code_exists) {
            return \Illuminate\Support\Facades\Response::json(['result' => 'Invalid code.'], 201);
        } elseif ($code_exists->active != 1) {
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
                $promo_price = round($code_exists->flat_rate);
                $card_price -= $promo_price; //for admin
                return Response::json(['promo_id' => $code_exists->id, 'price'=>$card_price, 'promo_price' => $promo_price,
                    'seller' => $seller_info, ], 200);
            } elseif ($code_exists->type == 2) {
                $promo_price = round(($card_price * $code_exists->percentage) / 100);
                $card_price -= $promo_price; //for admin
                return Response::json(['promo_id' => $code_exists->id, 'price'=>$card_price, 'promo_price' => $promo_price,
                    'seller' => $seller_info, ], 200);
            } else {
                return Response::json(['result' => 'Something Went wrong.'], 201);
            }
        }
//
//        $final_price = 0;
//        $msg = '';
//        if (!$code_exists) {
//            $msg = 'Code does not exist';
//            $error = 1;
//        } elseif ($code_exists->active != 1) {
//            $msg = 'Invalid Promo Code';
//            $error = 1;
//        } elseif ($code_exists->expiry_date < $today) {
//            $msg = 'Code Expired';
//            $error = 1;
//        } elseif ($code_exists->usage != 'unlimited' && $code_exists->usage <= $count) {
//            $msg = 'You Can Not Use This Promo';
//            $error = 1;
//        } else {
//            $error = 0;
//            $seller_info = CardSellerInfo::where('promo_ids', 'like', "%\"{$code_exists->id}\"%")->first();
//            if ($card_price) {
//                if ($renew == PromoType::RENEW && $seller_info) {
//                    $result['error'] = 1;
//                    $result['message'] = 'This promo code is not applicable on renew.';
//                    $result['price'] = $card_price;
//                    return Response::json($result);
//                }
//
//                if ($code_exists->membership_type != PromoType::ALL && $renew != $code_exists->membership_type) {
//                    if ($code_exists->membership_type == PromoType::CARD_PURCHASE) {
//                        $message = 'This promo code will only work for new members only.';
//                    } else if ($code_exists->membership_type == PromoType::RENEW) {
//                        $message = 'This promo code will only work while renewing the membership only.';
//                    } else if ($code_exists->membership_type == PromoType::UPGRADE) {
//                        $message = 'This promo code will only work while upgrading the membership only.';
//                    } else if ($code_exists->membership_type == PromoType::TRIAL) {
//                        $message = 'This promo code will only work while activating the trial membership only.';
//                    } else {
//                        $message = "Invalid Code";
//                    }
//                    $result['error'] = 1;
//                    $result['message'] = $message;
//                    $result['price'] = $card_price;
//                    return Response::json($result);
//                }else if ($month && $code_exists->month && $code_exists->month != $month) {
//                    if ($month > 1) {
//                        $txt_month = 'months';
//                    } else {
//                        $txt_month = 'month';
//                    }
//
//                    $result['error'] = 1;
//                    $result['message'] = "This promo code is not applicable for " . $month . ' ' . $txt_month . ' membership.';
//                    $result['price'] = $card_price;
//                    return Response::json($result);
//                }
//
//                if ($code_exists->type == 1) {
//                    $final_price = $card_price - $code_exists->flat_rate;
//                    $result['promo_price'] = round($code_exists->flat_rate);
//                } elseif ($code_exists->type == 2) {
//                    $result['promo_price'] = round(($card_price * $code_exists->percentage) / 100);
//                    $final_price = $card_price - $result['promo_price'];
//                } else {
//                    $final_price = 'Do not cheat';
//                }
//            } else {
//                //this is for trial activation promo check
//                $result['error'] = $error;
//                $result['code'] = $code_exists;
//                $result['price'] = $card_price;
//                $result['seller'] = $seller_info;
//                return Response::json($result);
//            }
//        }
//        $result['error'] = $error;
//        $result['message'] = $msg;
//        $result['price'] = $final_price;
//        return Response::json($result);
    }

    //function to send OTP to seller at spot purchase
    public function sendSpotPurchaseSellerOTP(Request $request)
    {
        $phone = $request->input('phone');
        $verify = (new otpFunctionController())->sendPhoneVerification($phone, VerificationType::spot_purchase);
        if ($verify->status() == 201) {
            $data['otp_sent'] = false;
            $data['message'] = $verify->getData()->result;

            return Response::json(['result' => $data], 201);
        } else {
            $data['otp_sent'] = true;
            $data['message'] = $verify->getData()->result;

            return Response::json(['result' => $data], 200);
        }
    }

    //function to get delivery report as json format
    public function deliveryReport()
    {
        $delivery_report = DB::table('card_delivery as cd')
            ->join('customer_info as ci', 'ci.customer_id', '=', 'cd.customer_id')
            ->select('ci.customer_full_name', 'ci.customer_contact_number', 'cd.shipping_address')
            ->where('cd.delivery_type', 1)
            ->get();

        return $delivery_report;
    }

    //function to get single review details
    public function singleReviewDetails($id)
    {
        $review = DB::table('customer_info as ci')
            ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
            ->join('review as rev', 'rev.customer_id', '=', 'ci.customer_id')
            ->leftJoin('review_comment as rc', 'rc.review_id', '=', 'rev.id')
            ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'rev.partner_account_id')
            ->join('partner_info as pi', 'pi.partner_account_id', '=', 'ppi.partner_account_id')
            ->join('transaction_table as tt', 'tt.review_id', '=', 'rev.id')
            ->join('partner_branch as pb', 'pb.id', '=', 'tt.branch_id')
            ->select(
                'ci.customer_profile_image',
                'ci.customer_id',
                'ci.customer_first_name',
                'ci.customer_last_name',
                'ci.customer_type',
                'ca.customer_username',
                'rev.*',
                'pi.partner_account_id',
                'ppi.partner_profile_image',
                'pi.partner_name',
                'pb.partner_area'
            )
            ->where('rev.id', $id)
            ->get();
        $review = json_decode(json_encode($review), true);
        if (!$review){
            return null;
        }
        $comments = DB::table('review_comment')
            ->select('comment', 'comment_type', 'posted_on')
            ->where('review_id', $id)
            ->get();
        $comments = json_decode(json_encode($comments), true);
        $review[0]['comments'] = $comments;
        //total likes of a specific review
        $likes_of_a_review = DB::table('likes_review')->where('review_id', $id)->count();
        $review[0]['total_likes_of_a_review'] = $likes_of_a_review;

        if (Session::has('customer_id') || Session::has('partner_id')) {
            if (Session::has('customer_id')) {
                $liker_id = Session::get('customer_id');
            } else {
                $liker_id = Session::get('partner_id');
            }
            $previousLike = DB::table('likes_review')
                ->select('id as like_review_id', 'review_id', 'liker_id', 'liker_type')
                ->where('review_id', $id)
                ->where('liker_id', $liker_id)
                ->get();
            $previousLike = json_decode(json_encode($previousLike), true);
        }
        if (isset($previousLike) && count($previousLike) > 0) {
            if ($previousLike[0]['liker_type'] == LikerType::customer && $previousLike[0]['liker_id'] == Session::has('customer_id')) {
                $review[0]['liked'] = 1;
                $review[0]['source_id'] = $previousLike[0]['like_review_id'];
            } elseif ($previousLike[0]['liker_type'] == LikerType::partner && $previousLike[0]['liker_id'] == Session::has('partner_id')) {
                $review[0]['liked'] = 1;
                $review[0]['source_id'] = $previousLike[0]['like_review_id'];
            }
        } else {
            $review[0]['liked'] = 0;
            $review[0]['source_id'] = 0;
        }
        $review_link = url('review-share/'.(new functionController)->socialShareEncryption('encrypt', $id));
        $review[0]['review_link'] = $review_link;
        //recent reviews of this partners
        $recentReviews = DB::table('customer_info as ci')
            ->join('customer_account as ca', 'ca.customer_id', '=', 'ci.customer_id')
            ->join('review as rev', 'rev.customer_id', '=', 'ci.customer_id')
            ->join('partner_profile_images as ppi', 'ppi.partner_account_id', '=', 'rev.partner_account_id')
            ->join('partner_info as pi', 'pi.partner_account_id', '=', 'ppi.partner_account_id')
            ->select(
                'ci.customer_profile_image',
                'ci.customer_id',
                'ci.customer_first_name',
                'ci.customer_last_name',
                'ci.customer_type',
                'ca.customer_username',
                'rev.*',
                'ppi.partner_profile_image',
                'pi.partner_name'
            )
            ->where('rev.partner_account_id', $review[0]['partner_account_id'])
            ->where('rev.id', '!=', $review[0]['id'])
            ->where('rev.heading', '!=', 'n/a')
            ->where('rev.heading', '!=', null)
            ->orderBy('rev.posted_on', 'DESC')
            ->take(3)
            ->get()->toArray();
        $recentReviews = json_decode(json_encode($recentReviews), true);

        if ($review) {
            $review = $review[0];

            return ['review' => $review, 'recent_reviews' => $recentReviews, 'partner_name' => $review['partner_name'],
                'partner_area'=>$review['partner_area'], ];
        }
    }

    //function show single review page
    public function singleReview($id)
    {
        $decrypted_id = (new functionController)->socialShareEncryption('decrypt', $id);
        $singleReviewDetails = $this->singleReviewDetails($decrypted_id);
        if (!$singleReviewDetails) {
            return redirect('page-not-found');
        }
        $title = 'Royalty - '.$singleReviewDetails['partner_name'];

        return view('single-review-details', compact('singleReviewDetails', 'title'));
    }

    //function to get single post details
    public function singlePostDetails($id)
    {
        $post_details = Post::where('id', $id)->with('like')->first();

        //for previous like
        $previous_like = 0;
        $previous_like_id = 0;
        $post_likes = $post_details->like;
        if (Session::has('customer_id')) {
            foreach ($post_likes as $like) {
                if ($like->liker_id == Session::get('customer_id')) {
                    $previous_like_id = $like->id;
                    $previous_like = 1;
                    break;
                }
            }
        }
        $total_likes = LikePost::where('post_id', $post_details->id)->count();

        $post_details->previous_like = $previous_like;
        $post_details->previous_like_id = $previous_like_id;
        $post_details->total_likes = $total_likes;

        //get info of top brands partners
        $topBrands = (new functionController)->topBrands();

        return [['post_details' => $post_details, 'top_brands' => $topBrands]];
    }

    //function to post share count
    public function postShareCount(Request $request)
    {
        $post_id = $request->input('post_id');

        if (Session::has('customer_id')) {
            $sharer_id = Session::get('customer_id');
            $sharer_type = SharerType::customer;
        } elseif (Session::has('partner_id')) {
            $sharer_id = Session::get('partner_id');
            $sharer_type = SharerType::partner;
        } else {
            $sharer_id = 0;
            $sharer_type = SharerType::anonymous;
        }
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

    //function to get top referrer & reviewer
    public function topReferrals()
    {
        //trending offers
        $topReferrer = DB::table('customer_info')
            ->where('reference_used', '!=', 0)
            ->where('customer_type', '!=', 3)
            ->orderBy('reference_used', 'DESC')
            ->get();
        //all customers
        $allCustomers = DB::table('customer_info')
            ->where('customer_type', '!=', 3)
            ->get();
        $allCustomers = json_decode(json_encode($allCustomers), true);
        $i = 0;
        foreach ($allCustomers as $customer) {
            $totalReviews = (new functionController)->reviewNumber($customer['customer_id']);
            $totalLikes = (new functionController)->likeNumber($customer['customer_id']);
            $allCustomers[$i]['reviews'] = $totalReviews;
            $allCustomers[$i]['likes'] = $totalLikes;
            $allCustomers[$i]['total'] = $totalReviews + $totalLikes;
            $i++;
        }
        array_multisort(array_column($allCustomers, 'total'), SORT_DESC, $allCustomers);

        if (Session::has('customer_id')) {
            //get all unseen notification of this customer
            $allNotifications = (new functionController)->allNotifications(Session::get('customer_id'));
            session(['customerAllNotifications' => $allNotifications]);
        }
        if (Session::has('partner_id')) {
            //get all unseen notification of this partner
            $unseenNotifications = (new functionController)->partnerUnseenNotifications(Session::get('partner_id'));
            session(['unseenNotifications' => $unseenNotifications]);
        }
        //return search result
        return view('top_referrals', compact('topReferrer', 'allCustomers'));
    }

    public function show()
    {
        $obj = new functionController();
        echo $obj->showInfo('browser');
        echo $obj->showInfo('version');
        echo $obj->showInfo('os');
        echo $obj->showInfo('all');
    }

    //function to get partner list according to selected category
    public function sortingInOffersPage(Request $request)
    {
        $selected_category = $request->input('category');
        $allPartners = Categories::where('type', $selected_category)->with([
            'info.account', 'info.activeBranches', 'info.rating', 'info.profileImage',
            'info.reviews', 'info.activeBranches',
            'info.PartnerCategoryRelation',
        ])->first();
        $newArray = [];
        for ($i = 0; $i < count($allPartners->info); $i++) {
            if (count($allPartners->info[$i]->activeBranches) > 0 && $allPartners->info[$i]->account['active'] == 1) {
                $newArray[$i] = $allPartners->info[$i];
            }
        }
        $newArray = array_values($newArray);

        foreach ($newArray as $partner) {
            $partner->branches = $partner->activeBranches;
            $partner->offer_heading = (new functionController2)->partnerOfferHeading($partner->partner_account_id);
            $partner->location = (new functionController2())->getBranchLocations($partner->activeBranches);
            $partner->featured = (new \App\Http\Controllers\FeaturedPartners\functionController())
                ->isFeaturedPartner($partner->partner_account_id, $selected_category);
        }
        usort($newArray, function ($a, $b) {
            return $a['featured'] < $b['featured'];
        });

        return response()->json($newArray);
    }

    //function to get division wise area list
    public function getDivisionWiseArea(Request $request)
    {
        $division = $request->input('division');

        $areas = DB::table('partner_branch as pb')
            ->select('pb.partner_area as area_name', 'a.id as id')
            ->join('partner_account as pa', 'pa.partner_account_id', '=', 'pb.partner_account_id')
            ->join('area as a', 'a.area_name', '=', 'pb.partner_area')
            ->where('pb.active', 1)
            ->where('pa.active', 1)
            ->where('pb.partner_division', $division)
            ->groupBy('pb.partner_area')
            ->groupBy('a.id')
            ->get();

        return Response::json($areas);
    }

    //function to get location list for modal in offers page through ajax
    public function PartnerLocationsForModal(Request $request)
    {
        $partner_id = $request->input('partner_id');

        $partner_info = PartnerInfo::with('branches')->where('partner_account_id', $partner_id)->first();
        $pname = str_replace("'", '', $partner_info->partner_name);
        $output = '';
        foreach ($partner_info->branches as $branch) {
            if ($branch->active == 1) {
                $output .= '<a href="'.url('partner-profile/'.$pname.'/'.$branch->id).'"><li><span>'.
                    $branch->partner_address.'</span></li></a>';
            }
        }

        $result['name'] = 'Branches of '.$partner_info->partner_name;
        $result['locations'] = $output;

        return Response::json($result);
    }

    //function to show blog
    public function blog()
    {
        $blogs = BlogPost::active()->orderBy('priority', 'DESC')->orderBy('id', 'DESC')->paginate(5);

        $categories = BlogCategory::with('posts')->get();
        foreach ($categories as $key => $category) {
            if (count($category->posts) == 0) {
                unset($categories[$key]);
            }
        }
        $title = 'Royalty Blogs | royaltybd.com';

        return view('bloghome', compact('blogs', 'categories', 'title'));
    }

    //function to show category wise blog
    public function categoryBlog($category)
    {
        $blogs = DB::table('blog_post as bp')
            ->join('blog_category as bc', 'bc.id', '=', 'bp.category_id')
            ->select('bp.*')
            ->whereRaw('REPLACE (bc.category,"?","") = "'.$category.'"')
            ->orderBy('bp.id', 'DESC')
            ->paginate(5);

        $categories = BlogCategory::with('posts')->get();
        foreach ($categories as $key => $category) {
            if (count($category->posts) == 0) {
                unset($categories[$key]);
            }
        }
        $title = 'Blogs Categorised | royaltybd.com';

        return view('bloghome', compact('blogs', 'categories', 'title'));
    }

    //function to show single blog post
    public function singleBlogPost($heading)
    {
        $blog = BlogPost::whereRaw('REPLACE (heading,"?","") LIKE "%'.$heading.'%"')->first();
        if (!$blog) {
            return redirect('/');
        }
        $blog->increment('visit_count', 1);
        $categories = BlogCategory::with('posts')->get();

        foreach ($categories as $key => $category) {
            if (count($category->posts) == 0) {
                unset($categories[$key]);
            }
        }

        return view('blogpost', compact('blog', 'categories'));
    }

    //function to know partner password (only for the developers)
    public function partnerPass($username)
    {
        //get encrypted password from partner username
        $encrypted_password = DB::table('partner_account')
            ->select('password')
            ->where('username', $username)
            ->get();
        $encrypted_password = json_decode(json_encode($encrypted_password), true);
        if ($encrypted_password) {
            $encrypted_password = $encrypted_password[0];
            $decrypted_password = (new functionController)->encrypt_decrypt('decrypt', $encrypted_password['password']);
            dd($decrypted_password);
        }
    }

    //function to know partner branch password (only for the developers)
    public function partnerBranchPass($username)
    {
        //get encrypted password from partner branch username
        $encrypted_password = DB::table('partner_branch')
            ->select('password')
            ->where('username', $username)
            ->get();
        $encrypted_password = json_decode(json_encode($encrypted_password), true);
        if ($encrypted_password) {
            $encrypted_password = $encrypted_password[0];
            $decrypted_password = (new functionController)->encrypt_decrypt('decrypt', $encrypted_password['password']);
            dd($decrypted_password);
        }
    }

    //function to know user password (only for the developers)
    public function userPin($phone)
    {
        $phone = '+880'.$phone;
        //get encrypted password from partner username
        $encrypted_pin = DB::table('customer_account as ca')
            ->join('customer_info as ci', 'ci.customer_id', '=', 'ca.customer_id')
            ->select('ca.pin')
            ->where('ci.customer_contact_number', $phone)
            ->first();
        if ($encrypted_pin) {
            $decrypted_pin = (new functionController)->encrypt_decrypt('decrypt', $encrypted_pin->pin);
            dd($decrypted_pin);
        } else {
            dd('nothing');
        }
    }

    public function updateInfoFromSSLResponse()
    {
        $result = '{"amount": "809.00",
                               "bank_tran_id": "CZ48822019100310117",
                               "base_fair": "0.00",
                               "card_brand": "MASTERCARD",
                               "card_issuer": "UNITED COMMERCIAL BANK LIMITED BANGLADESH",
                               "card_issuer_country": "Bangladesh",
                               "card_issuer_country_code": "BD",
                               "card_no": "526238XXXXXX3726",
                               "card_type": "MASTER-City Bank",
                               "currency": "BDT",
                               "currency_amount": "809.00",
                               "currency_rate": "1.0000",
                               "currency_type": "BDT",
                               "risk_level": "0",
                               "risk_title": "Safe",
                               "status": "VALID",
                               "store_amount": "788.77",
                               "store_id": "royaltybdlive",
                               "tran_date": "2019-09-21 02:40:32",
                               "tran_id": "ROYALTYBD9WFoHpKThKe9hzs",
                               "val_id": "191003103105sum3n8OJQLhew",
                               "value_a": "Sarmin Akther",
                               "value_b": "",
                               "value_c": "+8801723024460",
                               "value_d": "ROYALTYBD"}';

        $result = json_decode($result, true);

        return 'Successfully updated all info from ssl response;';
    }

    public function appBlog()
    {
        $blogs = BlogPost::orderBy('id', 'DESC')->orderBy('id', 'DESC')->paginate(5);
        $categories = BlogCategory::with('posts')->get();
        foreach ($categories as $key => $category) {
            if (count($category->posts) == 0) {
                unset($categories[$key]);
            }
        }
        $title = 'Blog for app | royaltybd.com';

        return view('blog_app', compact('blogs', 'categories', 'title'));
    }

    public function categoryAppBlog($category)
    {
        $blogs = DB::table('blog_post as bp')
            ->join('blog_category as bc', 'bc.id', '=', 'bp.category_id')
            ->select('bp.*')
            ->whereRaw('REPLACE (bc.category,"?","") = "'.$category.'"')
            ->orderBy('bp.id', 'DESC')
            ->paginate(5);

        $categories = BlogCategory::with('posts')->get();
        foreach ($categories as $key => $category) {
            if (count($category->posts) == 0) {
                unset($categories[$key]);
            }
        }
        $title = 'Blog Categorised App | royaltybd.com';

        return view('blog_app', compact('blogs', 'categories', 'title'));
    }

    //function to show single blog post in app
    public function appBlogSingle($heading)
    {
        $blog = BlogPost::whereRaw('REPLACE (heading,"?","") LIKE "%'.$heading.'%"')->first();
        $blog->increment('visit_count', 1);
        $categories = BlogCategory::with('posts')->get();
        foreach ($categories as $key => $category) {
            if (count($category->posts) == 0) {
                unset($categories[$key]);
            }
        }

        return view('blog_app_single', compact('blog', 'categories'));
    }

    //function to check FB Id already exists or not
    public function checkFbId(Request $request)
    {
        $requested_fb_id = $request->input('requested_fb_id');
        $previous_fb1 = DB::table('social_id')->where('customer_social_id', $requested_fb_id)->where('customer_social_type', 'facebook')->count();
        $previous_fb2 = DB::table('info_at_buy_card')
            ->where('customer_social_id', $requested_fb_id)
            ->where('customer_social_type', 'facebook')
            ->where('delivery_type', 3)
            ->count();

        if ($previous_fb1 > 0 || $previous_fb2 > 0) {
            $fbId_exists = 1;
        } else {
            $fbId_exists = 0;
        }

        return Response::json($fbId_exists);
    }

    //function to verify phone before sending to facebook account kit
    public function verifyPhone(Request $request)
    {
        $phone = str_replace(' ', '', $request->input('phone'));
        $phone = str_replace('-', '', $phone);

        $previous_phone1 = DB::table('customer_info')
            ->where('customer_contact_number', $phone)
            ->count();
        $previous_phone2 = DB::table('info_at_buy_card')
            ->where('customer_contact_number', $phone)
            ->where('delivery_type', 3)
            ->count();
        $previous_phone3 = DB::table('partner_branch')
            ->where('partner_mobile', $phone)
            ->count();
        if ($previous_phone1 > 0 || $previous_phone2 > 0 || $previous_phone3 > 0) {
            $phone_exists = 1;
        } else {
            $phone_exists = 0;
        }

        return Response::json($phone_exists);
    }

    //Get phone number from facebook
    public function getPhoneFromFB()
    {
        error_reporting(0);

        define('FB_ACCOUNT_KIT_APP_ID', '149014475722955');
        define('FB_ACCOUNT_KIT_APP_SECRET', 'c51c702a1014ab4b3a6bb08c04c8d811');

        $code = $_POST['code'];
        $csrf = $_POST['csrf'];

        $auth = file_get_contents('https://graph.accountkit.com/v1.1/access_token?grant_type=authorization_code&code='.$code.'&access_token=AA|'.FB_ACCOUNT_KIT_APP_ID.'|'.FB_ACCOUNT_KIT_APP_SECRET);

        $access = json_decode($auth, true);

        if (empty($access) || ! isset($access['access_token'])) {
            return ['status' => 2, 'message' => 'Unable to verify the phone number.'];
        }

        //App scret proof key Ref : https://developers.facebook.com/docs/graph-api/securing-requests
        $appsecret_proof = hash_hmac('sha256', $access['access_token'], FB_ACCOUNT_KIT_APP_SECRET);

        //echo 'https://graph.accountkit.com/v1.1/me/?access_token='. $access['access_token'];
        $ch = curl_init();

        // Set query data here with the URL
        curl_setopt($ch, CURLOPT_URL, 'https://graph.accountkit.com/v1.1/me/?access_token='.$access['access_token'].'&appsecret_proof='.$appsecret_proof);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, '4');
        $resp = trim(curl_exec($ch));

        curl_close($ch);

        $info = json_decode($resp, true);

        if (empty($info) || ! isset($info['phone']) || isset($info['error'])) {
            return ['status' => 2, 'message' => 'Unable to verify the phone number.'];
        }

        $prefix = $info['phone']['country_prefix'];
        $number = $info['phone']['national_number'];

        //{"id":"22763240XXXXXXX","phone":{"number":"+88017XXXXXXXX","country_prefix":"880","national_number":"179XXXXXXX"},"application":{"id":"149014475XXXXXXX"}}

        return Response::json(['prefix' => $prefix, 'number' => $number]);
    }

    //function to check registration email if already exists or not
    public function checkRegEmail(Request $request)
    {
        $email = $request->input('email');
        $previous_email1 = DB::table('customer_info')
            ->where('customer_email', $email)
            ->count();
        $delivery_types = [3, 4, 6, 7];
        $previous_email2 = DB::table('info_at_buy_card')
            ->where('customer_email', $email)
            ->whereIn('delivery_type', $delivery_types)
            ->count();
        if ($previous_email1 > 0 || $previous_email2 > 0) {
            $email_exists = 1;
        } else {
            $email_exists = 0;
        }

        return Response::json($email_exists);
    }

    //function to check registration username if already exists or not
    public function checkRegUsername(Request $request)
    {
        $username = $request->input('username');
        $previous_username1 = DB::table('customer_account')
            ->where('customer_username', $username)
            ->count();
        $previous_username2 = DB::table('info_at_buy_card')
            ->where('customer_username', $username)
            ->where('delivery_type', 3)
            ->count();
        if ($previous_username1 > 0 || $previous_username2 > 0) {
            $username_exists = 1;
        } else {
            $username_exists = 0;
        }

        return Response::json($username_exists);
    }

    public function referLeaderboard()
    {
        $referrer_ids = CustomerInfo::where('referrer_id', '!=', null)
            ->where('member_since', 'like', date('Y-m').'%')
            ->get()->unique('referrer_id')->pluck('referrer_id');
        $data = CustomerInfo::whereIn('customer_id', $referrer_ids)
            ->with('latestSSLTransaction.cardDelivery')
            ->orderBy('reference_used', 'DESC')->get();
        $data = $data->take(10);

        return view('refer_leaderboard', compact('data'));
    }
}/*controller ends*/
