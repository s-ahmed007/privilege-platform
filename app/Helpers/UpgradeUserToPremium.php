<?php


namespace App\Helpers;

use App\CustomerInfo;
use App\Http\Controllers\Enum\CustomerType;
use App\Http\Controllers\Enum\DeliveryType;
use App\Http\Controllers\Enum\PlatformType;
use App\Http\Controllers\Enum\SellerRole;
use App\Http\Controllers\Enum\ssl_validation_type;
use App\Http\Controllers\functionController2;
use App\Http\Controllers\JsonControllerV2;
use App\SslTransactionTable;
use App\Wish;
use Illuminate\Support\Facades\DB;

class UpgradeUserToPremium
{
    public function upgradeUser()
    {
        $expiry_date = date_create('2025-12-31');
        //create entry for guest user to required tables
        $guest_users = CustomerInfo::where('customer_type', 3)->get();
        $problematic_users = [];
        foreach ($guest_users as $customer) {
            try {
                DB::beginTransaction(); //to do query rollback

                $this->convertGuestToPremium($customer->customer_id, PlatformType::rbd_admin, SellerRole::fromAdmin);

                DB::commit(); //to do query rollback
            } catch (\Exception $e) {
                DB::rollBack(); //rollback all successfully executed queries
                array_push($problematic_users, $customer->customer_id);
            }
        }
        if (count($problematic_users) > 0) {
            $problematic_users = json_encode($problematic_users);
            $any_issue = "PROBLEM";

            $wish = new Wish();
            $wish->customer_id = $any_issue;
            $wish->comment = $problematic_users;
            $wish->posted_on = date('Y-m-d H:i:s');
            $wish->save();
        }

        DB::table('customer_info')->update([
            'customer_type' => 2,
            'expiry_date' => $expiry_date
        ]);
        DB::table('customer_history')->update([
            'type' => CustomerType::card_holder
        ]);

        return 0;
    }

    public function convertGuestToPremium($customer_id, $platform, $seller_id = null)
    {
        $amount = 0;
        $month =0;
        $tran_id = (new JsonControllerV2())->getSSLTransactionId();
        $promo_id = 0;
        $tran_date = date('Y-m-d H:i:s');
        $delivery_type = DeliveryType::home_delivery;
        $expiry_date = date_create('2025-12-31');

        $ssl_info = new SslTransactionTable([
            'customer_id' => $customer_id,
            'status' => ssl_validation_type::valid,
            'tran_id' => $tran_id,
            'amount' => $amount,
            'platform' => $platform,
            'tran_date' => $tran_date,
            'store_amount' => $amount,
        ]);
        $ssl_info->save();

        DB::table('customer_info')
            ->where('customer_id', $customer_id)
            ->update([
                'customer_type' => 2,
                'month' => $month,
                'expiry_date' => $expiry_date,
                'card_active' => 2,
                'delivery_status' => 1,
            ]);
        DB::table('card_delivery')->insert([
            'customer_id' => $customer_id,
            'delivery_type' => $delivery_type,
            'order_date' => date('Y-m-d'),
            'paid_amount' => $amount,
            'ssl_id' => $ssl_info->id,
        ]);

        (new functionController2())->addToCustomerHistory(
            $customer_id,
            $seller_id,
            CustomerType::card_holder,
            $ssl_info->id,
            $promo_id
        );
    }
}