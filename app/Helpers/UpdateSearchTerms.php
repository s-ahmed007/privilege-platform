<?php

namespace App\Helpers;

use App\Http\Controllers\functionController2;
use App\PartnerInfo;
use App\SearchTerm;

class UpdateSearchTerms
{
    public function updateSearchTerms()
    {
        $partners = PartnerInfo::with('profileImage', 'activeBranches.offers', 'rating')->get();
        $partners = $partners->where('account.active', 1);
        $partners = $partners->whereNotNull('activeBranches');

        foreach ($partners as $key => $partner) {
            if (count($partner->branches) > 0) {
                $images = [
                    'logo' => $partner->profileImage->partner_profile_image,
                    'cover_image' => $partner->profileImage->partner_cover_photo
                ];
                $area = '';
                $branches = [];
                $i=0;
                foreach ($partner->branches as $branch) {
                    if (++$i == count($partner->branches)) {
                        $area .= $branch->partner_area;
                    } else {
                        $area .= $branch->partner_area.', ';
                    }
                    $brnc = [
                        'id' => $branch->id,
                        'location' => $branch->partner_location
                    ];
                    array_push($branches, $brnc);
                }
                $offers = (new functionController2())->partnerOfferHeading($partner->partner_account_id);
                $searchTerm = SearchTerm::where('partner_id', $partner->partner_account_id)->first();

                if ($searchTerm) {
                    $searchTerm->name = $partner->partner_name;
                    $searchTerm->area = $area;
                    $searchTerm->images = json_encode($images);
                    $searchTerm->rating = $partner->rating->average_rating;
                    $searchTerm->offer = $offers;
                    $searchTerm->branches = json_encode($branches);
                    $searchTerm->save();
                } else {
                    $searchTerm = new SearchTerm();
                    $searchTerm->partner_id = $partner->partner_account_id;
                    $searchTerm->name = $partner->partner_name;
                    $searchTerm->area = $area;
                    $searchTerm->images = json_encode($images);
                    $searchTerm->rating = $partner->rating->average_rating;
                    $searchTerm->offer = $offers;
                    $searchTerm->branches = json_encode($branches);
                    $searchTerm->save();
                }
            } else {
                SearchTerm::where('partner_id', $partner->partner_account_id)->delete();
            }
        }
    }
}