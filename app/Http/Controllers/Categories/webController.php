<?php

namespace App\Http\Controllers\Categories;

use App\AllAmounts;
use App\BlogPost;
use App\Categories;
use App\Helpers\LengthAwarePaginator;
use App\Http\Controllers\Categories\functionController as catFunctionController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\functionController;
use App\Http\Controllers\functionController2;
use App\Http\Controllers\jsonController;
use App\PartnerAccount;
use App\PartnerBranch;
use App\Post;
use App\Rating;
use App\SubCat1;
use App\SubCat2;
use App\TopBrands;
use App\TrendingOffers;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class webController extends Controller
{
    public function getDivisionArea()
    {
        //get divisions and area
        $divisions = DB::table('partner_branch as pb')
            ->select('pb.partner_division as name', 'd.id as id')
            ->join('partner_account as pa', 'pa.partner_account_id', '=', 'pb.partner_account_id')
            ->join('division as d', 'd.name', '=', 'pb.partner_division')
            ->where('pb.active', 1)
            ->where('pa.active', 1)
            ->groupBy('pb.partner_division')
            ->groupBy('d.id')
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

        return $divisions;
    }

    public function getPaginatedPartners($all_partners)
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $col = new Collection($all_partners);
        $perPage = 20;
        $currentPageSearchResults = $col->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $partners = new LengthAwarePaginator($currentPageSearchResults, count($col), $perPage, $currentPage, ['path' => LengthAwarePaginator::resolveCurrentPath()]);

        return $partners;
    }

    public function getAllCatWisePartners()
    {
        $selected_category = 'all';
        $categories = Categories::all();
        // $all_partners = (new catFunctionController)->getAllMainCatPartners();
        $newArray = (new \App\Http\Controllers\FeaturedPartners\functionController())->getPartners($selected_category);

        $partners = (new functionController2())->getPaginatedData($newArray, 20);
        // dd($partners->toArray());
        $divisions = $this->getDivisionArea();
        $sub_cat_1 = 'show';
        $sub_cat_2 = 'hide';

        return view('offers.all_cat_offers', compact(
            'partners',
            'categories',
            'divisions',
            'sub_cat_1',
            'sub_cat_2'
        ));
    }

    public function getMainCatWisePartners($main_cat)
    {
        $categories = Categories::all();
        $selected_category = $categories->where('type', $main_cat)->first();
        $newArray = (new \App\Http\Controllers\FeaturedPartners\functionController())->getPartners($main_cat);

        $partners = (new functionController2())->getPaginatedData($newArray, 20);

        $all_sub_cats_1 = (new catFunctionController())->getSubCats($selected_category->id);

        $divisions = $this->getDivisionArea();
        $sub_cat_1 = 'show';
        $sub_cat_2 = 'hide';

        return view('offers.main_cat_offers', compact(
            'partners',
            'selected_category',
            'categories',
            'divisions',
            'all_sub_cats_1',
            'sub_cat_1',
            'sub_cat_2'
        ));
    }

    public function subCat1WisePartner($main_cat, $sub_cat_1)
    {
        $categories = Categories::all();
        $selected_category = $categories->where('type', $main_cat)->first();

//        $all_sub_cats_1 = (new catFunctionController())->getSubCats($selected_category->id);
        $sub_cat_1_id = SubCat1::where('cat_name', $sub_cat_1)->first()->id;
        $all_sub_cats_2 = (new catFunctionController())->getSecondSubCats($selected_category->id, $sub_cat_1_id);

        $newArray = (new \App\Http\Controllers\FeaturedPartners\functionController())->getPartners($main_cat);

        $partners = (new functionController2())->getPaginatedData($newArray, 20);

        $divisions = $this->getDivisionArea();
        $sub_cat_2 = 'show';

        return view('offers.sub_cat_1_offers', compact(
            'partners',
            'selected_category',
            'categories',
            'divisions',
            'all_sub_cats_2',
            'sub_cat_1',
            'sub_cat_2'
        ));
    }

    public function subCat2WisePartner($main_cat, $sub_cat_1, $sub_cat_2)
    {
        $categories = Categories::all();
        $selected_category = $categories->where('type', $main_cat)->first();

//        $all_sub_cats_1 = (new catFunctionController())->getSubCats($selected_category->id);
//        $all_sub_cats_2 = (new catFunctionController())->getSecondSubCats($selected_category->id,
//            $all_sub_cats_1->where('cat_name', $sub_cat_1)->first()->id);
        $sub_cat_1_id = SubCat1::where('cat_name', $sub_cat_1)->first()->id;
        $sub_cat_2_id = SubCat2::where('cat_name', $sub_cat_2)->first()->id;

        $newArray = (new \App\Http\Controllers\FeaturedPartners\functionController())->getPartners($main_cat);

        $partners = (new functionController2())->getPaginatedData($newArray, 20);

        $divisions = $this->getDivisionArea();

        return view('offers.sub_cat_2_offers', compact(
            'partners',
            'selected_category',
            'categories',
            'divisions',
            'sub_cat_1',
            'sub_cat_2'
        ));
    }

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
            ->get();
        $trending = TrendingOffers::all();
        $trending_collection = collect();
        foreach ($trending as $item) {
            foreach ($trendingOffers as $trendingOffer) {
                if ($item->partner_account_id == $trendingOffer->partner_account_id) {
                    $trending_collection->push($trendingOffer);
                }
            }
        }

        $trendingOffers = (new functionController)->partnerOffers($trending_collection);

        $i = 0;
        foreach ($trendingOffers as $offer) {
            $branchOffers = (new functionController)->branchOffers($offer->main_branch_id);
            $trendingOffers[$i]->branch_offers = $branchOffers;
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
            ->get();
        $top = TopBrands::all();
        $top_collection = collect();
        foreach ($top as $item) {
            foreach ($topBrands as $topBrand) {
                if ($item->partner_account_id == $topBrand->partner_account_id) {
                    $top_collection->push($topBrand);
                }
            }
        }
        $topBrands = (new functionController)->partnerOffers($top_collection);
        $i = 0;
        foreach ($topBrands as $offer) {
            $branchOffers = (new functionController)->branchOffers($offer->main_branch_id);
            $topBrands[$i]->branch_offers = $branchOffers;
            $i++;
        }

        $visited_partners = [];
        if (Session::has('customer_id')) {
            //get all unseen notification of this customer
            $allNotifications = (new functionController)->allNotifications(Session::get('customer_id'));
            session(['customerAllNotifications' => $allNotifications]);
            $visited_profile = (new functionController)->recentlyVisitedProfile(Session::get('customer_id'));
        }
        $categories = Categories::all();
        //blog posts
        $blogPosts = BlogPost::orderBy('id', 'DESC')->get();

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
                $branchOffers = (new functionController)->branchOffers($main_branch_info[0]->id);
                $rating = Rating::where('partner_account_id', $topPartners[$i]['partner_account_id'])->first();
                $topPartners[$i]['branch_offers'] = $branchOffers;
                $topPartners[$i]['offers'] = $offers_count;
                $topPartners[$i]['main_branch_id'] = $main_branch_info[0]->id;
                $topPartners[$i]['average_rating'] = $rating->average_rating;
            }
            $i++;
        }

        $card_prices = AllAmounts::all();
        $title = 'Royalty BD - Discover offers and discounts';

        $carousel_images = (new jsonController())->getCarousalImages();
        //dd($trendingOffers);

        return view('index_copy', compact(
            'trendingOffers',
            'topBrands',
            'categories',
            'topPartners',
            'blogPosts',
            'newsPosts',
            'card_prices',
            'visited_profile',
            'title',
            'carousel_images'
        ));
    }
}
