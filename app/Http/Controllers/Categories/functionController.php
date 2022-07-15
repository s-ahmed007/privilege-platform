<?php

namespace App\Http\Controllers\Categories;

use App\Categories;
use App\CategoryRelation;
use App\Http\Controllers\Controller;
use App\Http\Controllers\JsonControllerV2;
use App\PartnerCategoryRelation;
use App\PartnerGalleryImages;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class functionController extends Controller
{
    public function getCategories()
    {
        return Categories::orderBy('priority', 'DESC')->get();
    }

    public function getSubCats($main_cat_id)
    {
        $sub_1 = [];
        $i = 0;
        $main_cats = CategoryRelation::where('main_cat', $main_cat_id)->with('sub_cat_1')->get();
        foreach ($main_cats as $cat) {
            if ($cat->sub_cat_1) {
                $sub_1[$i++] = $cat->sub_cat_1;
            }
        }
        $sub_1 = Collection::make($sub_1)->unique();

        return $sub_1;
    }

    public function getSecondSubCats($main_cat_id, $sub_cat_id)
    {
        $sub_2 = [];
        $i = 0;
        $cats = CategoryRelation::where([['main_cat', $main_cat_id], ['sub_cat_1_id', $sub_cat_id]])->with('sub_cat_2')->get();
        foreach ($cats as $cat) {
            if ($cat->sub_cat_2) {
                $sub_2[$i++] = $cat->sub_cat_2;
            }
        }
        $sub_2 = Collection::make($sub_2)->unique();

        return $sub_2;
    }

    public function getAllMainCatPartners($main_cat_id = null)
    {
        $cat_rel = CategoryRelation::where('main_cat', $main_cat_id)->select('id')->get();
        if ($main_cat_id) {
            return $this->getPartnersByCategoryRelation($cat_rel);
        } else {
            return $this->getPartnersByCategoryRelation();
        }
    }

    public function getAllSubCatPartners($main_cat_id, $cat_id)
    {
        $cat_rel = CategoryRelation::where([['main_cat', $main_cat_id], ['sub_cat_1_id', $cat_id]])->get();

        return $this->getPartnersByCategoryRelation($cat_rel);
    }

    public function getAllSecondSubCatPartners($main_cat_id, $sub_cat_1, $sub_cat_2)
    {
        $cat_rel = CategoryRelation::where([['main_cat', $main_cat_id], ['sub_cat_1_id', $sub_cat_1], ['sub_cat_2_id', $sub_cat_2]])->get();

        return $this->getPartnersByCategoryRelation($cat_rel);
    }

    public function getPartnersByCategoryRelation($cat_rel = null)
    {
        $partners = [];
        $i = 0;
        if ($cat_rel) {
            foreach ($cat_rel as $cat) {
                $part_cats = PartnerCategoryRelation::where('cat_rel_id', $cat->id)->with(['info.profileImage',
                        'branches' => function ($query) {
                            $query->active();
                        },
                        'info.account' => function ($query) {
                            $query->where('active', 1);
                        }, ]
                )->get();
                foreach ($part_cats as $part_cat) {
                    if ($part_cat->info->account) {
                        $part_cat->info->pinned_gallery = $this->getPinnedGallery($part_cat->partner_id);
                        $part_cat->info->branches = $part_cat->branches;
                        foreach ($part_cat->info->branches as $branch) {
                            $offers = (new JsonControllerV2())->activeOffers($branch->id, null);
                            $offers = Collection::make($offers)->unique();
                            $branch->offers = $offers;
                        }
                        $partners[$i++] = $part_cat->info;
                    }
                }
            }
        } else {
            $part_cats = PartnerCategoryRelation::with(['info.profileImage',
                    'branches' => function ($query) {
                        $query->active();
                    },
                    'info.account' => function ($query) {
                        $query->where('active', 1);
                    }, ]
            )->get();
            foreach ($part_cats as $part_cat) {
                if ($part_cat->info->account) {
                    $part_cat->info->pinned_gallery = $this->getPinnedGallery($part_cat->partner_id);
                    $part_cat->info->branches = $part_cat->branches;
                    foreach ($part_cat->info->branches as $branch) {
                        $offers = (new JsonControllerV2())->activeOffers($branch->id, null);
                        $offers = Collection::make($offers)->unique();
                        $branch->offers = $offers;
                    }
                    $partners[$i++] = $part_cat->info;
                }
            }
        }

        $partners = Collection::make($partners)->unique();

        return $partners;
    }

    public function getPinnedGallery($partner_account_id)
    {
        $pinned_gallery = PartnerGalleryImages::where('partner_account_id', $partner_account_id)->where('pinned', 1)->first();

        return $pinned_gallery;
    }
}
