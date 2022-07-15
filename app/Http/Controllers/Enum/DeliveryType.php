<?php

/**
 * Created by PhpStorm.
 * User: Sohel
 * Date: 10/13/2018
 * Time: 11:15 AM.
 */

namespace App\Http\Controllers\Enum;

abstract class DeliveryType
{
    //enum type for delivery type
    const home_delivery = 1;
    const office_pickup = 2;
    const cod = 3;
    const guest_user = 4;
    const card_customization = 5;
    const lost_card_without_customization = 6;
    const lost_card_with_customization = 7;
    const b2b2c_user = 8;
    const spot_delivery = 9;
    const influencer_delivery = 10;
    const virtual_card = 11;
    const renew = 12;
    const made_by_admin = 13;
}
