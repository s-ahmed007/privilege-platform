<?php

namespace App\Http\Controllers\Membership;

use App\CardPrice;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\MembershipPriceType;
use App\Http\Controllers\Enum\PlatformType;
use Illuminate\Http\Request;

class membershipPriceController extends Controller
{
    public function membershipPrices()
    {
        $membership = CardPrice::orderBy('month', 'ASC')->orderBy('type', 'ASC')->get();

        return view('admin.production.membership_prices', compact('membership'));
    }

    public function addMembershipPrice(Request $request)
    {
        $type = $request->get('membership_price_type');
        $platform = $request->get('platform');
        $price = $request->get('card_price');
        $month = $request->get('card_duration');
        $membership_title = $request->get('membership_title');

        if ($price == 0 && $type == MembershipPriceType::renew) {
            return redirect()->back()->with('mem_plan_add_fail', 'Renew price can not be zero.');
        }
        $exists = CardPrice::where([['platform', $platform], ['type', $type], ['month', $month]])->count();
        if ($exists > 0) {
            return redirect()->back()->with('mem_plan_add_fail', 'This plan already exists.');
        } else {
            $card_price = new CardPrice();
            $card_price->platform = $platform;
            $card_price->type = $type;
            $card_price->month = $month;
            $card_price->price = $price;
            $card_price->title = $membership_title;
            $card_price->save();
        }

        return redirect()->back()->with('mem_plan_add_success', 'Membership plan added');
    }

    public function updateMembershipPrice(Request $request)
    {
        $web_prices = $request->get('web_prices');
        $web_validity = $request->get('web_validity');
        $renew_web_prices = $request->get('renew_web_prices');
        $renew_web_validity = $request->get('renew_web_validity');

        if ($web_prices) {
            for ($i = 0; $i < count($web_prices); $i++) {
                CardPrice::where([['platform', PlatformType::web], ['type', 0], ['month', $web_validity[$i]]])->update(['price'=>$web_prices[$i]]);
            }
        }
        if ($renew_web_prices) {
            for ($i = 0; $i < count($renew_web_prices); $i++) {
                CardPrice::where([['platform', PlatformType::web], ['type', 1], ['month', $renew_web_validity[$i]]])->update(['price'=>$renew_web_prices[$i]]);
            }
        }

        $android_prices = $request->get('android_prices');
        $android_validity = $request->get('android_validity');
        $renew_android_prices = $request->get('renew_android_prices');
        $renew_android_validity = $request->get('renew_android_validity');

        if ($android_prices) {
            for ($i = 0; $i < count($android_prices); $i++) {
                CardPrice::where([['platform', PlatformType::android], ['type', 0], ['month', $android_validity[$i]]])->update(['price'=>$android_prices[$i]]);
            }
        }
        if ($renew_android_prices) {
            for ($i = 0; $i < count($renew_android_prices); $i++) {
                CardPrice::where([['platform', PlatformType::android], ['type', 1], ['month', $renew_android_validity[$i]]])->update(['price'=>$renew_android_prices[$i]]);
            }
        }

        $ios_prices = $request->get('ios_prices');
        $ios_validity = $request->get('ios_validity');
        $renew_ios_prices = $request->get('renew_ios_prices');
        $renew_ios_validity = $request->get('renew_ios_validity');

        if ($ios_prices) {
            for ($i = 0; $i < count($ios_prices); $i++) {
                CardPrice::where([['platform', PlatformType::ios], ['type', 0], ['month', $ios_validity[$i]]])->update(['price'=>$ios_prices[$i]]);
            }
        }
        if ($renew_ios_prices) {
            for ($i = 0; $i < count($renew_ios_prices); $i++) {
                CardPrice::where([['platform', PlatformType::ios], ['type', 1], ['month', $renew_ios_validity[$i]]])->update(['price'=>$renew_ios_prices[$i]]);
            }
        }

        return redirect('admin/membership_prices')->with('update_success', 'Membership plan updated successfully');
    }

    public function deleteMembershipPrice($id)
    {
        CardPrice::where('id', $id)->forceDelete();

        return redirect('admin/membership_prices')->with('update_success', 'Membership plan deleted successfully');
    }

    public function getMembershipPrices($platform, $type)
    {
        return CardPrice::where('platform', $platform)->where('type', $type)->orderBy('month', 'ASC')->get();
    }
}
