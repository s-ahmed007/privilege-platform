<?php

namespace App\Http\Controllers\admin;

use App\CustomerHistory;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Enum\DeliveryType;
use App\Http\Controllers\functionController2;

class PurchaseHistoryController extends Controller
{
    public function getAllPurchaseHistory($status)
    {
        $history = CustomerHistory::with('customerInfo', 'sslInfo.cardDelivery', 'sellerInfo')->withCount('customerPaymentHistory')->get();
        $history = collect($history)
            ->where('sslInfo.cardDelivery.delivery_type', '!=', DeliveryType::virtual_card)
            ->where('sslInfo.cardDelivery.delivery_type', '!=', DeliveryType::guest_user)
            ->where('sslInfo.cardDelivery.delivery_type', '!=', DeliveryType::b2b2c_user);
        if ($status == 'new') {
            $tab_title = 'New';
            $history = collect($history)->where('customer_payment_history_count', '<', 2);
        } elseif ($status == 'expired') {
            $tab_title = 'Expired Purchase';
            $history = $history->reject(function ($item) {
                $days_remaining = (new \App\Http\Controllers\functionController2())->daysRemaining(date('Y-m-d', strtotime($item->sslInfo->tran_date.' + '.$item->sslInfo->month.' months')));

                return $days_remaining > 0;
            });
        } elseif ($status == 'renewed') {
            $tab_title = 'Renew';
            $history = collect($history)->where('sslInfo.cardDelivery.delivery_type', DeliveryType::renew);
            $history = $history->reject(function ($item) {
                return $item->customerInfo->isUpgrade() == true;
            });
        } elseif ($status == 'upgraded') {
            $tab_title = 'Upgrade';
            $history = collect($history)->where('sslInfo.cardDelivery.delivery_type', DeliveryType::home_delivery);
            $history = $history->reject(function ($item) {
                return $item->customerInfo->isUpgrade() != true;
            });
        } else {
            $tab_title = 'All';
        }
        $history = collect($history)->sortByDesc('sslInfo.tran_date');
        $data = (new functionController2())->getPaginatedData($history, 20);

        return view('admin.production.PurchaseHistory.all', compact('data', 'tab_title'));
    }
}
