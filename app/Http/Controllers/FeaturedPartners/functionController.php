<?php

namespace App\Http\Controllers\FeaturedPartners;

use App\Categories;
use App\FeaturedDeals;
use App\Http\Controllers\Controller;
use App\Http\Controllers\functionController2;
use App\PartnerAccount;
use App\PartnerInfo;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class functionController extends Controller
{
    public function addFeaturedPartner($partner_account_id, $cat_id, $order_num)
    {
        $feature_partner = new FeaturedDeals();
        $feature_partner->partner_account_id = $partner_account_id;
        $feature_partner->category_id = $cat_id;
        $feature_partner->order_num = $order_num;
        $feature_partner->save();

        return $feature_partner;
    }

    public function editFeaturedPartner($partner_account_id, $cat_id, $order_num, $ft_id)
    {
        $feature_partner = FeaturedDeals::find($ft_id);
        $feature_partner->partner_account_id = $partner_account_id;
        $feature_partner->category_id = $cat_id;
        $feature_partner->order_num = $order_num;
        $feature_partner->save();

        return $feature_partner;
    }

    public function deleteFeaturePartner($ft_id)
    {
        $feature_partner = FeaturedDeals::find($ft_id);
        $feature_partner->delete();

        return 'Deleted';
    }

    public function getAllPartners()
    {
        $featuredPartners = FeaturedDeals::where('category_id', null)->orderBy('order_num', 'ASC')->get();
        $partner_info = PartnerInfo::all();
        $feature_partner_ids = Arr::pluck($featuredPartners, 'partner_account_id');
        $featured_partners = collect($feature_partner_ids)->map(function ($id) {
            return PartnerInfo::where('partner_account_id', $id)->withCount('reviews')->first();
        });
        $featured_partners = $featured_partners->where('account.active', 1);

        $partner_ids = Arr::pluck($partner_info->whereNotIn('partner_account_id', $feature_partner_ids), 'partner_account_id');
        $partners = PartnerInfo::whereIn('partner_account_id', $partner_ids)->withCount('reviews')
            ->orderBy('partner_account_id', 'DESC')
            ->get();
        $partners = collect($partners)->where('account.active', 1);

        return $this->getMappedData($featured_partners->merge($partners), 'all');
    }

    public function getCategorizedPartners($type)
    {
        $category = Categories::where('type', $type)->first();
        $feature_partner_ids = Arr::pluck($category->featuredPartners, 'partner_account_id');
        $featured_partners = collect($feature_partner_ids)->map(function ($id) {
            return PartnerInfo::where('partner_account_id', $id)->withCount('reviews')->first();
        });
        $featured_partners = $featured_partners->where('account.active', 1);

        $partner_ids = Arr::pluck($category->info->whereNotIn('partner_account_id', $feature_partner_ids), 'partner_account_id');
        $partners = PartnerInfo::whereIn('partner_account_id', $partner_ids)->withCount('reviews')
            ->orderBy('partner_account_id', 'DESC')
            ->get();
        $partners = collect($partners)->where('account.active', 1);

        return $this->getMappedData($featured_partners->merge($partners), $type);
    }

    public function getPartners($type)
    {
        if ($type == 'all') {
            $data = $this->getAllPartners();
        } else {
            $data = $this->getCategorizedPartners($type);
        }

        return $data;
    }

    public function getMappedData($partners, $type)
    {
        return $partners->map(function ($item) use ($type) {
            return [
                'partner_account_id' => $item->partner_account_id,
                'partner_name' => $item->partner_name,
                'partner_profile_image' => $item->profileImage->partner_profile_image,
                'partner_cover_photo' => $item->profileImage->partner_cover_photo,
                'partner_type' => $item->partner_type,
                'average_rating' => $item->rating->average_rating,
                'partner_gallery_image' => $item->profileImage->partner_cover_photo,
                'offer_count' => $this->getActiveOfferCount($item->branches),
                'offer_heading' => (new functionController2())->partnerOfferHeading($item->partner_account_id),
                'branches' => $item->activeBranches,
                'total_reviews' => $item->reviews_count,
                'featured' => $this->isFeaturedPartner($item->partner_account_id, $type),
                'locations' => (new functionController2())->getBranchLocations($item->activeBranches),
            ];
        });
    }

    public function isFeaturedPartner($partner_account_id, $type)
    {
        if ($type == 'all') {
            $featured = FeaturedDeals::where('partner_account_id', $partner_account_id)
                ->where('category_id', null)
                ->first();
        } else {
            $category = Categories::where('type', $type)->first();
            $featured = FeaturedDeals::where('partner_account_id', $partner_account_id)
                ->where('category_id', $category->id)
                ->first();
        }
        if ($featured) {
            return true;
        } else {
            return false;
        }
    }

    public function getActiveOfferCount($branches)
    {
        $date = date('d-m-Y');
        $offers = 0;
        foreach ($branches as $branch) {
            foreach ($branch->offers as $key => $offer) {
                $offer_date = $offer['date_duration'][0];
                $offer['date_duration'] = $offer['date_duration'][0];
                $offer['weekdays'] = $offer['weekdays'][0];
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

        return $offers;
    }

    public function featuredPartners()
    {
        $featuredList = FeaturedDeals::with('partner')->orderBy('order_num', 'ASC')->get();
        //get all partners
        $allPartners = PartnerAccount::where('active', 1)->with('info.branches')->get();
        foreach ($allPartners as $key => $partner) {
            if (count($partner->info->branches) == 0) {
                unset($allPartners[$key]);
            }
        }
        $categories = Categories::orderBy('priority', 'DESC')->get();

        return view('admin.production.featuredPartner', compact('allPartners', 'featuredList', 'categories'));
    }

    public function addFeaturedPartners(Request $request, $id)
    {
        $partner_id = $request->get('partner');
        $cat_id = $id == 'all' ? null : $id;
        $exists = FeaturedDeals::where('partner_account_id', $partner_id)->where('category_id', $cat_id)->count();
        if ($exists == 0) {
            $this->addFeaturedPartner($partner_id, $cat_id, 0);

            return redirect()->back()->with('success', 'New partner added successfully');
        } else {
            return redirect()->back()->with('failed', 'Partner already exists');
        }
    }

    public function removeFeaturedPartners($id)
    {
        $this->deleteFeaturePartner($id);

        return redirect()->back()->with('success', 'Partner removed from featured');
    }

    public function updateFeaturedOrder(Request $request)
    {
        $ids = explode(',', $request->input('sort_order'));
        for ($i = 0; $i < count($ids); $i++) {
            $featured = FeaturedDeals::find($ids[$i]);
            $featured->order_num = $i + 1;
            $featured->save();
        }

        return \response()->json($ids);
    }
}
