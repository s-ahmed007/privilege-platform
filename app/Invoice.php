<?php
/**
 * Created by PhpStorm.
 * User: muhtadi
 * Date: 9/27/18
 * Time: 8:29 PM.
 */

namespace App;

use App\Http\Controllers\Enum\DeliveryType;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;

class Invoice
{
    protected $pdf;

    public function __construct()
    {
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $this->pdf = new Dompdf($options);
    }

    public function generateAdminActivityReport()
    {
        $this->pdf->loadHtml(
            View::make('pdf.notification_report')->render()
        );
        $this->pdf->render();

        return $this->pdf->output();
    }

    public function generate($exist)
    {
        $ssl_info = SslTransactionTable::where('tran_id', $exist->tran_id)->first();
        $card_usage = CardPromoCodeUsage::where('ssl_id', $ssl_info->id)->get();
        if (count($card_usage) > 0) {
            $promo_used = true;
        } else {
            $promo_used = false;
        }
        $card_prices = AllAmounts::all();
        $gold_card_price = $card_prices[0]->price;
        $platinum_card_price = $card_prices[1]->price;
        $price = $ssl_info->amount;
        $delivery_charge = $card_prices[3]->price;
        $customization_charge = $card_prices[4]->price;
        $lost_card_charge = $card_prices[5]->price;
        $customization_delivery_type = DeliveryType::card_customization;
        $lost_card_customization_delivery_type = DeliveryType::lost_card_with_customization;
        $renew_delivery_type = DeliveryType::renew;
        $lost_card_no_customization_delivery_type = DeliveryType::lost_card_without_customization;

        if ($exist->delivery_type == $customization_delivery_type || $exist->delivery_type == $lost_card_customization_delivery_type) {
            $customization_cost = $customization_charge;
        } elseif ($exist->delivery_type == DeliveryType::spot_delivery) {
            $delivery_charge = 0;
            $customization_cost = 0;
        } else {
            $customization_cost = 0;
        }

        $this->pdf->loadHtml(
            View::make('invoice', compact('exist', 'gold_card_price', 'promo_used', 'platinum_card_price', 'price', 'delivery_charge', 'lost_card_charge',
                'customization_cost', 'customization_delivery_type', 'lost_card_customization_delivery_type', 'lost_card_no_customization_delivery_type', 'renew_delivery_type'))->render()
        );
        $this->pdf->render();

        return $this->pdf->output();
    }
}
