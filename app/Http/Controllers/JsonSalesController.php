<?php

namespace App\Http\Controllers;

use App\AllAmounts;
use App\AssignedCard;
use App\CardPrice;
use App\CardPromoCodes;
use App\CardPromoCodeUsage;
use App\CardSellerAccount;
use App\CardSellerInfo;
use App\CustomerHistory;
use App\CustomerInfo;
use App\CustomerLoginSession;
use App\Helpers\LengthAwarePaginator;
use App\Http\Controllers\Enum\CustomerType;
use App\Http\Controllers\Enum\DeliveryType;
use App\Http\Controllers\Enum\LoginStatus;
use App\Http\Controllers\Enum\MembershipPriceType;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\Enum\SellerCommissionType;
use App\Http\Controllers\Enum\SellerRole;
use App\Http\Controllers\LoginRegister\functionController;
use App\Http\Controllers\Renew\apiController;
use App\SellerBalance;
use App\SellerCommissionHistory;
use App\SellerCreditRedeemed;
use App\SslTransactionTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JsonSalesController extends Controller
{
    public function authenticate(Request $request)
    {
        $phone = $request->post('phone');
        $pin = $request->post('pin');
        $user = CardSellerAccount::where('phone', '=', $phone)->with('info')->first();
        if (! $user) {
            return response()->json(['error' => 'There is no account with this phone number.'], 201);
        } elseif ($pin && $user->info->pin != $pin) {
            return response()->json(['error' => 'Incorrect pin.'], 201);
        } else {
            try {
                if (! $token = JWTAuth::fromUser($user)) {
                    return response()->json(['error' => 'invalid_credentials'], 400);
                }
            } catch (JWTException $e) {
                return response()->json(['error' => 'could_not_create_token'], 500);
            }
        }

        return response()->json(compact('token'));
    }

    public function getAuthenticatedUser()
    {
        try {
            if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
                return response()->json(['error' => 'Undefined user'], 201);
            } else {
                if ($login->role == SellerRole::cardSeller) {
                    $user = CardSellerAccount::where('id', $login->id)->with('info')->first();
                } else {
                    return response()->json(['error' => 'You do not have the access'], 201);
                }
            }
        } catch (TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        return response()->json($user);
    }

    public function getAssignedCardList()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['error' => 'Undefined user'], 201);
        } else {
            if ($login->role == SellerRole::cardSeller) {
                $user = CardSellerAccount::where('id', $login->id)->first();
                $assigned_cards = AssignedCard::where('seller_account_id', $user->id)->where('status', 0)->orderBy('card_type', 'ASC')->get();
                $total_gold_cards = AssignedCard::where('seller_account_id', $user->id)
                    ->where('status', 0)
                    ->where('card_type', 1)
                    ->orderBy('card_type', 'ASC')->count();
                $total_platinum_cards = AssignedCard::where('seller_account_id', $user->id)
                    ->where('status', 0)
                    ->where('card_type', 2)
                    ->orderBy('card_type', 'ASC')->count();

                //pagination
                // Get current page form url e.x. &page=1
                $currentPage = LengthAwarePaginator::resolveCurrentPage();

                // Create a new Laravel collection from the array data
                $itemCollection = collect($assigned_cards);

                // Define how many items we want to be visible in each page
                $perPage = 10;

                // Slice the collection to get the items to display in current page
                $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

                // Create our paginator and pass it to the view
                $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);

                $paginatedItems->setPath('');
                $paginatedItems->setArrayName('assigned_cards');
                $response_array = [];
                $response_array['total_gold_cards'] = $total_gold_cards;
                $response_array['total_platinum_cards'] = $total_platinum_cards;
                $response_array['cards'] = $paginatedItems;

                return response()->json($response_array);
            } else {
                return response()->json(['error' => 'You do not have the access'], 201);
            }
        }
    }

    public function getSoldCardList()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['error' => 'Undefined user'], 201);
        } else {
            if ($login->role == SellerRole::cardSeller) {
                $user = CardSellerAccount::where('id', $login->id)->first();

                $sales = SellerCommissionHistory::where('seller_id', $user->info->id)->with('ssl')
                    ->orderBy('id', 'DESC')->get();

                //pagination
                // Get current page form url e.x. &page=1
                $currentPage = LengthAwarePaginator::resolveCurrentPage();

                // Create a new Laravel collection from the array data
                $itemCollection = collect($sales);

                // Define how many items we want to be visible in each page
                $perPage = 10;

                // Slice the collection to get the items to display in current page
                $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

                // Create our paginator and pass it to the view
                $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);

                $paginatedItems->setPath('');
                $paginatedItems->setArrayName('sold_cards');

                return response()->json($paginatedItems);
            } else {
                return response()->json(['error' => 'You do not have the access'], 201);
            }
        }
    }

    public function getTotalSaleValue()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['error' => 'Undefined user'], 201);
        } else {
            if ($login->role == SellerRole::cardSeller) {
                $user = CardSellerAccount::where('id', $login->id)->first();
                $sales = SellerCommissionHistory::where('seller_id', $user->info->id)->with('ssl')
                    ->orderBy('id', 'DESC')->get();

                $total_sell = $sales->sum('ssl.amount');

                $trial_count = CustomerHistory::where('seller_id', $user->id)->where('type', CustomerType::trial_user)->count();
                $virtual_count = CustomerHistory::where('seller_id', $user->id)->where('type', CustomerType::virtual_card_holder)->count();

                return response()->json(compact('total_sell', 'trial_count', 'virtual_count'));
            } else {
                return response()->json(['error' => 'You do not have the access'], 201);
            }
        }
    }

    public function getBalance()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['error' => 'Undefined user'], 201);
        } else {
            if ($login->role == SellerRole::cardSeller) {
                $user = CardSellerAccount::where('id', $login->id)->with('info')->first();
                $balance = SellerBalance::where('seller_id', $user->info->id)->first();

                $commission = SellerCommissionHistory::where('seller_id', $user->info->id)
                    ->where('paid', 0)->with('ssl')->get();

                $balance->app_sales = $commission->where('type', SellerCommissionType::SALES_APP)->sum('ssl.amount');
                $balance->online_sales = $commission->where('type', SellerCommissionType::ONLINE_PAY)->sum('ssl.amount');
                $balance->app_commission = $commission->where('type', SellerCommissionType::SALES_APP)->sum('commission');
                $balance->online_commission = $commission->where('type', SellerCommissionType::ONLINE_PAY)->sum('commission');

                return response()->json($balance);
            } else {
                return response()->json(['error' => 'You do not have the access'], 201);
            }
        }
    }

    public function getBalanceRedeemHistory()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['error' => 'Undefined user'], 201);
        } else {
            if ($login->role == SellerRole::cardSeller) {
                $user = CardSellerAccount::where('id', $login->id)->first();
                $balance_redeem_history = SellerCreditRedeemed::where('seller_account_id', $user->id)->get();

                //pagination
                // Get current page form url e.x. &page=1
                $currentPage = LengthAwarePaginator::resolveCurrentPage();

                // Create a new Laravel collection from the array data
                $itemCollection = collect($balance_redeem_history);

                // Define how many items we want to be visible in each page
                $perPage = 10;

                // Slice the collection to get the items to display in current page
                $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->values();

                // Create our paginator and pass it to the view
                $paginatedItems = new LengthAwarePaginator($currentPageItems, count($itemCollection), $perPage);

                $paginatedItems->setPath('');
                $paginatedItems->setArrayName('redeem_history');

                return response()->json($paginatedItems);
            } else {
                return response()->json(['error' => 'You do not have the access'], 201);
            }
        }
    }

    public function setFirebaseToken(Request $request)
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['error' => 'Undefined user'], 201);
        } else {
            CardSellerAccount::where('id', $login->id)
                ->update(
                    [
                        'f_token' => $request->post('f_token'),
                    ]
                );
            if ($login->role == SellerRole::cardSeller) {
                $user = CardSellerAccount::where('id', $login->id)->with('info')->first();
            } else {
                return response()->json(['error' => 'You do not have the access'], 201);
            }
        }

        return response()->json($user);
    }

    public function removeFirebaseToken()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['error' => 'Undefined user'], 201);
        } else {
            CardSellerAccount::where('id', $login->id)
                ->update(
                    [
                        'f_token' => 0,
                    ]
                );
            if ($login->role == SellerRole::cardSeller) {
                $user = CardSellerAccount::where('id', $login->id)->with('info')->first();
            } else {
                return response()->json(['error' => 'You do not have the access'], 201);
            }
        }

        return response()->json($user);
    }

    public function getCardsForSell(Request $request)
    {
        $card_type = $request->post('card_type');
//        $pin = $request->post('pin');
        if ($card_type == 7) {
            $card_type = 1;
        } elseif ($card_type == 8) {
            $card_type = 2;
        } elseif ($card_type == 9) {
            $card_type = 1;
        } elseif ($card_type == 10) {
            $card_type = 2;
        }

        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['error' => 'Undefined user'], 201);
        } else {
            if ($login->role == SellerRole::cardSeller) {
                $user = CardSellerAccount::where('id', $login->id)->with('info')->first();
//                if ($user->info->pin == $pin) {
//                    $assigned_cards = AssignedCard::where('status', 0)
//                        ->where('card_type', $card_type)
//                        ->where('seller_account_id', $user->id)->get();
//                    return response()->json(compact('assigned_cards'));
//                } else {
//                    return response()->json(['error' => 'You have entered wrong pin.'], 201);
//                }
                $assigned_cards = AssignedCard::where('status', 0)
                    ->where('card_type', $card_type)
                    ->where('seller_account_id', $user->id)->get();

                return response()->json(compact('assigned_cards'));
            } else {
                return response()->json(['error' => 'You do not have the access'], 201);
            }
        }
    }

    public function getPromos()
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['message' => 'Undefined user'], 406);
        } else {
            if ($login->role == SellerRole::cardSeller) {
                $user = CardSellerAccount::where('id', $login->id)->with('info')->first();
                $promos = [];
                $i = 0;
                if ($user->info->promo_ids) {
                    foreach ($user->info->promo_ids as $promo_id) {
                        $promo = CardPromoCodes::where('id', $promo_id)->first();
                        $promo_used_count = CardPromoCodeUsage::where('promo_id', $promo_id)->count();
                        $promo->used_count = $promo_used_count;
                        $promos[$i++] = $promo;
                    }
                }

                return response()->json($promos, 200);
            } else {
                return response()->json(['message' => 'You do not have the access'], 406);
            }
        }
    }

    public function createVirtualUser(Request $request)
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['message' => 'Undefined user'], 406);
        } else {
            if ($login->role == SellerRole::cardSeller) {
                $user = CardSellerAccount::where('id', $login->id)->with('info')->first();
                $seller_balance = SellerBalance::where('seller_id', $user->info->id)->first();
                $trial_commission = $user->info->trial_commission;
                $platform = PlatformType::sales_app;
                $name = $request->post('name');
                $email = $request->post('email');
                $phone = $request->post('phone');
                $pin = $request->post('pin');
                $month = $request->post('month');
                if ((new jsonController)->emailExist($email)) {
                    return response()->json(['message' => 'Email already exists.'], 406);
                } elseif ((new jsonController)->phoneNumberExist($phone)) {
                    return response()->json(['message' => 'Phone number already exists.'], 406);
                } else {
                    $customer = (new functionController())->register($name, $email, $phone, $pin, $platform);
                    $seller_balance->increment('credit', $trial_commission);
                    $seller_balance->decrement('debit', $trial_commission);

                    return (new apiController())->createVirtualTrialUser(PlatformType::sales_app, $month, $customer->customer_id, 0, $user->id);
                }
            } else {
                return response()->json(['message' => 'You do not have the access'], 406);
            }
        }
    }

    public function checkUser(Request $request)
    {
        $email = $request->post('email');
        $phone = $request->post('phone');
        if ($email && (new jsonController)->emailExist($email)) {
            return response()->json(['message' => 'Email already exists.'], 406);
        } elseif ($phone && (new jsonController)->phoneNumberExist($phone)) {
            return response()->json(['message' => 'Phone number already exists.'], 406);
        } else {
            return response()->json('Successful!', 200);
        }
    }

    public function manualRegistration(Request $request)
    {
        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['error' => 'Undefined user'], 201);
        } else {
            if ($login->role == SellerRole::cardSeller) {
                $user = CardSellerAccount::where('id', $login->id)->with('info')->first();
                $seller_balance = SellerBalance::where('seller_id', $user->info->id)->first();
                $all_amount = AllAmounts::all();
                $per_card_sell = $all_amount[11]['price'];
                $commission = $user->info->commission;
                $full_name = $request->post('full_name');
                $email = $request->post('email');
                $contact = $request->post('phone');
                $pin = $request->post('pin');
                $image_url = 'https://s3-ap-southeast-1.amazonaws.com/royalty-bd/static-images/registration/user.png';
//                $assigned_card_id = $request->post('assigned_card_id');
                $promo_id = $request->post('promo_id');
                $card_amount = $request->post('card_amount');
//                $assigned_card = AssignedCard::where('id', $assigned_card_id)->first();
                $promo = CardPromoCodes::where('id', $promo_id)->first();
                if ($promo) {
                    $other_seller_promo = CardSellerInfo::where('promo_ids', 'like', "%\"{$promo_id}\"%")
                        ->where('seller_account_id', '!=', $user->id)->first();
                    if ($other_seller_promo) {
                        return Response::json(['error' => 'You can not use other seller promo code here.'], 201);
                    }
                }
                $month = $request->post('month');
                if (! $month) {
                    $month = 12;
                }

                if ((new jsonController)->emailExist($email)) {
                    return response()->json(['error' => 'Email already exists.'], 201);
                } elseif ((new jsonController)->phoneNumberExist($contact)) {
                    return response()->json(['error' => 'Phone number already exists.'], 201);
                } else {
                    A:
                    if ((new jsonController)->usernameExist((new JsonControllerV2())->getUsernameFromEmail($email))) {
                        $username = (new JsonControllerV2())->randomUsername($full_name);
                        if ((new jsonController)->usernameExist($username)) {
                            goto A;
                        }
                    } else {
                        $username = (new JsonControllerV2())->getUsernameFromEmail($email);
                    }

                    // make password encrypted
                    $encrypted_pin = (new \App\Http\Controllers\functionController())->encrypt_decrypt('encrypt', $pin);

//                    if ($assigned_card_id){
//                        $main_customer_id = $assigned_card->card_number;
//                        $customer_type = $assigned_card->card_type;
//                    }else{
                    $genCustomerID = (new functionController())->generate_customer_id();
                    $main_customer_id = $genCustomerID->getData()->id;
                    $customer_id_6 = $genCustomerID->getData()->serial;
                    $customer_type = 2;
//                    }
                    $royaltybd_transaction_id = (new JsonControllerV2)->getSSLTransactionId();
                    try {
                        DB::beginTransaction(); //to do query rollback
                        //save data in customer_account table
                        DB::table('customer_account')->insert([
                            'customer_id' => $main_customer_id,
                            'customer_serial_id' => $customer_id_6,
                            'customer_username' => $username,
                            'pin' => $encrypted_pin,
                            'platform' => PlatformType::sales_app,
                            'moderator_status' => 2,
                        ]);

                        //generate referral number
                        $token = (new functionController())->generate_refer_code();

                        //expiry date for 12 months
                        $date = date_create(date('Y-m-d'));
                        $expiry_date = date_add($date, date_interval_create_from_date_string($month.' months'));
                        $expiry_date = $expiry_date->format('Y-m-d');

                        //save data in customer_info table
                        $customer = new CustomerInfo([
                            'customer_id' => $main_customer_id,
                            'customer_full_name' => $full_name,
                            'customer_email' => $email,
                            'customer_contact_number' => $contact,
                            'customer_profile_image' => $image_url,
                            'customer_type' => $customer_type,
                            'month' => $month,
                            'card_active' => 2,
                            'card_activation_code' => 0,
                            'expiry_date' => $expiry_date,
                            'member_since' => date('Y-m-d H:i:s'),
                            'firebase_token' => 0,
                            'delivery_status' => 3,
                            'approve_date' => date('Y-m-d H:i:s'),
                            'referral_number' => $token,
                        ]);
                        $customer->save();

                        //save email in subscribers table
                        DB::table('subscribers')->insert([
                            'email' => $email,
                        ]);
                        //get main price to calculate commission
                        $main_price = CardPrice::where('platform', PlatformType::android)
                            ->where('type', MembershipPriceType::buy)
                            ->where('month', $month)
                            ->first();
                        $commission_received = $this->updateSellerBalance($main_price->price, $commission, $seller_balance,
                            $per_card_sell, $month, true, $card_amount);

                        //store new customer_id
                        $assigned_card = new AssignedCard();
                        $assigned_card->card_number = $main_customer_id;
                        $assigned_card->status = 1;
                        $assigned_card->card_type = 2;
                        $assigned_card->month = $month;
                        $assigned_card->seller_account_id = $user->id;
                        $assigned_card->assigned_on = date('Y-m-d H:i:s');
                        $assigned_card->sold_on = date('Y-m-d H:i:s');
                        $assigned_card->save();

                        //insert into ssl transaction table
                        $ssl_transaction = new SslTransactionTable([
                            'customer_id' => $main_customer_id,
                            'status' => 1,
                            'tran_date' => date('Y-m-d H:i:s'),
                            'tran_id' => $royaltybd_transaction_id,
                            'val_id' => '',
                            'amount' => $card_amount,
                            'store_amount' => '0.00',
                            'card_type' => 'CASH',
                            'card_no' => '',
                            'currency' => 'BDT',
                            'bank_tran_id' => '',
                            'card_issuer' => '',
                            'card_brand' => '',
                            'platform' => PlatformType::sales_app,
                            'card_issuer_country' => '',
                            'card_issuer_country_code' => '',
                            'month' => $month,
                            'currency_amount' => '0.00',
                        ]);
                        $ssl_transaction->save();
                        DB::table('card_delivery')->insert([
                            'customer_id' => $main_customer_id,
                            'delivery_type' => DeliveryType::spot_delivery,
                            'shipping_address' => 'On Spot',
                            'order_date' => date('Y-m-d'),
                            'paid_amount' => $card_amount,
                            'ssl_id' => $ssl_transaction->id,
                        ]);

                        if ($promo) {
                            CardPromoCodeUsage::insert([
                                'customer_id' => $main_customer_id,
                                'promo_id' => $promo_id,
                                'ssl_id' => $ssl_transaction->id,
                            ]);
                        }
                        $this->saveSellerCommissionHistory($user->info->id, $ssl_transaction->id, $commission_received, SellerCommissionType::SALES_APP);

                        (new \App\Http\Controllers\AdminNotification\functionController())->salesAppCardSoldNotification($assigned_card);
                        DB::commit(); //to do query rollback
                        (new functionController2())->addToCustomerHistory($main_customer_id, $user->id, CustomerType::card_holder,
                            $ssl_transaction->id, $promo_id);
                        //for mailing
                        $customer->tran_id = $ssl_transaction->tran_id;
                        $customer->delivery_type = DeliveryType::spot_delivery;
                        $validity = $customer->month == 12 ? 'one year' : $customer->month.' months';
                        (new adminController())->OnlinePaymentMail($customer->customer_full_name, $customer->customer_email,
                            $customer, $validity);
                        //for mailing

                        return response()->json(['success' => 'Card sell was successful.'], 200);
                    } catch (\Exception $e) {
//                        dd($e->getMessage() . ' file:' . $e->getFile() . ' file:' . $e->getLine());
                        DB::rollBack(); //rollback all successfully executed queries
                        return response()->json(['error' => 'Something went wrong'], 201);
                    }
                }
            } else {
                return response()->json(['error' => 'You do not have the access'], 201);
            }
        }
    }

    public function spotPurchase(Request $request)
    {
        $promo_id = $request->post('promo_id');
        $card_amount = $request->post('card_amount');
//        $assigned_card_id = $request->post('assigned_card_id');
        $customer_id = $request->post('customer_id');
        $month = $request->post('month');
        if (! $month) {
            $month = 12;
        }

        if (! $login = JWTAuth::toUser(JWTAuth::getToken())) {
            return response()->json(['error' => 'Undefined user'], 201);
        } else {
            if ($login->role == SellerRole::cardSeller) {
                $user = CardSellerAccount::where('id', $login->id)->with('info')->first();
                $commission = $user->info->commission;
                //balance section
                $seller_balance = SellerBalance::where('seller_id', $user->info->id)->first();
                $all_amount = AllAmounts::all();
                $per_card_sell = $all_amount[11]['price'];
                $royaltybd_transaction_id = (new JsonControllerV2)->getSSLTransactionId();

                //card section
//                $assigned_card = AssignedCard::where('id', $assigned_card_id)->first();
                $promo = CardPromoCodes::where('id', $promo_id)->first();
//                $main_customer_id = $assigned_card->card_number;
//                $customer_type = $assigned_card->card_type;

                //expiry date for 12 months
                $date = date_create(date('Y-m-d'));
                $expiry_date = date_add($date, date_interval_create_from_date_string($month.' months'));
                $expiry_date = $expiry_date->format('Y-m-d');

//                $id_exist = CustomerInfo::where('customer_id', $main_customer_id)->first();
//                if ($id_exist) {
//                    return response()->json(['error' => 'This card number is already sold.'], 201);
//                } else {
                try {
                    DB::beginTransaction(); //to do query rollback
                    $customer_history_count = CustomerHistory::where('customer_id', $customer_id)->count();
                    CustomerInfo::where('customer_id', $customer_id)
                            ->update(
                                [
                                    'customer_type' => 2,
                                    'month' => $month,
                                    'card_active' => 2,
                                    'card_activation_code' => 0,
                                    'expiry_date' => $expiry_date,
                                    'approve_date' => date('Y-m-d H:i:s'),
                                    'delivery_status' => 3,

                                ]
                            );

//                        (new \App\Http\Controllers\functionController())->updateCustomerId($customer_id, $main_customer_id, 1);
                    $main_price = CardPrice::where('platform', PlatformType::android)
                            ->where('type', MembershipPriceType::buy)
                            ->where('month', $month)
                            ->first();
                    if ($customer_history_count > 0) {
                        $commission_received = $this->updateSellerBalance($main_price->price, 0,
                                $seller_balance, $per_card_sell, $month, true, $card_amount);
                    } else {
                        $commission_received = $this->updateSellerBalance($main_price->price, $commission,
                                $seller_balance, $per_card_sell, $month, true, $card_amount);
                    }
                    //store new customer_id
                    $assigned_card = new AssignedCard();
                    $assigned_card->card_number = $customer_id;
                    $assigned_card->status = 1;
                    $assigned_card->card_type = 2;
                    $assigned_card->month = $month;
                    $assigned_card->seller_account_id = $user->id;
                    $assigned_card->assigned_on = date('Y-m-d H:i:s');
                    $assigned_card->sold_on = date('Y-m-d H:i:s');
                    $assigned_card->save();

//                        AssignedCard::where('id', $assigned_card_id)
//                            ->update(
//                                [
//                                    'status' => 1,
//                                    'sold_on' => date("Y-m-d H:i:s"),
//                                    'month' => $month
//
//                                ]
//                            );
//                        $assigned_card->month = $month;

                    //insert into ssl transaction table
                    $ssl_transaction = new SslTransactionTable([
                            'customer_id' => $customer_id,
                            'status' => 1,
                            'tran_date' => date('Y-m-d H:i:s'),
                            'tran_id' => $royaltybd_transaction_id,
                            'val_id' => '',
                            'amount' => $card_amount,
                            'store_amount' => '0.00',
                            'card_type' => 'CASH',
                            'card_no' => '',
                            'currency' => 'BDT',
                            'bank_tran_id' => '',
                            'card_issuer' => '',
                            'platform' => PlatformType::sales_app,
                            'card_brand' => '',
                            'card_issuer_country' => '',
                            'card_issuer_country_code' => '',
                            'currency_amount' => '0.00',
                            'month' => $month,
                        ]);
                    $ssl_transaction->save();
                    DB::table('card_delivery')->insert([
                            'customer_id' => $customer_id,
                            'delivery_type' => DeliveryType::spot_delivery,
                            'shipping_address' => 'On Spot',
                            'paid_amount' => $card_amount,
                            'order_date' => date('Y-m-d'),
                            'ssl_id' => $ssl_transaction->id,
                        ]);

                    if ($promo) {
                        CardPromoCodeUsage::insert([
                                'customer_id' => $customer_id,
                                'promo_id' => $promo_id,
                                'ssl_id' => $ssl_transaction->id,
                            ]);
                    }

                    $this->saveSellerCommissionHistory($user->info->id, $ssl_transaction->id, $commission_received, SellerCommissionType::SALES_APP);
                    (new \App\Http\Controllers\AdminNotification\functionController())->salesAppCardSoldNotification($assigned_card);
                    DB::commit(); //to do query rollback
                    $customer = CustomerInfo::where('customer_id', $customer_id)->first();
                    $this->sendBuyCardNotification('', $customer);
                    (new functionController2())->addToCustomerHistory($customer_id, $user->id, CustomerType::card_holder,
                            $ssl_transaction->id, $promo_id);

                    //for mailing
                    $customer->tran_id = $ssl_transaction->tran_id;
                    $customer->delivery_type = DeliveryType::spot_delivery;
                    $validity = $customer->month == 12 ? 'one year' : $customer->month.' months';
                    (new adminController())->OnlinePaymentMail($customer->customer_full_name, $customer->customer_email,
                            $customer, $validity);
                    //for mailing
                    return response()->json(['success' => 'Card sell was successful.'], 200);
                } catch (\Exception $e) {
                    DB::rollBack(); //rollback all successfully executed queries
                    return response()->json(['error' => 'Something went wrong'], 201);
                }
//                }
            } else {
                return response()->json(['error' => 'You do not have the access'], 201);
            }
        }
    }

    public function spotPurchaseFromUser(Request $request)
    {
        $promo_id = $request->post('promo_id');
        $card_amount = $request->post('card_amount');
        $customer_id = $request->post('customer_id');
        $month = $request->post('month');
        $seller_id = $request->post('seller_id');
        $platform = $request->post('platform');
        $purchased = $this->storeSpotPurchaseFromUser($promo_id, $card_amount, $customer_id, $month, $seller_id, $platform);
        if ($purchased) {
            $month_txt = $purchased->month > 1 ? ' months' : ' month';
            $msg = 'Congratulations! Your payment for '.$purchased->month.$month_txt.' membership has been successful and it will expire on '.
                date('M d, Y', strtotime($purchased->expiry_date)).'.';

            return Response::json(['result' => $msg], 200);
        } else {
            return Response::json(['result' => 'Something went wrong, please try again.'], 201);
        }
    }

    //store user spot purchased info
    public function storeSpotPurchaseFromUser($promo_id, $card_amount, $customer_id, $month, $seller_id, $platform)
    {
        $user = CardSellerAccount::where('id', $seller_id)->with('info')->first();
        $commission = $user->info->commission;
        //balance section
        $seller_balance = SellerBalance::where('seller_id', $user->info->id)->first();
        $all_amount = AllAmounts::all();
        $per_card_sell = $all_amount[11]['price'];
        $royaltybd_transaction_id = (new JsonControllerV2)->getSSLTransactionId();

        $promo = CardPromoCodes::where('id', $promo_id)->first();

        //expiry date
        $date = date_create(date('Y-m-d'));
        $expiry_date = date_add($date, date_interval_create_from_date_string($month.' months'));
        $expiry_date = $expiry_date->format('Y-m-d');

        try {
            DB::beginTransaction(); //to do query rollback
            $customer_history_count = CustomerHistory::where('customer_id', $customer_id)->count();
            CustomerInfo::where('customer_id', $customer_id)
                ->update([
                        'customer_type' => 2,
                        'month' => $month,
                        'card_active' => 2,
                        'card_activation_code' => 0,
                        'expiry_date' => $expiry_date,
                        'approve_date' => date('Y-m-d H:i:s'),
                        'delivery_status' => 3,
                    ]);

            $main_price = CardPrice::where('platform', $platform)
                ->where('type', MembershipPriceType::buy)
                ->where('month', $month)
                ->first();
            if ($customer_history_count > 0) {
                $commission_received = $this->updateSellerBalance($main_price->price, 0,
                    $seller_balance, $per_card_sell, $month, true, $card_amount);
            } else {
                $commission_received = $this->updateSellerBalance($main_price->price, $commission,
                    $seller_balance, $per_card_sell, $month, true, $card_amount);
            }
            //store new customer_id
            $assigned_card = new AssignedCard();
            $assigned_card->card_number = $customer_id;
            $assigned_card->status = 1;
            $assigned_card->card_type = 2;
            $assigned_card->month = $month;
            $assigned_card->seller_account_id = $user->id;
            $assigned_card->assigned_on = date('Y-m-d H:i:s');
            $assigned_card->sold_on = date('Y-m-d H:i:s');
            $assigned_card->save();

            //insert into ssl transaction table
            $ssl_transaction = new SslTransactionTable([
                'customer_id' => $customer_id,
                'status' => 1,
                'tran_date' => date('Y-m-d H:i:s'),
                'tran_id' => $royaltybd_transaction_id,
                'val_id' => '',
                'amount' => $card_amount,
                'store_amount' => '0.00',
                'card_type' => 'CASH',
                'card_no' => '',
                'currency' => 'BDT',
                'bank_tran_id' => '',
                'card_issuer' => '',
                'platform' => $platform,
                'card_brand' => '',
                'card_issuer_country' => '',
                'card_issuer_country_code' => '',
                'currency_amount' => '0.00',
                'month' => $month,
            ]);
            $ssl_transaction->save();
            DB::table('card_delivery')->insert([
                'customer_id' => $customer_id,
                'delivery_type' => DeliveryType::spot_delivery,
                'shipping_address' => 'On Spot',
                'paid_amount' => $card_amount,
                'order_date' => date('Y-m-d'),
                'ssl_id' => $ssl_transaction->id,
            ]);

            if ($promo) {
                CardPromoCodeUsage::insert([
                    'customer_id' => $customer_id,
                    'promo_id' => $promo_id,
                    'ssl_id' => $ssl_transaction->id,
                ]);
            }
            $this->saveSellerCommissionHistory($user->info->id, $ssl_transaction->id, $commission_received, SellerCommissionType::SALES_APP);
            (new \App\Http\Controllers\AdminNotification\functionController())->salesAppCardSoldNotification($assigned_card);
            DB::commit(); //to do query rollback
            $customer = CustomerInfo::where('customer_id', $customer_id)->first();
            (new self())->sendSellerBalanceSMS($user->info->id, $user->phone,
                $main_price->price, $commission, $month, $customer->customer_full_name);
//            $this->sendBuyCardNotification("", $customer);
            (new functionController2())->addToCustomerHistory($customer_id, $user->id, CustomerType::card_holder,
                $ssl_transaction->id, $promo_id);

            //for mailing
            $customer->tran_id = $ssl_transaction->tran_id;
            $customer->delivery_type = DeliveryType::spot_delivery;
            $validity = $customer->month == 12 ? 'one year' : $customer->month.' months';
            (new adminController())->OnlinePaymentMail($customer->customer_full_name, $customer->customer_email,
                $customer, $validity);
            //for mailing ends

            return CustomerInfo::where('customer_id', $customer_id)->first();
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return false;
        }
    }

    //save as commission history
    public function saveSellerCommissionHistory($id, $ssl_id, $commission, $type)
    {
        $commission_history = new SellerCommissionHistory();
        $commission_history->seller_id = $id;
        $commission_history->ssl_id = $ssl_id;
        $commission_history->commission = $commission;
        $commission_history->type = $type;
        $commission_history->save();

        return $commission_history;
    }

    public function updateSellerBalance($card_amount, $commission, $seller_balance, $per_card_sell, $month, $update_debit = true, $user_paid = 0)
    {
        if ($card_amount > 0) {
            $final_commission = round($card_amount * ($commission / 100));
            $seller_balance->increment('credit', $final_commission);
            if ($update_debit) {
                $seller_balance->increment('debit', $user_paid - $final_commission);
            }

            return $final_commission;
        }
    }

    public function sendSellerBalanceSMS($id, $phone, $price, $commission, $month, $user_name)
    {
        $seller_cur_balance = SellerBalance::where('seller_id', $id)->first();
        $final_commission = ceil($price * ($commission / 100));
        $month_text = $month > 1 ? ' months ' : ' month ';
        $message = $user_name.' has purchased '.$month.$month_text.'Royalty Membership on '.date('M d, Y h:i A').
            ". You've earned Tk ".$final_commission.' commission. Your total balance is Tk '.$seller_cur_balance->credit.'.';
        (new apiController())->sendSms($phone, $message);
    }

    public function sendBuyCardNotification($message, $customer)
    {
        $session = CustomerLoginSession::where('customer_id', $customer->customer_id)->orderBy('id', 'DESC')->first();
        if ($session && $session->status == LoginStatus::logged_in) {
            if ($session->platform == PlatformType::android) {
                $this->sendAndroidBuyCardNotification($message, $session->physical_address);
            } elseif ($session->platform == PlatformType::ios) {
                $this->sendIosBuyCardNotification($message, $session->physical_address);
            }
        }
    }

    public function sendAndroidBuyCardNotification($message, $firebaseRegId)
    {

        // notification title
        $res = [];
        $res['data']['title'] = 'Royalty';
        $res['data']['is_background'] = false;
        $res['data']['message'] = $message;
        $res['data']['timestamp'] = date('Y-m-d G:i:s');
        // optional payload
        $buy_card = [];
        $buy_card['notification_type'] = 112; //feed type notification
        $res['data']['buy_card'] = $buy_card;
        (new jsonController())->sendFirebaseMessage($firebaseRegId, $res);
    }

    public function sendIosBuyCardNotification($message, $firebaseRegId)
    {

        // notification title
        $res = [];
        $res['title'] = 'Royalty';
        $res['is_background'] = false;
        $res['body'] = $message;
        $res['timestamp'] = date('Y-m-d G:i:s');
        // optional payload
        $buy_card = [];
        $buy_card['notification_type'] = 112; //feed type notification
        $res['buy_card'] = $buy_card;
        (new jsonController())->sendIosFirebaseMessage($firebaseRegId, $res);
    }

    public function versionControl()
    {
        $data2 = [
            'version' => 21,
            'force_update' => true,
            'app_url' => 'https://play.google.com/store/apps/details?id=com.royaltybd.royalty_salesforce&hl=en',
        ];

        return response()->json($data2);
    }

    public function salesEncrypt(Request $request)
    {
        $enc_txt = $request->post('enc_txt');

        return response()->json(((new \App\Http\Controllers\functionController)->encrypt_decrypt('encrypt', $enc_txt)));
    }

    public function salesDecrypt(Request $request)
    {
        $enc_txt = $request->post('enc_txt');

        return response()->json(((new \App\Http\Controllers\functionController)->encrypt_decrypt('decrypt', $enc_txt)));
    }
}
