<?php

namespace App\Http\Controllers\Renew;

use App\AllAmounts;
use App\CardPromoCodeUsage;
use App\CardSellerInfo;
use App\CustomerAccount;
use App\CustomerHistory;
use App\Http\Controllers\adminController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\CustomerType;
use App\Http\Controllers\Enum\DeliveryType;
use App\Http\Controllers\Enum\ssl_validation_type;
use App\Http\Controllers\functionController2;
use App\InfoAtBuyCard;
use App\SellerBalance;
use App\SslTransactionTable;
use DateTime;
use Illuminate\Support\Facades\DB;

class functionController extends Controller
{
    public function insertInfoRenew($customer, $tran_id, $month, $delivery_type, $promo_id, $amount, $platform, $isAdmin = false)
    {
        if ($month == null) {
            $month = 12;
        }
        try {
            \DB::beginTransaction();
            {
                $info_at_buy_card = new InfoAtBuyCard([
                    'customer_id' => $customer->customer_id,
                    'tran_id' => $tran_id,
                    'customer_serial_id' => $customer->customer_serial_id,
                    'customer_username' => $customer->customer_username,
                    'password' => 'Asdf1234',
                    'moderator_status' => $customer->moderator_status,
                    'customer_first_name' => 'RENEW',
                    'customer_last_name' => 'RENEW',
                    'customer_full_name' => $customer->info->customer_full_name,
                    'customer_email' => $customer->info->customer_email,
                    'customer_dob' => $customer->info->customer_dob,
                    'customer_gender' => $customer->info->customer_gender,
                    'customer_contact_number' => $customer->info->customer_contact_number,
                    'customer_address' => $customer->info->customer_address,
                    'customer_profile_image' => $customer->info->customer_profile_image,
                    'customer_type' => 2,
                    'month' => $month,
                    'expiry_date' => $customer->info->expiry_date,
                    'member_since' => $customer->info->member_since,
                    'referral_number' => '0',
                    'reference_used' => $customer->info->reference_used,
                    'card_active' => 2,
                    'card_activation_code' => $customer->info->card_activation_code,
                    'firebase_token' => $customer->info->firebase_token,
                    'delivery_status' => 0,
                    'review_deleted' => $customer->info->review_deleted,
                    'delivery_type' => $delivery_type,
                    'card_promo_id' => $promo_id,
                    'order_date' => date('Y-m-d H:i:s'),
                    'paid_amount' => $amount,
                    'platform' => $platform,
                ]);
            }
            $info_at_buy_card->save();
            if ($delivery_type != 11) {
                $customer_history = CustomerHistory::where('customer_id', $customer->customer_id)->with('customerInfo')->orderBy('id', 'DESC')->first();
                $isUpgrade = false;

                if ($customer_history && $customer_history->type == 3) {
                    if (date('Y-m-d') <= $customer_history->customerInfo->expiry_date) {
                        $isUpgrade = true;
                    }
                }
                if (! $isAdmin) {
                    (new \App\Http\Controllers\AdminNotification\functionController())->renewAttemptNotification($info_at_buy_card, $isUpgrade);
                }
            }

            //insert info into ssl transaction table
            \DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            \DB::rollBack(); //rollback all successfully executed queries
            return null;
        }

        return $info_at_buy_card;
    }

    public function insertSSLRenew($customer_id, $tran_id, $amount, $platform)
    {
        try {
            \DB::beginTransaction();
            $temp_ssL_data = new SslTransactionTable([
                'customer_id' => $customer_id,
                'status' => ssl_validation_type::not_valid,
                'tran_id' => $tran_id,
                'amount' => $amount,
                'platform' => $platform,
            ]);
            $temp_ssL_data->save();
            \DB::commit(); //to do query rollback
        } catch (\Exception $e) {
            \DB::rollBack(); //rollback all successfully executed queries
            return null;
        }

        return $temp_ssL_data;
    }

