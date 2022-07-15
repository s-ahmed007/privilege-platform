<?php

namespace App\Http\Controllers;

use App\AllAmounts;
use App\AssignedCard;
use App\CardPromoCodes;
use App\CardPromoCodeUsage;
use App\CardSellerAccount;
use App\CardSellerInfo;
use App\CustomerHistory;
use App\CustomerInfo;
use App\Http\Controllers\Enum\SellerCommissionType;
use App\Http\Controllers\Enum\SellerRole;
use App\Http\Controllers\Renew\apiController;
use App\SellerBalance;
use App\SellerCommissionHistory;
use App\SellerCreditRedeemed;
use App\SslTransactionTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CardSaleController extends Controller
{
    //function to get all card seller list
    public function allSellers()
    {
        $allSellers = CardSellerAccount::with(['info.commissionHistory.ssl', 'balance', 'cardSold', 'creditRedeemed'=>function ($query) {
            $query->orderBy('id', 'DESC');
        }])->orderBy('id', 'DESC')->get();

        foreach ($allSellers as $seller) {
            //get seller promo codes
            $seller->info->promo = null;
            if ($seller->info->promo_ids) {
                $seller->info->promo = CardPromoCodes::whereIn('id', $seller->info->promo_ids)->get();
            }
            $card_holder = $virtual = $trial = 0;
            if (count($seller->cardSold) != 0) {
                foreach ($seller->cardSold as $card) {
                    if (count($seller->creditRedeemed) > 0) {
                        if ($card->updated_at >= $seller->creditRedeemed[0]->posted_on) {
                            if ($card->type == 1) {
                                $card_holder++;
                            } elseif ($card->type == 3) {
                                $trial++;
                            }
//                            elseif ($card->type == 2) {
//                                $virtual++;
//                            }
                        }
                    } else {
                        if ($card->type == 1) {
                            $card_holder++;
                        } elseif ($card->type == 3) {
                            $trial++;
                        }
//                        elseif ($card->type == 2) {
//                            $virtual++;
//                        }
                    }
                }
                $seller->sold_card = $card_holder;
//                $seller->sold_virtual = $virtual;
                $seller->sold_trial = $trial;
            } else {
                $seller->sold_card = $card_holder;
//                $seller->sold_virtual = $virtual;
                $seller->sold_trial = $trial;
            }
        }

        return view('admin.production.cardSale.all_seller', compact('allSellers'));
    }

    //function to active/deactive user
    public function sellerApproval(Request $request)
    {
        $status = $request->input('status');
        $seller_id = $request->input('userId');
        if ($status == 2) {
            $user = CardSellerAccount::find($seller_id);
            $user->active = 0;
            $user->save();
        } else {
            $user = CardSellerAccount::find($seller_id);
            $user->active = 1;
            $user->save();
        }

        return Response::json($status);
    }

    //function to show seller create view
    public function createSeller()
    {
        $promo_codes = CardPromoCodes::active()->get();
        foreach ($promo_codes as $key => $promo) {
            $count = CardPromoCodeUsage::where('promo_id', $promo->id)->count();
            if ($promo->usage != 'unlimited' && $promo->usage <= $count) {
                unset($promo_codes[$key]);
            } else {
                $assigned = \DB::table('card_seller_info')->whereRaw(
                    "JSON_CONTAINS(promo_ids, '[\"$promo->id\"]')"
                )->get();
                if (count($assigned) != 0) {
                    unset($promo_codes[$key]);
                }
            }
        }
        $promo_codes = json_decode(json_encode($promo_codes), true);
        $promo_codes = array_values($promo_codes);

        return view('admin.production.cardSale.createSeller', compact('promo_codes'));
    }

    //function to store new branch Seller
    public function storeSeller(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required',
            'last_name' => 'required',
            'username' => 'required',
            'phone_number' => 'required|unique:card_seller_account,phone|min:14',
            'pin' => 'required',
            'commission' => 'sometimes|nullable|numeric',
            'trial_commission' => 'sometimes|nullable|numeric',
        ]);
        $request->flashOnly('first_name', 'last_name', 'username', 'phone_number', 'pin', 'commission', 'trial_commission');
        $first_name = $request->get('first_name');
        $last_name = $request->get('last_name');
        $username = $request->get('username');
