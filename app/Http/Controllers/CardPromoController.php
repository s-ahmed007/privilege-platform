<?php

namespace App\Http\Controllers;

use App\CardPromoCodes;
use App\CardPromoCodeUsage;
use App\CardPromoType;
use App\CardSellerAccount;
use App\CardSellerInfo;
use App\InfluencerPayment;
use App\Rules\unique_if_changed;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CardPromoController extends Controller
{
    public function index()
    {
        $promos = cardPromoCodes::orderBy('id', 'DESC')->with('userInfo')->withCount('promoUsage')->get();
        foreach ($promos as $promo) {
            $seller_info = CardSellerInfo::where('promo_ids', 'like', "%\"{$promo->id}\"%")->first();
            $promo->seller = null;
            if ($seller_info) {
                $promo->seller = $seller_info->first_name.' '.$seller_info->last_name;
            }
        }

        return view('admin.production.cardPromo.index', compact('promos'));
    }

    public function create()
    {
        $sellers = CardSellerInfo::with('account')->get();

        return view('admin.production.cardPromo.create', compact('sellers'));
    }

    public function store(Request $request)
    {
        if ($request->flat_rate != null && $request->percentage != null) {
            return Redirect()->back()->with('multiple-promo', 'Multiple promo type requested!');
        }

        if ($request->flat_rate == null && $request->percentage == null) {
            return Redirect()->back()->with('no-promo', 'Please enter at least one promo type!');
        }

        if ($request->flat_rate != null) {
            $promo_type = 1;
        } elseif ($request->percentage != null) {
            $promo_type = 2;
        }

        $request->validate([
            'mem_type' => 'required',
            'code' => 'required|unique:card_promo,code',
            'text' => 'required',
            'expiry_date' => 'required',
            'usage' => 'required',
        ]);

        try {
            DB::beginTransaction(); //to do query rollback

            $promo = new cardPromoCodes;
            $promo->code = $request->code;
            $promo->text = $request->text;
            $promo->flat_rate = $request->flat_rate;
            $promo->percentage = $request->percentage;
            $promo->expiry_date = $request->expiry_date;
            $promo->active = 1;
            $promo->type = $promo_type;
            $promo->usage = strtolower($request->usage);
            $promo->influencer_id = $request->influencer_id;
            $promo->membership_type = $request->mem_type;
            $promo->month = $request->month;
            $promo->save();

            //assign promo to seller
            if ($request->seller) {
                $seller = CardSellerInfo::find($request->seller);
                $promo_ids = $seller->promo_ids == null ? [] : $seller->promo_ids;
                array_push($promo_ids, "$promo->id");
                $seller->promo_ids = $promo_ids;
                $seller->save();
            }
            $exists = InfluencerPayment::where('influencer_id', $request->influencer_id)->count();
            if ($request->influencer_id != null && $exists == 0) {
                InfluencerPayment::insert([
                    'influencer_id' => $request->influencer_id,
                    'total_amount' => 0,
                    'paid_amount' => 0,
                ]);
            }

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            // dd($e);
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return redirect('/card-promo')->with('status', 'Promo Created Successfully!');
    }

    public function destroy($id)
    {
        $total_card_promo_used = CardPromoCodeUsage::where('promo_id', $id)->count();
        if ($total_card_promo_used > 0) {
            return redirect()->back()->with('try_again', 'You cant delete this promo.');
        }
        //remove promo id from seller account
        $seller_info = CardSellerInfo::where('promo_ids', 'like', "%\"{$id}\"%")->first();
        if ($seller_info) {
            if (count($seller_info->promo_ids) > 1) {
                $promo_ids = $seller_info->promo_ids;
                if (($key = array_search($id, $promo_ids)) !== false) {
                    unset($promo_ids[$key]);
                }
                $seller_info->promo_ids = array_values($promo_ids);
            } else {
                $seller_info->promo_ids = null;
            }
            $seller_info->save();
        }
        //delete promo
        $promo = CardPromoCodes::findOrFail($id);
        $promo->delete();

        return redirect('/card-promo')->with('status', 'Promo Deleted Successfully!');
    }

    public function edit($id)
    {
        $promo = cardPromoCodes::findOrFail($id);

        return view('admin.production.cardPromo.edit', compact('promo'));
    }

    public function update($id, Request $request)
    {
        if ($request->flat_rate != null && $request->percentage != null) {
            return Redirect()->back()->with('multiple-promo', 'Multiple promo type requested!');
        }

        if ($request->flat_rate == null && $request->percentage == null) {
            return Redirect()->back()->with('no-promo', 'Please enter atleast one promo type!');
        }

        if ($request->flat_rate != null) {
            $promo_type = 1;
        } elseif ($request->percentage != null) {
            $promo_type = 2;
        }

        $request->validate([
            'code' => ['required', new unique_if_changed($id, 'card_promo', 'code', 'id', 'code has already been taken')],
            'text' => 'required',
            'expiry_date' => 'required',
            'usage' => 'required',
        ]);

        $promo = CardPromoCodes::findOrFail($id);
        $promo->code = $request->code;
        $promo->text = $request->text;
        $promo->flat_rate = $request->flat_rate;
        $promo->percentage = $request->percentage;
        $promo->expiry_date = $request->expiry_date;
        $promo->type = $promo_type;
        $promo->usage = strtolower($request->usage);
        $promo->influencer_id = $request->influencer_id;
        $promo->month = $request->month;
        $promo->save();

        $exists = InfluencerPayment::where('influencer_id', $request->influencer_id)->count();
        if ($request->influencer_id != null && $exists == 0) {
            InfluencerPayment::insert([
                'influencer_id' => $request->influencer_id,
                'total_amount' => 0,
                'paid_amount' => 0,
            ]);
        }

        return redirect('/card-promo')->with('status', 'Promo Updated Successfully!');
    }
}