    public function updateSSLRenew($amount, $tran_id, $tran_date, $val_id, $store_amount, $card_type,
                                   $card_no, $currency, $bank_tran_id, $card_issuer,
                                   $card_brand, $card_issuer_country, $card_issuer_country_code,
                                   $currency_amount, $customer_id, $seller_id = null, $admin_id = null)
    {
        $transaction = SslTransactionTable::where('amount', $amount)->where('tran_id', $tran_id)->first();
        $temporary_info = DB::table('info_at_buy_card')->where('tran_id', $tran_id)->first();
        $customer = CustomerAccount::with('info')->where('customer_id', $customer_id)->first();
        $curDate = date('Y-m-d');
        $exp_date = $customer->info->expiry_date;

        if (! $transaction) {
            return null;
        }
        try {
            \DB::beginTransaction();
            SslTransactionTable::where('tran_id', $tran_id)
                ->where('amount', $amount)
                ->update([
                    'status' => ssl_validation_type::valid,
                    'tran_date' => date('Y-m-d H:i:s'),
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
            $ssl_info = SslTransactionTable::where('tran_id', $tran_id)->first();
            $cur_date = new DateTime($curDate);
            $expiry_date = new DateTime($exp_date);
            $interval = date_diff($cur_date, $expiry_date);
            $daysRemaining = $interval->format('%R%a');
            $date = date_create(date('Y-m-d'));
            $expiry_date = date_add($date, date_interval_create_from_date_string($temporary_info->month.' month'));

            if ($daysRemaining > 0 && $daysRemaining < 11) {
                $expiry_date = date_add($expiry_date, date_interval_create_from_date_string($daysRemaining.' days'));
            }
            $expiry_date = $expiry_date->format('Y-m-d');

            if ($temporary_info->delivery_type == DeliveryType::renew) {
                $delivery_status = 3;
            } else {
                $delivery_status = 1;
            }
            DB::table('customer_info')
                ->where('customer_id', $customer_id)
                ->update([
                    'customer_type' => 2,
                    'month' => $temporary_info->month,
                    'expiry_date' => $expiry_date,
                    'card_active' => 2,
                    'delivery_status' => $delivery_status,
                ]);
            DB::table('card_delivery')->insert([
                'customer_id' => $customer_id,
                'delivery_type' => $temporary_info->delivery_type,
                'shipping_address' => $temporary_info->shipping_address,
                'order_date' => $temporary_info->order_date,
                'paid_amount' => $amount,
                'ssl_id' => $ssl_info->id,
            ]);

            //save card promo usage data if exists
            if ($temporary_info->card_promo_id != null && $temporary_info->card_promo_id != 0) {
                CardPromoCodeUsage::insert([
                    'customer_id' => $temporary_info->customer_id,
                    'promo_id' => $temporary_info->card_promo_id,
                    'ssl_id' => $ssl_info->id,
                ]);
                //update influencer payment info if this promo belongs to anyone
                (new \App\Http\Controllers\functionController)->updateInfluencerPaymentInfo($temporary_info->card_promo_id, $amount);
            }
            \DB::commit(); //to do query rollback

            if ($temporary_info->month == 12) {
                $validity = 'one year';
            } elseif ($temporary_info->month > 1) {
                $validity = $temporary_info->month.' months';
            } else {
                $validity = $temporary_info->month.' month';
            }

            //sales and email
            $seller_info = CardSellerInfo::where('promo_ids', 'like', "%\"{$temporary_info->card_promo_id}\"%")->first();
            if ($seller_info) {
                $seller_balance = SellerBalance::where('seller_id', $seller_info->id)->first();
                $commission = $seller_info->commission;
                $trial_commission = $seller_info->trial_commission;
            } else {
                $seller_balance = null;
                $commission = null;
                $trial_commission = 0;
            }
            $all_amount = AllAmounts::all();
            $per_card_sell = $all_amount[11]['price'];
            if ($temporary_info->delivery_type == DeliveryType::virtual_card) {
                //make history
                if ($seller_info) {
                    $history = (new functionController2())->addToCustomerHistory($customer_id, $seller_info->seller_account_id,
                        CustomerType::trial_user,
                        $ssl_info->id, $temporary_info->card_promo_id);
                    $seller_balance->increment('credit', $trial_commission);
                    $seller_balance->decrement('debit', $trial_commission);
                } else {
                    $history = (new functionController2())->addToCustomerHistory($customer_id, $seller_id,
                        CustomerType::trial_user,
                        $ssl_info->id, $temporary_info->card_promo_id, $admin_id);
                }
                //mail user
                (new adminController)->VirtualUserMail($temporary_info->customer_full_name, $temporary_info->customer_email,
                    $temporary_info, $validity);
                (new \App\Http\Controllers\AdminNotification\functionController())->trialActivateNotification($temporary_info, $history);
            } else {
                //make history
                $customer_history = CustomerHistory::where('customer_id', $customer_id)->with('customerInfo')->orderBy('id', 'DESC')->first();
                $isUpgrade = false;

                if ($customer_history && $customer_history->type == 3) {
                    $date_to_check = date('Y-m-d', strtotime(date('Y-m-d').' + 10 days'));
                    if ($date_to_check < $exp_date) {
                        $isUpgrade = true;
                    }
                }
                (new functionController2())->addToCustomerHistory($customer_id, $seller_id,
                    CustomerType::card_holder,
                    $ssl_info->id, $temporary_info->card_promo_id);
                //mail user
//                if ($seller_id == null) {
                (new adminController)->RenewPaymentMail($temporary_info->customer_full_name, $temporary_info->customer_email,
                        $temporary_info, $validity, $isUpgrade);
//                }
                (new \App\Http\Controllers\AdminNotification\functionController())->renewNotification($temporary_info, $isUpgrade, $admin_id);
            }
            //sales and email

            DB::table('info_at_buy_card')->where('customer_id', $customer_id)->delete();

            return $customer->info;
        } catch (\Exception $e) {
            DB::rollBack(); //rollback all successfully executed queries
            return null;
        }
    }
}