//        $password = $request->get('password');
        $password = 'Asdf1234';
        $phone = $request->get('phone_number');
        $pin = $request->get('pin');
        $commission = $request->get('commission');
        $trial_commission = $request->get('trial_commission');

        $promo_ids = null;
        if ($request->has('promo_code')) {
            $promo_ids = $request->get('promo_code');
        }

        $password = preg_replace('/\s+/', '', $password);
        $encrypted_password = (new functionController)->encrypt_decrypt('encrypt', $password);

        $allCardSeller = new CardSellerAccount();
        $allCardSeller->username = $username;
        $allCardSeller->password = $encrypted_password;
        $allCardSeller->phone = $phone;
        $allCardSeller->role = SellerRole::cardSeller;
        $allCardSeller->active = 1;

        $allCardSeller->save();

        if ($trial_commission == null) {
            $CardSellerInfo = new CardSellerInfo();
            $CardSellerInfo->first_name = $first_name;
            $CardSellerInfo->last_name = $last_name;
            $CardSellerInfo->seller_account_id = $allCardSeller->id;
            $CardSellerInfo->pin = $pin;
            $CardSellerInfo->commission = $commission;
            $CardSellerInfo->promo_ids = $promo_ids;
        } else {
            $CardSellerInfo = new CardSellerInfo();
            $CardSellerInfo->first_name = $first_name;
            $CardSellerInfo->last_name = $last_name;
            $CardSellerInfo->seller_account_id = $allCardSeller->id;
            $CardSellerInfo->pin = $pin;
            $CardSellerInfo->commission = $commission;
            $CardSellerInfo->trial_commission = $trial_commission;
            $CardSellerInfo->promo_ids = $promo_ids;
        }

        $CardSellerInfo->save();

        $SellerBalance = new SellerBalance();
        $SellerBalance->Seller_id = $CardSellerInfo->id;
        $SellerBalance->credit = 0;
        $SellerBalance->credit_used = 0;

        $SellerBalance->save();

        return Redirect('card-seller')->with('created', 'Seller Created Successfully');
    }

    //function to show edit view of Seller
    public function editSeller($seller_id)
    {
        $user = CardSellerAccount::with('info')->where('id', $seller_id)->first();
        $promo_codes = CardPromoCodes::active()->get();

        foreach ($promo_codes as $key => $promo) {
            $count = CardPromoCodeUsage::where('promo_id', $promo->id)->count();
            $assigned = \DB::table('card_seller_info')->where('seller_account_id', '!=', $seller_id)->whereRaw(
                "JSON_CONTAINS(promo_ids, '[\"$promo->id\"]')"
            )->get();
            if ($promo->usage != 'unlimited' && $promo->usage <= $count) {
                unset($promo_codes[$key]);
            } elseif (count($assigned) != 0) {
                unset($promo_codes[$key]);
            } elseif ($user->info->promo_ids) {
                if (in_array($promo->id, $user->info->promo_ids)) {
                    $promo->selected = true;
                } else {
                    $promo->selected = false;
                }
            } else {
                $promo->selected = false;
            }
        }
        $promo_codes = json_decode(json_encode($promo_codes), true);
        $promo_codes = array_values($promo_codes);

        return view('admin.production.cardSale.editSeller', compact('user', 'promo_codes'));
    }

    //function to store new user
    public function updateSellerInfo(Request $request, $Seller_id)
    {
        $user = CardSellerAccount::with('info')->where('id', $Seller_id)->first();
        if ($request->get('phone_number') == $user->phone && $request->get('pin') == $user->info->pin) {
            $this->validate($request, [
                'first_name' => 'required',
                'last_name' => 'required',
                'username' => 'required',
                'phone_number' => 'required',
                'pin' => 'required',
                'commission' => 'sometimes|nullable|numeric',
                'trial_commission' => 'required|numeric',
            ]);
        } elseif ($request->get('phone_number') == $user->phone && $request->get('pin') != $user->info->pin) {
            $this->validate($request, [
                'first_name' => 'required',
                'last_name' => 'required',
                'username' => 'required',
                'phone_number' => 'required',
                'pin' => 'required',
                'commission' => 'sometimes|nullable|numeric',
                'trial_commission' => 'required|numeric',
            ]);
        } elseif ($request->get('phone_number') != $user->phone && $request->get('pin') != $user->info->pin) {
            $this->validate($request, [
                'first_name' => 'required',
                'last_name' => 'required',
                'username' => 'required',
                'phone_number' => 'required|unique:card_seller_account,phone|min:14',
                'pin' => 'required',
                'commission' => 'sometimes|nullable|numeric',
                'trial_commission' => 'required|numeric',
            ]);
        }
        $request->flashOnly('first_name', 'last_name', 'username', 'phone_number', 'pin');

        $first_name = $request->get('first_name');
        $last_name = $request->get('last_name');
        $username = $request->get('username');
//        $password = $request->get('password');
        $phone = $request->get('phone_number');
        $pin = $request->get('pin');
        $commission = $request->get('commission');
        $trial_commission = $request->get('trial_commission');

        $promo_ids = null;
        if ($request->has('promo_code')) {
            $promo_ids = $request->get('promo_code');
        }

//        $password = preg_replace('/\s+/', '', $password);
//        $encrypted_password = (new functionController)->encrypt_decrypt('encrypt', $password);

//        $Seller = CardSellerInfo::all()->where('seller_id', $Seller_id)->first();

        $seller_info = CardSellerInfo::where('seller_account_id', $Seller_id)->first();
        $seller_info->first_name = $first_name;
        $seller_info->last_name = $last_name;
        $seller_info->pin = $pin;
        $seller_info->commission = $commission;
        $seller_info->trial_commission = $trial_commission;
        $seller_info->promo_ids = $promo_ids;
        $seller_info->save();
//        CardSellerInfo::where('seller_account_id', $Seller_id)->update([
//            'first_name'       => $first_name,
//            'last_name'        => $last_name,
//            'pin'              => $pin,
//            'commission'       => $commission,
//            'trial_commission' => $trial_commission,
//            'promo_ids'        => $promo_ids
//        ]);

        CardSellerAccount::where('id', $Seller_id)->update([
            'username' => $username,
            'phone' => $phone,
        ]);

        return Redirect('card-seller')->with('updated', 'Information Updated Successfully');
    }

    //function to pay Seller
    public function paySeller($Seller_id)
    {
        $all_amount = AllAmounts::all();
        $min_redeem_credit = $all_amount[12]['price'];
        $user = CardSellerAccount::where('id', $Seller_id)->with('info')->first();
        $seller_balance = SellerBalance::where('seller_id', $user->info->id)->first();
        $seller_credit = $seller_balance->credit;
        $seller_debit = $seller_balance->debit;
        if ($seller_credit < $min_redeem_credit) {
            return Redirect('card-seller')->with('over_limit', 'You do not have enough credit to redeem.');
        } else {
            $seller_balance->decrement('credit', $seller_credit);
            $seller_balance->increment('credit_used', $seller_credit);

            $seller_balance->decrement('debit', $seller_debit);
            $seller_balance->increment('debit_used', $seller_debit);
            //insert into redeem table
            $redeem = new SellerCreditRedeemed([
                'credit' => $seller_credit,
                'debit' => $seller_debit,
                'seller_account_id' => $user->id,
                'status' => 1,
                'posted_on' => date('Y-m-d H:i:s'),
            ]);
            $redeem->save();
            //update paid status in commission history table
            SellerCommissionHistory::where('seller_id', $user->info->id)->update(['paid' => 1]);

            $message = 'You have been paid Tk '.$seller_credit.' from Royalty on '.
                date('M d, Y h:i A', strtotime($redeem->posted_on)).'. Your current balance is Tk '.$seller_balance->credit.'.';
            (new apiController())->sendSms($user->phone, $message);

            return Redirect('card-seller')->with('updated', 'Seller payment successful');
        }
    }

    //function to delete branch Seller
    public function deleteSeller($user_id)
    {
        try {
            DB::beginTransaction(); //to do query rollback

            $user = CardSellerAccount::find($user_id);
            $user->delete();

            DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return redirect()->back()->with('try_again', 'Please try again!');
        }

        return Redirect('card-seller')->with('user_deleted', 'One Seller deleted');
    }

    //function to assign card to seller
    public function assignedCard($userId)
    {
        $assigned_card = AssignedCard::where('seller_account_id', $userId)->with([
            'ssl'=>function ($query) {
                $query->where('status', 1)->orderBy('id', 'DESC');
            },
            'cardPromoUsage'=>function ($query) {
                $query->orderBy('id', 'DESC');
            },
            'cardPromoUsage.promoCode', ])->get();
        $user = CardSellerAccount::where('id', $userId)->with('info')->first();
//         dd($assigned_card->toArray());
        return view('admin.production.cardSale.assigned-card', compact('assigned_card', 'user'));
    }

    //function to show sales history of a specific seller
    public function salesHistory($seller_id)
    {
        $history = CardSellerInfo::where('seller_account_id', $seller_id)->with('salesHistory.customerInfo',
            'salesHistory.sslInfo.sellerCommission', 'account.balance')->first();

        return view('admin.production.cardSale.sales_history', compact('history'));
    }

    //function to assign card to seller
    public function assignCard($userId)
    {
        return view('admin.production.cardSale.assign-card', compact('userId'));
    }

    //function to store assigned card
    public function storeAssignedCard(Request $request, $userId)
    {
        $cards = [];
        if (isset($_POST['card_number'])) {
            for ($i = 0; $i < count($_POST['card_number']); $i++) {
                if ($_POST['card_number'][$i] != '') {
                    $cards[] = [
                        'number' => $_POST['card_number'][$i],
                        'type' => $_POST['card_type'][$i],
                    ];
                }
            }
        } else {
            $cards = [];
        }
        if (count($cards) > 0) {
            $card_exists = [];
            $skip = 0;
            foreach ($cards as $card) {
                if (AssignedCard::where('card_number', $card['number'])->exists()) {
                    //skip this entry
                    $skip += 1;
                } elseif (CustomerInfo::where('customer_id', $card['number'])->exists()) {
                    array_push($card_exists, $card['number']);
                } else {
                    $assigned_card = new AssignedCard();
                    $assigned_card->card_number = $card['number'];
                    $assigned_card->status = 0;
                    $assigned_card->card_type = $card['type'];
                    $assigned_card->seller_account_id = $userId;
                    $assigned_card->assigned_on = date('Y-m-d H:i:s');
                    $assigned_card->save();
                }
            }

            return redirect('assigned-card/'.$userId)->with('assign_success', 'Card successfully assigned')
                ->with('double_entry', $skip)->with('card_exists', $card_exists);
        }

        return redirect()->back()->with('assign_fail', 'Please try again');
    }

    //function to edit assigned card
    public function editAssignedCard($id)
    {
        $card = AssignedCard::find($id);

        return view('admin.production.cardSale.edit-assign-card', compact('card'));
    }

    //function to update assigned card
    public function updateAssignedCard(Request $request, $id)
    {
        $this->validate($request, [
            'card_number' => 'required|unique:assigned_card,card_number',
        ]);
        $request->flashOnly('card_number');

        $card_number = $request->get('card_number');
        $card_type = $request->get('card_type');

        $card = AssignedCard::find($id);
        $card->card_number = $card_number;
        $card->card_type = $card_type;
        $card->save();

        return redirect('assigned-card/'.$card->seller_account_id)->with('update_success', 'Update successful');
    }

    //function to delete assigned card
    public function deleteAssignedCard($id)
    {
        $card = AssignedCard::find($id);
        $card->delete();

        return redirect('assigned-card/'.$card->seller_account_id)->with('deleted', 'Deleted successfully');
    }

    //function to get all requests
    public function sellerRequest()
    {
        $requests = SellerCreditRedeemed::with('account.info')->orderBy('id', 'DESC')->get();

        return view('admin.production.cardSale.sellerRequests', compact('requests'));
    }

    //function to Accept seller requests
    public function sellerRequestAccept(Request $request)
    {
        $status = $request->input('status');
        $id = $request->input('id');

        $value = $status == 1 ? 1 : 0;
        SellerCreditRedeemed::where('id', $id)->update(['status' => $value]);

        return Response::json($status);
    }
}
